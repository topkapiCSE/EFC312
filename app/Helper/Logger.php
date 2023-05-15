<?php

require MODEL_PATH."LoggerModel.php";

class Logger{

    private $model;

    private $controller;
    private $method;
    private $ip;
    private $userAgent;
    private $isMobile;
    private $userId;
    public function __construct($class)
    {
        $this->model = new LoggerModel($class::TABLE);

        $this->ip = $_SERVER["REMOTE_ADDR"];
        $this->userAgent = $_SERVER["HTTP_USER_AGENT"];
        $this->userId = Services::jwt()->getUserId(); //-1
        $this->method = $this->getMethodFromPathString($_SERVER["QUERY_STRING"]);
        $this->controller = $this->getControllerFromPathString($_SERVER["QUERY_STRING"]);
        $this->isMobile = $this->isMobile() ? "TRUE":"FALSE";
    }

    public function create($type,$data = null){

        //veriyi stringe dönüştürme
        if(is_object($data)){
            $data = (array)$data;
        }

        if(is_array($data)){
            $data = http_build_query($data,'',', ');
        }

        $this->model->create([
           "user_id" => $this->userId,
           "is_mobile" => $this->isMobile,
            "type" => $type,
            "data" => $data,
            "controller" => $this->controller,
            "method" => $this->method,
            "ip" => $this->ip,
            "time" => time(),
            "user_agent" => $this->userAgent
        ]);
    }

    public function getCountOfLogs($type,$data,$time){
        return $this->model->getCountOfLogs($type,$data,$time);
    }

    private function getControllerFromPathString($str){
        return @explode("/",$str)[1] ?? "unknown";
    }

    private function getMethodFromPathString($str){
        $m = @explode("/",$str)[2];
        if(isset($m) && !empty($m))
            return $m;

        return  "index";
    }

    private function isMobile(){
        $aMobileUA = array(
            '/iphone/i' => 'iPhone',
            '/ipod/i' => 'iPod',
            '/ipad/i' => 'iPad',
            '/android/i' => 'Android',
            '/blackberry/i' => 'BlackBerry',
            '/webos/i' => 'Mobile'
        );

        foreach($aMobileUA as $sMobileKey => $sMobileOS){
            if(preg_match($sMobileKey, $_SERVER['HTTP_USER_AGENT'])){
                return true;
            }
        }
        return false;
    }
}