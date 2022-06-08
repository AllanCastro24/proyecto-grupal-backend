<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Models\DB;

$app->get('/api/costos_programados/consultar/{sucursal}/{tienda}', function(Request $request, Response $response){
  $sucursal = $request->getAttribute('sucursal');
  $tienda = $request->getAttribute('tienda');
    $consulta = "SELECT gastos_fijos_programados.id_gasto_fijo, tipo_gasto.nombre as nom_tipo, gastos_fijos_programados.descripcion, gastos_fijos_programados.cantidad,
    gastos_fijos_programados.fecha, sucursales.Pseudonimo as nom_sucursal, gastos_fijos_programados.periodicidad, 
    gastos_fijos_programados.status, status.nom_status, gastos_fijos_programados.tipo_gasto, gastos_fijos_programados.id_sucursal FROM gastos_fijos_programados
       INNER JOIN tipo_gasto 
       ON gastos_fijos_programados.tipo_gasto = tipo_gasto.id_tipo
       INNER JOIN sucursales 
       ON gastos_fijos_programados.id_sucursal = sucursales.ID_sucursal 
       INNER JOIN status
       ON gastos_fijos_programados.status = status.idstatus
       WHERE gastos_fijos_programados.status = '1' and gastos_fijos_programados.id_sucursal = '$sucursal' and gastos_fijos_programados.id_tienda = '$tienda'";
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
  $app->post('/api/costos_programados/add', function(Request $request, Response $response, array $args){
    $data = $request->getParsedBody();
    
    $tipo_gasto = $data["tipo_gasto"];
    $descripcion = $data["descripcion"];
    $cantidad = $data["cantidad"];
    $fecha = $data["fecha"];
    $id_sucursal = $data["id_sucursal"];
    $id_tienda = $data["id_tienda"];
    $periodicidad = $data["periodicidad"];
    $status = $data["status"];
    $sql="INSERT INTO pruebas.gastos_fijos_programados VALUES (null,:tipo_gasto, :descripcion, :cantidad, :fecha, :id_sucursal, :id_tienda, :periodicidad, :estado)";
    //$sql = "INSERT INTO gastos_fijos_programados VALUES (:tipo_gasto, :descripcion, :cantidad, :fecha, :id_sucursal, :id_tienda, :periodicidad, :status);";
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
        $resultado->bindParam(':estado', $status);
        //$resultado->bindParam(':id', null);
 
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
 $app->put('/api/costos_programados/update/{id}',function (Request $request, Response $response, array $args) {
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
   
    $sql = "UPDATE gastos_fijos_programados SET
              tipo_gasto = :tipo_gasto,
              descripcion = :descripcion,
              cantidad = :cantidad,
              fecha  = :fecha,
              id_sucursal = :id_sucursal,
              id_tienda = :id_tienda,
              periodicidad = :periodicidad,
              status = :status
    WHERE id_gasto_fijo = '$id' ";
   
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
  $app->put('/api/costos_programados/baja/{id}/{status}',function (Request $request, Response $response, array $args) {
    $id = $request->getAttribute('id');
    $status = $request->getAttribute('status');
    if($status == '1'){
      $sql = "UPDATE gastos_fijos_programados SET status = '2' WHERE id_gasto_fijo = '$id' ";
    }else if ($status == '2'){
      $sql = "UPDATE gastos_fijos_programados SET status = '1' WHERE id_gasto_fijo = '$id' ";

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
 