<?php
require_once "BaseModel.php";

class LoginModel extends BaseModel
{
    public function checkUser($email,$password){
        $result = $this->db->query("select * from tbl_users where email='$email' and password='$password' and deleted_at is NULL");
        if(count($result->fetch_all()) != 1){
         return false;
        }
        return true;
    }

    public function getUserRole($email){
        $result = $this->db->query("select role from tbl_users where email='$email'");
        return $result->fetch_assoc()["role"];
    }

    public function getUserId($email){
        $result = $this->db->query("select id from tbl_users where email='$email'");
        return $result->fetch_assoc()["id"];
    }
}