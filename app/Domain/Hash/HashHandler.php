<?php 
namespace App\Domain\Hash;

use Illuminate\Support\Str;

class HashHandler{

    private int $key_length = 8;
    private string $prefix = '0000';
    private string $str;

    public function __construct(string $str)
    {
        $this->str = $str;
    }

    private function validateHash(string $hash)
    {
        $hash_prefix = (string) substr($hash, 0, 4);
        if( strcmp($this->prefix, $hash_prefix) == 0){
            return true;
        }
        return false;
    }

    private function getRandomKey() : string
    {
        return Str::random($this->key_length);
    }

    private function generateHash() : array
    {
        $key  = $this->getRandomKey();
        $str  = trim($this->str).$key;
        $hash = md5($str);
        return ['key_found' => $key, 'hash' => $hash];
    }

    private function find() : array
    {
        $found       = false;
        $result      = [];
        $count       = 0;
        $tries_limit = 999999;

        while($found == false){
            $count ++;
            $hash_result = $this->generateHash();
            if($this->validateHash($hash_result['hash'])){
                $found = true;
                $hash_result['tries'] = $count;
                $result = $hash_result;
            }
            if($count == $tries_limit){
                break;
            }
        }
        return $result;
    }

    public function execute() : array
    {
       $result = $this->find();
       return $result;
    }

}

?>