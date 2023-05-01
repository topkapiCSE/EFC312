<?php

require CONTROLLERS_PATH . "BaseController.php";
require MODEL_PATH . "PermissionModel.php";
require MODEL_PATH . "SignModel.php";
require MODEL_PATH . "HomeModel.php";

class Home extends BaseController
{
    public function init($method = null, $parameter = null)
    {
        if($this->userId == -1){
            $this->toast("warning",text("Home.mustLogin"));
            header("Refresh:2; url=".BASE_URL."/Login",true,200);
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
            $this->toast("error",text("Home.permissionDenied"));
            header("Refresh:3; url=".BASE_URL."/Home",true,200);
            exit();
        }

        $homeModel = new HomeModel();
        if($homeModel->create($data)){
            $this->toast("success",text("Home.successAdd"));
            header("Refresh:2; url=".BASE_URL."/Home",true,200);
        }else{
            $this->toast("error",text("Home.errorUnkown"));
            header("Refresh:2; url=".BASE_URL."/Home",true,200);
        }
    }


    private function delete($id)
    {
        $permissionModel = new PermissionModel();
        $permissionId = $permissionModel->getPermissionId("delete");
        $userRole = $permissionModel->getUserRole($this->userId);
        if(!$permissionModel->checkRolePermission($userRole,$permissionId)){
            $this->toast("error",text("Home.permissionDenied"));
            header("Refresh:3; url=".BASE_URL."/Home",true,200);
            exit();
        }

        $homeModel = new HomeModel();
        if($homeModel->delete($id)){
            $this->toast("success",text("Home.successDelete"));
            header("Refresh:2; url=".BASE_URL."/Home",true,200);
        }else{
            $this->toast("error",text("Home.errorUnkown"));
            header("Refresh:2; url=".BASE_URL."/Home",true,200);
        }
    }

    private function user($id){
        $homeModel = new HomeModel();
        $user = $homeModel->getUser($id);
        $this->view("User",["data" => $user]);
    }

}


