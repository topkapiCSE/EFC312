<?php

require_once "BaseModel.php";

class SignModel extends BaseModel
{
    public function register($data){
        $query = "INSERT INTO tbl_users(name,surname,email,password,role) VALUES('".$data["name"]."','".$data["surname"]."','".$data["email"]."','".$data["password"]."',1)";
        return $this->db->query($query);
    }

    public function isRegistered($email){
        $result = $this->db->query("select * from tbl_users where email='$email'");
        if(count($result->fetch_all()) > 0){
            return true;
        }
        return false;
    }

    public function getUserId($email){
        $result = $this->db->query("select * from tbl_users where email='$email'");
        return $result->fetch_assoc()["id"];
    }


}