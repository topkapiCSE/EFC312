<?php

/*

innerJoin->
select tbl_users.name,tbl_users.surname,tbl_roles.id as role_id, tbl_roles.name as role_name,tbl_role_permission.permission_id as permission_id from tbl_users INNER JOIN tbl_roles on tbl_users.role=tbl_roles.id inner join tbl_role_permission on tbl_roles.id=tbl_role_permission.role_id

 */

require_once "BaseModel.php";

class HomeModel extends BaseModel
{
    public function create($data)
    {
        $query = "INSERT INTO tbl_notes(user_id,title,note) VALUES('" . $data["user_id"] . "','" . $data["title"] . "','" . $data["note"] . "')";
        return $this->db->query($query);
    }

    public function delete($id)
    {
        $query = "UPDATE tbl_notes set deleted_at=CURRENT_TIMESTAMP where id='$id'";
        return $this->db->query($query);
    }

    public function getUserNotes($userId)
    {
        $result = $this->db->query("select * from tbl_notes where user_id='$userId' and deleted_at is NULL");
        $ret = [];

        while($temp=$result->fetch_assoc()){
            $ret[] = $temp;
        }
        return $ret;
    }

    public function getUser($id){
        $result = $this->db->query("select * from tbl_users where id='$id' and deleted_at is NULL");
        return $result->fetch_assoc();
    }


}