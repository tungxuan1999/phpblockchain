<?php

require 'restful_api.php';
require_once './login.php';
require_once './exchange.php';

class api extends restful_api {
    protected $login;
    protected $testCoin;
    function __construct(){
        $this->login = new Login();
        $this->testCoin = new Exchange();
        parent::__construct();
    }

    function CreateAccount(){
        if ($this->method == 'GET'){
            $username = isset($_GET['username']) ? $_GET['username'] : die();
            $password = isset($_GET['password']) ? $_GET['password'] : die();
            $name = isset($_GET['name']) ? $_GET['name'] : die();
            $address = isset($_GET['address']) ? $_GET['address'] : die();
            $phone = isset($_GET['phone']) ? $_GET['phone'] : die();
            $this->response(200, $this->login->CreateAccount($username,$password,$name,$address,$phone));
        }
    }

    function LoginAccount()
    {
        if ($this->method == 'GET'){
            $username = isset($_GET['username']) ? $_GET['username'] : die();
            $password = isset($_GET['password']) ? $_GET['password'] : die();
            $this->response(200, $this->login->LoginAccount($username,$password));
        }
    }

    function CheckAccountExchange()
    {
        if ($this->method == 'GET'){
            $username = isset($_GET['username']) ? $_GET['username'] : die();
            $token = isset($_GET['token']) ? $_GET['token'] : die();
            $data = $this->login->CheckToken($username,$token);
            if($data['success'])
            {
                if($this->testCoin->getData($username)=="null")
                {
                    $this->response(200, array("success"=>false,"data"=>"Account Exchange has not created"));
                }
                else{
                    $this->response(200, array("success"=>true,"data"=>"Account Exchange has created"));
                }
            }
            else{
                $this->response(200, $data['data']);
            }
            $this->response(200, $this->login->CheckToken($username,$token));
        }
    }

    function CreateAccountExchange()
    {
        if ($this->method == 'GET'){
            $username = isset($_GET['username']) ? $_GET['username'] : die();
            $token = isset($_GET['token']) ? $_GET['token'] : die();
            $data = $this->login->CheckToken($username,$token);
            if($data['success'])
            {
                $this->response(200, $this->testCoin->CreateAccount($username));
            }
            else{
                $this->response(200, $data['data']);
            }
        }
    }

    function RechargeAccountExchange()
    {
        if ($this->method == 'GET'){
            $username = isset($_GET['username']) ? $_GET['username'] : die();
            $token = isset($_GET['token']) ? $_GET['token'] : die();
            $amount = isset($_GET['amount']) ? $_GET['amount'] : die();
            $data = $this->login->CheckToken($username,$token);
            if($data['success'])
            {
                $this->response(200, $this->testCoin->rechargeAccount($username,$amount));
            }
            else{
                $this->response(200, $data['data']);
            }
        }
    }

    function TransferAccountExchange()
    {
        if ($this->method == 'GET'){
            $username = isset($_GET['username']) ? $_GET['username'] : die();
            $token = isset($_GET['token']) ? $_GET['token'] : die();
            $amount = isset($_GET['amount']) ? $_GET['amount'] : die();
            $username2 = isset($_GET['username2']) ? $_GET['username2'] : die();
            $data = $this->login->CheckToken($username,$token);
            if($data['success'])
            {
                $this->response(200, $this->testCoin->transferAccount($username,$username2,$amount));
            }
            else{
                $this->response(200, $data['data']);
            }
        }
    }

}
new api();