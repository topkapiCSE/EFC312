<?php

require CONTROLLERS_PATH."BaseController.php";
require MODEL_PATH."LoginModel.php";
class Login extends BaseController {
    public function init($method=null,$parameter=null){
        if(method_exists($this,$method))
            return $this->{$method}($parameter);
        else
            return -1;
    }

    private function index(){
        $this->view("Login");
    }

    private function check(){
        $email = $_POST["email"];
        $password = $_POST["password"];
        $model = new LoginModel();
        if($model->checkUser($email,$password)){
            $role = $model->getUserRole($email);
            session_start();
            $_SESSION["role"] = $role;
            $_SESSION["email"] = $email;

            $this->toast("success","Giriş başarılı.");
            header("Refresh:3; url=http://localhost/ders/Home",true,200);
        }else{
            $this->toast("error","Kullanıcı adı veya şifre hatalı");
            $this->view("Login");
        }
    }

    private function test(){
        session_start();
        var_dump($_SESSION);
    }
}


