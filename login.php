<?php
require_once("./loginDAO.php");
require_once("./exchange.php");
class Login{
    protected $loginDAO;

    public function __construct()
    {
        $this->loginDAO = new LoginDAO();
    }

    public function CreateAccount($username,$password,$name,$address,$phone)
    {
        if($this->loginDAO->GetDetailAccount($username) != "null")
        {
            return array("success"=>false,"data"=>"$username has been created");
        }
        else{
            $data = json_decode($this->loginDAO->CreateAccount($username,$password,$name,$address,$phone),true);
            if(isset($data['error']))
            {
                return array("success"=>false,"data"=>$data['error']);
            }
            else{
                return array("success"=>true,"data"=>"$username created success\nLogin again");
            }
        }
    }

    public function LoginAccount($username, $password)
    {
        if($this->loginDAO->GetDetailAccount($username) == "null")
        {
            return array("success"=>false,"data"=>"$username has not been created");
        }
        else{
            $data = json_decode($this->loginDAO->GetDetailAccount($username),true);
            if(isset($data['error']))
            {
                return array("success"=>false,"data"=>$data['error']);
            }
            else{
                if($data['password'] == $password)
                {
                    $token = $this->getRandomToken();
                    $data = json_decode($this->loginDAO->LoginAccount($username,$token),true);
                    if(isset($data['error']))
                    {
                        return array("success"=>false,"data"=>$data['error']);
                    }
                    else{
                        return array("success"=>true,"data"=>$token);
                    }
                }
            }
        }
    }

    public function GetDetailNamePhone($username)
    {
        if($this->loginDAO->GetDetailAccount($username) == "null")
        {
            return array("success"=>false,"data"=>"$username has not been created");
        }
        else{
            $data = json_decode($this->loginDAO->CreateAccount($username,$password,$name,$address,$phone),true);
            if(isset($data['error']))
            {
                return array("success"=>false,"data"=>$data['error']);
            }
            else{
                return array("success"=>true,"data"=>array("name"=>$data['name'],"phone"=>$data['phone']));
            }
        }
    }

    public function CheckToken($username,$token)
    {
        if($this->loginDAO->GetDetailAccount($username) == "null")
        {
            return array("success"=>false,"data"=>"$username has not been created");
        }
        else{
            if($this->loginDAO->CheckToken($username) == "null")
            {
                return array("success"=>false,"data"=>"$username must login");
            }
            else{
                if($this->loginDAO->CheckToken($username) == '"'.$token.'"')
                {
                    return array("success"=>true,"data"=>"Check success");
                }
                else{
                    return array("success"=>false,"data"=>"Token expires");
                }
            }
        }
    }

    private function getRandomToken()
    {
        $a = (int) random_int(100,200);
        $string = '';
        $array = ['0','1','2','3','4','5','6','7','8','9','A','B','C','D','E','F','G','H','J','K','L','O','P','Q','R','S','X','M','N','T','V','W'];
        for($i=0;$i<$a;$i++){
            $string .= $array[(int) random_int(0,31)];
        }
        return $string;
    }
}