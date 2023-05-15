<?php
require_once "BaseModel.php";

class LoggerModel extends BaseModel{

    private $table;
    public function __construct($table)
    {
        parent::__construct();
        $this->table = $table;
    }

    public function create($data){
        extract($data);
        $sql = "
            INSERT INTO $this->table (user_id,is_mobile,type,data,controller,method,time,ip,user_agent)
                    VALUES($user_id,'$is_mobile','$type','$data','$controller','$method',$time,'$ip','$user_agent')
        ";

        $this->db->query($sql);
    }

    public function getCountOfLogs($type,$data,$time){

        $timex = time() - $time;
        $sql = "SELECT COUNT(*) as count from $this->table where type='$type' AND data='$data' AND time > $timex";
        return $this->db->query($sql)->fetch_assoc()["count"];
    }

}