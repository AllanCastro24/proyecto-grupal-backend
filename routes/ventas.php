<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

//Consultar todas los pedidos terminadas
$app->get('/api/ventas_terminadas/consultar', function(Request $request, Response $response){
    $consulta = "SELECT pedidoscomida.idcli, tiendas.Nombre, sucursales.Pseudonimo, pedidoscomida.idpla,
    pedidoscomida.name,  pedidoscomida.description, pedidoscomida.price, pedidoscomida.cartCount, categoriasmenu.name as categoria,
    pedidoscomida.estatus FROM pedidoscomida
    INNER JOIN tiendas ON pedidoscomida.idtienda = tiendas.ID_tienda
    INNER JOIN sucursales ON pedidoscomida.idsuc = sucursales.ID_sucursal 
    INNER JOIN categoriasmenu ON pedidoscomida.categoryId = categoriasmenu.id
    WHERE pedidoscomida.estatus = 'Terminado'";
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


 //Consultar todas los pedidos terminadas
$app->get('/api/ventas_espera/consultar', function(Request $request, Response $response){
    $consulta = "SELECT pedidoscomida.idcli, tiendas.Nombre, sucursales.Pseudonimo, pedidoscomida.idpla,
    pedidoscomida.name,  pedidoscomida.description, pedidoscomida.price, pedidoscomida.cartCount, categoriasmenu.name as categoria,
    pedidoscomida.estatus FROM pedidoscomida
    INNER JOIN tiendas ON pedidoscomida.idtienda = tiendas.ID_tienda
    INNER JOIN sucursales ON pedidoscomida.idsuc = sucursales.ID_sucursal 
    INNER JOIN categoriasmenu ON pedidoscomida.categoryId = categoriasmenu.id
    WHERE pedidoscomida.estatus = 'En espera'";
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



//CONSULTA POR MES
$app->get('/api/ventas/mes/{mes}/{year}', function(Request $request, Response $response){
    $mes = $request->getAttribute('mes');
    $year = $request->getAttribute('year');
    $consulta = "SELECT pedidoscomida.idcli, tiendas.Nombre, sucursales.Pseudonimo, pedidoscomida.idpla,
    pedidoscomida.name, pedidoscomida.description, pedidoscomida.fecha, pedidoscomida.price, pedidoscomida.cartCount, categoriasmenu.name as categoria,
    pedidoscomida.estatus FROM pedidoscomida
    INNER JOIN tiendas ON pedidoscomida.idtienda = tiendas.ID_tienda
    INNER JOIN sucursales ON pedidoscomida.idsuc = sucursales.ID_sucursal 
    INNER JOIN categoriasmenu ON pedidoscomida.categoryId = categoriasmenu.id
    WHERE pedidoscomida.estatus = 'Terminado' and MONTH(pedidoscomida.fecha) = '$mes' and YEAR(pedidoscomida.fecha) = '$year'";
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
    $consulta = "SELECT pedidoscomida.idcli, tiendas.Nombre, sucursales.Pseudonimo, pedidoscomida.idpla,
    pedidoscomida.name, pedidoscomida.description, pedidoscomida.fecha, pedidoscomida.price, pedidoscomida.cartCount, categoriasmenu.name as categoria,
    pedidoscomida.estatus FROM pedidoscomida
    INNER JOIN tiendas ON pedidoscomida.idtienda = tiendas.ID_tienda
    INNER JOIN sucursales ON pedidoscomida.idsuc = sucursales.ID_sucursal 
    INNER JOIN categoriasmenu ON pedidoscomida.categoryId = categoriasmenu.id
    WHERE pedidoscomida.estatus = 'Terminado'  and YEAR(pedidoscomida.fecha) = '$year' ";
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
    $consulta = "SELECT pedidoscomida.idcli, tiendas.Nombre, sucursales.Pseudonimo, pedidoscomida.idpla,
    pedidoscomida.name, pedidoscomida.description, pedidoscomida.fecha, pedidoscomida.price, pedidoscomida.cartCount, categoriasmenu.name as categoria,
    pedidoscomida.estatus FROM pedidoscomida
    INNER JOIN tiendas ON pedidoscomida.idtienda = tiendas.ID_tienda
    INNER JOIN sucursales ON pedidoscomida.idsuc = sucursales.ID_sucursal 
    INNER JOIN categoriasmenu ON pedidoscomida.categoryId = categoriasmenu.id
    WHERE pedidoscomida.estatus = 'Terminado'  and date(pedidoscomida.fecha) = '$day' ";
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
    $consulta = "SELECT pedidoscomida.idcli, tiendas.Nombre, sucursales.Pseudonimo, pedidoscomida.idpla,
    pedidoscomida.name, pedidoscomida.description, pedidoscomida.fecha, pedidoscomida.price, pedidoscomida.cartCount, categoriasmenu.name as categoria,
    pedidoscomida.estatus FROM pedidoscomida
    INNER JOIN tiendas ON pedidoscomida.idtienda = tiendas.ID_tienda
    INNER JOIN sucursales ON pedidoscomida.idsuc = sucursales.ID_sucursal 
    INNER JOIN categoriasmenu ON pedidoscomida.categoryId = categoriasmenu.id
    WHERE pedidoscomida.estatus = 'Terminado'  and  date(pedidoscomida.fecha) BETWEEN '$inicio' AND '$fin';";
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
  
  
  /*
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
  */
   
  
  