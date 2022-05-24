<?php

namespace App\Models;

use \PDO;

class DB
{
    /* private $host = 'localhost';
    private $user = 'root';
    private $pass = '1234';
    private $dbname = 'almacen'; 
    
    IP.- 187.216.118.68
    DB.- pruebas
    usr.- pruebas
    pswd.- xtPduvLlPHo8IPVE
    */

    private $host = '187.216.118.68';
    private $user = 'pruebas';
    private $pass = 'xtPduvLlPHo8IPVE';
    private $dbname = 'pruebas';

    public function connect()
    {
        $conn_str = "mysql:host=$this->host;dbname=$this->dbname";
        $conn = new PDO($conn_str, $this->user, $this->pass);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        return $conn;
    }
}
