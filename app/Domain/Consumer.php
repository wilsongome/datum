<?php
namespace App\Domain;

use App\Models\RequestResult;
use Exception;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class Consumer{

    private $str = null;
    private $requests = 1;
    private $url_root = null;
    private $max_requests = 10;
    private $wait_seconds = 60;
    private $route = "/hash/generate/";
   

    public function __construct(string $str, int $requests)
    {
        $this->str      = $str;
        $this->requests = $requests;
        $this->url_root = env('APP_URL') . '/api';
    }

    /**
     * Como utilizo datas em várias partes do código, criei este método para padronizar o formato
     */
    private function getCurDate() : string
    {
        $datetime = Carbon::now();
        return $datetime->toDateTimeString();
    }

    /**
     * Este método irá salvar o resultado no banco, utilizando a Model
     * Configurei os fillables na Model, então nem preciso setar os atributos
     */
    private function storeResult(array $result) : void
    {
        try{
            RequestResult::create($result);
        }catch(Exception $e){
            Log::error($e);
        }
    }

    /**
     * Este método faz a requisição na rota, e retorna o valor
     */
    private function request() : array
    {
        $result = array();
        try{
            $route = $this->url_root.$this->route.$this->str;
            $response = Http::get($route);
            $result['status'] = $response->status();
            if($response->status() == 200){
                $result['data'] = json_decode($response, true);
                return $result;
            }
            /*Como eu não tenho controle das chamadas de comandos anteriores, e a rota é independente,
              coloquei este controle que, caso retorne 429, vai aguardar o tempo padrão de 1 minuto.
              Até porque, a rota pode estar sendo consuminda também fora do command, contabilizando no seu limite de requisições

              Para evitar isso, eu teria que criar algum controle no acionamento da rota, LOG, para que eu pudsse criar uma lógica
              baseada nas requisições da rota, independente de onde partiram.
              Mas o teste não pde isso, então eu optei por seguir os requisitos sem gerar mais complexidade.
            */
            if($response->status() == 429){
                sleep($this->wait_seconds);
                $result = $this->request();
                if($result['status'] == 200){
                    $result['status'] = $response->status();
                }
            }
            return $result;
        }catch(Exception $e){
            $result['status'] = 500;
            return $result;
        }
    }

    /**
     * Este método faz a lógica do intervalo nas requisições, conforme o limite por minuto.
     * Ele calcula quanto tempo terá que esperar, baseado no horário das últimas requisições
     */
    private function wait($firstRequestDateTime, $lastRequestDateTime) : void
    {
        $first = Carbon::createFromFormat('Y-m-d H:i:s', $firstRequestDateTime);
        $last  = Carbon::createFromFormat('Y-m-d H:i:s', $lastRequestDateTime);
        
        $seconds = $last->diffInSeconds($first);
        $secondsToWait = ( ($this->wait_seconds - $seconds) + 1 );
        if($secondsToWait <= 0){
            $secondsToWait = 1;
        }
        if($secondsToWait > $this->wait_seconds){
            $secondsToWait = $this->wait_seconds;
        }
        sleep($secondsToWait);
    }

    /**
     * Este é o método principal, que faz todo o controle das requisições, conforme a quantidade informada.
     */
    public function execute()
    {
        $requestsProcessed    = 0;
        $requestsPerMinute    = 0;
        $batch                = $this->getCurDate();
        $firstRequestDateTime = $this->getCurDate();
        $lastRequestDateTime  = $this->getCurDate();

        while($requestsProcessed < $this->requests){
            //Contadores
            $requestsPerMinute++;
            $requestsProcessed++;
            
            //Array que será salvo no banco
            $result = [];
            $result['batch']        = $batch;
            $result['order_number'] = $requestsProcessed;
            $result['str_in']       = $this->str;

            $response = $this->request();
            $lastRequestDateTime = $this->getCurDate();

            if($response['status'] != 200 && $response['status'] != 429){
                die("Erro ".$response['status']);
                break;
            }

            if($response['status'] == 429){
                $requestsPerMinute = 0;
                $firstRequestDateTime = $this->getCurDate();
            }
          
            //Carrega a nova string, conforme retorno
            $this->str = $response['data']["hash"];

            //Prepara o resulado
            $result = array_merge($result, $response['data']);
            
            //Salvar o resultado no banco
            $this->storeResult($result);
            
            //Controle para evitar erro 429
            if($requestsPerMinute == $this->max_requests){
                $this->wait($firstRequestDateTime, $lastRequestDateTime);
                $requestsPerMinute = 0;
                $firstRequestDateTime = $this->getCurDate();
            }
        }
    }

}
?>