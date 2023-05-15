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
        Services::logger(LOGIN_LOG_DEFINES::class)->create( LOGIN_LOG_DEFINES::LOGOUT);
        Services::jwt()->destroy();
        $this->toast("success",text("Login.logout"));
        header("Refresh:3; url=".BASE_URL."/Home",true,200);
    }

    private function check(){
        $email = $_POST["email"];
        $password = $_POST["password"];


        $countOfUserTry = Services::logger(LOGIN_LOG_DEFINES::class)->getCountOfLogs(LOGIN_LOG_DEFINES::WRONG_PASS,$email,COUNT_OF_FAILED_LOGIN_TIME);
        if($countOfUserTry >= LOGIN_OVER_TRY){
            Services::logger(LOGIN_LOG_DEFINES::class)->create( LOGIN_LOG_DEFINES::OVER_LOGIN_TRY,$email);
            $this->toast("error",text("Login.loginOverTry"));
            header("Refresh:3; url=".BASE_URL."/Login",true,200);
            exit();
        }


        $model = new LoginModel();
        if($model->checkUser($email,$password)){
            $role = $model->getUserRole($email);
            $userId = $model->getUserId($email);

            Services::jwt()->generateToken([
                "role" => $role,
                "userId" => $userId
            ]);

            Services::logger(LOGIN_LOG_DEFINES::class)->create( LOGIN_LOG_DEFINES::SUCCESS,$email);
            $this->toast("success",text("Login.success"));
            header("Refresh:3; url=".BASE_URL."/Home",true,200);
        }else{
            Services::logger(LOGIN_LOG_DEFINES::class)->create( LOGIN_LOG_DEFINES::WRONG_PASS,$email);
            $this->toast("error",text("Login.error"));
            $this->view("Login");
        }
    }

    private function test(){
        $jwt = Services::jwt();

        var_dump($jwt->getUserId());

    }
}


