<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

 //TODAS LAS ENTRADAS
 $app->get('/api/almacen/movimientos/entradas/todas', function(Request $request, Response $response){
    $consulta = 'SELECT movimientos.id_movimientos, detalle_insumo.presentacion as name, movimientos.cantidad as value, movimientos.fecha, tipo_movimiento.descripcion FROM pruebas.movimientos
    INNER JOIN detalle_insumo ON movimientos.id_detalle_insumo = detalle_insumo.id_detalle_insumo
    INNER JOIN tipo_movimiento ON movimientos.id_tipo_movimiento = tipo_movimiento.id_tipo_movimiento
    WHERE movimientos.id_tipo_movimiento = 1 ';
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
  
  //ENTRADAS POR DIA
  $app->get('/api/almacen/movimientos/entradas/day/{day}', function(Request $request, Response $response){
    $day = $request->getAttribute('day');
  
    $consulta = "SELECT movimientos.id_movimientos, detalle_insumo.presentacion as name, movimientos.cantidad as value, movimientos.fecha, tipo_movimiento.descripcion FROM pruebas.movimientos
    INNER JOIN detalle_insumo ON movimientos.id_detalle_insumo = detalle_insumo.id_detalle_insumo
    INNER JOIN tipo_movimiento ON movimientos.id_tipo_movimiento = tipo_movimiento.id_tipo_movimiento
    WHERE movimientos.id_tipo_movimiento = 1 
    AND movimientos.fecha = '$day' ";
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
  
  
  //TODAS LAS SALIDAS
  $app->get('/api/almacen/movimientos/salidas/todas', function(Request $request, Response $response){
    $consulta = 'SELECT movimientos.id_movimientos, detalle_insumo.presentacion as name, movimientos.cantidad as value, movimientos.fecha, tipo_movimiento.descripcion FROM pruebas.movimientos
    INNER JOIN detalle_insumo ON movimientos.id_detalle_insumo = detalle_insumo.id_detalle_insumo
    INNER JOIN tipo_movimiento ON movimientos.id_tipo_movimiento = tipo_movimiento.id_tipo_movimiento
    WHERE movimientos.id_tipo_movimiento = 2 ';
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
  
  //SALIDAS POR DIA
  $app->get('/api/almacen/movimientos/salidas/day/{day}', function(Request $request, Response $response){
    $day = $request->getAttribute('day');
  
    $consulta = "SELECT movimientos.id_movimientos, detalle_insumo.presentacion as name , movimientos.cantidad as value, movimientos.fecha, tipo_movimiento.descripcion FROM pruebas.movimientos
    INNER JOIN detalle_insumo ON movimientos.id_detalle_insumo = detalle_insumo.id_detalle_insumo
    INNER JOIN tipo_movimiento ON movimientos.id_tipo_movimiento = tipo_movimiento.id_tipo_movimiento
    WHERE movimientos.id_tipo_movimiento = 2 
    AND movimientos.fecha = '$day' ";
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
  