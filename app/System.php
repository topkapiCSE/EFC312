<?php

class System{
    public static function run($class = null, $method = "index", $parameter = null){

        $path = dirname(__FILE__);

        if($class){
            $className = $class;
            $fullPath = $path.DIRECTORY_SEPARATOR."Controller".DIRECTORY_SEPARATOR.$className.".php";
        }else{
            $className = "Login";
            $fullPath = $path.DIRECTORY_SEPARATOR."Controller".DIRECTORY_SEPARATOR."Login.php";
        }

        if(is_file($fullPath)){
            require $fullPath;
            $obj = new $className();
            $result = $obj->init($method,$parameter);
            return $result == -1 ? self::notFound() : $result;
        }else{
            require "Controller/Login.php";
            $home =  new Login();
            return $home->init();
        }

    }

    private static function notFound(){
        return "System not found";
    }
}