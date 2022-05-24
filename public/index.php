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

require_once __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../src/Models/db.php';


$db = new Db();
$conn = $db->connect();

$app = AppFactory::create();
$app->addBodyParsingMiddleware();
$app->addRoutingMiddleware();
$app->setBasePath('/proyecto-grupal-backend/public');
$app->add(new BasePathMiddleware($app));
$app->addErrorMiddleware(true, true, true);

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










$app->map(['GET', 'POST', 'PUT', 'DELETE', 'PATCH'], '/{routes:.+}', function($req, $res) {
  $handler = $this->notFoundHandler; // handle using the default Slim page not found handler
  return $handler($req, $res);
});




$app->run();