<?php

class mydb{
    private $servername='localhost';
    private $username='root';
    private $password='';
    private $dbname='wordpress';
    private $result=array();
    public $mysqli='';

    public function __construct(){
        $this->mysqli = new mysqli($this->servername,$this->username,$this->password,$this->dbname);
    }
}

$config = new mydb;