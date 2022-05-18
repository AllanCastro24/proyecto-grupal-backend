<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: *');
header('Access-Control-Allow-Methods: *');
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../src/Models/db.php';



$bd = new BD();
$bd = $bd->conexionBD();

$app = AppFactory::create();
$app->addBodyParsingMiddleware();
$app->setBasePath('/proyecto-grupal-backend/public');

$app->get('/', function (Request $request, Response $response, $args) {
    $response->getBody()->write("Hello world!");
    return $response;
});



/**=======================================================================
 * =======================================================================
 * SECCIÓN DE GASTOS FIJOS
 * CONSULTAR, AÑADIR, MODIFICAR, DAR DE BAJA
 * =======================================================================
 */
//consultar gastos fijos

$app->get('/api/costos_fijos/consultar', function(Request $request, Response $response){
   $consulta = 'SELECT * FROM vista_gastos_fijos';
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
  $data = $request->getParsedBody();
  $status = $data["status"];
  
  
   try {
     $db = new BD();
     $db = $db->conexionBD();
      $sql = "UPDATE gastos_fijos SET status = '2' WHERE id_gasto = '$id' ";
     $resultado = $db->prepare($sql);
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

/**=======================================================================
 * =======================================================================
 * SECCIÓN DE TIPO DE GASTOS
 * CONSULTAR, AÑADIR, MODIFICAR, DAR DE BAJA
 * 
 * =======================================================================
 */
//consultar TIPO DE GASTOS

$app->get('/api/tipo_gastos/consultar', function(Request $request, Response $response){
   $consulta = 'SELECT * FROM tipo_gasto';
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

//Añadir TIPO GASTO
$app->post('/api/tipo_gastos/add', function(Request $request, Response $response, array $args){
   $data = $request->getParsedBody();
   $nombre = $data["nombre"];
   $status = $data["status"];
   
   $sql = "INSERT INTO tipo_gasto ( nombre, status) VALUES 
           (:nombre, :status)";
   try {
       $db = new BD();
       $db = $db->conexionBD();
       $resultado = $db->prepare($sql);
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
     $db = new BD();
     $db = $db->conexionBD();
    
     $resultado = $db->prepare($sql);

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
$app->put('/api/tipo_gastos/baja/{id}',function (Request $request, Response $response, array $args) {
   $id = $request->getAttribute('id');
   $data = $request->getParsedBody();
   $status = $data["status"];
  
   $sql = "UPDATE tipo_gasto SET
             status = :status
   WHERE id_tipo = '$id' ";
  
   try {
     $db = new BD();
     $db = $db->conexionBD();
    
     $resultado = $db->prepare($sql);
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

//Consultas de VENTAS
/**=======================================================================
 * =======================================================================
 * SECCIÓN DE CONSULTAS DE VENTAS
 * Mes, Año, dia, rango de fechas
 * =======================================================================
 */

//CONSULTA POR MES
$app->get('/api/ventas/mes/{mes}/{year}', function(Request $request, Response $response){
  $mes = $request->getAttribute('mes');
  $year = $request->getAttribute('year');
  $consulta = "SELECT * FROM vista_ventas4 WHERE MONTH(fecha) = '$mes'  and year(fecha) = '$year'";
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

//CONSULTA POR AÑO
$app->get('/api/ventas/year/{year}', function(Request $request, Response $response){
  $year = $request->getAttribute('year');
  $consulta = "SELECT * FROM vista_ventas4 WHERE YEAR(fecha) = '$year' ";
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

//CONSULTA POR DIA
$app->get('/api/ventas/day/{day}', function(Request $request, Response $response){
  $day = $request->getAttribute('day');
  $consulta = "SELECT * FROM vista_ventas4 WHERE fecha = '$day' ";
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

//CONSULTA POR RANGO
$app->get('/api/ventas/range/{inicio}/{fin}', function(Request $request, Response $response){
  $inicio = $request->getAttribute('inicio');
  $fin = $request->getAttribute('fin');
  $consulta = "SELECT * FROM vista_ventas4 WHERE fecha BETWEEN '$inicio' AND '$fin';";
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


//Consultas de Productos mas y menos vendidos
/**=======================================================================
 * =======================================================================
 * SECCIÓN DE CONSULTAS DE VENTAS
 * Mes, Año, dia, rango de fechas
 * =======================================================================
 */
//CONSULTA MAS VENDIDO POR MES
$app->get('/api/producto/mas_vendido/mes/{mes}/{year}', function(Request $request, Response $response){
  $mes = $request->getAttribute('mes');
  $year = $request->getAttribute('year');
  $consulta = "SELECT name, value FROM mas_vendido2 WHERE MONTH(fecha) = '$mes' and year(fecha) = '$year' ";
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

//CONSULTA MAS VENDIDO POR AÑO
$app->get('/api/producto/mas_vendido/year/{year}', function(Request $request, Response $response){
  $year = $request->getAttribute('year');
  $consulta = "SELECT name, value FROM mas_vendido2 WHERE year(fecha) = '$year' ";
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


//CONSULTA MENOS VENDIDO POR MES
$app->get('/api/producto/menos_vendido/mes/{mes}/{year}', function(Request $request, Response $response){
  $mes = $request->getAttribute('mes');
  $year = $request->getAttribute('year');
  $consulta = "SELECT name, value FROM menos_vendido2 WHERE MONTH(fecha) = '$mes' and year(fecha) = '$year' ";
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

//CONSULTA MENOS VENDIDO POR AÑO
$app->get('/api/producto/menos_vendido/year/{year}', function(Request $request, Response $response){
  $year = $request->getAttribute('year');
  $consulta = "SELECT name, value FROM menos_vendido2 WHERE year(fecha) = '$year' ";
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


/**=======================================================================
 * =======================================================================
 * SECCIÓN DE CONSULTAS DE SUCURSALES
 * =======================================================================
 */
$app->get('/api/sucursales/consultar', function(Request $request, Response $response){
  $consulta = 'SELECT * FROM sucursal';
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

/**=======================================================================
 * =======================================================================
 * SECCIÓN DE CONSULTAS DE ALMACEN
 * =======================================================================
 */
$app->get('/api/almacen/consultar', function(Request $request, Response $response){
  $consulta = 'SELECT * FROM vista_almacen';
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

/**=======================================================================
 * =======================================================================
 * SECCIÓN DE MOVIMIENTOS DE ALMACEN
 * =======================================================================
 */

 //TODAS LAS ENTRADAS
$app->get('/api/almacen/movimientos/entradas/todas', function(Request $request, Response $response){
  $consulta = 'SELECT movimientos.id_movimientos, detalle_insumo.presentacion as name, movimientos.cantidad as value, movimientos.fecha, tipo_movimiento.descripcion FROM pruebas.movimientos
  INNER JOIN detalle_insumo ON movimientos.id_detalle_insumo = detalle_insumo.id_detalle_insumo
  INNER JOIN tipo_movimiento ON movimientos.id_tipo_movimiento = tipo_movimiento.id_tipo_movimiento
  WHERE movimientos.id_tipo_movimiento = 1 ';
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


//ENTRADAS POR MES
$app->get('/api/almacen/movimientos/entradas/mes/{mes}/{year}', function(Request $request, Response $response){
  $year = $request->getAttribute('year');
  $mes = $request->getAttribute('mes');

  $consulta = "SELECT movimientos.id_movimientos, detalle_insumo.presentacion as name, movimientos.cantidad as value, movimientos.fecha, tipo_movimiento.descripcion FROM pruebas.movimientos
  INNER JOIN detalle_insumo ON movimientos.id_detalle_insumo = detalle_insumo.id_detalle_insumo
  INNER JOIN tipo_movimiento ON movimientos.id_tipo_movimiento = tipo_movimiento.id_tipo_movimiento
  WHERE movimientos.id_tipo_movimiento = 1 
  AND MONTH(movimientos.fecha) = '$mes' AND YEAR(movimientos.fecha) = '$year' ";
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

//ENTRADAS POR DIA
$app->get('/api/almacen/movimientos/entradas/day/{day}', function(Request $request, Response $response){
  $day = $request->getAttribute('day');

  $consulta = "SELECT movimientos.id_movimientos, detalle_insumo.presentacion as name, movimientos.cantidad as value, movimientos.fecha, tipo_movimiento.descripcion FROM pruebas.movimientos
  INNER JOIN detalle_insumo ON movimientos.id_detalle_insumo = detalle_insumo.id_detalle_insumo
  INNER JOIN tipo_movimiento ON movimientos.id_tipo_movimiento = tipo_movimiento.id_tipo_movimiento
  WHERE movimientos.id_tipo_movimiento = 1 
  AND movimientos.fecha = '$day' ";
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

//ENTRADAS POR RANGO DE FECHA 
$app->get('/api/almacen/movimientos/entradas/range/{inicio}/{fin}', function(Request $request, Response $response){
  $inicio = $request->getAttribute('inicio');
  $fin = $request->getAttribute('fin');


  $consulta = "SELECT movimientos.id_movimientos, detalle_insumo.presentacion as name, movimientos.cantidad as value, movimientos.fecha, tipo_movimiento.descripcion FROM pruebas.movimientos
  INNER JOIN detalle_insumo ON movimientos.id_detalle_insumo = detalle_insumo.id_detalle_insumo
  INNER JOIN tipo_movimiento ON movimientos.id_tipo_movimiento = tipo_movimiento.id_tipo_movimiento
  WHERE movimientos.id_tipo_movimiento = 1 
  AND movimientos.fecha BETWEEN '$inicio' AND '$fin' ";
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


//TODAS LAS SALIDAS
$app->get('/api/almacen/movimientos/salidas/todas', function(Request $request, Response $response){
  $consulta = 'SELECT movimientos.id_movimientos, detalle_insumo.presentacion as name, movimientos.cantidad as value, movimientos.fecha, tipo_movimiento.descripcion FROM pruebas.movimientos
  INNER JOIN detalle_insumo ON movimientos.id_detalle_insumo = detalle_insumo.id_detalle_insumo
  INNER JOIN tipo_movimiento ON movimientos.id_tipo_movimiento = tipo_movimiento.id_tipo_movimiento
  WHERE movimientos.id_tipo_movimiento = 2 ';
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


//SALIDAS POR MES
$app->get('/api/almacen/movimientos/salidas/mes/{mes}/{year}', function(Request $request, Response $response){
  $year = $request->getAttribute('year');
  $mes = $request->getAttribute('mes');

  $consulta = "SELECT movimientos.id_movimientos, detalle_insumo.presentacion as name, movimientos.cantidad as value, movimientos.fecha, tipo_movimiento.descripcion FROM pruebas.movimientos
  INNER JOIN detalle_insumo ON movimientos.id_detalle_insumo = detalle_insumo.id_detalle_insumo
  INNER JOIN tipo_movimiento ON movimientos.id_tipo_movimiento = tipo_movimiento.id_tipo_movimiento
  WHERE movimientos.id_tipo_movimiento = 2 
  AND MONTH(movimientos.fecha) = '$mes' AND YEAR(movimientos.fecha) = '$year' ";
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

//SALIDAS POR DIA
$app->get('/api/almacen/movimientos/salidas/day/{day}', function(Request $request, Response $response){
  $day = $request->getAttribute('day');

  $consulta = "SELECT movimientos.id_movimientos, detalle_insumo.presentacion as name , movimientos.cantidad as value, movimientos.fecha, tipo_movimiento.descripcion FROM pruebas.movimientos
  INNER JOIN detalle_insumo ON movimientos.id_detalle_insumo = detalle_insumo.id_detalle_insumo
  INNER JOIN tipo_movimiento ON movimientos.id_tipo_movimiento = tipo_movimiento.id_tipo_movimiento
  WHERE movimientos.id_tipo_movimiento = 2 
  AND movimientos.fecha = '$day' ";
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

//SALIDAS POR RANGO DE FECHA 
$app->get('/api/almacen/movimientos/salidas/range/{inicio}/{fin}', function(Request $request, Response $response){
  $inicio = $request->getAttribute('inicio');
  $fin = $request->getAttribute('fin');


  $consulta = "SELECT movimientos.id_movimientos, detalle_insumo.presentacion as name, movimientos.cantidad as value, movimientos.fecha, tipo_movimiento.descripcion FROM pruebas.movimientos
  INNER JOIN detalle_insumo ON movimientos.id_detalle_insumo = detalle_insumo.id_detalle_insumo
  INNER JOIN tipo_movimiento ON movimientos.id_tipo_movimiento = tipo_movimiento.id_tipo_movimiento
  WHERE movimientos.id_tipo_movimiento = 2 
  AND movimientos.fecha BETWEEN '$inicio' AND '$fin' ";
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


$app->run();