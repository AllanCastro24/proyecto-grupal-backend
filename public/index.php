<?php


header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: GET, POST, HEAD, PUT");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
header('Access-Control-Allow-Credentials: true');
header("Access-Control-Allow-Headers: Origin, Content-Type, Accept");

use App\Models\DB;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Selective\BasePath\BasePathMiddleware;
use Slim\Factory\AppFactory;
use App\Middleware\FlashMessageMiddleware;
use Slim\Middleware\MethodOverrideMiddleware;
use Slim\Views\TwigMiddleware;
use Zeuxisoo\Whoops\Slim\WhoopsMiddleware;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;


use Slim\Http\UploadedFile;

require_once __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../conexion/conexion.php';

require './PHPMailer-master/src/Exception.php';
require './PHPMailer-master/src/PHPMailer.php';
require './PHPMailer-master/src/SMTP.php';
$app = AppFactory::create();
$app->addBodyParsingMiddleware();
$app->addRoutingMiddleware();
$app->add(new BasePathMiddleware($app));
$app->addErrorMiddleware(true, true, true);
$app->setBasePath('/proyecto-grupal-backend/public');
$bd = new BD();
$bd = $bd->coneccionBD();

$app->options('/{routes:.+}', function ($request, $response, $args) {
  return $response;
});

$app->add(function ($request, $handler) {
  $response = $handler->handle($request);
  return $response
      ->withHeader('Access-Control-Allow-Origin', '*')
      ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
      ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS');
});



//========================================Solicitud===============================
//$app->get('/', function (Request $request, Response $response) {
  // $response->getBody()->write('Hello World!');
  // return $response;
//});

require __DIR__ . '/../routes/gastos_fijos.php';
require __DIR__ . '/../routes/tipo_gastos.php';
require __DIR__ . '/../routes/ventas.php';
require __DIR__ . '/../routes/sucursales.php';
require __DIR__ . '/../routes/almacen.php';
require __DIR__ . '/../routes/gastos_fijos_programados.php';

/**
 * USUARIOS
 */

//Insertar empleado (Si da de alta empleado, agregarle el mismo numero de usuario que tiene)
$app->post('/api/empleado/add', function(Request $request, Response $response, array $args){
  $data = $request->getParsedBody();
  $id_user = $data['ID_empleado'];
  $nombre = $data['Nombre'];
  $apellidos = $data['Apellidos'];
  $sueldo = $data['Sueldo'];
  $direccion = $data['Direccion'];
  $telefono = $data['Telefono'];
  $genero = $data['Genero'];
  $puesto = $data['ID_puesto'];
  $tipo_pago = $data['ID_tipo_pago'];
  $tienda = $data['ID_tienda'];
  $sucursal = $data['ID_sucursal'];

  $sql = "INSERT INTO pruebas.empleado VALUES($id_user,:nombre, :apellido, :sueldo, :direccion, :telefono, :genero, :puesto, :tipo_pago, :tienda,:sucursal);";
  try {
      $db = new BD();
      $db = $db->conexionBD();
      $resultado = $db->prepare($sql);
      $resultado->bindParam(':nombre', $nombre);
      $resultado->bindParam(':apellido', $apellidos);
      $resultado->bindParam(':sueldo', $sueldo);
      $resultado->bindParam(':direccion', $direccion);
      $resultado->bindParam(':telefono', $telefono);
      $resultado->bindParam(':genero', $genero);
      $resultado->bindParam(':puesto', $puesto);
      $resultado->bindParam(':tipo_pago', $tipo_pago);
      $resultado->bindParam(':tienda', $tienda);
      $resultado->bindParam(':sucursal', $sucursal);
      
      $resultado->execute();
      
      $db = null;

      $response->getBody()->write("Se contrató con exito");
      return $response 
          ->withHeader('content-type','aplication/json')
          ->withStatus(200);

  } catch (PDOException $e) {
      echo '{"errorr": {"text":  '.$e->getMessage().'}';
      $error = array(
          "message" => $e->getMessage()
      );

      $response->getBody()->write(json_encode($error));
      return $response
          ->withHeader('content-type','aplication/json')
          ->withStatus(500);
  }
});

