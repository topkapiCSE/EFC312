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

}