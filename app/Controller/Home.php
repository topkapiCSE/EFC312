<?php

require CONTROLLERS_PATH . "BaseController.php";
require MODEL_PATH . "PermissionModel.php";
require MODEL_PATH . "SignModel.php";
require MODEL_PATH . "HomeModel.php";

class Home extends BaseController
{
    private $userId;
    public function init($method = null, $parameter = null)
    {
        session_start();
        if(!@$_SESSION["email"]){
            $this->toast("warning","Giriş yapmalısınız");
            header("Refresh:2; url=http://localhost/ders/Login",true,200);
        }else{
            $model = new SignModel();
            $this->userId = $model->getUserId($_SESSION["email"]);
        }
        if (method_exists($this, $method))
            return $this->{$method}($parameter);
        else
            return -1;
    }

    private function index()
    {
        $homeModel = new HomeModel();
        $userNotes = $homeModel->getUserNotes($this->userId);
        $this->view("Home",["data" => $userNotes]);
    }

    private function add()
    {
        $title = $_POST["title"];
        $note = $_POST["value"];
        $data = [
            "user_id" => $this->userId,
            "title" => $_POST["title"],
            "note"  => $_POST["value"]
        ];

        $permissionModel = new PermissionModel();
        $permissionId = $permissionModel->getPermissionId("create");
        $userRole = $permissionModel->getUserRole($this->userId);
        if(!$permissionModel->checkRolePermission($userRole,$permissionId)){
            $this->toast("error","Bu işlem için yetkiniz bulunmuyor");
            header("Refresh:3; url=http://localhost/ders/Home",true,200);
            exit();
        }

        $homeModel = new HomeModel();
        if($homeModel->create($data)){
            $this->toast("success","Kayıt başarılı");
            header("Refresh:2; url=http://localhost/ders/Home",true,200);
        }else{
            $this->toast("error","Bir hata meydana geldi");
            header("Refresh:2; url=http://localhost/ders/Home",true,200);
        }
    }


    private function delete($id)
    {
        $permissionModel = new PermissionModel();
        $permissionId = $permissionModel->getPermissionId("delete");
        $userRole = $permissionModel->getUserRole($this->userId);
        if(!$permissionModel->checkRolePermission($userRole,$permissionId)){
            $this->toast("error","Bu işlem için yetkiniz bulunmuyor");
            header("Refresh:3; url=http://localhost/ders/Home",true,200);
            exit();
        }

        $homeModel = new HomeModel();
        if($homeModel->delete($id)){
            $this->toast("success","Başarıyla silindi");
            header("Refresh:2; url=http://localhost/ders/Home",true,200);
        }else{
            $this->toast("error","Bir hata meydana geldi");
            header("Refresh:2; url=http://localhost/ders/Home",true,200);
        }
    }

    private function user($id){
        $homeModel = new HomeModel();
        $user = $homeModel->getUser($id);
        $this->view("User",["data" => $user]);
    }

}


