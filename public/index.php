<?php
require "../app/System.php";

define("APP_PATH",dirname(__FILE__)."/../app/");
define("PUBLIC_PATH",dirname(__FILE__)."/");
define("CONTROLLERS_PATH",APP_PATH."Controller/");
define("VIEW_PATH",APP_PATH."View/");
define("LANG_PATH",APP_PATH."Language/");
define("MODEL_PATH",APP_PATH."Model/");

$params = @$_GET["p"];
if($params){
    $params = explode("/",$params);
}
$controller = null;
$method = "index";
$parameter = null;
if(is_array($params)){
    if($params[0] === "tr" || $params[0]==="en"){
        define("LOCALE",$params[0]);
        array_shift($params);
    }else{
        define("LOCALE","tr");
    }
    switch (count($params)){
        case 1:
            $controller = $params[0];
            break;
        case 2:
            $controller = $params[0];
            $method = $params[1] != "" ? $params[1] : "index";
            break;
        case 3:
            $controller = $params[0];
            $method = $params[1];
            $parameter = $params[2];
            break;
    }
}

define("BASE_URL","http://localhost/ders/".LOCALE);
define("VIEW_URL","http://localhost/ders/app/View/");

function text($str){
    $str = explode(".",$str);
    if(!is_array($str)){
        return "unkown str";
    }
    $filePath = LANG_PATH.LOCALE."/".$str[0].".php";
    if(is_file($filePath)){
        $messages = require $filePath;
        return $messages[$str[1]] ?? "str not found";
    }
    return "text file not found";
}

$result = System::run($controller,$method,$parameter);
die(text("Login.success"));
?>