<?php

namespace App\Console\Commands;

use App\Domain\Consumer;
use Illuminate\Console\Command;

class HashTest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'avato:test {str} {--requests=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Esse comando irá gerar tantos hashes quanto o valor informando em --requests. Vai salvar em banco de dados.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $str      = $this->argument('str');
        $requests = $this->option('requests');

        if(!$str){
            $this->error('The str parameter is required!');
            exit;
        }

        if( !is_numeric($requests) || $requests <=0 ){
            $this->error('The --requests option is required and must be an integer value > 0 !');
            exit;
        }

        $consumer = new Consumer($str, $requests);
        $this->info('Executing... Please wait!');
        $consumer->execute();
        $this->info('The command was successful!');
        
    }
}
