<?php

class BD{
    private $host = 'localhost';
    private $user = 'root';
    private $pass = '';
    private $nombreBD = 'restauranteplanb';

    public function conexionBD(){
        $mysqlConn = "mysql:host=$this->host;dbname=$this->nombreBD";
        $dbConn = new PDO($mysqlConn, $this->user, $this->pass);
        $dbConn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        return $dbConn;
    }
}