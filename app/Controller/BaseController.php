<?php

class BaseController
{
    public $view;
    private $path;
    public function __construct()
    {
        $this->path = dirname(__FILE__)."/../View/";
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