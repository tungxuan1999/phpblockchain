<?php
require_once("./blockchain.php");

class Exchange{
    protected $url = "https://demoblockchain.firebaseio.com/exchange/";

    public function __construct()
    {
        $this->difficulty = 4;
    }

    private function pushData($name,$block)
    {
        $curl = curl_init();
        $ar = $block->data;
        if($block->previousHash==null)
        {
            $previousHash = "null";
        }
        else{
            $previousHash = $block->previousHash;
        }
        $data = array(
            CURLOPT_URL => $this->url."$name/.json",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "PATCH",
            CURLOPT_POSTFIELDS =>"{\r\n    \"$block->index\": {\r\n        \"nonce\": $block->nonce,\r\n        \"index\": $block->index,\r\n        \"timestamp\": $block->timestamp,\r\n        \"data\": {\r\n            \"amount\": \"".$ar['amount']."\",\r\n            \"result\": \"".$ar['result']."\"\r\n        },\r\n        \"previousHash\": \"$previousHash\"\r\n,\r\n        \"hash\": \"$block->hash\"\r\n    }\r\n}",
            CURLOPT_HTTPHEADER => array(
                "Content-Type: application/json"
            ),
        );
        curl_setopt_array($curl, $data);

        $response = curl_exec($curl);

        curl_close($curl);
    }

    public function getData($account)
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => $this->url."$account/.json",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return $response;
    }

    private function getDataDetail($account,$stt)
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => $this->url."$account/$stt/.json",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return $response;
    }
    public function createAccount($account)
    {
        if($this->getData($account) == "null"){
            $data = new BlockChain($account);
            return array("success"=>true,"data"=>"$account successfully created");
        }
        else{
            return array("success"=>false,"data"=>"$account already exists");
        }
    }

    public function rechargeAccount($account,$amount)
    {
        $data = $this->getData($account);
        if($data == "null"){
            return array("success"=>false,"data"=>"$account has not been created");
        }
        else{
            $stt = 0;
            $json = json_decode($data,true);
            while(true){
                if(isset($json[$stt+1]))
                {
                    $stt++;
                }
                else{
                    break;
                }
            }
            date_default_timezone_set("Asia/Ho_chi_minh");
            $time = date("H:i:s d-m-Y");
            $abc = (int) $json[$stt]['data']['amount']+$amount;
            $acd = array("amount"=>"$abc","result"=>"Recharge successfully: +$amount");
            $this->push($account,new Block($stt+1,strtotime($time),$acd),$json[$stt]['hash']);
            return array("success"=>true,"data"=>"Recharge successfully +".$amount);
        }
    }

    public function transferAccount($account1, $account2, $amount)
    {
        $data1 = $this->getData($account1);
        $data2 = $this->getData($account2);
        if($data1 == 'null')
        {
            return array("success"=>false,"data"=>"$account1 has not been created");
        }
        else{
            $stt = 0;
            $json = json_decode($data1,true);
            while(true){
                if(isset($json[$stt+1]))
                {
                    $stt++;
                }
                else{
                    break;
                }
            }
            if((int) $json[$stt]['data']['amount'] < (int) $amount)
            {
                return array("success"=>false,"data"=>"Amount (".(int) $json[$stt]['data']['amount'].") in account must >= $amount");
            }
            else{
                date_default_timezone_set("Asia/Ho_chi_minh");
                $time = date("H:i:s d-m-Y");
                $abc = (int) $json[$stt]['data']['amount']-$amount;
                $acd = array("amount"=>"$abc","result"=>"Transfer successfully to $account2: -$amount");
                $this->push($account1,new Block($stt+1,strtotime($time),$acd),$json[$stt]['hash']);
                if($data2 == 'null')
                {
                    $data1 = $this->getData($account1);
                    $stt = 0;
                    $json = json_decode($data1,true);
                    while(true){
                        if(isset($json[$stt+1]))
                        {
                            $stt++;
                        }
                        else{
                            break;
                        }
                    }
                    date_default_timezone_set("Asia/Ho_chi_minh");
                    $time = date("H:i:s d-m-Y");
                    $abc = (int) $json[$stt]['data']['amount']+$amount;
                    $acd = array("amount"=>"$abc","result"=>"Transfer successfully to $account2 but $account2 has not been existed: +$amount");
                    $this->push($account1,new Block($stt+1,strtotime($time),$acd),$json[$stt]['hash']);
                    return array("success"=>true,"data"=>"Transfer successfully to $account2 but $account2 has not been existed: +$amount");
                }
                else{
                    $stt = 0;
                    $json = json_decode($data2,true);
                    while(true){
                        if(isset($json[$stt+1]))
                        {
                            $stt++;
                        }
                        else{
                            break;
                        }
                    }
                    date_default_timezone_set("Asia/Ho_chi_minh");
                    $time = date("H:i:s d-m-Y");
                    $abc = (int) $json[$stt]['data']['amount']+$amount;
                    $acd = array("amount"=>"$abc","result"=>"Receive successful money from $account1: +$amount");
                    $this->push($account2,new Block($stt+1,strtotime($time),$acd),$json[$stt]['hash']);
                    return array("success"=>false,"data"=>"$account2 receive successful money from $account1: +$amount");
                }
            }
        }
    }

    /**
     * Pushes a new block onto the chain.
     */
    public function push($name,$block,$previousHash)
    {
        $block->previousHash = $previousHash;
        $this->mine($block);
        $this->pushData($name,$block);
    }

    /**
     * Mines a block.
     */
    public function mine($block)
    {
        while (substr($block->hash, 0, $this->difficulty) !== str_repeat("0", $this->difficulty)) {
            $block->nonce++;
            $block->hash = $block->calculateHash();
        }

        // echo "Block mined: ".$block->hash."\n";
    }
}