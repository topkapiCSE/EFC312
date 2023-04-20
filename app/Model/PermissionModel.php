<?php
require_once "BaseModel.php";

class PermissionModel extends BaseModel
{
    public function checkRolePermission($roleId, $permissionId)
    {
        $result = $this->db->query("select * from tbl_role_permission where role_id='$roleId' and permission_id='$permissionId'");
        if (count($result->fetch_all()) != 1) {
            return false;
        }
        return true;
    }

    public function getPermissionId($permission){
        $result = $this->db->query("select * from tbl_permission where permission='$permission'");
        return $result->fetch_assoc()["id"];
    }

    public function getUserRole($userId)
    {
        $result = $this->db->query("select role from tbl_users where id='$userId'");
        return $result->fetch_assoc()["role"];
    }
}