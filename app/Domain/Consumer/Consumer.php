<?php
namespace App\Domain\Consumer;

use App\Models\RequestResult;
use Exception;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class Consumer{

    private string $str;
    private int $requests = 1;
    private string $url_root ;
    private int $max_requests = 10;
    private int $wait_seconds = 60;
    private string $route = "/hash/generate/";

    public function __construct(string $str, int $requests)
    {
        $this->str      = $str;
        $this->requests = $requests;
        $this->url_root = env('APP_URL') . '/api';
    }
    
    private function getCurDate() : string
    {
        $datetime = Carbon::now();
        return $datetime->toDateTimeString();
    }

    private function storeResult(array $result) : void
    {
        try{
            RequestResult::create($result);
        }catch(Exception $e){
            Log::error($e);
        }
    }

   
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

    
    private function wait(string $firstRequestDateTime, string $lastRequestDateTime) : void
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

    
    public function execute()
    {
        $requestsProcessed    = 0;
        $requestsPerMinute    = 0;
        $batch                = $this->getCurDate();
        $firstRequestDateTime = $this->getCurDate();
        $lastRequestDateTime  = $this->getCurDate();

        while($requestsProcessed < $this->requests){
            $requestsPerMinute++;
            $requestsProcessed++;
            
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
          
            $this->str = $response['data']["hash"];
            $result = array_merge($result, $response['data']);
            $this->storeResult($result);
        
            if($requestsPerMinute == $this->max_requests){
                $this->wait($firstRequestDateTime, $lastRequestDateTime);
                $requestsPerMinute = 0;
                $firstRequestDateTime = $this->getCurDate();
            }
        }
    }

}
?>