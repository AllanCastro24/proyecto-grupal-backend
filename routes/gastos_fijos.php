<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Models\DB;

//consultar gastos fijos

$app->get('/api/costos_fijos/consultar/{sucursal}/{tienda}', function(Request $request, Response $response){
    $sucursal = $request->getAttribute('sucursal');
    $tienda = $request->getAttribute('tienda');
    $consulta = "SELECT gastos_fijos.id_gasto, tipo_gasto.nombre as nom_tipo, gastos_fijos.descripcion, gastos_fijos.cantidad, gastos_fijos.fecha, sucursales.Pseudonimo as nom_sucursal, 
    gastos_fijos.periodicidad, gastos_fijos.status, status.nom_status, gastos_fijos.tipo_gasto,gastos_fijos.id_sucursal FROM gastos_fijos
        INNER JOIN tipo_gasto 
        ON gastos_fijos.tipo_gasto = tipo_gasto.id_tipo
        INNER JOIN sucursales 
        ON gastos_fijos.id_sucursal = sucursales.ID_sucursal
        INNER JOIN status
       ON gastos_fijos.status = status.idstatus 
       WHERE   gastos_fijos.id_sucursal = '$sucursal' and gastos_fijos.id_tienda = '$tienda' ";
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
 
 //Añadir gastos fijos
 $app->post('/api/costos_fijos/add', function(Request $request, Response $response, array $args){
    $data = $request->getParsedBody();
    
    $tipo_gasto = $data["tipo_gasto"];
    $descripcion = $data["descripcion"];
    $cantidad = $data["cantidad"];
    $fecha = $data["fecha"];
    $id_sucursal = $data["id_sucursal"];
    $id_tienda = $data["id_tienda"];
    $periodicidad = $data["periodicidad"];
    $status = $data["status"];
 
    $sql = "INSERT INTO gastos_fijos (tipo_gasto, descripcion, cantidad, fecha, id_sucursal, id_tienda, periodicidad, status) VALUES 
            (:tipo_gasto, :descripcion, :cantidad, :fecha, :id_sucursal, :id_tienda, :periodicidad, :status)";
    try {
      $db = new Db();
      $conn = $db->connect();
      $resultado = $conn->query($sql);
        $resultado->bindParam(':tipo_gasto', $tipo_gasto);
        $resultado->bindParam(':descripcion', $descripcion);
        $resultado->bindParam(':cantidad', $cantidad);
        $resultado->bindParam(':fecha', $fecha);
        $resultado->bindParam(':id_sucursal', $id_sucursal);
        $resultado->bindParam(':id_tienda', $id_tienda);
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
    $id_tienda = $data["id_tienda"];
    $periodicidad = $data["periodicidad"];
    $status = $data["status"];
   
    $sql = "UPDATE pruebas.gastos_fijos SET tipo_gasto=:tipo_gasto, descripcion=:descripcion, cantidad=:cantidad, fecha=:fecha, id_sucursal=:id_sucursal, id_tienda=:id_tienda, periodicidad=:periodicidad, status=:status WHERE id_gasto=:id";
    /**$sql = "UPDATE gastos_fijos SET
              tipo_gasto = :tipo_gasto,
              descripcion = :descripcion,
              cantidad = :cantidad,
              fecha  = :fecha,
              id_sucursal = :id_sucursal,
              periodicidad = :periodicidad,
              status = :status
    WHERE id_gasto = '$id' ";**/
   
    try {
      $db = new Db();
      $conn = $db->connect();
      $resultado = $conn->prepare($sql);

      $resultado->bindParam(':tipo_gasto', $tipo_gasto);
      $resultado->bindParam(':descripcion', $descripcion);
      $resultado->bindParam(':cantidad', $cantidad);
      $resultado->bindParam(':fecha', $fecha);
      $resultado->bindParam(':id_sucursal', $id_sucursal);
      $resultado->bindParam(':id_tienda', $id_tienda);
      $resultado->bindParam(':periodicidad', $periodicidad);
      $resultado->bindParam(':status', $status);
      $resultado->bindParam(':id', $id);
 
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
 $app->put('/api/costos_fijos/baja/{id}/{status}',function (Request $request, Response $response, array $args) {
   $id = $request->getAttribute('id');
   $status = $request->getAttribute('status');
  
   if($status == '1'){
    $sql = "UPDATE gastos_fijos SET gastos_fijos.status = '2' WHERE id_gasto = '$id' ";
   }else if($status == '2'){
    $sql = "UPDATE gastos_fijos SET status = '1' WHERE id_gasto = '$id' ";
   }


    try {
      $db = new Db();
      $conn = $db->connect();
      $resultado = $conn->query($sql);
 
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