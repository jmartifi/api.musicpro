<?php
if($_SERVER['REQUEST_METHOD'] === 'OPTIONS'){
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: POST, GET, DELETE, PUT, PATCH, OPTIONS');
    header('Access-Control-Allow-Headers: Token, Content-Type');
    header('Access-Control-Max-Age: 1728000');
    header('Content-Length: 0');
    header('Content-Type: text/plain');
    die();
}

header('Access-Control-Allow-Origin: *');

require_once 'class/respuestas.class.php';
require_once 'class/menu.class.php';

$_respuesta = new respuestas;
$_menu = new menu;

if($_SERVER['REQUEST_METHOD'] == 'GET'){
    
    if(isset($_GET['categoria'])){
        
    }else{
        $menu = $_menu->listarMenu();
        header('Content-type: application/json');
        http_response_code(200);
        echo json_encode($menu);
    }

}elseif($_SERVER['REQUEST_METHOD'] == 'POST'){
    $postBody = file_get_contents('php://input');
    $datosArray = $_log->post($postBody);
    //delvolvemos una respuesta
    header('Content-Type: application/json');
    if(isset($datosArray["result"]["error_id"])){
        $responseCode = $datosArray["result"]["error_id"];
        http_response_code($responseCode);
    }else{
        http_response_code(201);
    }
    echo json_encode($datosArray);
    
}elseif($_SERVER['REQUEST_METHOD'] == 'PUT'){
    echo 'hola put';
}elseif($_SERVER['REQUEST_METHOD'] == 'DELETE'){
    echo 'hola delete';
}else{
    header('Content-Type: application/json');
    $datosArray = $_respuestas->error_405();
    echo json_encode($datosArray);
}
?>