<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;


//CONSULTAR SUCURSALES

$app->get('/api/sucursales/consultar', function(Request $request, Response $response){
    $consulta = "SELECT * FROM sucursales where sucursales.status = '1'";
    try{
        $db = new Db();
        $conn = $db->connect();
        $ejecutar = $conn->query($consulta);
        $sucursales = $ejecutar->fetchAll(PDO::FETCH_OBJ);
        $db = null;
        $response->getBody()->write(json_encode($sucursales));
        return $response;// $gastos;
    } catch(PDOException $e){
        echo '{"error": {"text":  '.$e->getMessage().'}';
    }
  });

