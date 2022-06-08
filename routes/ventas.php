<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Models\DB;

//Consultar todas los pedidos terminadas
$app->get('/api/ventas_terminadas/consultar/{sucursal}/{tienda}', function(Request $request, Response $response){
    $sucursal = $request->getAttribute('sucursal');
    $tienda = $request->getAttribute('tienda');
    $consulta = "SELECT pedidoscomida.idcli, tiendas.Nombre, sucursales.Pseudonimo, pedidoscomida.idpla,
    pedidoscomida.name,  pedidoscomida.description, pedidoscomida.price as value, pedidoscomida.cartCount, categoriasmenu.name as categoria,
    pedidoscomida.estatus FROM pedidoscomida
    INNER JOIN tiendas ON pedidoscomida.idtienda = tiendas.ID_tienda
    INNER JOIN sucursales ON pedidoscomida.idsuc = sucursales.ID_sucursal 
    INNER JOIN categoriasmenu ON pedidoscomida.categoryId = categoriasmenu.id
    WHERE pedidoscomida.estatus = 'Terminado' and pedidoscomida.idsuc = '$sucursal' and pedidoscomida.idtienda = '$tienda' ";
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


 //Consultar todas los pedidos en espera
$app->get('/api/ventas_espera/consultar/{sucursal}/{tienda}', function(Request $request, Response $response){
    $sucursal = $request->getAttribute('sucursal');
    $tienda = $request->getAttribute('tienda');
    $consulta = "SELECT pedidoscomida.idcli, tiendas.Nombre, sucursales.Pseudonimo, pedidoscomida.idpla,
    pedidoscomida.name,  pedidoscomida.description, pedidoscomida.price as value, pedidoscomida.cartCount, categoriasmenu.name as categoria,
    pedidoscomida.estatus FROM pedidoscomida
    INNER JOIN tiendas ON pedidoscomida.idtienda = tiendas.ID_tienda
    INNER JOIN sucursales ON pedidoscomida.idsuc = sucursales.ID_sucursal 
    INNER JOIN categoriasmenu ON pedidoscomida.categoryId = categoriasmenu.id
    WHERE pedidoscomida.estatus = 'Procesando' and pedidoscomida.idsuc = '$sucursal' and pedidoscomida.idtienda = '$tienda' ";
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



//CONSULTA POR MES
$app->get('/api/ventas/mes/{mes}/{year}/{sucursal}/{tienda}', function(Request $request, Response $response){
    $mes = $request->getAttribute('mes');
    $year = $request->getAttribute('year');
    $sucursal = $request->getAttribute('sucursal');
    $tienda = $request->getAttribute('tienda');
    $consulta = "SELECT pedidoscomida.idcli, tiendas.Nombre, sucursales.Pseudonimo, pedidoscomida.idpla,
    pedidoscomida.name, pedidoscomida.description, pedidoscomida.fecha, pedidoscomida.price as value, pedidoscomida.cartCount, categoriasmenu.name as categoria,
    pedidoscomida.estatus FROM pedidoscomida
    INNER JOIN tiendas ON pedidoscomida.idtienda = tiendas.ID_tienda
    INNER JOIN sucursales ON pedidoscomida.idsuc = sucursales.ID_sucursal 
    INNER JOIN categoriasmenu ON pedidoscomida.categoryId = categoriasmenu.id
    WHERE pedidoscomida.estatus = 'Terminado' and MONTH(pedidoscomida.fecha) = '$mes' and YEAR(pedidoscomida.fecha) = '$year' and pedidoscomida.idsuc = '$sucursal' and pedidoscomida.idtienda = '$tienda' ";
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
  
  //CONSULTA POR AÑO
  $app->get('/api/ventas/year/{year}/{sucursal}/{tienda}', function(Request $request, Response $response){
    $sucursal = $request->getAttribute('sucursal');
    $tienda = $request->getAttribute('tienda');
    $year = $request->getAttribute('year');
    $consulta = "SELECT pedidoscomida.idcli, tiendas.Nombre, sucursales.Pseudonimo, pedidoscomida.idpla,
    pedidoscomida.name, pedidoscomida.description, pedidoscomida.fecha, pedidoscomida.price as value, pedidoscomida.cartCount, categoriasmenu.name as categoria,
    pedidoscomida.estatus FROM pedidoscomida
    INNER JOIN tiendas ON pedidoscomida.idtienda = tiendas.ID_tienda
    INNER JOIN sucursales ON pedidoscomida.idsuc = sucursales.ID_sucursal 
    INNER JOIN categoriasmenu ON pedidoscomida.categoryId = categoriasmenu.id
    WHERE pedidoscomida.estatus = 'Terminado'  and YEAR(pedidoscomida.fecha) = '$year' and pedidoscomida.idsuc = '$sucursal' and pedidoscomida.idtienda = '$tienda'";
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
  
  //CONSULTA POR DIA
  $app->get('/api/ventas/day/{day}/{sucursal}/{tienda}', function(Request $request, Response $response){
    $day = $request->getAttribute('day');
    $sucursal = $request->getAttribute('sucursal');
    $tienda = $request->getAttribute('tienda');
    $consulta = "SELECT pedidoscomida.idcli, tiendas.Nombre, sucursales.Pseudonimo, pedidoscomida.idpla,
    pedidoscomida.name, pedidoscomida.description, pedidoscomida.fecha, pedidoscomida.price as value, pedidoscomida.cartCount, categoriasmenu.name as categoria,
    pedidoscomida.estatus FROM pedidoscomida
    INNER JOIN tiendas ON pedidoscomida.idtienda = tiendas.ID_tienda
    INNER JOIN sucursales ON pedidoscomida.idsuc = sucursales.ID_sucursal 
    INNER JOIN categoriasmenu ON pedidoscomida.categoryId = categoriasmenu.id
    WHERE pedidoscomida.estatus = 'Terminado'  and date(pedidoscomida.fecha) = '$day' and pedidoscomida.idsuc = '$sucursal' and pedidoscomida.idtienda = '$tienda' ";
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
  
  //CONSULTA POR RANGO
  $app->get('/api/ventas/range/{inicio}/{fin}/{sucursal}/{tienda}', function(Request $request, Response $response){
    $inicio = $request->getAttribute('inicio');
    $fin = $request->getAttribute('fin');
    $sucursal = $request->getAttribute('sucursal');
    $tienda = $request->getAttribute('tienda');
    $consulta = "SELECT pedidoscomida.idcli, tiendas.Nombre, sucursales.Pseudonimo, pedidoscomida.idpla,
    pedidoscomida.name, pedidoscomida.description, pedidoscomida.fecha, pedidoscomida.price as value, pedidoscomida.cartCount, categoriasmenu.name as categoria,
    pedidoscomida.estatus FROM pedidoscomida
    INNER JOIN tiendas ON pedidoscomida.idtienda = tiendas.ID_tienda
    INNER JOIN sucursales ON pedidoscomida.idsuc = sucursales.ID_sucursal 
    INNER JOIN categoriasmenu ON pedidoscomida.categoryId = categoriasmenu.id
    WHERE pedidoscomida.estatus = 'Terminado'   and pedidoscomida.idsuc = '$sucursal' and pedidoscomida.idtienda = '$tienda' and  date(pedidoscomida.fecha) BETWEEN '$inicio' AND '$fin';";
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
  
  /*
    PRODUCTOS O PLATILLOS MÁS VENDIDOS
  */
 
  
  //CONSULTA MAS VENDIDO POR MES
  $app->get('/api/producto/mas_vendido/mes/{mes}/{year}/{sucursal}/{tienda}', function(Request $request, Response $response){
    $mes = $request->getAttribute('mes');
    $year = $request->getAttribute('year');
    $sucursal = $request->getAttribute('sucursal');
    $tienda = $request->getAttribute('tienda');
    $consulta = "SELECT pedidoscomida.name, SUM(pedidoscomida.price) AS value
    FROM pedidoscomida  where  month(pedidoscomida.fecha) = '$mes' and year(pedidoscomida.fecha) = '$year' and estatus = 'Terminado'  and pedidoscomida.idsuc = '$sucursal' and pedidoscomida.idtienda = '$tienda'
    GROUP BY pedidoscomida.idpla
    ORDER BY SUM(pedidoscomida.price) DESC
    LIMIT 0 , 30 ";
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
  
  //CONSULTA MAS VENDIDO POR AÑO
  $app->get('/api/producto/mas_vendido/year/{year}', function(Request $request, Response $response){
    $year = $request->getAttribute('year');
    $consulta = "SELECT pedidoscomida.name, SUM(pedidoscomida.price) AS value
    FROM pedidoscomida  where  year(pedidoscomida.fecha) = '$year' and estatus = 'Terminado' 
    GROUP BY pedidoscomida.idpla
    ORDER BY SUM(pedidoscomida.price) DESC
    LIMIT 0 , 30 ";
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
  
  //MAS VENDIDO POR RANGO

  $app->get('/api/producto/mas_vendido/range/{inicio}/{fin}/{sucursal}/{tienda}', function(Request $request, Response $response){
    $inicio = $request->getAttribute('inicio');
    $fin = $request->getAttribute('fin');
    $sucursal = $request->getAttribute('sucursal');
    $tienda = $request->getAttribute('tienda');
    $consulta = "SELECT pedidoscomida.name, SUM(pedidoscomida.price) AS value
    FROM pedidoscomida  where  pedidoscomida.idsuc = '$sucursal' and pedidoscomida.idtienda = '$tienda' AND date(pedidoscomida.fecha) BETWEEN '$inicio' AND '$fin' and estatus = 'Terminado' 
    GROUP BY pedidoscomida.idpla
    ORDER BY SUM(pedidoscomida.price) DESC
    LIMIT 0 , 30 ";
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


   //MAS VENDIDO POR DIA

   $app->get('/api/producto/mas_vendido/day/{day}/{sucursal}/{tienda}', function(Request $request, Response $response){
    $day = $request->getAttribute('day');
    $sucursal = $request->getAttribute('sucursal');
    $tienda = $request->getAttribute('tienda');
    $consulta = "SELECT pedidoscomida.name, SUM(pedidoscomida.price) AS value
    FROM pedidoscomida  where  date(pedidoscomida.fecha) = '$day' and estatus = 'Terminado' and pedidoscomida.idsuc = '$sucursal' and pedidoscomida.idtienda = '$tienda' 
    GROUP BY pedidoscomida.idpla
    ORDER BY SUM(pedidoscomida.price) DESC
    LIMIT 0 , 30 ";
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
  
  

  
   
  
  