//Insertar nombre de usuario, contraseña y correo (Todos se pueden registrar, pero no todos serán empleados)
$app->post('/api/usuarios/add', function(Request $request, Response $response, array $args){
  $data = $request->getParsedBody();
  
  $usuario = $data['username'];//$data["user"];//"Profe herman";
  $mail = $data['email'];//$data["mail"];//"profeherman@gmail.com";
  $pass = $data['password'];//$data["pass"];//"profe123";
  $fecha = date('Y-m-d');
  $imagen = "";
  $sql = "INSERT INTO pruebas.usuarios VALUES (null,:usuario,:pass,'S',:fecha,:fecha,:mail,:imagen)";
  try {
      $db = new BD();
      $db = $db->conexionBD();
      $resultado = $db->prepare($sql);
      $resultado->bindParam(':usuario', $usuario);
      $resultado->bindParam(':mail', $mail);
      $resultado->bindParam(':pass', $pass);
      $resultado->bindParam(':fecha', $fecha);
      $resultado->bindParam(':imagen', $imagen);
      
      $resultado->execute();
      
      $db = null;

      $response->getBody()->write("Se registró con exito");
      return $response 
          ->withHeader('content-type','aplication/json')
          ->withStatus(200);

  } catch (PDOException $e) {
      echo '{"errorr": {"text":  '.$e->getMessage().'}';
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
  $consulta = 'SELECT * FROM pruebas.usuarios INNER JOIN empleado ON usuarios.ID_usuario = empleado.ID_empleado';
  try{
    $bd = new BD();
    $bd = $bd->conexionBD();
    $resultado = $bd->query($consulta);

    if ($resultado->rowCount() > 0){
      $user = $resultado->fetchAll(PDO::FETCH_OBJ);
      //echo json_encode($user);
      $response->getBody()->write(json_encode($user));
      return $response
      ->withHeader('content-type','aplication/json')
      ->withStatus(200);
    }else {
      echo json_encode("No existen empleados en la BD.");
    }
    $resultado = null;
    $db = null;
  }catch(PDOException $e){
    echo '{"error" : {"text":'.$e->getMessage().'}';
  }
});

$app->get('/api/usuarios/consultar_usuarios', function(Request $request, Response $response){
  //$consulta = 'SELECT * FROM pruebas.usuarios';
  $consulta = "SELECT * FROM usuarios Where Not exists (select ID_empleado from empleado Where ID_usuario = ID_empleado)";
  try{
    $bd = new BD();
    $bd = $bd->conexionBD();
    $resultado = $bd->query($consulta);

    if ($resultado->rowCount() > 0){
      $user = $resultado->fetchAll(PDO::FETCH_OBJ);
      //echo json_encode($user);
      $response->getBody()->write(json_encode($user));
      return $response
      ->withHeader('content-type','aplication/json')
      ->withStatus(200);
    }else {
      echo json_encode("No existen usuarios en la BD.");
    }
    $resultado = null;
    $db = null;
  }catch(PDOException $e){
    echo '{"error" : {"text":'.$e->getMessage().'}';
  }
});

//Loggin
$app->post('/api/usuarios/login', function(Request $request, Response $response, array $args){
  $data = $request->getParsedBody();
  
  $user = $data['username'];
  $pass = $data['password'];
  //SELECT ID_usuario, Usuario, Contraseña, Activo, `Fecha-registro`, `Ultimo-ingreso`, Correo FROM pruebas.usuarios
  $consulta = 'SELECT * FROM usuarios WHERE Usuario=:user AND pass=:pass AND Activo="S"';
  try {
    $db = new BD();
    $db = $db->conexionBD();
    $resultado = $db->prepare($consulta);
    $resultado->bindParam(':user', $user);
    $resultado->bindParam(':pass', $pass);
    $resultado->execute();
    
    $db = null;
    $usuario = $resultado->fetch(PDO::FETCH_ASSOC);
    if ($usuario){
      $response->getBody()->write("Bienvenido");
      return $response 
          ->withHeader('content-type','aplication/json')
          ->withStatus(200);
    } else{
      $response->getBody()->write("Usuario o contraseña incorrecto");
      return $response 
          ->withHeader('content-type','aplication/json')
          ->withStatus(200);
    }
    

  } catch (PDOException $e) {
    echo '{"errorr": {"text":  '.$e->getMessage().'}';
    $error = array(
        "message" => $e->getMessage()
    );

    $response->getBody()->write(json_encode($error));
    return $response
        ->withHeader('content-type','aplication/json')
        ->withStatus(500);
  }
});
//Modificar usuario
$app->put('/api/usuarios/modificar/{id}', function(Request $request, Response $response, array $args){
  $data = $request->getParsedBody();
  
  $id = $request->getAttribute('id');
  $usuario = $data['name'];
  $correo = $data['email'];
  $imagen = $data['image'];
  
  $sql = "UPDATE pruebas.usuarios SET Usuario=:user, Correo=:mail, image=:imagen WHERE ID_usuario=:id";
  try {
      $db = new BD();
      $db = $db->conexionBD();
      $resultado = $db->prepare($sql);
      $resultado->bindParam(':id', $id);
      $resultado->bindParam(':user', $usuario);
      $resultado->bindParam(':mail', $correo);
      $resultado->bindParam(':imagen', $imagen);
      $resultado->execute();
      
      $db = null;

      $response->getBody()->write("Se modificó con exito");
      return $response 
          ->withHeader('content-type','aplication/json')
          ->withStatus(200);

  } catch (PDOException $e) {
      echo '{"errorr": {"text":  '.$e->getMessage().'}';
      $error = array(
          "message" => $e->getMessage()
      );

      $response->getBody()->write(json_encode($error));
      return $response
          ->withHeader('content-type','aplication/json')
          ->withStatus(500);
  }
});
//Modificar contraseña usuario
$app->put('/api/usuarios/modificar_pass/{id}', function(Request $request, Response $response, array $args){
  $data = $request->getParsedBody();
  
  $id = $request->getAttribute('id');;
  $pass = $data['newPassword'];

  $sql = "UPDATE pruebas.usuarios SET pass=:pass WHERE ID_usuario=:id";
  try {
      $db = new BD();
      $db = $db->conexionBD();
      $resultado = $db->prepare($sql);
      $resultado->bindParam(':id', $id);
      $resultado->bindParam(':pass', $pass);
      $resultado->execute();
      
      $db = null;

      $response->getBody()->write("Se modificó con exito");
      return $response 
          ->withHeader('content-type','aplication/json')
          ->withStatus(200);

  } catch (PDOException $e) {
      echo '{"errorr": {"text":  '.$e->getMessage().'}';
      $error = array(
          "message" => $e->getMessage()
      );

      $response->getBody()->write(json_encode($error));
      return $response
          ->withHeader('content-type','aplication/json')
          ->withStatus(500);
  }
});
//Modificar empleado / usuario en back
$app->put('/api/empleado/modificar/{id}', function(Request $request, Response $response, array $args){
  $data = $request->getParsedBody();
  
  $id = $request->getAttribute('id');
  $nombre = $data['Nombre'];
  $apellido = $data['Apellidos'];
  $genero = $data['Genero'];
  $tienda = $data['ID_tienda'];
  $puesto = $data['ID_puesto'];
  $sueldo = $data['Sueldo'];
  $direccion = $data['Direccion'];
  $telefono = $data['Telefono'];
  $pago = $data['ID_tipo_pago'];
  $sucursal = $data['ID_sucursal'];

  $sql = "UPDATE pruebas.empleado SET Nombre=:nombre, Apellidos=:apellido, Sueldo=:sueldo, Direccion=:direccion, Telefono=:telefono, Genero=:genero, ID_puesto=:puesto, `ID_tipo_pago`=:pago, ID_tienda=:tienda, ID_sucursal=:sucursal WHERE ID_empleado=:id";
  try {
      $db = new BD();
      $db = $db->conexionBD();
      $resultado = $db->prepare($sql);
      $resultado->bindParam(':id', $id);
      $resultado->bindParam(':nombre', $nombre);
      $resultado->bindParam(':apellido', $apellido);
      $resultado->bindParam(':sueldo', $sueldo);
      $resultado->bindParam(':direccion', $direccion);
      $resultado->bindParam(':telefono', $telefono);
      $resultado->bindParam(':genero', $genero);
      $resultado->bindParam(':puesto', $puesto);
      $resultado->bindParam(':pago', $pago);
      $resultado->bindParam(':tienda', $tienda);
      $resultado->bindParam(':sucursal', $sucursal);

      $resultado->execute();
      
      $db = null;

      $response->getBody()->write("Se modificó con exito");
      return $response 
          ->withHeader('content-type','aplication/json')
          ->withStatus(200);

  } catch (PDOException $e) {
      echo '{"errorr": {"text":  '.$e->getMessage().'}';
      $error = array(
          "message" => $e->getMessage()
      );

      $response->getBody()->write(json_encode($error));
      return $response
          ->withHeader('content-type','aplication/json')
          ->withStatus(500);
  }
});

//Activar / Desactivar usuario
$app->put('/api/usuarios/activar/{id}', function(Request $request, Response $response, array $args){
  $data = $request->getParsedBody();
  
  $id = $request->getAttribute('id');

  $sql = "UPDATE pruebas.usuarios SET Activo='S' WHERE ID_usuario=:id";
  try {
      $db = new BD();
      $db = $db->conexionBD();
      $resultado = $db->prepare($sql);
      $resultado->bindParam(':id', $id);
      
      $resultado->execute();
      
      $db = null;

      $response->getBody()->write("Se modificó con exito");
      return $response 
          ->withHeader('content-type','aplication/json')
          ->withStatus(200);

  } catch (PDOException $e) {
      echo '{"errorr": {"text":  '.$e->getMessage().'}';
      $error = array(
          "message" => $e->getMessage()
      );

      $response->getBody()->write(json_encode($error));
      return $response
          ->withHeader('content-type','aplication/json')
          ->withStatus(500);
  }
});

$app->put('/api/usuarios/desactivar/{id}', function(Request $request, Response $response, array $args){
  $data = $request->getParsedBody();
  
  $id = $request->getAttribute('id');

  $sql = "UPDATE pruebas.usuarios SET Activo='N' WHERE ID_usuario=:id";
  try {
      $db = new BD();
      $db = $db->conexionBD();
      $resultado = $db->prepare($sql);
      $resultado->bindParam(':id', $id);
      
      $resultado->execute();
      
      $db = null;

      $response->getBody()->write("Se modificó con exito");
      return $response 
          ->withHeader('content-type','aplication/json')
          ->withStatus(200);

  } catch (PDOException $e) {
      echo '{"errorr": {"text":  '.$e->getMessage().'}';
      $error = array(
          "message" => $e->getMessage()
      );

      $response->getBody()->write(json_encode($error));
      return $response
          ->withHeader('content-type','aplication/json')
          ->withStatus(500);
  }
});

//======================================== OBTENER Almacen Compuesto ==============================================
$app->get('/GetAlmacenCompuesto', function (Request $request, Response $response) {
  $sql = "SELECT almacen.id_almacen, insumos.codigo, insumos.nombre, detalle_insumo.tamano, detalle_insumo.presentacion, detalle_insumo.imagen, 
  almacen.cantidad, almacen.stock_minimo, unidad_de_medida.unidad FROM almacen INNER JOIN detalle_insumo ON	almacen.id_detalle_insumo = detalle_insumo.id_detalle_insumo INNER JOIN insumos ON	detalle_insumo.id_insumos = insumos.id_insumos INNER JOIN unidad_de_medida
  ON detalle_insumo.id_unidad_de_medida = unidad_de_medida.id_unidad_de_medida;";
  try {
    $db = new Db();
    $conn = $db->connect();
    $stmt = $conn->query($sql);
    $customers = $stmt->fetchAll(PDO::FETCH_OBJ);
    $db = null;
    $response->getBody()->write(json_encode($customers));
    return $response
    ->withHeader('content-type', 'application/json')
    ->withStatus(200);
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

//===========================================================================================================
//========================================INICIO PROVEEDOR============================================

//========================================ACTUALIZAR Detalle INSUMO===============================
$app->put('/ActualizarDetalleInsumo/{id}', function (Request $request, Response $response, array $args){
  $id = $request->getAttribute('id');
  $data = $request->getParsedBody();

  $tamano = $data["tamano"];
  $imagen = $data["imagen"];
  $presentacion = $data["presentacion"];
  $id_unidad_de_medida = $data["id_unidad_de_medida"];
  
  $sql = "UPDATE detalle_insumo SET
          tamano = :tamano,
          presentacion = :presentacion,
          id_unidad_de_medida = :id_unidad_de_medida,
          imagen = :imagen

  WHERE id_detalle_insumo = $id";

  try {
  $db = new Db();
  $conn = $db->connect();
  $stmt = $conn->prepare($sql);
  $stmt->bindParam(':tamano', $tamano);
  $stmt->bindParam(':presentacion', $presentacion);
  $stmt->bindParam(':id_unidad_de_medida', $id_unidad_de_medida);
  $stmt->bindParam(':imagen', $imagen);
  $result = $stmt->execute();
  $db = null;
  echo "Update successful! ";
  $response->getBody()->write(json_encode($result));
  return $response
      ->withHeader('content-type', 'application/json')
      ->withStatus(200);
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
//===========================================================================================================
//========================================ACTUALIZAR INSUMO===============================
$app->put('/ActualizarInsumo/{id}', function (Request $request, Response $response, array $args){
  $id = $request->getAttribute('id');
  $data = $request->getParsedBody();
  $codigo = $data["codigo"];
  $nombre = $data["nombre"];
  $id_categoria_insumos = $data["id_categoria_insumos"];
  $estatus = $data["estatus"];
  $producto = $data["producto"];
  
  $sql = "UPDATE insumos SET
          codigo = :codigo,
          nombre = :nombre,
          id_categoria_insumos = :id_categoria_insumos,
          estatus = :estatus,
          producto = :producto

  WHERE id_insumos = $id";

  try {
  $db = new Db();
  $conn = $db->connect();
  $stmt = $conn->prepare($sql);
  $stmt->bindParam(':codigo', $codigo);
  $stmt->bindParam(':nombre', $nombre);
  $stmt->bindParam(':id_categoria_insumos', $id_categoria_insumos);
  $stmt->bindParam(':estatus', $estatus);
  $stmt->bindParam(':producto', $producto);
  $result = $stmt->execute();
  $db = null;
  echo "Update successful! ";
  $response->getBody()->write(json_encode($result));
  return $response
      ->withHeader('content-type', 'application/json')
      ->withStatus(200);
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
//======================================== OBTENER insumo Compuesta ==============================================
$app->get('/GetInsumoCompuesto', function (Request $request, Response $response) {
   $sql = "SELECT detalle_insumo.id_detalle_insumo, insumos.codigo, insumos.nombre FROM
   detalle_insumo INNER JOIN insumos ON detalle_insumo.id_insumos = insumos.id_insumos;";
   try {
     $db = new Db();
     $conn = $db->connect();
     $stmt = $conn->query($sql);
     $customers = $stmt->fetchAll(PDO::FETCH_OBJ);
     $db = null;
     $response->getBody()->write(json_encode($customers));
     return $response
     ->withHeader('content-type', 'application/json')
     ->withStatus(200);
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



//======================================== OBTENER mermas Compuesta ==============================================
$app->get('/GetMermaCom', function (Request $request, Response $response) {
  /*  $sql = "SELECT id_insumos FROM insumos ORDER BY id_insumos DESC LIMIT 1;"; */
   $sql = "SELECT merma.id_merma, merma.id_detalle_insumo, insumos.codigo, insumos.nombre, 
   insumos.id_insumos, merma.fecha, merma.detalle FROM merma INNER JOIN detalle_insumo ON 
  merma.id_detalle_insumo = detalle_insumo.id_detalle_insumo INNER JOIN insumos ON 
  detalle_insumo.id_insumos = insumos.id_insumos";
   try {
     $db = new Db();
     $conn = $db->connect();
     $stmt = $conn->query($sql);
     $customers = $stmt->fetchAll(PDO::FETCH_OBJ);
     $db = null;
    
     $response->getBody()->write(json_encode($customers));
     return $response
     ->withHeader('content-type', 'application/json')
     ->withStatus(200);
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
//========================================OBTENER ULTIMO ID DE INSUMOS==============================================
$app->get('/GetLastInsumo', function (Request $request, Response $response) {
 /*  $sql = "SELECT id_insumos FROM insumos ORDER BY id_insumos DESC LIMIT 1;"; */
  $sql = "SELECT MAX( id_insumos ) FROM insumos LIMIT 1;";
  try {
    $db = new Db();
    $conn = $db->connect();
    $stmt = $conn->query($sql);
    $customers = $stmt->fetchAll(PDO::FETCH_OBJ);
    $db = null;
   
    $response->getBody()->write(json_encode($customers));
    return $response
    ->withHeader('content-type', 'application/json')
    ->withStatus(200);
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

 //========================================INSERTAR INSUMOS==============================================

/*  
 	id_insumos: 11
id_unidad_de_medida: 2
presentacion: "dñcm}ñkf"
tamaño: "56"
 		
  */

 $app->post('/InsertarDetalleInsumo', function (Request $request, Response $response, array $args) {
  $data = $request->getParsedBody();
  $tamano = $data["tamano"];
  $imagen = $data["imagen"];
  $presentacion = $data["presentacion"];
  $id_insumos = $data["id_insumos"];
  $id_unidad_de_medida = $data["id_unidad_de_medida"];
 
  $sql = "INSERT INTO detalle_insumo (tamano, presentacion, id_insumos, id_unidad_de_medida, imagen ) VALUES (:tamano, :presentacion, :id_insumos, :id_unidad_de_medida, :imagen);";
 
  try {
    $db = new Db();
    $conn = $db->connect();
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':tamano', $tamano);
    $stmt->bindParam(':presentacion', $presentacion);
    $stmt->bindParam(':id_insumos', $id_insumos);
    $stmt->bindParam(':id_unidad_de_medida', $id_unidad_de_medida);
    $stmt->bindParam(':imagen', $imagen);
    $result = $stmt->execute();
    $db = null;
    $response->getBody()->write(json_encode($result));
    return $response
      ->withHeader('content-type', 'application/json')
      ->withStatus(200);
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

//========================================INSERTAR INSUMOS==============================================
 $app->post('/InsertarInsumo', function (Request $request, Response $response, array $args) {
  $data = $request->getParsedBody();
  $codigo = $data["codigo"];
  $nombre = $data["nombre"];
  $id_categoria_insumos = $data["id_categoria_insumos"];
  $estatus = $data["estatus"];
  $producto = $data["producto"];
 
  $sql = "INSERT INTO insumos (codigo, nombre, id_categoria_insumos, estatus, producto) VALUES (:codigo, :nombre, :id_categoria_insumos, :estatus, :producto);";
 
  try {
    $db = new Db();
    $conn = $db->connect();
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':codigo', $codigo);
    $stmt->bindParam(':nombre', $nombre);
    $stmt->bindParam(':id_categoria_insumos', $id_categoria_insumos);
    $stmt->bindParam(':estatus', $estatus);
    $stmt->bindParam(':producto', $producto);
    $result = $stmt->execute();
    $db = null;
    $response->getBody()->write(json_encode($result));
    return $response
      ->withHeader('content-type', 'application/json')
      ->withStatus(200);
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
//========================================CONSULTA POR ID DE CATEGORIA===============================
$app->get('/GetCategoria/{id}', function (Request $request, Response $response) {
  $id = $request->getAttribute('id');
  $sql = "SELECT * FROM categoria_insumos WHERE id_categoria_insumos = $id;";
  try {
    $db = new Db();
    $conn = $db->connect();
    $stmt = $conn->query($sql);
    $customers = $stmt->fetchAll(PDO::FETCH_OBJ);
    $db = null;
    $response->getBody()->write(json_encode($customers));
    return $response
      ->withHeader('content-type', 'application/json')
      ->withStatus(200);
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
 //========================================CONSULTAR CATEGORIA==========================================
$app->get('/GetCategoria', function (Request $request, Response $response) {
  $sql = "SELECT * FROM categoria_insumos";
  try {
    $db = new Db();
    $conn = $db->connect();
    $stmt = $conn->query($sql);
    $customers = $stmt->fetchAll(PDO::FETCH_OBJ);
    $db = null;
   
    $response->getBody()->write(json_encode($customers));
    return $response
    ->withHeader('content-type', 'application/json')
    ->withStatus(200);
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

 //========================================INSERTAR CATEGORIA==============================================

$app->post('/InsertarCategoria', function (Request $request, Response $response, array $args) {
  $data = $request->getParsedBody();
  $descripcion = $data["descripcion"];      
  $categoria = $data["categoria"];
 
  $sql = "INSERT INTO categoria_insumos (categoria, descripcion) VALUES (:categoria, :descripcion)";
 
  try {
    $db = new Db();
    $conn = $db->connect();
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':descripcion', $descripcion);
    $stmt->bindParam(':categoria', $categoria);
    $result = $stmt->execute();
    $db = null;
    $response->getBody()->write(json_encode($result));
    return $response
      ->withHeader('content-type', 'application/json')
      ->withStatus(200);
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

 //========================================ACTUALIZAR CATEGORIA===============================
$app->put('/ActualizarCategoria/{id}', function (Request $request, Response $response, array $args){
  $id = $request->getAttribute('id');
  $data = $request->getParsedBody();
  $descripcion = $data["descripcion"];      
  $categoria = $data["categoria"];    

  $sql = "UPDATE categoria_insumos SET categoria = :categoria, descripcion = :descripcion WHERE id_categoria_insumos = $id";

  try {
  $db = new Db();
  $conn = $db->connect();
  $stmt = $conn->prepare($sql);
  $stmt->bindParam(':descripcion', $descripcion);
  $stmt->bindParam(':categoria', $categoria);
  $result = $stmt->execute();

  $db = null;
  echo "Update successful! ";
  $response->getBody()->write(json_encode($result));
  return $response
      ->withHeader('content-type', 'application/json')
      ->withStatus(200);
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

//======================================== ELIMINAR  CATEGORIA ===============================
$app->delete('/DeleteCategoria/{id}', function (Request $request, Response $response, array $args) {
  $id = $request->getAttribute('id');
  $sql = "DELETE FROM categoria_insumos WHERE id_categoria_insumos = $id";
  try {
    $db = new Db();
    $conn = $db->connect();
    $stmt = $conn->prepare($sql);
    $result = $stmt->execute();
    $db = null;
    $response->getBody()->write(json_encode($result));
    return $response
      ->withHeader('content-type', 'application/json')
      ->withStatus(200);
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


 //========================================CONSULTA PROVEEDOR==========================================
$app->get('/GetProveedor', function (Request $request, Response $response) {
  $sql = "SELECT * FROM proveedor";
  try {
    $db = new Db();
    $conn = $db->connect();
    $stmt = $conn->query($sql);
    $customers = $stmt->fetchAll(PDO::FETCH_OBJ);
    $db = null;
   
    $response->getBody()->write(json_encode($customers));
    return $response
      ->withHeader('Access-Control-Allow-Origin', '*')
      ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
      ->withHeader('content-type', 'application/json')
      ->withStatus(200);
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
//========================================CONSULTA POR ID DE PROVEEDOR===============================
$app->get('/GetProveedor/{id}', function (Request $request, Response $response) {
  $id = $request->getAttribute('id');
  $sql = "SELECT * FROM proveedor WHERE id_proveedor = $id;";
  try {
    $db = new Db();
    $conn = $db->connect();
    $stmt = $conn->query($sql);
    $customers = $stmt->fetchAll(PDO::FETCH_OBJ);
    $db = null;
   
    $response->getBody()->write(json_encode($customers));
    return $response
      ->withHeader('content-type', 'application/json')
      ->withStatus(200);
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
 //========================================INSERTAR PROVEEDOR===============================

$app->post('/InsertarProveedor', function (Request $request, Response $response, array $args) {

  $data = $request->getParsedBody();
  $nombre_proveedor = $data["nombre_proveedor"];      
  $rfc_proveedor = $data["rfc_proveedor"];      
  $telefono_proveedor = $data["telefono_proveedor"];    
  $correo_proveedor = $data["correo_proveedor"];     
  $direccion_proveedor = $data["direccion_proveedor"];      
  $telefono_alterno_proveedor = $data["telefono_alterno_proveedor"];      
  $web = $data["web"];   
  $estatus = $data["estatus"];    
 
  $sql = "INSERT INTO proveedor (nombre_proveedor,rfc_proveedor,telefono_proveedor,correo_proveedor,direccion_proveedor,telefono_alterno_proveedor,web,estatus) VALUES (:nombre_proveedor,:rfc_proveedor,:telefono_proveedor,:correo_proveedor,:direccion_proveedor,:telefono_alterno_proveedor,:web,:estatus)";
 
  try {
    $db = new Db();
    $conn = $db->connect();
   
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':nombre_proveedor', $nombre_proveedor);
    $stmt->bindParam(':rfc_proveedor', $rfc_proveedor);
    $stmt->bindParam(':telefono_proveedor', $telefono_proveedor);
    $stmt->bindParam(':correo_proveedor', $correo_proveedor);
    $stmt->bindParam(':direccion_proveedor', $direccion_proveedor);
    $stmt->bindParam(':telefono_alterno_proveedor', $telefono_alterno_proveedor);
    $stmt->bindParam(':web', $web);
    $stmt->bindParam(':estatus', $estatus);
 
    $result = $stmt->execute();
 
    $db = null;
    $response->getBody()->write(json_encode($result));
    return $response
      ->withHeader('content-type', 'application/json')
      ->withStatus(200);
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

 //========================================ACTUALIZAR PROVEEDORES===============================
$app->put('/ActualizarProveedor/{id}', function (Request $request, Response $response, array $args){
  $id = $request->getAttribute('id');
  $data = $request->getParsedBody();
  $nombre_proveedor = $data["nombre_proveedor"];      
  $rfc_proveedor = $data["rfc_proveedor"];      
  $telefono_proveedor = $data["telefono_proveedor"];    
  $correo_proveedor = $data["correo_proveedor"];     
  $direccion_proveedor = $data["direccion_proveedor"];      
  $telefono_alterno_proveedor = $data["telefono_alterno_proveedor"];      
  $web = $data["web"];   
  $estatus = $data["estatus"]; 

  $sql = "UPDATE proveedor SET
          nombre_proveedor = :nombre_proveedor,
          rfc_proveedor = :rfc_proveedor,
          telefono_proveedor = :telefono_proveedor,
          correo_proveedor = :correo_proveedor,
          direccion_proveedor = :direccion_proveedor,
          telefono_alterno_proveedor = :telefono_alterno_proveedor,
          web = :web,
          estatus = :estatus
  WHERE id_proveedor = $id";

  try {
  $db = new Db();
  $conn = $db->connect();
  $stmt = $conn->prepare($sql);

    $stmt->bindParam(':nombre_proveedor', $nombre_proveedor);
    $stmt->bindParam(':rfc_proveedor', $rfc_proveedor);
    $stmt->bindParam(':telefono_proveedor', $telefono_proveedor);
    $stmt->bindParam(':correo_proveedor', $correo_proveedor);
    $stmt->bindParam(':direccion_proveedor', $direccion_proveedor);
    $stmt->bindParam(':telefono_alterno_proveedor', $telefono_alterno_proveedor);
    $stmt->bindParam(':web', $web);
    $stmt->bindParam(':estatus', $estatus);

  $result = $stmt->execute();

  $db = null;
  echo "Update successful! ";
  $response->getBody()->write(json_encode($result));
  return $response
      ->withHeader('content-type', 'application/json')
      ->withStatus(200);
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
//===========================================================================================================
//========================================ACTUALIZAR PROVEEDORES===============================
$app->put('/ActualizarEstatusProveedor/{id}', function (Request $request, Response $response, array $args){
  $id = $request->getAttribute('id');
  $data = $request->getParsedBody();
  $estatus = $data["estatus"]; 
  $sql = "UPDATE proveedor SET
          estatus = :estatus
  WHERE id_proveedor = $id";
  try {
  $db = new Db();
  $conn = $db->connect();
  $stmt = $conn->prepare($sql);
  $stmt->bindParam(':estatus', $estatus);
  $result = $stmt->execute();
  $db = null;
  echo "Update successful! ";
  $response->getBody()->write(json_encode($result));
  return $response
      ->withHeader('content-type', 'application/json')
      ->withStatus(200);
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
//===========================================================================================================
//========================================FIN PROVEEDOR===============================================
//===========================================================================================================

//===========================================================================================================
//========================================INICIO COMPRA======================================================
//===========================================================================================================
//========================================CONSULTA COMPRA====================================================
$app->get('/GetCompra', function (Request $request, Response $response) {
  $sql = "SELECT * FROM compra";
  try {
    $db = new Db();
    $conn = $db->connect();
    $stmt = $conn->query($sql);
    $customers = $stmt->fetchAll(PDO::FETCH_OBJ);
    $db = null;
   
    $response->getBody()->write(json_encode($customers));
    return $response
      ->withHeader('content-type', 'application/json')
      ->withStatus(200);
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
//========================================CONSULTA POR ID DE COMPRA===============================
$app->get('/GetCompra/{id}', function (Request $request, Response $response) {
  $id = $request->getAttribute('id');
  $sql = "SELECT * FROM compra WHERE id_compra = $id;";
  try {
    $db = new Db();
    $conn = $db->connect();
    $stmt = $conn->query($sql);
    $customers = $stmt->fetchAll(PDO::FETCH_OBJ);
    $db = null;
   
    $response->getBody()->write(json_encode($customers));
    return $response
      ->withHeader('content-type', 'application/json')
      ->withStatus(200);
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
//========================================INSERTAR COMPRAS==============================================

$app->post('/InsertarCompra', function (Request $request, Response $response, array $args) {

  $data = $request->getParsedBody();

  $id_proveedor = $data["id_proveedor"];      
  $cantidad = $data["cantidad"];      
  $precio_unitario = $data["precio_unitario"];    
  $precio_total = $data["precio_total"];     
  $fecha = $data["fecha"];      
  $id_tipo_pago = $data["id_tipo_pago"];      
  $id_detalle_insumo = $data["id_detalle_insumo"];   
  $sql = "INSERT INTO compra (id_proveedor,cantidad,precio_unitario,precio_total,fecha,id_tipo_pago,id_detalle_insumo) VALUES (:id_proveedor,:cantidad,:precio_unitario,:precio_total,:fecha,:id_tipo_pago,:id_detalle_insumo)";

  try {

    $db = new Db();
    $conn = $db->connect();
   
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id_proveedor', $id_proveedor);
    $stmt->bindParam(':cantidad', $cantidad);
    $stmt->bindParam(':precio_unitario', $precio_unitario);
    $stmt->bindParam(':precio_total', $precio_total);
    $stmt->bindParam(':fecha', $fecha);
    $stmt->bindParam(':id_tipo_pago', $id_tipo_pago);
    $stmt->bindParam(':id_detalle_insumo', $id_detalle_insumo);
 
    $result = $stmt->execute();
 
    $db = null;
    $response->getBody()->write(json_encode($result));
    return $response
      ->withHeader('content-type', 'application/json')
      ->withStatus(200);
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

//========================================ACTUALIZAR COMPRAS===========================================
$app->put('/ActualizarCompra/{id}', function (Request $request, Response $response, array $args){
  $id = $request->getAttribute('id');
  $data = $request->getParsedBody();

  $id_proveedor = $data["id_proveedor"];      
  $cantidad = $data["cantidad"];      
  $precio_unitario = $data["precio_unitario"];    
  $precio_total = $data["precio_total"];     
  $fecha = $data["fecha"];      
  $id_tipo_pago = $data["id_tipo_pago"];      
  $id_detalle_insumo = $data["id_detalle_insumo"]; 


  $sql = "UPDATE compra SET
          id_proveedor = :id_proveedor,
          cantidad = :cantidad,
          precio_unitario = :precio_unitario,
          precio_total = :precio_total,
          fecha = :fecha,
          id_tipo_pago = :id_tipo_pago,
          id_detalle_insumo = :id_detalle_insumo
  WHERE id_compra = $id";

  try {
  $db = new Db();
  $conn = $db->connect();
  $stmt = $conn->prepare($sql);

  $stmt->bindParam(':id_proveedor', $id_proveedor);
  $stmt->bindParam(':cantidad', $cantidad);
  $stmt->bindParam(':precio_unitario', $precio_unitario);
  $stmt->bindParam(':precio_total', $precio_total);
  $stmt->bindParam(':fecha', $fecha);
  $stmt->bindParam(':id_tipo_pago', $id_tipo_pago);
  $stmt->bindParam(':id_detalle_insumo', $id_detalle_insumo);

  $result = $stmt->execute();

  $db = null;
  echo "Update successful! ";
  $response->getBody()->write(json_encode($result));
  return $response
      ->withHeader('content-type', 'application/json')
      ->withStatus(200);
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
//===========================================================================================================
//========================================FIN COMPRAS========================================================
//===========================================================================================================

//===========================================================================================================
//========================================INICIO MERMAS======================================================
//===========================================================================================================
//========================================CONSULTA MERMAS====================================================
$app->get('/GetMerma', function (Request $request, Response $response) {
  $sql = "SELECT * FROM merma";
  try {
    $db = new Db();
    $conn = $db->connect();
    $stmt = $conn->query($sql);
    $customers = $stmt->fetchAll(PDO::FETCH_OBJ);
    $db = null;
   
    $response->getBody()->write(json_encode($customers));
    return $response
      ->withHeader('content-type', 'application/json')
      ->withStatus(200);
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

 //========================================CONSULTA POR ID DE MERMA===============================
$app->get('/GetMerma/{id}', function (Request $request, Response $response) {
  $id = $request->getAttribute('id');
  $sql = "SELECT * FROM merma WHERE id_merma = $id;";
  try {
    $db = new Db();
    $conn = $db->connect();
    $stmt = $conn->query($sql);
    $customers = $stmt->fetchAll(PDO::FETCH_OBJ);
    $db = null;
   
    $response->getBody()->write(json_encode($customers));
    return $response
      ->withHeader('content-type', 'application/json')
      ->withStatus(200);
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
//========================================INSERTAR MERMAS==============================================

$app->post('/InsertarMerma', function (Request $request, Response $response, array $args) {

  $data = $request->getParsedBody();
  $id_detalle_insumo = $data["id_detalle_insumo"];      
  $fecha = $data["fecha"];      
  $detalle = $data["detalle"];     
 
  $sql = "INSERT INTO merma (id_detalle_insumo, fecha, detalle) VALUES (:id_detalle_insumo, :fecha, :detalle)";
 
  try {

    $db = new Db();
    $conn = $db->connect();
   
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id_detalle_insumo', $id_detalle_insumo);
    $stmt->bindParam(':fecha', $fecha);
    $stmt->bindParam(':detalle', $detalle);
 
    $result = $stmt->execute();
 
    $db = null;
    $response->getBody()->write(json_encode($result));
    return $response
      ->withHeader('content-type', 'application/json')
      ->withStatus(200);
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
//========================================ACTUALIZAR MERMAS===============================
$app->put('/ActualizarMerma/{id}', function (Request $request, Response $response, array $args){
  $id = $request->getAttribute('id');
  $data = $request->getParsedBody();

  $id_detalle_insumo = $data["id_detalle_insumo"];      
  $fecha = $data["fecha"];      
  $detalle = $data["detalle"]; 


  $sql = "UPDATE merma SET
          id_detalle_insumo = :id_detalle_insumo,
          fecha = :fecha,
          detalle = :detalle
  WHERE id_merma = $id";

  try {
  $db = new Db();
  $conn = $db->connect();
  $stmt = $conn->prepare($sql);

  $stmt->bindParam(':id_detalle_insumo', $id_detalle_insumo);
  $stmt->bindParam(':fecha', $fecha);
  $stmt->bindParam(':detalle', $detalle);

  $result = $stmt->execute();

  $db = null;
  echo "Update successful! ";
  $response->getBody()->write(json_encode($result));
  return $response
      ->withHeader('content-type', 'application/json')
      ->withStatus(200);
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
//===========================================================================================================

$app->get('/Get_Stock_Minimo', function (Request $request, Response $response) {
  $sql = "SELECT almacen.id_almacen, almacen.id_detalle_insumo, almacen.stock_minimo, insumos.nombre, detalle_insumo.presentacion, insumos.codigo, 
	unidad_de_medida.unidad, detalle_insumo.id_insumos, detalle_insumo.tamano FROM almacen INNER JOIN detalle_insumo ON 
		almacen.id_detalle_insumo = detalle_insumo.id_detalle_insumo INNER JOIN insumos ON detalle_insumo.id_insumos = insumos.id_insumos
	INNER JOIN unidad_de_medida ON detalle_insumo.id_unidad_de_medida = unidad_de_medida.id_unidad_de_medida;";
  try {
    $db = new Db();
    $conn = $db->connect();
    $stmt = $conn->query($sql);
    $customers = $stmt->fetchAll(PDO::FETCH_OBJ);
    $db = null;
    $response->getBody()->write(json_encode($customers));
    return $response
      ->withHeader('content-type', 'application/json')
      ->withStatus(200);
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
//========================================FIN MERMAS========================================================
//===========================================================================================================

//===========================================================================================================
//========================================INICIO TIPO DE PAGO================================================
//===========================================================================================================
 //========================================ACTUALIZAR stock===============================
 $app->put('/ActualizarStockMinimo/{id}', function (Request $request, Response $response, array $args){
  $id = $request->getAttribute('id');
  $data = $request->getParsedBody();

  $stock_minimo = $data["stock_minimo"];      

  $sql = "UPDATE almacen SET stock_minimo = :stock_minimo WHERE id_almacen = $id";

  try {
  $db = new Db();
  $conn = $db->connect();
  $stmt = $conn->prepare($sql);

  $stmt->bindParam(':stock_minimo', $stock_minimo);

  $result = $stmt->execute();

  $db = null;
  echo "Update successful! ";
  $response->getBody()->write(json_encode($result));
  return $response
      ->withHeader('content-type', 'application/json')
      ->withStatus(200);
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
//========================================CONSULTA TIPO DE PAGO==============================================
$app->get('/GetTipo_Pago', function (Request $request, Response $response) {
  $sql = "SELECT * FROM tipo_pago";
  try {
    $db = new Db();
    $conn = $db->connect();
    $stmt = $conn->query($sql);
    $customers = $stmt->fetchAll(PDO::FETCH_OBJ);
    $db = null;
    $response->getBody()->write(json_encode($customers));
    return $response
      ->withHeader('content-type', 'application/json')
      ->withStatus(200);
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

 //========================================CONSULTA POR TIPO DE PAGO===============================
$app->get('/GetTipo_Pago/{id}', function (Request $request, Response $response) {
  $id = $request->getAttribute('id');
  $sql = "SELECT * FROM tipo_pago WHERE id_tipo_pago = $id;";
  try {
    $db = new Db();
    $conn = $db->connect();
    $stmt = $conn->query($sql);
    $customers = $stmt->fetchAll(PDO::FETCH_OBJ);
    $db = null;
   
    $response->getBody()->write(json_encode($customers));
    return $response
      ->withHeader('content-type', 'application/json')
      ->withStatus(200);
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
//========================================INSERTAR TIPO DE PAGO==============================================

$app->post('/InsertarTipo_Pago', function (Request $request, Response $response, array $args) {
  $data = $request->getParsedBody();
  $descripcion = $data["descripcion"];      
  $sql = "INSERT INTO tipo_pago (descripcion) VALUES (:descripcion)";
  try {
    $db = new Db();
    $conn = $db->connect();
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':descripcion', $descripcion);
    $result = $stmt->execute();
    $db = null;
    $response->getBody()->write(json_encode($result));
    return $response
      ->withHeader('content-type', 'application/json')
      ->withStatus(200);
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
 //========================================ACTUALIZAR TIPO DE PAGO===============================
$app->put('/ActualizarTipo_Pago/{id}', function (Request $request, Response $response, array $args){
  $id = $request->getAttribute('id');
  $data = $request->getParsedBody();

  $descripcion = $data["descripcion"];      

  $sql = "UPDATE tipo_pago SET descripcion = :descripcion WHERE id_tipo_pago = $id";

  try {
  $db = new Db();
  $conn = $db->connect();
  $stmt = $conn->prepare($sql);

  $stmt->bindParam(':descripcion', $descripcion);

  $result = $stmt->execute();

  $db = null;
  echo "Update successful! ";
  $response->getBody()->write(json_encode($result));
  return $response
      ->withHeader('content-type', 'application/json')
      ->withStatus(200);
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

//============== ELIMINAR TIPO PAGO ===============================
$app->delete('/DeleteTipo_Pago/{id}', function (Request $request, Response $response, array $args) {
  $id = $request->getAttribute('id');
  $sql = "DELETE FROM tipo_pago WHERE id_tipo_pago = $id";
  try {
    $db = new Db();
    $conn = $db->connect();
    $stmt = $conn->prepare($sql);
    $result = $stmt->execute();
    $db = null;
    $response->getBody()->write(json_encode($result));
    return $response
      ->withHeader('content-type', 'application/json')
      ->withStatus(200);
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
//===========================================================================================================
//========================================FIN TIPO DE PAGO===================================================
//===========================================================================================================

//===========================================================================================================
//========================================INICIO UNIDAD DE MEDIDA============================================
//===========================================================================================================
//========================================CONSULTA UNIDAD DE MEDIDA==========================================
$app->get('/GetUnidad-Medida', function (Request $request, Response $response) {
  $sql = "SELECT * FROM unidad_de_medida";
  try {
    $db = new Db();
    $conn = $db->connect();
    $stmt = $conn->query($sql);
    $customers = $stmt->fetchAll(PDO::FETCH_OBJ);
    $db = null;
   
    $response->getBody()->write(json_encode($customers));
    return $response
      ->withHeader('content-type', 'application/json')
      ->withStatus(200);
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

 //========================================CONSULTA POR ID DE UNIDAD DE MEDIDA===============================
 $app->get('/GetUnidad-Medida/{id}', function (Request $request, Response $response) {
  $id = $request->getAttribute('id');
  $sql = "SELECT * FROM unidad_de_medida WHERE id_unidad_de_medida = $id;";
  try {
    $db = new Db();
    $conn = $db->connect();
    $stmt = $conn->query($sql);
    $customers = $stmt->fetchAll(PDO::FETCH_OBJ);
    $db = null;
   
    $response->getBody()->write(json_encode($customers));
    return $response
      ->withHeader('content-type', 'application/json')
      ->withStatus(200);
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

//========================================INSERTAR UNIDAD DE MEDIDA===============================

$app->post('/Insertar-Unidad-Medida', function (Request $request, Response $response, array $args) {
  $data = $request->getParsedBody();
  $unidad = $data["unidad"];
  $descripcion = $data["descripcion"];
 
  $sql = "INSERT INTO unidad_de_medida (unidad, descripcion) VALUES (:unidad, :descripcion)";
 
  try {
    $db = new Db();
    $conn = $db->connect();
   
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':unidad', $unidad);
    $stmt->bindParam(':descripcion', $descripcion);
 
    $result = $stmt->execute();
 
    $db = null;
    $response->getBody()->write(json_encode($result));
    return $response
      ->withHeader('content-type', 'application/json')
      ->withStatus(200);
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


//========================================ACTUALIZAR UNIDAD DE MEDIDA===============================
$app->put('/Actualizar-Unidad-Medida/{id}', function (Request $request, Response $response, array $args){
  $id = $request->getAttribute('id');
  $data = $request->getParsedBody();
  $unidad = $data["unidad"];
  $descripcion = $data["descripcion"];

  $sql = "UPDATE unidad_de_medida SET
          unidad = :unidad,
          descripcion = :descripcion
  WHERE id_unidad_de_medida = $id";

  try {
  $db = new Db();
  $conn = $db->connect();
  $stmt = $conn->prepare($sql);
  $stmt->bindParam(':unidad', $unidad);
  $stmt->bindParam(':descripcion', $descripcion);

  $result = $stmt->execute();

  $db = null;
  echo "Update successful! ";
  $response->getBody()->write(json_encode($result));
  return $response
      ->withHeader('content-type', 'application/json')
      ->withStatus(200);
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
//============== ELIMINAR UNIDAD DE MEDIDA ===============================
$app->delete('/Delete_Unidad_Medida/{id}', function (Request $request, Response $response, array $args) {
  $id = $request->getAttribute('id');
  $sql = "DELETE FROM unidad_de_medida WHERE id_unidad_de_medida = $id";
  try {
    $db = new Db();
    $conn = $db->connect();
    $stmt = $conn->prepare($sql);
    $result = $stmt->execute();
    $db = null;
    $response->getBody()->write(json_encode($result));
    return $response
      ->withHeader('content-type', 'application/json')
      ->withStatus(200);
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
//===========================================================================================================
//========================================FIN UNIDAD DE MEDIDA===============================================
//===========================================================================================================

//===========================================================================================================
//========================================INICIO ALMACEN================================================
//===========================================================================================================
//========================================CONSULTA ALMACEN==============================================
$app->get('/GetAlmacen', function (Request $request, Response $response) {
  $sql = "SELECT * FROM almacen";
  try {
    $db = new Db();
    $conn = $db->connect();
    $stmt = $conn->query($sql);
    $customers = $stmt->fetchAll(PDO::FETCH_OBJ);
    $db = null;
   
    $response->getBody()->write(json_encode($customers));
    return $response
      ->withHeader('content-type', 'application/json')
      ->withStatus(200);
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
 //========================================CONSULTA POR ID DE ALMACEN===============================
 $app->get('/GetAlmacen/{id}', function (Request $request, Response $response) {
  $id = $request->getAttribute('id');
  $sql = "SELECT * FROM almacen WHERE id_almacen = $id;";
  try {
    $db = new Db();
    $conn = $db->connect();
    $stmt = $conn->query($sql);
    $customers = $stmt->fetchAll(PDO::FETCH_OBJ);
    $db = null;
   
    $response->getBody()->write(json_encode($customers));
    return $response
      ->withHeader('content-type', 'application/json')
      ->withStatus(200);
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
 //========================================INSERTAR ALMACEN===============================

$app->post('/InsertarAlmacen', function (Request $request, Response $response, array $args) {
  $data = $request->getParsedBody();

  $id_detalle_insumo = $data["id_detalle_insumo"];
  $detalles = $data["detalles"];
  $cantidad = $data["cantidad"];
  $stock_minimo = $data["stock_minimo"];
 
  $sql = "INSERT INTO almacen (id_detalle_insumo, detalles, cantidad, stock_minimo) VALUES (:id_detalle_insumo, :detalles, :cantidad, :stock_minimo)";
 
  try {
    $db = new Db();
    $conn = $db->connect();
   
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id_detalle_insumo', $id_detalle_insumo);
    $stmt->bindParam(':detalles', $detalles);
    $stmt->bindParam(':cantidad', $cantidad);
    $stmt->bindParam(':stock_minimo', $stock_minimo);
    $result = $stmt->execute();
 
    $db = null;
    $response->getBody()->write(json_encode($result));
    return $response
      ->withHeader('content-type', 'application/json')
      ->withStatus(200);
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
//========================================ACTUALIZAR ALMACEN===============================
$app->put('/ActualizarAlmacen/{id}', function (Request $request, Response $response, array $args){
  $id = $request->getAttribute('id');
  $data = $request->getParsedBody();

  $id_detalle_insumo = $data["id_detalle_insumo"];
  $detalles = $data["detalles"];
  $cantidad = $data["cantidad"];
  $stock_minimo = $data["stock_minimo"];

  $sql = "UPDATE almacen SET
          id_detalle_insumo = :id_detalle_insumo,
          detalles = :detalles,
          cantidad = :cantidad,
          stock_minimo = :stock_minimo

  WHERE id_almacen = $id";

  try {
  $db = new Db();
  $conn = $db->connect();
  $stmt = $conn->prepare($sql);
  $stmt->bindParam(':id_detalle_insumo', $id_detalle_insumo);
  $stmt->bindParam(':detalles', $detalles);
  $stmt->bindParam(':cantidad', $cantidad);
  $stmt->bindParam(':stock_minimo', $stock_minimo);
  $result = $stmt->execute();
  $db = null;
  echo "Update successful! ";
  $response->getBody()->write(json_encode($result));
  return $response
      ->withHeader('content-type', 'application/json')
      ->withStatus(200);
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
//===========================================================================================================
$app->get('/GetinsumosYDetalleID/{id}', function (Request $request, Response $response) {
  $id = $request->getAttribute('id');
  $sql = "SELECT detalle_insumo.id_detalle_insumo, detalle_insumo.tamano, detalle_insumo.presentacion, insumos.id_insumos, 
	detalle_insumo.id_unidad_de_medida, insumos.codigo, insumos.nombre, insumos.id_categoria_insumos, insumos.estatus, 
	insumos.producto, detalle_insumo.imagen FROM detalle_insumo INNER JOIN insumos ON detalle_insumo.id_insumos = insumos.id_insumos 
  WHERE detalle_insumo.id_detalle_insumo =  $id;";

  try {
    $db = new Db();
    $conn = $db->connect();
    $stmt = $conn->query($sql);
    $customers = $stmt->fetchAll(PDO::FETCH_OBJ);
    $db = null;
    $response->getBody()->write(json_encode($customers));
    return $response
      ->withHeader('content-type', 'application/json')
      ->withStatus(200);
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

 //===========================================================================================================
$app->get('/GetinsumosYDetalle', function (Request $request, Response $response) {

  $sql = "SELECT detalle_insumo.id_detalle_insumo, detalle_insumo.tamano, detalle_insumo.presentacion, insumos.id_insumos, 
	detalle_insumo.id_unidad_de_medida, insumos.codigo, insumos.nombre, insumos.id_categoria_insumos, insumos.estatus, 
	insumos.producto, detalle_insumo.imagen FROM detalle_insumo INNER JOIN insumos ON detalle_insumo.id_insumos = insumos.id_insumos;";

  try {
    $db = new Db();
    $conn = $db->connect();
    $stmt = $conn->query($sql);
    $customers = $stmt->fetchAll(PDO::FETCH_OBJ);
    $db = null;
    $response->getBody()->write(json_encode($customers));
    return $response
      ->withHeader('content-type', 'application/json')
      ->withStatus(200);
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
//========================================FIN ALMACEN========================================================
//===========================================================================================================

//========================================CONSULTA===============================
$app->get('/customers-data/all', function (Request $request, Response $response) {
    $sql = "SELECT * FROM customers";
    try {
      $db = new Db();
      $conn = $db->connect();
      $stmt = $conn->query($sql);
      $customers = $stmt->fetchAll(PDO::FETCH_OBJ);
      $db = null;
     
      $response->getBody()->write(json_encode($customers));
      return $response
        ->withHeader('content-type', 'application/json')
        ->withStatus(200);
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
   

   //========================================CONSULTA_POR_ID===============================
$app->get('/customers-data/all/{id}', function (Request $request, Response $response) {
    $id = $request->getAttribute('id');
    $sql = "SELECT * FROM customers WHERE id = $id;";
    try {
      $db = new Db();
      $conn = $db->connect();
      $stmt = $conn->query($sql);
      $customers = $stmt->fetchAll(PDO::FETCH_OBJ);
      $db = null;
     
      $response->getBody()->write(json_encode($customers));
      return $response
        ->withHeader('content-type', 'application/json')
        ->withStatus(200);
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


    //========================================AGREGAR===============================

   $app->post('/customers-data/add', function (Request $request, Response $response, array $args) {
    $data = $request->getParsedBody();
    $name = $data["name"];
    $email = $data["email"];
    $phone = $data["phone"];
   
    $sql = "INSERT INTO customers (name, email, phone) VALUES (:name, :email, :phone)";
   
    try {
      $db = new Db();
      $conn = $db->connect();
      $stmt = $conn->prepare($sql);
      $stmt->bindParam(':name', $name);
      $stmt->bindParam(':email', $email);
      $stmt->bindParam(':phone', $phone);
      $result = $stmt->execute();
      $db = null;
      $response->getBody()->write(json_encode($result));
      return $response
        ->withHeader('content-type', 'application/json')
        ->withStatus(200);
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


//========================================ACTUALIZAR===============================
   $app->put('/customers-data/update/{id}', function (Request $request, Response $response, array $args){
        $id = $request->getAttribute('id');
        $data = $request->getParsedBody();
        $name = $data["name"];
        $email = $data["email"];
        $phone = $data["phone"];

        $sql = "UPDATE customers SET
                name = :name,
                email = :email,
                phone = :phone
        WHERE id = $id";

        try {
        $db = new Db();
        $conn = $db->connect();
        
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':phone', $phone);

        $result = $stmt->execute();

        $db = null;
        echo "Update successful! ";
        $response->getBody()->write(json_encode($result));
        return $response
            ->withHeader('content-type', 'application/json')
            ->withStatus(200);
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

//=================================================ELIMINAR=========================================
$app->delete('/customers-data/delete/{id}', function (Request $request, Response $response, array $args) {
    $id = $args["id"];
   
    $sql = "DELETE FROM customers WHERE id = $id";
   
    try {
      $db = new Db();
      $conn = $db->connect();
     
      $stmt = $conn->prepare($sql);
      $result = $stmt->execute();
   
      $db = null;
      $response->getBody()->write(json_encode($result));
      return $response
        ->withHeader('content-type', 'application/json')
        ->withStatus(200);
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

 

  $app->POST('/subirimagen', function (Request $request, Response $response, array $args) {
   $data = $request->getParsedBody();
   $img = $data;
   print_r($img);
   $dir = "//img/";
   $nombreArchivo = $img['nombreArchivo'];
   $archivo = $img['base64textString'];
   list($type, $archivo) = explode(';', $archivo);
   list(, $archivo)      = explode(',', $archivo);
   $archivo = base64_decode($archivo);
   $filePath = $_SERVER['DOCUMENT_ROOT']."{$dir}".$nombreArchivo;
   file_put_contents($filePath, $archivo);
   });













//equipo 2

$app->get('/', function (Request $request, Response $response) {
  $response->getBody()->write('Hello World!');
  return $response;
});
//enviar correo
$app->post('/enviar',function(Request $request, Response $response)
{
       $requestParamter = $request->getParsedBody();
       $dat = ($requestParamter['estatus']);
       $estatus = ($dat['name']);
       //print_r($estatus);
       //print_r($requestParamter['email']);

       $name = $requestParamter['name'];
       $cartCount = $requestParamter['cartCount'];
       $price = $requestParamter['price'];
       $middleName = $requestParamter['middleName'];
       $lastName = $requestParamter['lastName'];
       $namee = $requestParamter['namee'];
       $email = $requestParamter['email'];
       $totalprecio = $cartCount * $price;
       //$id = '1';
       sendVerificationEmail($name,$cartCount,$price, $middleName,$lastName,$namee,$email,$estatus,$totalprecio);

});


//Function to send mail, 
function sendVerificationEmail($name,$cartCount,$price, $middleName,$lastName,$namee,$email,$estatus,$totalprecio)
{      
   $mail = new PHPMailer;

   $mail->SMTPDebug=1;
   $mail->isSMTP();

   $mail->Host="smtp.gmail.com";
   $mail->Port=587;
   $mail->SMTPSecure="tls";
   $mail->SMTPAuth=true;
   $mail->Username="restaurantaut2021@gmail.com";
   $mail->Password="javier456";

   $mail->addAddress($email,"User Name");
   $mail->Subject="Detalles de su pedido";
   $mail->isHTML();
   $htmlContent = ' $name,$cartCount,$price, $middleName,$lastName,$namee,$email,$estatus,$totalprecio  
   
<html lang="en" xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
<head>
  

   <link href="https://fonts.googleapis.com/css?family=Work+Sans:200,300,400,500,600,700" rel="stylesheet">

   <!-- CSS Reset : BEGIN -->
   <style>

       /* What it does: Remove spaces around the email design added by some email clients. */
       /* Beware: It can remove the padding / margin and add a background color to the compose a reply window. */
       html,
body {
   margin: 0 auto !important;
   padding: 0 !important;
   height: 100% !important;
   width: 100% !important;
   background: #f1f1f1;
}

/* What it does: Stops email clients resizing small text. */
* {
   -ms-text-size-adjust: 100%;
   -webkit-text-size-adjust: 100%;
}

/* What it does: Centers email on Android 4.4 */
div[style*="margin: 16px 0"] {
   margin: 0 !important;
}

/* What it does: Stops Outlook from adding extra spacing to tables. */
table,
td {
   mso-table-lspace: 0pt !important;
   mso-table-rspace: 0pt !important;
}

/* What it does: Fixes webkit padding issue. */
table {
   border-spacing: 0 !important;
   border-collapse: collapse !important;
   table-layout: fixed !important;
   margin: 0 auto !important;
}

/* What it does: Uses a better rendering method when resizing images in IE. */
img {
   -ms-interpolation-mode:bicubic;
}

/* What it does: Prevents Windows 10 Mail from underlining links despite inline CSS. Styles for underlined links should be inline. */
a {
   text-decoration: none;
}

.unstyle-auto-detected-links *,
.aBn {
   border-bottom: 0 !important;
   cursor: default !important;
   color: inherit !important;
   text-decoration: none !important;
   font-size: inherit !important;
   font-family: inherit !important;
   font-weight: inherit !important;
   line-height: inherit !important;
}


.a6S {
   display: none !important;
   opacity: 0.01 !important;
}


.im {
   color: inherit !important;
}


img.g-img + div {
   display: none !important;
}



/* iPhone 4, 4S, 5, 5S, 5C, and 5SE */
@media only screen and (min-device-width: 320px) and (max-device-width: 374px) {
   u ~ div .email-container {
       min-width: 320px !important;
   }
}
/* iPhone 6, 6S, 7, 8, and X */
@media only screen and (min-device-width: 375px) and (max-device-width: 413px) {
   u ~ div .email-container {
       min-width: 375px !important;
   }
}
/* iPhone 6+, 7+, and 8+ */
@media only screen and (min-device-width: 414px) {
   u ~ div .email-container {
       min-width: 414px !important;
   }
}
   </style>

   <!-- CSS Reset : END -->

   <!-- Progressive Enhancements : BEGIN -->
   <style>

     .primary{
 background: #17bebb;
}
.bg_white{
 background: #ffffff;
}
.bg_light{
 background: #f7fafa;
}
.bg_black{
 background: #000000;
}
.bg_dark{
 background: rgba(0,0,0,.8);
}
.email-section{
 padding:2.5em;
}

/*BUTTON*/
.btn{
 padding: 10px 15px;
 display: inline-block;
}
.btn.btn-primary{
 border-radius: 5px;
 background: #17bebb;
 color: #ffffff;
}
.btn.btn-white{
 border-radius: 5px;
 background: #ffffff;
 color: #000000;
}
.btn.btn-white-outline{
 border-radius: 5px;
 background: transparent;
 border: 1px solid #fff;
 color: #fff;
}
.btn.btn-black-outline{
 border-radius: 0px;
 background: transparent;
 border: 2px solid #000;
 color: #000;
 font-weight: 700;
}
.btn-custom{
 color: rgba(0,0,0,.3);
 text-decoration: underline;
}

h1,h2,h3,h4,h5,h6{
 font-family: "Work Sans", sans-serif;
 color: #000000;
 margin-top: 0;
 font-weight: 400;
}

body{
 font-family: "Work Sans", sans-serif;
 font-weight: 400;
 font-size: 15px;
 line-height: 1.8;
 color: rgba(0,0,0,.4);
}

a{
 color: #17bebb;
}

table{
}
/*LOGO*/

.logo h1{
 margin: 0;
}
.logo h1 a{
 color: #17bebb;
 font-size: 24px;
 font-weight: 700;
 font-family: "Work Sans", sans-serif;
}

/*HERO*/
.hero{
 position: relative;
 z-index: 0;
}

.hero .text{
 color: rgba(0,0,0,.3);
}
.hero .text h2{
 color: #000;
 font-size: 34px;
 margin-bottom: 15px;
 font-weight: 300;
 line-height: 1.2;
}
.hero .text h3{
 font-size: 24px;
 font-weight: 200;
}
.hero .text h2 span{
 font-weight: 600;
 color: #000;
}


/*PRODUCT*/
.product-entry{
 display: block;
 position: relative;
 float: left;
 padding-top: 20px;
}
.product-entry .text{
 width: calc(100% - 125px);
 padding-left: 20px;
}
.product-entry .text h3{
 margin-bottom: 0;
 padding-bottom: 0;
}
.product-entry .text p{
 margin-top: 0;
}
.product-entry img, .product-entry .text{
 float: left;
}

ul.social{
 padding: 0;
}
ul.social li{
 display: inline-block;
 margin-right: 10px;
}

/*FOOTER*/

.footer{
 border-top: 1px solid rgba(0,0,0,.05);
 color: rgba(0,0,0,.5);
}
.footer .heading{
 color: #000;
 font-size: 20px;
}
.footer ul{
 margin: 0;
 padding: 0;
}
.footer ul li{
 list-style: none;
 margin-bottom: 10px;
}
.footer ul li a{
 color: rgba(0,0,0,1);
}


@media screen and (max-width: 500px) {


}


   </style>


</head>

<body width="100%" style="margin: 0; padding: 0 !important; mso-line-height-rule: exactly; background-color: #f1f1f1;">
 <center style="width: 100%; background-color: #f1f1f1;">
   <div style="display: none; font-size: 1px;max-height: 0px; max-width: 0px; opacity: 0; overflow: hidden; mso-hide: all; font-family: sans-serif;">
     &zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;
   </div>
   <div style="max-width: 600px; margin: 0 auto;" class="email-container">
     <!-- BEGIN BODY -->
     <table align="center" role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin: auto;">
       <tr>
         <td valign="top" class="bg_white" style="padding: 1em 2.5em 0 2.5em;">
           <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
             <tr>
               <td class="logo" style="text-align: left;">
                 <h1><a >Compra</a></h1>
               </td>
             </tr>
           </table>
         </td>
       </tr><!-- end tr -->
       <tr>
         <td valign="middle" class="hero bg_white" style="padding: 2em 0 2em 0;">
           <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
             <tr>
               <td style="padding: 0 2.5em; text-align: left;">
                 <div class="text">
                   <h2>Ronald your shopping cart misses you</h2>
                   <h3>Amazing deals, updates, interesting news right in your inbox</h3>
                 </div>
               </td>
             </tr>
           </table>
         </td>
       </tr><!-- end tr -->
       <tr>
         <table class="bg_white" role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
           <tr style="border-bottom: 1px solid rgba(0,0,0,.05);">
             <th width="80%" style="text-align:left; padding: 0 2.5em; color: #000; padding-bottom: 20px">Item</th>
             <th width="20%" style="text-align:right; padding: 0 2.5em; color: #000; padding-bottom: 20px">Price</th>
           </tr>
           <tr style="border-bottom: 1px solid rgba(0,0,0,.05);">
             <td valign="middle" width="80%" style="text-align:left; padding: 0 2.5em;">
               <div class="product-entry">
                 <img src="images/prod-1.jpg" alt="" style="width: 100px; max-width: 600px; height: auto; margin-bottom: 20px; display: block;">
                 <div class="text">
                   <h3>Analog Wrest Watch</h3>
                   <span>Small</span>
                   <p>A small river named Duden flows by their place and supplies it with the necessary regelialia.</p>
                 </div>
               </div>
             </td>
             <td valign="middle" width="20%" style="text-align:left; padding: 0 2.5em;">
               <span class="price" style="color: #000; font-size: 20px;">$120</span>
             </td>
           </tr>
           <tr style="border-bottom: 1px solid rgba(0,0,0,.05);">
             <td valign="middle" width="80%" style="text-align:left; padding: 0 2.5em;">
               <div class="product-entry">
                 <img src="images/prod-2.jpg" alt="" style="width: 100px; max-width: 600px; height: auto; margin-bottom: 20px; display: block;">
                 <div class="text">
                   <h3>Analog Wrest Watch</h3>
                   <span>Small</span>
                   <p>A small river named Duden flows by their place and supplies it with the necessary regelialia.</p>
                 </div>
               </div>
             </td>
             <td valign="middle" width="20%" style="text-align:left; padding: 0 2.5em;">
               <span class="price" style="color: #000; font-size: 20px;">$120</span>
             </td>
           </tr>
           <tr>
           <!-- 	<td valign="middle" style="text-align:left; padding: 1em 2.5em;">
               <p><a href="#" class="btn btn-primary">Continue to your order</a></p>
             </td> -->
           </tr>
         </table>
       </tr><!-- end tr -->
     <!-- 1 Column Text + Button : END -->
     </table>
     

   </div>
 </center>
</body>
</html>'; 
//$mail->Body="$htmlContent";
   $mail->Body=" 
   
   Hola, $middleName $lastName su pedido de: $name, con cantidad de: $cartCount 
   se encuentra con un estatus de: $estatus
   y con metodo de envio: $namee y su total a pagar es: $totalprecio  

   ";
   $mail->From="restaurantaut2021@gmail.com";
   $mail->FromName="PlanB";

   if($mail->send())
   {
       echo "Email Has Been Sent Your Email Address";
   }
   else
   {
       echo "Failed To Sent An Email To Your Email Address";
   }


}
//obtener menu estado alta
$app->get('/cus', function (Request $request, Response $response) {
 $estatus = "ALTA";
 $sql = "SELECT * FROM `menu` WHERE estatus = '1' ";

 try {
   $db = new BD();
   $conn = $db->coneccionBD();
   $stmt = $conn->query($sql);
   $customers = $stmt->fetchAll(PDO::FETCH_OBJ);
   $db = null;
  
   $response->getBody()->write(json_encode($customers));
   return $response
     ->withHeader('content-type', 'application/json')
     ->withStatus(200);
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
//obtener pedidos por tienda y sucursal
$app->get('/mostpedidos/{idtienda}/{idsuc}', function (Request $request, Response $response) {
 $estatus = "ALTA";
 $idtienda = $request->getAttribute('idtienda');
 $idsuc = $request->getAttribute('idsuc');
 //Checar el where de estatus repite varias veces
 //$sql = "SELECT * FROM `pedidoscomida` WHERE estatus = 'ALTA' ";
 $sql = "SELECT pc.idcli, pc.name, pc.estatus, pc.price, pc.cartCount,pc.fecha, pe.id,pe.firstName,pe.lastName,pe.middleName,pe.email, pe.address, pe.city,pe.place,pe.postalCode,pe.phone,pe.descr,pe.namee,pe.valuee,pe.cardNumber,pe.expiredMonth,pe.expiredYear, pe.descri, pe.nameee, me.id, me.image FROM `pedidoscomida` pc, `pedidos` pe, `menu` me
  WHERE  pc.idcli = pe.id AND pc.idpla = me.id AND (pc.estatus = 'ALTA' OR pc.estatus = 'Procesando' OR pc.estatus = 'En espera' OR pc.estatus = 'Reembolsado' OR pc.estatus = 'Pendiente') AND pc.idtienda = $idtienda AND pc.idsuc = $idsuc ";
 try {
   $db = new BD();
   $conn = $db->coneccionBD();
   $stmt = $conn->query($sql);
   $customers = $stmt->fetchAll(PDO::FETCH_OBJ);
   $db = null;
  
   $response->getBody()->write(json_encode($customers));
   return $response
     ->withHeader('content-type', 'application/json')
     ->withStatus(200);
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
//obtener peiddos completosd
$app->get('/mostpedidoscomple/{idtienda}/{idsuc}', function (Request $request, Response $response) {
 $estatus = "ALTA";
 $idtienda = $request->getAttribute('idtienda');
 $idsuc = $request->getAttribute('idsuc');
 //Checar el where de estatus repite varias veces
 //$sql = "SELECT * FROM `pedidoscomida` WHERE estatus = 'ALTA' ";
 $sql = "SELECT pc.idcli, pc.name, pc.estatus, pc.price, pc.cartCount, pc.idtienda,pc.idsuc, pe.id,pe.firstName,pe.lastName,pe.middleName,pe.email, pe.address, pe.city,pe.place,pe.postalCode,pe.phone,pe.descr,pe.namee,pe.valuee,pe.cardNumber,pe.expiredMonth,pe.expiredYear, me.id, me.image FROM `pedidoscomida` pc, `pedidos` pe, `menu` me
  WHERE  pc.idcli = pe.id AND pc.idpla = me.id AND (pc.estatus = 'Terminado') AND pc.idtienda = $idtienda AND pc.idsuc = $idsuc";
 try {
   $db = new BD();
   $conn = $db->coneccionBD();
   $stmt = $conn->query($sql);
   $customers = $stmt->fetchAll(PDO::FETCH_OBJ);
   $db = null;
  
   $response->getBody()->write(json_encode($customers));
   return $response
     ->withHeader('content-type', 'application/json')
     ->withStatus(200);
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
//obtener categorias

$app->get('/categoriasmenu/{idtienda}/{idsuc}', function (Request $request, Response $response) {
// $estatus = "ALTA";
 $idtienda = $request->getAttribute('idtienda');
 $idsuc = $request->getAttribute('idsuc');
 //Checar el where de estatus repite varias veces
 //$sql = "SELECT * FROM `pedidoscomida` WHERE estatus = 'ALTA' ";
 $sql = "SELECT
 categoriasmenu.id, 
 categoriasmenu.`name`, 
 categoriasmenu.description,
 categoriasmenu.estatus,
 categoriasmenu.idtienda,
 categoriasmenu.idsucursal
FROM
 categoriasmenu where estatus = '1' AND idtienda = $idtienda AND idsucursal = $idsuc";
 try {
   $db = new BD();
   $conn = $db->coneccionBD();
   $stmt = $conn->query($sql);
   $customers = $stmt->fetchAll(PDO::FETCH_OBJ);
   $db = null;
  
   $response->getBody()->write(json_encode($customers));
   return $response
     ->withHeader('content-type', 'application/json')
     ->withStatus(200);
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

$app->get('/categoriasmenusuctienda/{id}/{idsuc}', function (Request $request, Response $response) {
 // $estatus = "ALTA";
  $idtienda = $request->getAttribute('id');
  $idsuc = $request->getAttribute('idsuc');
  //Checar el where de estatus repite varias veces
  //$sql = "SELECT * FROM `pedidoscomida` WHERE estatus = 'ALTA' ";
  $sql = "SELECT
  categoriasmenu.id, 
  categoriasmenu.`name`, 
  categoriasmenu.description,
  categoriasmenu.estatus,
  categoriasmenu.idtienda,
  categoriasmenu.idsucursal
FROM
  categoriasmenu where estatus = '1' AND idtienda = $idtienda AND idsucursal = $idsuc";
  try {
    $db = new BD();
    $conn = $db->coneccionBD();
    $stmt = $conn->query($sql);
    $customers = $stmt->fetchAll(PDO::FETCH_OBJ);
    $db = null;
   
    $response->getBody()->write(json_encode($customers));
    return $response
      ->withHeader('content-type', 'application/json')
      ->withStatus(200);
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


//dar dealta y baja cateogrias menu
$app->get('/categoriasmenualtabaja/{idtienda}/{idsuc}', function (Request $request, Response $response) {
 // $estatus = "ALTA";
  $idtienda = $request->getAttribute('idtienda');
  $idsuc = $request->getAttribute('idsuc');
  //Checar el where de estatus repite varias veces
  //$sql = "SELECT * FROM `pedidoscomida` WHERE estatus = 'ALTA' ";
  $sql = "SELECT
  categoriasmenu.id, 
  categoriasmenu.`name`, 
  categoriasmenu.`description`,
  categoriasmenu.`estatus`,
  categoriasmenu.`idtienda`,
  categoriasmenu.`idsucursal`
FROM
  categoriasmenu WHERE idtienda = $idtienda AND idsucursal = $idsuc";
  try {
    $db = new BD();
    $conn = $db->coneccionBD();
    $stmt = $conn->query($sql);
    $customers = $stmt->fetchAll(PDO::FETCH_OBJ);
    $db = null;
   
    $response->getBody()->write(json_encode($customers));
    return $response
      ->withHeader('content-type', 'application/json')
      ->withStatus(200);
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
//pedidos comletos para ticket
$app->get('/mostpedidoscompletick/{idtienda}/{idsuc}/{id}', function (Request $request, Response $response) {
 $estatus = "ALTA";
 $idtienda = $request->getAttribute('idtienda');
 $idsuc = $request->getAttribute('idsuc');
 $id = $request->getAttribute('id');
 //Checar el where de estatus repite varias veces
 //$sql = "SELECT * FROM `pedidoscomida` WHERE estatus = 'ALTA' ";
 $sql = "SELECT pc.idcli, pc.name, pc.estatus, pc.price, pc.cartCount, pc.idtienda,pc.idsuc, pe.id,pe.firstName,pe.lastName,pe.middleName,pe.email, pe.address, pe.city,pe.place,pe.postalCode,pe.phone,pe.descr,pe.namee,pe.valuee,pe.cardNumber,pe.expiredMonth,pe.expiredYear, me.id, me.image FROM `pedidoscomida` pc, `pedidos` pe, `menu` me
 WHERE  pc.idcli = pe.id AND pc.idpla = me.id AND (pc.estatus = 'Terminado') AND pc.idtienda = $idtienda AND pc.idsuc = $idsuc AND pc.idcli = $id ";
 try {
   $db = new BD();
   $conn = $db->coneccionBD();
   $stmt = $conn->query($sql);
   $customers = $stmt->fetchAll(PDO::FETCH_OBJ);
   $db = null;
  
   $response->getBody()->write(json_encode($customers));
   return $response
     ->withHeader('content-type', 'application/json')
     ->withStatus(200);
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
//mostrar las tiendas y sucursales
$app->get('/mostrartiesuc', function (Request $request, Response $response) {
 $estatus = "ALTA";
 //Checar el where de estatus repite varias veces
 //$sql = "SELECT * FROM `pedidoscomida` WHERE estatus = 'ALTA' ";
 $sql = "SELECT * FROM `sucursales` WHERE Status = '1' ";
 try {
   $db = new BD();
   $conn = $db->coneccionBD();
   $stmt = $conn->query($sql);
   $customers = $stmt->fetchAll(PDO::FETCH_OBJ);
   $db = null;
  
   $response->getBody()->write(json_encode($customers));
   return $response
     ->withHeader('content-type', 'application/json')
     ->withStatus(200);
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
//obtener el menu por tienda y sucursal de ambos estatus
$app->get('/suc/{id}/{idsuc}', function (Request $request, Response $response, $args) {
 $estatus = "ALTA";
 $id = $args['id'];
 $idusc =  $args['idsuc'];
 $sql = "SELECT * FROM `menu` WHERE  `idtienda`=$id AND `idsuc`=$idusc ";

 try {
   $db = new BD();
   $conn = $db->coneccionBD();
   $stmt = $conn->query($sql);
   $customers = $stmt->fetchAll(PDO::FETCH_OBJ);
   $db = null;
  
   $response->getBody()->write(json_encode($customers));
   return $response
     ->withHeader('content-type', 'application/json')
     ->withStatus(200);
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
//obrtener menu por tienda y sucursal en estatus de activo
$app->get('/succ/{id}/{idsuc}', function (Request $request, Response $response, $args) {
 $estatus = "ALTA";
 $id = $args['id'];
 $idusc =  $args['idsuc'];
 
 $sql = "SELECT * FROM `menu` WHERE  `idtienda`=$id AND `idsuc`=$idusc AND estatus = '1'  ";

 try {
   $db = new BD();
   $conn = $db->coneccionBD();
   $stmt = $conn->query($sql);
   $customers = $stmt->fetchAll(PDO::FETCH_OBJ);
   $db = null;
  
   $response->getBody()->write(json_encode($customers));
   return $response
     ->withHeader('content-type', 'application/json')
     ->withStatus(200);
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
//obtener datts de platos para editar
$app->get('/cuse/{id}', function (Request $request, Response $response, $args) {
 //Show book identified by $id
 $id = $args['id'];
 $sql = " SELECT `id`, `idtienda`, `idsuc`, `name`, `description`, `price`, `image`, `discount`, `ratingsCount`, `ratingsValue`, `availibilityCount`, `cartCount`, `weight`, `ingrediente1`, `peso1`, `weight1`, `ingrediente2`, `peso2`, `weight2`, `ingrediente3`, `peso3`, `weight3`, `ingrediente4`, `peso4`, `weight4`, `ingrediente5`, `peso5`, `weight5`, `ingrediente6`, `peso6`, `weight6`, `ingrediente7`, `peso7`, `weight7`, `ingrediente8`, `peso8`, `weight8`, `ingrediente9`, `peso9`, `weight9`, `ingrediente10`, `peso10`, `weight10`, `ingrediente11`, `peso11`, `weight11`, `ingrediente12`, `peso12`, `weight12`, `isVegetarian`, `categoryId`, `estatus` FROM `menu`
  WHERE `id`=$id";

 try {
   $db = new BD();
   $conn = $db->coneccionBD();
   $stmt = $conn->query($sql);
   $customers = $stmt->fetchAll(PDO::FETCH_OBJ);
   $db = null;
  
   $response->getBody()->write(json_encode($customers));
   return $response
     ->withHeader('content-type', 'application/json')
     ->withStatus(200);
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
//agregar platillos
$app->POST('/addplatillos/{idtienda}/{idsuc}', function (Request $request, Response $response, array $args) {
 $data = $request->getParsedBody();
 //print_r($data);
 //echo 'nombre' . $data['name'];
 //$json = file_get_contents('php://input');
 //$data = json_decode($json);
 $id = ($data['id']);
 /* echo $idtienda ;
 echo $idsuc ;  */
 $idtienda = $request->getAttribute('idtienda');
 $idsuc = $request->getAttribute('idsuc');

 /* $idtienda = $argss['idtienda'];
 $idsuc =  $argss['idsuc']; */

 $name = ($data['name']);
 $description = ($data['description']);
 $price = ($data['price']);
 $image = ($data['image']);
 //$image = "ss";
 $discount = '0';
 $ratingsCount ='0';
 $ratingsValue = '0';
 $availibilityCount = ($data['availibilityCount']);
 $cartCount = '1';
 $weight = ($data['weight']);
 $isVegetarian = '0';
 $categoryId = ($data['categoryId']);

 $ingrediente1 = ($data['ingrediente1']);
 $peso1 = ($data['peso1']);//unidad de medidad
 $weight1 = ($data['weight1']);

 $ingrediente2 = ($data['ingrediente2']);
 $peso2 = ($data['peso2']);//unidad de medidad
 $weight2 = ($data['weight2']);

 $ingrediente3 = ($data['ingrediente3']);
 $peso3 = ($data['peso3']);//unidad de medidad
 $weight3 = ($data['weight3']);

 $ingrediente4 = ($data['ingrediente4']);
 $peso4 = ($data['peso4']);//unidad de medidad
 $weight4 = ($data['weight4']);

 $ingrediente5 = ($data['ingrediente5']);
 $peso5 = ($data['peso5']);//unidad de medidad
 $weight5 = ($data['weight5']);

 $ingrediente6 = isset(($data['ingrediente6']));
 $peso6 = isset(($data['peso6']));//unidad de medidad
 $weight6 = isset(($data['weight6']));

 $ingrediente7 = isset(($data['ingrediente7']));
 $peso7 = isset(($data['peso7']));//unidad de medidad
 $weight7 = isset(($data['weight7']));

 $ingrediente8 = isset(($data['ingrediente8']));
 $peso8 = isset(($data['peso8']));//unidad de medidad
 $weight8 = isset(($data['weight8']));

 $ingrediente9 = isset(($data['ingrediente9']));
 $peso9 = isset(($data['peso9']));//unidad de medidad
 $weight9 = isset(($data['weight9']));

 $ingrediente10 = isset(($data['ingrediente10']));
 $peso10 = isset(($data['peso10']));//unidad de medidad
 $weight10 = isset(($data['weight10']));

 $ingrediente11 = isset(($data['ingrediente11']));
 $peso11 = isset(($data['peso11']));//unidad de medidad
 $weight11 = isset(($data['weight11']));

 $ingrediente12 = isset(($data['ingrediente12']));
 $peso12 = isset(($data['peso12']));//unidad de medidad
 $weight12 = isset(($data['weight12']));

 

 $estatus = "1";
 

 $sql = "INSERT INTO menu (id,idtienda,idsuc,name, description, price, image,discount,ratingsCount,ratingsValue,availibilityCount,cartCount,weight,ingrediente1,peso1,weight1,ingrediente2,peso2,weight2,ingrediente3,peso3,weight3,ingrediente4,peso4,weight4,ingrediente5,peso5,weight5,ingrediente6,peso6,weight6,ingrediente7,peso7,weight7,ingrediente8,peso8,weight8,ingrediente9,peso9,weight9,ingrediente10,peso10,weight10,ingrediente11,peso11,weight11,ingrediente12,peso12,weight12,isVegetarian, categoryId, estatus) 
 VALUES (:id, :idtienda, :idsuc, :name, :description, :price, :image,:discount,:ratingsCount,:ratingsValue,:availibilityCount,:cartCount,:weight,:ingrediente1,:peso1,:weight1,:ingrediente2,:peso2,:weight2,:ingrediente3,:peso3,:weight3,:ingrediente4,:peso4,:weight4,:ingrediente5,:peso5,:weight5,:ingrediente6,:peso6,:weight6,:ingrediente7,:peso7,:weight7,:ingrediente8,:peso8,:weight8,:ingrediente9,:peso9,:weight9,:ingrediente10,:peso10,:weight10,:ingrediente11,:peso11,:weight11,:ingrediente12,:peso12,:weight12,:isVegetarian, :categoryId, :estatus)";

 try {
   $db = new BD();
   $conn = $db->coneccionBD();
  
   $stmt = $conn->prepare($sql);
   $stmt->bindParam(':id', $id);

   $stmt->bindParam(':idtienda', $idtienda);
   $stmt->bindParam(':idsuc', $idsuc);

   $stmt->bindParam(':name', $name);
   $stmt->bindParam(':description', $description);
   $stmt->bindParam(':price', $price);
   $stmt->bindParam(':image', $image);
   $stmt->bindParam(':discount', $discount);
   $stmt->bindParam(':ratingsCount', $ratingsCount);
   $stmt->bindParam(':ratingsValue', $ratingsValue);
   $stmt->bindParam(':availibilityCount', $availibilityCount);
   $stmt->bindParam(':cartCount', $cartCount);
   $stmt->bindParam(':weight', $weight);

   $stmt->bindParam(':ingrediente1', $ingrediente1);
   $stmt->bindParam(':peso1', $peso1);
   $stmt->bindParam(':weight1', $weight1);

   $stmt->bindParam(':ingrediente2', $ingrediente2);
   $stmt->bindParam(':peso2', $peso2);
   $stmt->bindParam(':weight2', $weight2);

   $stmt->bindParam(':ingrediente3', $ingrediente3);
   $stmt->bindParam(':peso3', $peso3);
   $stmt->bindParam(':weight3', $weight3);

   $stmt->bindParam(':ingrediente4', $ingrediente4);
   $stmt->bindParam(':peso4', $peso4);
   $stmt->bindParam(':weight4', $weight4);

   $stmt->bindParam(':ingrediente5', $ingrediente5);
   $stmt->bindParam(':peso5', $peso5);
   $stmt->bindParam(':weight5', $weight5);

   $stmt->bindParam(':ingrediente6', $ingrediente6);
   $stmt->bindParam(':peso6', $peso6);
   $stmt->bindParam(':weight6', $weight6);
   
   $stmt->bindParam(':ingrediente7', $ingrediente7);
   $stmt->bindParam(':peso7', $peso7);
   $stmt->bindParam(':weight7', $weight7);
   
   $stmt->bindParam(':ingrediente8', $ingrediente8);
   $stmt->bindParam(':peso8', $peso8);
   $stmt->bindParam(':weight8', $weight8);

   $stmt->bindParam(':ingrediente9', $ingrediente9);
   $stmt->bindParam(':peso9', $peso9);
   $stmt->bindParam(':weight9', $weight9);

   $stmt->bindParam(':ingrediente10', $ingrediente10);
   $stmt->bindParam(':peso10', $peso10);
   $stmt->bindParam(':weight10', $weight10);

   $stmt->bindParam(':ingrediente11', $ingrediente11);
   $stmt->bindParam(':peso11', $peso11);
   $stmt->bindParam(':weight11', $weight11);

   $stmt->bindParam(':ingrediente12', $ingrediente12);
   $stmt->bindParam(':peso12', $peso12);
   $stmt->bindParam(':weight12', $weight12);

   $stmt->bindParam(':isVegetarian', $isVegetarian);
   $stmt->bindParam(':categoryId', $categoryId);
   $stmt->bindParam(':estatus', $estatus);

   $result = $stmt->execute();

   $db = null;
   $response->getBody()->write(json_encode($result));
   return $response
     ->withHeader('content-type', 'application/json')
     ->withStatus(200);
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

$app->post('/agg', function (Request $request, Response $response) {

 // Get POST data
 $post = (array)$request->getParsedBody();

 $row = [
     'name' => $post['name'],
     'description' => $post['description'],
     'price' => $post['price']
 ];

 $sql = "INSERT INTO menu SET name=:name, description=:description, price=:price;";

 /** @var PDO $pdo */
 $pdo = $this->get(PDO::class);
 $success = $pdo->prepare($sql)->execute($row);

 return $response->withJson(['success' => $success]);
});
//modificar datos de menu por platillos
$app->put('/mod/{idtienda}/{idsuc}/{id}',function (Request $request, Response $response, array $args) {
$id = $request->getAttribute('id');

$idtienda = $request->getAttribute('idtienda');
$idsuc = $request->getAttribute('idsuc');

$data = $request->getParsedBody();
$name = $data["name"];
$description = $data["description"];
$categoryId = $data["categoryId"];
$price = $data["price"];
$image = $data["image"];
$availibilityCount = $data["availibilityCount"];
$weight = $data["weight"];

$sql = "UPDATE menu SET
        name = :name,
        description = :description,
        categoryId = :categoryId,
        price = :price,
        image = :image,
        availibilityCount = :availibilityCount,
        weight = :weight
        
WHERE idtienda = $idtienda AND idsuc = $idsuc AND id = $id ";

try {
 $db = new BD();
 $conn = $db->coneccionBD();

$stmt = $conn->prepare($sql);
$stmt->bindParam(':name', $name);
$stmt->bindParam(':description', $description);
$stmt->bindParam(':categoryId', $categoryId);
$stmt->bindParam(':price', $price);
$stmt->bindParam(':image', $image);
$stmt->bindParam(':availibilityCount', $availibilityCount);
$stmt->bindParam(':weight', $weight);

$result = $stmt->execute();

$db = null;
echo "Update successful! ";
$response->getBody()->write(json_encode($result));
return $response
  ->withHeader('content-type', 'application/json')
  ->withStatus(200);
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
//modificar pedidio
$app->put('/modpedido',function (Request $request, Response $response, array $args) {
 
 //$json = file_get_contents('php://input');
 //$data = json_decode($json);
 $data = $request->getParsedBody();
 //$deliveryAddress = ($data['deliveryAddress']);
 //$idcli = $request->getAttribute('idcli');
 print_r($data);
 $idcli =($data['idcli']);
 $idpla =($data['id']);
 print_r($idcli);
 print_r($idpla);
 $dat = ($data['estatus']);
 $estatus = ($dat['name']);
 print_r($estatus);
 //echo $id;
 //$estatus = $data["estatus"];
 
 $sql = "UPDATE pedidoscomida SET
          estatus = :estatus where `idcli` = $idcli AND `idpla` = $idpla";
 
 try {
   $db = new BD();
   $conn = $db->coneccionBD();
 
  $stmt = $conn->prepare($sql);
  $stmt->bindParam(':estatus', $estatus);

 
  $result = $stmt->execute();
 
  $db = null;
  echo "Update successful! ";
  $response->getBody()->write(json_encode($result));
  return $response
    ->withHeader('content-type', 'application/json')
    ->withStatus(200);
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
//alta baja menu
$app->put('/baja/{idtienda}/{idsuc}/{id}',function (Request $request, Response $response, array $args) {
 $id = $request->getAttribute('id');
 $idtienda = $request->getAttribute('idtienda');
$idsuc = $request->getAttribute('idsuc');

$data = $request->getParsedBody();
 $estatus = $data['estatus'];
 
 $sql = "UPDATE menu SET
          
          estatus = :estatus
          
 WHERE idtienda = $idtienda AND idsuc = $idsuc AND id = $id ";
 
 try {
   $db = new BD();
   $conn = $db->coneccionBD();
 
  $stmt = $conn->prepare($sql);
  
  $stmt->bindParam(':estatus', $estatus);
 
  $result = $stmt->execute();
 
  $db = null;
  echo "Update successful! ";
  $response->getBody()->write(json_encode($result));
  return $response
    ->withHeader('content-type', 'application/json')
    ->withStatus(200);
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
//baja alta categoria menu
 $app->put('/bajacatmenu/{id}/{idtienda}/{idsuc}',function (Request $request, Response $response, array $args) {
   $id = $request->getAttribute('id');
   
   $idtienda = $request->getAttribute('idtienda');
 //$idsuc = $request->getAttribute('idsuc');
 
 $data = $request->getParsedBody();
 print_r($data);
   $estatus = $data['estatus'];
   
   $sql = "UPDATE categoriasmenu SET
            
            estatus = :estatus
            
   WHERE id = $id AND idtienda = $idtienda ";
   
   try {
     $db = new BD();
     $conn = $db->coneccionBD();
   
    $stmt = $conn->prepare($sql);
    
    $stmt->bindParam(':estatus', $estatus);
   
    $result = $stmt->execute();
   
    $db = null;
    echo "Update successful! ";
    $response->getBody()->write(json_encode($result));
    return $response
      ->withHeader('content-type', 'application/json')
      ->withStatus(200);
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
//add pedidos dtos de cliente
$app->POST('/addpedidos', function (Request $request, Response $response, array $args) {
 $data = $request->getParsedBody();
// print_r($data);
 //echo 'deliveryAddress' . $data['deliveryAddress'];
 //$json = file_get_contents('php://input');
 //$data = json_decode($json);
 $deliveryAddress = ($data['deliveryAddress']);
 $deliveryMethod = ($data['deliveryMethod']['method']);
 $paymentMethod = ($data['paymentMethod']);
 $paymentMethods = ($data['paymentMethods']['method']);
 print_r($paymentMethod);
 $id = 0;
 //Direccion
 $address =($deliveryAddress['address']);
 $city = ($deliveryAddress['city']);
 $email = ($deliveryAddress['email']);
 $firstName = ($deliveryAddress['firstName']);
 $lastName = ($deliveryAddress['lastName']);
 $middleName =($deliveryAddress['middleName']);
 $phone = ($deliveryAddress['phone']);
 $place = ($deliveryAddress['place']);
 $postalCode = ($deliveryAddress['postalCode']);
 
 $descr = ($deliveryMethod['desc']);
 $namee = ($deliveryMethod['name']);
 $valuee = ($deliveryMethod['value']);
 
 /* $cardHolderName = ($paymentMethod['cardHolderName']);
 $cardNumber = ($paymentMethod['cardNumber']);
 $cvv = ($paymentMethod['cvv']);
 $expiredMonth = ($paymentMethod['expiredMonth']);
 $expiredYear = ($paymentMethod['expiredYear']);
*/
 $method = ($paymentMethods['method']);

 $descri = ($paymentMethods['desc']);
 $nameee = ($paymentMethods['name']);
 $valueee = ($paymentMethods['value']);
 //$deliveryMethod = ($data['deliveryMethod']);
 //$paymentMethod = 'paymentMethod';
 //$categoryId = ($data['categoryId']);

 $sql = "INSERT INTO pedidos (id,address, city, email, firstName, lastName, middleName, phone, place, postalCode, descr, namee, valuee,descri,nameee,valueee ) 
 VALUES (:id, :address, :city, :email, :firstName,:lastName,:middleName,:phone,:place,:postalCode,:descr,:namee,:valuee,:descri,:nameee,:valueee)";

 try {
   $db = new BD();
   $conn = $db->coneccionBD();
  
   $stmt = $conn->prepare($sql);
   $stmt->bindParam(':id', $id);
   //direccion
   $stmt->bindParam(':address', $address);
   $stmt->bindParam(':city', $city);
   $stmt->bindParam(':email', $email);
   $stmt->bindParam(':firstName', $firstName);
   $stmt->bindParam(':lastName', $lastName);
   $stmt->bindParam(':middleName', $middleName);
   $stmt->bindParam(':phone', $phone);
   $stmt->bindParam(':place', $place);
   $stmt->bindParam(':postalCode', $postalCode);
//envio
   $stmt->bindParam(':descr', $descr);
   $stmt->bindParam(':namee', $namee);
   $stmt->bindParam(':valuee', $valuee);
   //pago
   $stmt->bindParam(':descri', $descri);
   $stmt->bindParam(':nameee', $nameee);
   $stmt->bindParam(':valueee', $valueee);
   /* $stmt->bindParam(':cardHolderName', $cardHolderName);
   $stmt->bindParam(':cardNumber', $cardNumber);
   $stmt->bindParam(':cvv', $cvv);
   $stmt->bindParam(':expiredMonth', $expiredMonth);
   $stmt->bindParam(':expiredYear', $expiredYear); */
   //$stmt->bindParam(':deliveryMethod', $deliveryMethod);
   //$stmt->bindParam(':paymentMethod', $paymentMethod);
   //$stmt->bindParam(':categoryId', $categoryId);

   $result = $stmt->execute();

   $db = null;
   $response->getBody()->write(json_encode($result));
   return $response
     ->withHeader('content-type', 'application/json')
     ->withStatus(200);
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
//add pedidos de comida por cliente
$app->POST('/addpedidoscomi', function (Request $request, Response $response, array $args) {
 //$dataa = $request->getParsedBody();
//print_r($dataa);
 //echo 'deliveryAddress' . $data['deliveryAddress'];
 $json = file_get_contents('php://input');
 $data = json_decode($json, true);
 /* $deliveryAddress = ($data['deliveryAddress']);
 $deliveryMethod = ($data['deliveryMethod']['method']);
 $paymentMethod = ($data['paymentMethod']); */
 

 

 //$valor =  sizeof($data);
 //echo sizeof($data);
 //print_r($data);
 //$conta = 0;

 //$i=0;
 $comida = ($data);
 //print_r($comida);

 $dbs = new BD();
   $conne = $dbs->coneccionBD();

/* $sqlll = "SELECT MAX(id) AS id FROM pedidos";
$resulta = $conne->query($sqlll); */
/* $query = "SELECT `*` FROM pedidos";
$results = mysqli_query($conne, $query);
print_r($results); */
 
 $id = 0;
 $idtienda = ($comida['idtienda']);
 $idsuc = ($comida['idsuc']);
 $idpla = ($comida['id']);
 $name = ($comida['name']);
 $description = ($comida['description']);
 $price = ($comida['price']);
 $cartCount = ($comida['cartCount']);
 $categoryId = ($comida['categoryId']);
 $estatus = "ALTA";
 $Object = new DateTime();  
$Object->setTimezone(new DateTimeZone('Mexico/BajaSur'));
$DateAndTime = $Object->format("d-m-Y h:i:sa");  
print_r($DateAndTime);
 /* $sql = "INSERT INTO pedidoscomida (id,idpla, name, description, price, cartCount, categoryId) 
 VALUES (:id, :idpla, :name, :description, :price,:cartCount,:categoryId)"; */
///VER COMO INSERTAR EL PLATILLOY PLATILLOS
$sql = "INSERT INTO pedidoscomida (idcli, idtienda,idsuc,idpla, name, description, price, cartCount, categoryId,estatus,fecha)
SELECT MAX(id), :idtienda, :idsuc, :idpla, :name, :description, :price, :cartCount, :categoryId, :estatus, NOW()
FROM pedidos " ;        

/*  $sql = "INSERT INTO pedidoscomida (id, idpla, name,description,price,cartCount,categoryId)
 SELECT id, idpla ,name,description,price,cartCount,categoryId FROM pedidos WHERE id=1"; */

 try {
   $db = new BD();
   $conn = $db->coneccionBD();
  
   $stmt = $conn->prepare($sql);
  // $stmt->bindParam(':id', $id);
   //direccion
   $stmt->bindParam(':idtienda', $idtienda);
   $stmt->bindParam(':idsuc', $idsuc);
   $stmt->bindParam(':idpla', $idpla);
   $stmt->bindParam(':name', $name);
   $stmt->bindParam(':description', $description);
   $stmt->bindParam(':price', $price);
   $stmt->bindParam(':cartCount', $cartCount);
   $stmt->bindParam(':categoryId', $categoryId);
   $stmt->bindParam(':estatus', $estatus);
   //$stmt->bindParam('fecha', $$DateAndTime);
   //$stmt->bindParam(':deliveryMethod', $deliveryMethod);
   //$stmt->bindParam(':paymentMethod', $paymentMethod);
   //$stmt->bindParam(':categoryId', $categoryId);
   
   $result = $stmt->execute();
   //$i=$i+1;
   $db = null;
   $response->getBody()->write(json_encode($result));
   return $response
     ->withHeader('content-type', 'application/json')
     ->withStatus(200);
 } catch (PDOException $e) {
   $error = array(
     "message" => $e->getMessage()
   );

   $response->getBody()->write(json_encode($error));
   return $response
     ->withHeader('content-type', 'application/json')
     ->withStatus(500);
 }
 
//}
});
//tiendas-------------------------------------------------------------------------------
$app->POST('/addtienda', function (Request $request, Response $response, array $args) {
 //$dataa = $request->getParsedBody();

 $json = file_get_contents('php://input');
 $data = json_decode($json, true);
 $tiendas = ($data);

   /* $idtienda = $request->getAttribute('idtienda');
   $idsuc = $request->getAttribute('idsuc'); */
 $id = 0;
 $Nombre = ($tiendas['Nombre']);
 $Telefono = ($tiendas['Telefono']);
 $Correo = ($tiendas['Correo']);
 $Fecha = ($tiendas['Fecha']);


 $estatus = "1";

$sql = "INSERT INTO `tiendas`(ID_tienda, Nombre, Telefono,Correo,Fecha) VALUES (:ID_tienda, :Nombre, :Telefono,:Correo,NOW())" ;        

 try {
   $db = new BD();
   $conn = $db->coneccionBD();
  
   $stmt = $conn->prepare($sql);

   $stmt->bindParam(':ID_tienda', $id);
   $stmt->bindParam(':Nombre', $Nombre);
   $stmt->bindParam(':Telefono', $Telefono);
   $stmt->bindParam(':Correo', $Correo);
   //$stmt->bindParam(':Fecha', $Fecha);
   $result = $stmt->execute();

   $db = null;
   $response->getBody()->write(json_encode($result));
   return $response
     ->withHeader('content-type', 'application/json')
     ->withStatus(200);
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

$app->put('/edittiendas/{id}',function (Request $request, Response $response, array $args) {
 
 //$json = file_get_contents('php://input');
 //$data = json_decode($json);
 $data = $request->getParsedBody();
 //$deliveryAddress = ($data['deliveryAddress']);
 $id = $request->getAttribute('id');
 $Nombre = ($data['Nombre']);
 $Telefono = ($data['Telefono']);
 $Correo = ($data['Correo']);
 //$Fecha = ($data['Fecha']);
 print_r($data);
 /* print_r($description);
 print_r($estatus); */
 //print_r($estatus);
 //echo $id;
 //$estatus = $data["estatus"];
 
 $sql = "UPDATE tiendas SET Nombre = :Nombre, Telefono = :Telefono, Correo = :Correo where `ID_tienda` = $id ";
 
 try {
   $db = new BD();
   $conn = $db->coneccionBD();
 
  $stmt = $conn->prepare($sql);
  $stmt->bindParam(':Nombre', $Nombre);
   $stmt->bindParam(':Telefono', $Telefono);
   $stmt->bindParam(':Correo', $Correo);
   
 
  $result = $stmt->execute();
 
  $db = null;
  echo "Update successful! ";
  $response->getBody()->write(json_encode($result));
  return $response
    ->withHeader('content-type', 'application/json')
    ->withStatus(200);
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


 $app->get('/tiendasaltabaja', function (Request $request, Response $response) {
   // $estatus = "ALTA";
    /* $idtienda = $request->getAttribute('idtienda');
    $idsuc = $request->getAttribute('idsuc' );*/
    //Checar el where de estatus repite varias veces
    //$sql = "SELECT * FROM `pedidoscomida` WHERE estatus = 'ALTA' ";
    $sql = "SELECT
    tiendas.ID_tienda, 
    tiendas.`Nombre`, 
    tiendas.`Telefono`,
    tiendas.`Correo`,
    tiendas.`Fecha`
    
  FROM
  tiendas ";
    try {
      $db = new BD();
      $conn = $db->coneccionBD();
      $stmt = $conn->query($sql);
      $customers = $stmt->fetchAll(PDO::FETCH_OBJ);
      $db = null;
     
      $response->getBody()->write(json_encode($customers));
      return $response
        ->withHeader('content-type', 'application/json')
        ->withStatus(200);
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


//tiendas-------------------------------------------------------------------------------

//sucursales-------------------------------------------------------------------------------

$app->put('/bajasucursal/{id}',function (Request $request, Response $response, array $args) {
 
 //$idtienda = $request->getAttribute('idtienda');
// $idsucursal = $request->getAttribute('idsuc');
 $id = $request->getAttribute('id');

$data = $request->getParsedBody();
print_r($data);
 $estatus = $data['estatus'];
 
 $sql = "UPDATE sucursales SET
          
          `Status` = :Status
          
 WHERE ID_sucursal = $id ";
 
 try {
   $db = new BD();
   $conn = $db->coneccionBD();
 
  $stmt = $conn->prepare($sql);
  
  $stmt->bindParam(':Status', $estatus);
 
  $result = $stmt->execute();
 
  $db = null;
  echo "Update successful! ";
  $response->getBody()->write(json_encode($result));
  return $response
    ->withHeader('content-type', 'application/json')
    ->withStatus(200);
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


$app->POST('/addsucursal', function (Request $request, Response $response, array $args) {
 //$dataa = $request->getParsedBody();

 $json = file_get_contents('php://input');
 $data = json_decode($json, true);
 $sucursales = ($data);
print_r($sucursales);
   /* $idtienda = $request->getAttribute('idtienda');
   $idsuc = $request->getAttribute('idsuc'); */
 $id = 0;
 $Pseudonimo = ($sucursales['Pseudonimo']);
 $Ubicacion = ($sucursales['Ubicacion']);
 $Fechaalta = ($sucursales['Fechaalta']);
 $idzonsucursal = ($sucursales['ID_zonasucursal']);
 $idempleado = ($sucursales['ID_empleado']);
 $status = '1';
 $idtienda = ($sucursales['ID_tienda']);
 //$ID_sucursal = ($sucursales['ID_sucursal']);
 $idhorario = ($sucursales['ID_horario']);


 $estatus = "1";
//fallaba porque se les ocurrio poner el nombre dividirlo con - un ejemplo era ID_zona-sucursal asi no se reconoce
$sql = "INSERT INTO `sucursales`(ID_sucursal, Pseudonimo, Ubicacion,Fechaalta,Status,ID_zonasucursal,ID_empleado,ID_tienda,ID_horario) VALUES (:ID_sucursal, :Pseudonimo, :Ubicacion,NOW(),:Status,:ID_zonasucursal,:ID_empleado,:ID_tienda,:ID_horario)" ;        

 try {
   $db = new BD();
   $conn = $db->coneccionBD();
  
   $stmt = $conn->prepare($sql);

   $stmt->bindParam(':ID_sucursal', $id);
   $stmt->bindParam(':Pseudonimo', $Pseudonimo);
   $stmt->bindParam(':Ubicacion', $Ubicacion);
   //$stmt->bindParam(':Fechaalta', $Fechaalta);
   $stmt->bindParam(':Status', $status);
   $stmt->bindParam(':ID_zonasucursal', $idzonsucursal);
   $stmt->bindParam(':ID_empleado', $idempleado);
   $stmt->bindParam(':ID_tienda', $idtienda);
   $stmt->bindParam(':ID_horario', $idhorario);
   $result = $stmt->execute();

   $db = null;
   $response->getBody()->write(json_encode($result));
   return $response
     ->withHeader('content-type', 'application/json')
     ->withStatus(200);
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

$app->put('/editsucursal/{idtienda}/{idsucursal}',function (Request $request, Response $response, array $args) {
 
 //$json = file_get_contents('php://input');
 //$data = json_decode($json);
 $data = $request->getParsedBody();
 //$deliveryAddress = ($data['deliveryAddress']);
 $idtienda = $request->getAttribute('idtienda');
 $idsucursal = $request->getAttribute('idsucursal');

 $Pseudonimo = ($data['Pseudonimo']);
 $Ubicacion = ($data['Ubicacion']);
// $Fechaalta = ($data['Fechaalta']);
 //$idzonsucursal = ($data['idzonsucursal']);
 $idempleado = ($data['idempleado']);
 //$status = ($data['status']);
// $idtienda = ($data['idtienda']);
 //$idhorario = ($data['idhorario']);
 print_r($data);
 /* print_r($description);
 print_r($estatus); */
 //print_r($estatus);
 //echo $id;
 //$estatus = $data["estatus"];
 
 $sql = "UPDATE sucursales SET Pseudonimo = :Pseudonimo, Ubicacion = :Ubicacion where `ID_tienda` = $idtienda AND `ID_sucursal` = $idsucursal ";
 
 try {
   $db = new BD();
   $conn = $db->coneccionBD();
 
  $stmt = $conn->prepare($sql);
  $stmt->bindParam(':Pseudonimo', $Pseudonimo);
   $stmt->bindParam(':Ubicacion', $Ubicacion);
  /*  $stmt->bindParam(':Correo', $Correo); */
   
 
  $result = $stmt->execute();
 
  $db = null;
  echo "Update successful! ";
  $response->getBody()->write(json_encode($result));
  return $response
    ->withHeader('content-type', 'application/json')
    ->withStatus(200);
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


 $app->get('/sucursalaltabaja', function (Request $request, Response $response) {
   // $estatus = "ALTA";
    /* $idtienda = $request->getAttribute('idtienda');
    $idsuc = $request->getAttribute('idsuc' );*/
    //Checar el where de estatus repite varias veces
    //$sql = "SELECT * FROM `pedidoscomida` WHERE estatus = 'ALTA' ";
    $sql = "SELECT
    sucursales.ID_sucursal, 
    sucursales.`Pseudonimo`, 
    sucursales.`Ubicacion`,
    sucursales.`Fechaalta`,
    sucursales.`Status`,
    sucursales.`ID_zonasucursal`,
    sucursales.`ID_empleado`,
    sucursales.`ID_tienda`,
    sucursales.`ID_horario`
  FROM
  sucursales ";
    try {
      $db = new BD();
      $conn = $db->coneccionBD();
      $stmt = $conn->query($sql);
      $customers = $stmt->fetchAll(PDO::FETCH_OBJ);
      $db = null;
     
      $response->getBody()->write(json_encode($customers));
      return $response
        ->withHeader('content-type', 'application/json')
        ->withStatus(200);
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


   $app->get('/horariossuc', function (Request $request, Response $response) {
     // $estatus = "ALTA";
      /* $idtienda = $request->getAttribute('idtienda');
      $idsuc = $request->getAttribute('idsuc' );*/
      //Checar el where de estatus repite varias veces
      //$sql = "SELECT * FROM `pedidoscomida` WHERE estatus = 'ALTA' ";
      $sql = "SELECT
      horario.ID_horario, 
      horario.`InicioLunes`, 
      horario.`FinLunes`, 
      horario.`InicioMartes`, 
      horario.`FinMartes`, 
      horario.`InicioMiercoles`, 
      horario.`FinMiercoles`, 
      horario.`InicioJueves`, 
      horario.`FinJueves`, 
      horario.`InicioViernes`, 
      horario.`FinViernes`, 
      horario.`InicioSabado`, 
      horario.`FinSabado`, 
      horario.`InicioDomingo`, 
      horario.`FinDomingo`
    FROM
      horario ";
      try {
        $db = new BD();
        $conn = $db->coneccionBD();
        $stmt = $conn->query($sql);
        $customers = $stmt->fetchAll(PDO::FETCH_OBJ);
        $db = null;
       
        $response->getBody()->write(json_encode($customers));
        return $response
          ->withHeader('content-type', 'application/json')
          ->withStatus(200);
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
 

//sucursales-------------------------------------------------------------------------------
//agregar categoriar por suc y tienda
$app->POST('/addcategoriasmenu/{idtienda}/{idsuc}', function (Request $request, Response $response, array $args) {
 //$dataa = $request->getParsedBody();

 $json = file_get_contents('php://input');
 $data = json_decode($json, true);
 $catmenu = ($data);
 $dbs = new BD();
   $conne = $dbs->coneccionBD();
   $idtienda = $request->getAttribute('idtienda');
   $idsuc = $request->getAttribute('idsuc');
 $id = 0;
 $description = ($catmenu['description']);
 $name = ($catmenu['name']);

 $estatus = "1";

$sql = "INSERT INTO `categoriasmenu`(id, name, description,estatus,idtienda,idsucursal) VALUES (:id,:name,:description,:estatus,:idtienda,:idsucursal)" ;        


 try {
   $db = new BD();
   $conn = $db->coneccionBD();
  
   $stmt = $conn->prepare($sql);

   $stmt->bindParam(':id', $id);
   $stmt->bindParam(':name', $name);
   $stmt->bindParam(':description', $description);
   $stmt->bindParam(':estatus', $estatus);
   $stmt->bindParam(':idtienda', $idtienda);
   $stmt->bindParam(':idsucursal', $idsuc);

   $result = $stmt->execute();

   $db = null;
   $response->getBody()->write(json_encode($result));
   return $response
     ->withHeader('content-type', 'application/json')
     ->withStatus(200);
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
//editar categorias 
$app->put('/editcategoriasmenu/{id}',function (Request $request, Response $response, array $args) {
 
 //$json = file_get_contents('php://input');
 //$data = json_decode($json);
 $data = $request->getParsedBody();
 //$deliveryAddress = ($data['deliveryAddress']);
 $id = $request->getAttribute('id');
 $description = ($data['description']);
 $name = ($data['name']);
 $estatus = ($data['estatus']);
 $idtienda = ($data['idtienda']);
 $idsucursal = ($data['idsucursal']);
 print_r($data);
 print_r($description);
 print_r($estatus);
 //print_r($estatus);
 //echo $id;
 //$estatus = $data["estatus"];
 
 $sql = "UPDATE categoriasmenu SET description = :description, name = :name where `id` = $id AND idtienda = $idtienda AND idsucursal = $idsucursal ";
 
 try {
   $db = new BD();
   $conn = $db->coneccionBD();
 
  $stmt = $conn->prepare($sql);
  $stmt->bindParam(':name', $name);
  $stmt->bindParam(':description', $description);

 
  $result = $stmt->execute();
 
  $db = null;
  echo "Update successful! ";
  $response->getBody()->write(json_encode($result));
  return $response
    ->withHeader('content-type', 'application/json')
    ->withStatus(200);
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

//subir imagen 
$app->POST('/subirimg', function (Request $request, Response $response, array $args) {
  $data = $request->getParsedBody();
 // print_r($data);
  //echo 'deliveryAddress' . $data['deliveryAddress'];
  //$json = file_get_contents('php://input'); // RECIBE EL JSON DE ANGULAR

 //$params = json_decode($json, true); // DECODIFICA EL JSON Y LO GUARADA EN LA VARIABLE
 
 /* require("conexion.php"); // IMPORTA EL ARCHIVO CON LA CONEXION A LA DB

 $conexion = conexion(); // CREA LA CONEXION */
 $img = $data;
 print_r($img);

$dir = "/Slim/public/img/";
   //$nombre = $params->nombre;
   $nombreArchivo = $img['nombreArchivo'];
   $archivo = $img['base64textString'];
  // echo $nombreArchivo;
   //echo $archivo;
   list($type, $archivo) = explode(';', $archivo);
list(, $archivo)      = explode(',', $archivo);
$archivo = base64_decode($archivo);
   //$archivo = base64_decode($archivo);
   //echo $archivo;
   //$descripcion2 = $params->descripcion2;
   //$precio2 = $params->precio2;
   //$carpeta_destino = 'C:/Users/dxcen/Documents/Angular 12/Restaurante/src/assets/';
///$idproducto2 = $params->idproducto2;
   $filePath = $_SERVER['DOCUMENT_ROOT']."{$dir}".$nombreArchivo;
   //echo $nombreArchivo;
   //print_r($nombreArchivo);
   //print_r($archivo);
   //echo $archivo;
   //echo $nombreArchivo;
   //echo  $_SERVER['DOCUMENT_ROOT'];
   file_put_contents($filePath, $archivo);
  //ver como subir imagen 
//}
 });

// equipo 2



//no borrar nada solo poner lo suyo despues de esta linea

























// pueden poner antes de esta linea peor no despues
// no borrar esto y dejar a lo ultimo 
$app->map(['GET', 'POST', 'PUT', 'DELETE', 'PATCH'], '/{routes:.+}', function($req, $res) {
  $handler = $this->notFoundHandler; // handle using the default Slim page not found handler
  return $handler($req, $res);
});
// no borrarr

$app->run();