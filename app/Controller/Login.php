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


    private function logout(){
        Services::jwt()->destroy();
        $this->toast("success",text("Login.logout"));
        header("Refresh:3; url=".BASE_URL."/Home",true,200);
    }

    private function check(){
        $email = $_POST["email"];
        $password = $_POST["password"];
        $model = new LoginModel();
        if($model->checkUser($email,$password)){
            $role = $model->getUserRole($email);
            $userId = $model->getUserId($email);

            Services::jwt()->generateToken([
                "role" => $role,
                "userId" => $userId
            ]);

            $this->toast("success",text("Login.success"));
            header("Refresh:3; url=".BASE_URL."/Home",true,200);
        }else{
            $this->toast("error",text("Login.error"));
            $this->view("Login");
        }
    }

    private function test(){
        session_start();
        var_dump($_SESSION);
    }
}


