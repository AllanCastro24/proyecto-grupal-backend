<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: *');
header('Access-Control-Allow-Methods: *');
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../src/Models/db.php';



$db = new Db();
$conn = $db->connect();

$app = AppFactory::create();
$app->addBodyParsingMiddleware();
$app->setBasePath('/proyecto-grupal-backend/public');

$app->get('/', function (Request $request, Response $response, $args) {
    $response->getBody()->write("Hello world!");
    return $response;
});


require __DIR__ . '/../routes/gastos_fijos.php';
require __DIR__ . '/../routes/tipo_gastos.php';
require __DIR__ . '/../routes/ventas.php';
require __DIR__ . '/../routes/sucursales.php';
require __DIR__ . '/../routes/almacen.php';
require __DIR__ . '/../routes/gastos_fijos_programados.php';


$app->run();