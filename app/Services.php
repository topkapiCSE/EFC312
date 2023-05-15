<?php

class Services{

    private static $jwt = null;

    public static function jwt(){
        if(self::$jwt == null){
            require HELPER_PATH."Jwt.php";
            self::$jwt = new Jwt();
        }

        return self::$jwt;
    }
    public static function logger($class){
        require_once APP_PATH."log_defines.php";
        require_once HELPER_PATH."Logger.php";
        return new Logger($class);
    }

}