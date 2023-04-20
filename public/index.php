<?php
require "../app/System.php";

define("BASE_URL","http://localhost/ders/");

define("VIEW_URL",BASE_URL."app/View/");
define("APP_PATH",dirname(__FILE__)."/../app/");
define("PUBLIC_PATH",dirname(__FILE__)."/");
define("CONTROLLERS_PATH",APP_PATH."Controller/");
define("VIEW_PATH",APP_PATH."View/");
define("MODEL_PATH",APP_PATH."Model/");
define("CONTROLLERS_PATH",APP_PATH."Controller/");

$params = @$_GET["p"];
if($params){
    $params = explode("/",$params);
}
$controller = null;
$method = "index";
$parameter = null;
if(is_array($params)){
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
$result = System::run($controller,$method,$parameter);
?>