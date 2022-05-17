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

/**
 * USUARIOS
 */

//Insertar empleado (Si da de alta empleado, agregarle el mismo numero de usuario que tiene)

//INSERT INTO pruebas.empleado (Nombre, Apellidos, Sueldo, Direccion, Telefono, Genero, Correo, ID_puesto, `ID_tipo-pago`, ID_tienda) VALUES('', '', 0, '', '', '', '', 0, 0, 0);

//Insertar nombre de usuario, contraseña y correo (Todos se pueden registrar, pero no todos serán empleados)
$app->post('/api/usuarios/add', function(Request $request, Response $response, array $args){
  //$data = $request->getParsedBody();
  try {
  $usuario = "Alondra123";
  $mail = "alondra.gardea24@gmail.com";
  $pass = "alondra123";
  $fecha = date('Y-m-d');

 /* $sql = "INSERT INTO usuarios (Usuario, Contraseña, Activo, Inactivo, Bloqueado, Desbloqueado, Fecha-registro, Correo) VALUES
          (:usuario, :pass, 'S', 'N', 'N', 'S', :fecha, :mail)";*/
$sql = "INSERT INTO pruebas.usuarios VALUES
(null,'Allan Castro','allan123','S','N','N','S','2022-05-14','2022-05-14',NULL,'alan.castro.1226.ac@gmail.com')";
  
      $db = new BD();
      $db = $db->conexionBD();
      $resultado = $db->prepare($sql);
     /* $resultado->bindParam(':usuario', $usuario);
      $resultado->bindParam(':mail', $mail);
      $resultado->bindParam(':pass', $pass);
      $resultado->bindParam(':fecha', $fecha);
*/
      $resultado->execute();
      
      $db = null;

      $response->getBody()->write("hola");//json_encode($resultado));
      return $response 
          ->withHeader('content-type','aplication/json')
          ->withStatus(200);
          $resultado = null;
  } catch (PDOException $e) {
      echo '{"errorrrrrrrrrrrrrrrrrr": {"text":  '.$e->getMessage().'}';
      $error = array(
          "message" => $e->getMessage()
      );

      $response->getBody()->write(json_encode($error));
      return $response
          ->withHeader('content-type','aplication/json')
          ->withStatus(500);
  }
});

//Consultar a todos los usuarios y empleados para el admin (falta where id usuario = id_usuario_empleado)
$app->get('/api/usuarios/consultar_empleado', function(Request $request, Response $response){
  $consulta = 'SELECT * FROM usuarios,empleado WHERE id_usuario = id_empleado';
  try{
      $db = new BD();
      $db = $db->conexionBD();
      $ejecutar = $db->query($consulta);
      $user = $ejecutar->fetchAll(PDO::FETCH_OBJ);
      $response->getBody()->write(json_encode($user));
      $db = null;
      return $response;
  } catch(PDOException $e){
      echo '{"error": {"text":  '.$e->getMessage().'}';
  }
});
$app->get('/api/usuarios/consultar_usuarios', function(Request $request, Response $response){
  $consulta = 'SELECT * FROM usuarios,empleado WHERE id_usuario <> id_empleado';
  try{
      $db = new BD();
      $db = $db->conexionBD();
      $ejecutar = $db->query($consulta);
      $user = $ejecutar->fetchAll(PDO::FETCH_OBJ);
      $db = null;
      $response->getBody()->write(json_encode($user));
      return $response;
  } catch(PDOException $e){
      echo '{"error": {"text":  '.$e->getMessage().'}';
  }
});
//Loggin

//Modificar usuario

//Modificar empleado

/**=======================================================================
 * =======================================================================
 * SECCIÓN DE GASTOS FIJOS
 * CONSULTAR, AÑADIR, MODIFICAR, DAR DE BAJA
 * =======================================================================
 */
//consultar gastos fijos

$app->get('/api/costos_fijos/consultar', function(Request $request, Response $response){
   $consulta = 'SELECT * FROM gastos_fijos';
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
$app->get('/api/ventas/mes/{mes}', function(Request $request, Response $response){
  $mes = $request->getAttribute('mes');
  $consulta = "SELECT * FROM vista_ventas4 WHERE MONTH(fecha) = '$mes' ";
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



$app->run();