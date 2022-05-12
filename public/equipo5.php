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

//consultar todos los usuarios
$app->get('/api/usuarios/consultar', function(Request $request, Response $response){
   $consulta = 'SELECT * FROM usuarios';
   try{
       $db = new BD();
       $db = $db->conexionBD();
       $ejecutar = $db->query($consulta);
       $user = $ejecutar->fetchAll(PDO::FETCH_OBJ);
       $db = null;
       $response->getBody()->write(json_encode($user));
       return $user;
   } catch(PDOException $e){
       echo '{"error": {"text":  '.$e->getMessage().'}';
   }
});

$app->run();