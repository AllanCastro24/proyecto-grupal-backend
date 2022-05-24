<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

//consultar TIPO DE GASTOS

$app->get('/api/tipo_gastos/consultar', function(Request $request, Response $response){
    $consulta = "SELECT id_tipo, nombre, status, status.nom_status FROM tipo_gasto 
    INNER JOIN status
    ON tipo_gasto.status = status.idstatus";
    try{
        $db = new Db();
        $conn = $db->connect();
        $ejecutar = $conn->query($consulta);
        $gastos = $ejecutar->fetchAll(PDO::FETCH_OBJ);
        $db = null;
        $response->getBody()->write(json_encode($gastos));
        return $response;// $gastos;
    } catch(PDOException $e){
        echo '{"error": {"text":  '.$e->getMessage().'}';
    }
 });
// consulta select
 $app->get('/api/tipo_gastos/select', function(Request $request, Response $response){
  $consulta = "SELECT * FROM tipo_gasto where tipo_gasto.status ='1'";
  try{
      $db = new Db();
      $conn = $db->connect();
      $ejecutar = $conn->query($consulta);
      $gastos = $ejecutar->fetchAll(PDO::FETCH_OBJ);
      $db = null;
      $response->getBody()->write(json_encode($gastos));
      return $response;// $gastos;
  } catch(PDOException $e){
      echo '{"error": {"text":  '.$e->getMessage().'}';
  }
});

 
 //Añadir TIPO GASTO
 $app->post('/api/tipo_gastos/add', function(Request $request, Response $response, array $args){
    $data = $request->getParsedBody();
    $nombre = $data["nombre"];
    $status = $data["status"];
    
    $sql = "INSERT INTO tipo_gasto ( nombre, status) VALUES 
            (:nombre, :status)";
    try {
      $db = new Db();
      $conn = $db->connect();
      $resultado = $conn->query($sql);
        $resultado->bindParam(':nombre', $nombre);
        $resultado->bindParam(':status', $status);
 
        $resultado->execute();
        $db = null;
 
        $response->getBody()->write(json_encode($resultado));
        return $response 
            ->withHeader('content-type','aplication/json')
            ->withStatus(200);
            $resultado = null;
    } catch (PDOException $e) {
        //echo '{"error": {"text":  '.$e->getMessage().'}';
        $error = array(
            "message" => $e->getMessage()
        );
 
        $response->getBody()->write(json_encode($error));
        return $response
            ->withHeader('content-type','aplication/json')
            ->withStatus(500);
    }
 });
 
 //Modificar TIPO GASTO
 $app->put('/api/tipo_gastos/update/{id}',function (Request $request, Response $response, array $args) {
    $id = $request->getAttribute('id');
    $data = $request->getParsedBody();
 
    $nombre = $data["nombre"];
    $status = $data["status"];
   
    $sql = "UPDATE tipo_gasto SET
              nombre = :nombre,
              status = :status
    WHERE id_tipo = '$id' ";
   
    try {
      $db = new Db();
      $conn = $db->connect();
      $resultado = $conn->query($sql);
 
      $resultado->bindParam(':nombre', $nombre);
      $resultado->bindParam(':status', $status);
 
      $resultado->execute();
   
      $db = null;
      
      echo "Update successful! ";
      $response->getBody()->write(json_encode($resultado));
      return $response
        ->withHeader('content-type', 'application/json')
        ->withStatus(200);
      $resultado = null;
    } catch (PDOException $e) {
      $error = array(
        "message" => $e->getMessage()
      );
   
      $response->getBody()->write(json_encode($error));
      return $response
        ->withHeader('content-type', 'application/json')
        ->withStatus(500);
    }
 });

 //Dar de baja TIPO GASTO
$app->put('/api/tipo_gastos/baja/{id}/{status}',function (Request $request, Response $response, array $args) {
    $id = $request->getAttribute('id');
    $status = $request->getAttribute('status');
    if($status == '1'){
      $sql = "UPDATE tipo_gasto SET status = '2' WHERE id_tipo = '$id' ";
    }else if ($status == '2'){
      $sql = "UPDATE tipo_gasto SET status = '1' WHERE id_tipo = '$id' ";

    }
    try {
      $db = new Db();
      $conn = $db->connect();
      $resultado = $conn->query($sql);
 
      $resultado->execute();
   
      $db = null;
      
    
      $response->getBody()->write(json_encode($resultado));
      return $response
        ->withHeader('content-type', 'application/json')
        ->withStatus(200);
      $resultado = null;
    } catch (PDOException $e) {
      $error = array(
        "message" => $e->getMessage()
      );
   
      $response->getBody()->write(json_encode($error));
      return $response
        ->withHeader('content-type', 'application/json')
        ->withStatus(500);
    }
 });