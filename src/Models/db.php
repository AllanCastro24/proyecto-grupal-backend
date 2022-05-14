<?php

class BD{
    private $host = '187.216.118.68';
    private $user = 'pruebas';
    private $pass = 'xtPduvLlPHo8IPVE';
    private $nombreBD = 'pruebas';

    public function conexionBD(){
        $mysqlConn = "mysql:host=$this->host;dbname=$this->nombreBD";
        $dbConn = new PDO($mysqlConn, $this->user, $this->pass);
        $dbConn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        return $dbConn;
    }
}