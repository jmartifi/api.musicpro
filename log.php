<?php
require_once 'class/respuestas.class.php';
require_once 'class/log.class.php';

$_respuesta = new respuestas;
$_log = new log;

if($_SERVER['REQUEST_METHOD'] == 'GET'){
    if(isset($_GET['pagina'])){
        $pagina = $_GET['pagina'];
        $log = $_log->listaLog($pagina);
        header('Content-type: application/json');
        echo json_encode($log);
        http_response_code(200);
    }elseif(isset($_GET['id'])){
        $logId = $_GET['id'];
        $datosLog = $_log->obtenerLog($logId);
        header('Content-type: application/json');
        echo json_encode($datosLog);
        http_response_code(200);
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