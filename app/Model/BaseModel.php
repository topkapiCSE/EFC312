<?php
class BaseModel{

    public $db;
    public function __construct()
    {
        $this->db = new mysqli("localhost", "root", "","ders");
        if ($this->db->connect_error) {
            die("Connection failed: " . $this->db->connect_error);
        }
    }

}