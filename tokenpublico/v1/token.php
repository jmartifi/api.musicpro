<?php
require_once 'class/respuestas.class.php';
require_once 'class/token.class.php';

$_respuestas = new respuestas;
$_token = new token;

if($_SERVER['REQUEST_METHOD'] == 'GET'){
    $token = $_token->crearToken();
    header('Content-type: application/json');
    echo json_encode($token);
    http_response_code(200);
}else{
    header('Content-Type: application/json');
    $datosArray = $_respuestas->error_401();
    http_response_code(401);
    echo json_encode($datosArray);
}
?>