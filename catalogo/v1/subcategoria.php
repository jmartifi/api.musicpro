<?php
if($_SERVER['REQUEST_METHOD'] === 'OPTIONS'){
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: POST, GET, DELETE, PUT, OPTIONS');
    header('Access-Control-Allow-Headers: Token, Content-Type');
    header('Access-Control-Max-Age: 1728000');
    header('Content-Length: 0');
    header('Content-Type: text/plain');
    die();
}

header('Access-Control-Allow-Origin: *');

require_once 'class/respuestas.class.php';
require_once 'class/subcategoria.class.php';

$_respuestas = new respuestas;
$_subcategoria = new subcategoria;

if($_SERVER['REQUEST_METHOD'] == 'GET'){
    
    if(isset($_GET['id'])){
        $id = $_GET['id'];
        $datos = $_subcategoria->detalle($id);
        header('Content-type: application/json');
        http_response_code($datos['response']);
        echo json_encode($datos);
    }elseif(isset($_GET['categoria'])){
        $categoria = $_GET['categoria'];
        $categorias = $_subcategoria->listar($categoria);
        header('Content-type: application/json');
        http_response_code(200);
        echo json_encode($categorias);
    }else{
        header('Content-Type: application/json');
        $datosArray = $_respuestas->error_400();
        http_response_code(400);
        echo json_encode($datosArray);
    }

}elseif($_SERVER['REQUEST_METHOD'] == 'POST'){
    $postBody = file_get_contents('php://input');
    $datosArray = $_subcategoria->post($postBody);
    //delvolvemos una respuesta
    
    if(isset($datosArray["result"]["error"])){
        header('Content-Type: application/json');
        $responseCode = $datosArray["result"]["error"];
        http_response_code(400);
        echo json_encode($datosArray);
    }else{
        header('Content-Type: application/json');
        http_response_code(201);
        echo json_encode($datosArray);
    }
    
}elseif($_SERVER['REQUEST_METHOD'] == 'PUT'){
    if(isset($_GET['id'])){
        $id = $_GET['id'];
        $postBody = file_get_contents('php://input');
        $datos = $_subcategoria->put($postBody, $id);
        header('Content-type: application/json');
        http_response_code($datos['response']);
        echo json_encode($datos);
    }else{
        header('Content-Type: application/json');
        $datosArray = $_respuestas->error_400();
        http_response_code(400);
        echo json_encode($datosArray);
    }
}elseif($_SERVER['REQUEST_METHOD'] == 'DELETE'){
    if(isset($_GET['id'])){
        $id = $_GET['id'];
        $postBody = file_get_contents('php://input');
        $datos = $_subcategoria->delete($id);
        header('Content-type: application/json');
        http_response_code($datos['response']);
        echo json_encode($datos);
    }else{
        header('Content-Type: application/json');
        $datosArray = $_respuestas->error_400();
        http_response_code(400);
        echo json_encode($datosArray);
    }
}else{
    header('Content-Type: application/json');
    $datosArray = $_respuestas->error_405();
    echo json_encode($datosArray);
}
?>