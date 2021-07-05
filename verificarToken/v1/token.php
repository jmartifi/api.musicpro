<?php
require_once 'class/respuestas.class.php';
require_once 'class/token.class.php';

$_respuestas = new respuestas;
$_token = new token;

if($_SERVER['REQUEST_METHOD'] == 'GET'){
    $headers = getallheaders();
    if(isset($headers['Token'])){
        $token = $headers['Token'];
        $log = $_token->verifyToken($token);
        header('Content-type: application/json');
        echo json_encode($log);
        http_response_code(200);
    }else{
        header('Content-Type: application/json');
        $datosArray = $_respuestas->error_400();
        http_response_code(400);
        echo json_encode($datosArray);
    }
}else{
    header('Content-Type: application/json');
    $datosArray = $_respuestas->error_401();
    http_response_code(401);
    echo json_encode($datosArray);
}
?>