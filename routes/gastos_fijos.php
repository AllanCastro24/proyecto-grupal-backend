<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

//consultar gastos fijos

$app->get('/api/costos_fijos/consultar', function(Request $request, Response $response){
    $consulta = "SELECT gastos_fijos.id_gasto, tipo_gasto.nombre as nom_tipo, gastos_fijos.descripcion, gastos_fijos.cantidad, gastos_fijos.fecha, sucursales.Pseudonimo as nom_sucursal, 
    gastos_fijos.periodicidad, gastos_fijos.status, gastos_fijos.tipo_gasto,gastos_fijos.id_sucursal FROM gastos_fijos
        INNER JOIN tipo_gasto 
        ON gastos_fijos.tipo_gasto = tipo_gasto.id_tipo
        INNER JOIN sucursales 
        ON gastos_fijos.id_sucursal = sucursales.ID_sucursal 
        WHERE gastos_fijos.status = '1'";
    try{
        $db = new BD();
        $db = $db->conexionBD();
        $ejecutar = $db->query($consulta);
        $gastos = $ejecutar->fetchAll(PDO::FETCH_OBJ);
        $db = null;
        $response->getBody()->write(json_encode($gastos));
        return $response;// $gastos;
    } catch(PDOException $e){
        echo '{"error": {"text":  '.$e->getMessage().'}';
    }
 });
 
 //Añadir gastos fijos
 $app->post('/api/costos_fijos/add', function(Request $request, Response $response, array $args){
    $data = $request->getParsedBody();
    
    $tipo_gasto = $data["tipo_gasto"];
    $descripcion = $data["descripcion"];
    $cantidad = $data["cantidad"];
    $fecha = $data["fecha"];
    $id_sucursal = $data["id_sucursal"];
    $periodicidad = $data["periodicidad"];
    $status = $data["status"];
 
    $sql = "INSERT INTO gastos_fijos (tipo_gasto, descripcion, cantidad, fecha, id_sucursal, periodicidad, status) VALUES 
            (:tipo_gasto, :descripcion, :cantidad, :fecha, :id_sucursal, :periodicidad, :status)";
    try {
        $db = new BD();
        $db = $db->conexionBD();
        $resultado = $db->prepare($sql);
        $resultado->bindParam(':tipo_gasto', $tipo_gasto);
        $resultado->bindParam(':descripcion', $descripcion);
        $resultado->bindParam(':cantidad', $cantidad);
        $resultado->bindParam(':fecha', $fecha);
        $resultado->bindParam(':id_sucursal', $id_sucursal);
        $resultado->bindParam(':periodicidad', $periodicidad);
        $resultado->bindParam(':status', $status);
 
        $resultado->execute();
        //echo json_encode("Nuevo gasto fijo agregado.");
        
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
 
 //Modificar gasto fijo
 $app->put('/api/costos_fijos/update/{id}',function (Request $request, Response $response, array $args) {
    $id = $request->getAttribute('id');
    $data = $request->getParsedBody();
    $tipo_gasto = $data["tipo_gasto"];
    $descripcion = $data["descripcion"];
    $cantidad = $data["cantidad"];
    $fecha = $data["fecha"];
    $id_sucursal = $data["id_sucursal"];
    $periodicidad = $data["periodicidad"];
    $status = $data["status"];
   
    $sql = "UPDATE gastos_fijos SET
              tipo_gasto = :tipo_gasto,
              descripcion = :descripcion,
              cantidad = :cantidad,
              fecha  = :fecha,
              id_sucursal = :id_sucursal,
              periodicidad = :periodicidad,
              status = :status
    WHERE id_gasto = '$id' ";
   
    try {
      $db = new BD();
      $db = $db->conexionBD();
     
      $resultado = $db->prepare($sql);
      $resultado->bindParam(':tipo_gasto', $tipo_gasto);
      $resultado->bindParam(':descripcion', $descripcion);
      $resultado->bindParam(':cantidad', $cantidad);
      $resultado->bindParam(':fecha', $fecha);
      $resultado->bindParam(':id_sucursal', $id_sucursal);
      $resultado->bindParam(':periodicidad', $periodicidad);
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
 
 
 //Dar de baja
 $app->put('/api/costos_fijos/baja/{id}',function (Request $request, Response $response, array $args) {
   $id = $request->getAttribute('id');
 
    try {
      $db = new BD();
      $db = $db->conexionBD();
       $sql = "UPDATE gastos_fijos SET status = '2' WHERE id_gasto = '$id' ";
      $resultado = $db->prepare($sql);
 
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