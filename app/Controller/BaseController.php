<?php

class BaseController
{
    public $view;
    private $path;
    protected $userId;
    public function __construct()
    {
        $this->path = dirname(__FILE__)."/../View/";
        $this->userId = Services::jwt()->getUserId();
    }

    public function view($view,$values=null){

        if(is_array($values)){
            extract($values);
        }
        require $this->path."$view/index.php";
        exit();
    }

    public function toast($type,$message){
        require $this->path."Toast/index.php";
    }


}