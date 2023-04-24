<?php

require "BaseController.php";
require MODEL_PATH . "SignModel.php";
class Sign extends BaseController {


    public function init($method=null,$parameter=null)
    {
        if(method_exists($this,$method))
            return $this->{$method}($parameter);
        else
            return -1;
    }

    private function index(){
        $this->view("Sign");
    }

    private function register(){
        $model = new SignModel();
        $data = [
            "name" => $_POST["name"],
            "surname" => $_POST["surname"],
            "email" => $_POST["email"],
            "password" => $_POST["password"]
        ];

        if($model->isRegistered($data["email"])){
            $this->toast("error",text("Sign.registeredEmail"));
            $this->view("Sign");
        }

        if($model->register($data)){
            $this->toast("success",text("Sign.success"));
            $this->view("Login");
        }else{
            $this->toast("error",text("Sign.errorUnkown"));
            $this->view("Sign");
        }
    }


    private function UUID($length = 4, $data = null)
    {
        // Generate 16 bytes (128 bits) of random data or use the data passed into the function.
        $data = $data ?? random_bytes(16);
        assert(strlen($data) == 16);

        // Set version to 0100
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        // Set bits 6-7 to 10
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);

        // Output the 18 character UUID.
        return vsprintf('%s%s-%s-%s', str_split(bin2hex($data), $length));
    }
}


