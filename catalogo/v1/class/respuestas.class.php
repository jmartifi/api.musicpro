<?php 

class respuestas{

    public  $response = [
        'versión' => 'v1',
        'status' => "ok",
        'response' => '200',
        "result" => array()
    ];
    
    public  $responseCreate = [
        'versión' => 'v1',
        'status' => "created",
        'response' => '201',
        "result" => array()
    ];
    
    public  $responseFull = [
        'cantidad' => '0',
        'anterior' => '',
        'siguiente' => '',
        'response' => '200',
        "productos" => array()
    ];
    
    public  $responsePr = [
        'serie del producto' => '',
        'marca' => '',
        'codigo' => '',
        'nombre' => '',
        'status' => '',
        'result' => '',
        "precios" => array()
    ];

    public function error_405(){
        $this->response['status'] = "error";
        $this->response['result'] = array(
            "error_id" => "405",
            "error_msg" => "Metodo no permitido"
        );
        return $this->response;
    }

    public function error_200($valor = "Datos incorrectos"){
        $this->response['status'] = "error";
        $this->response['result'] = array(
            "error_id" => "200",
            "error_msg" => $valor
        );
        return $this->response;
    }
    
    public function error_204($valor = "Consulta exitosa, pero sin registros"){
        $this->response['status'] = "error";
        $this->response['response'] = 204;
        $this->response['result'] = array(
            "error_id" => "204",
            "error_msg" => $valor
        );
        return $this->response;
    }

    public function error_400(){
        $this->response['status'] = "error";
        $this->response['response'] = 400;
        $this->response['result'] = array(
            "error_id" => "400",
            "error_msg" => "Datos enviados incompletos o con formato incorrecto"
        );
        return $this->response;
    }

    public function error_500($valor = "Error interno del servidor"){
        $this->response['status'] = "error";
        $this->response['result'] = array(
            "error_id" => "500",
            "error_msg" => $valor
        );
        return $this->response;
    }

    public function error_401($valor = "No autorizado"){
        $this->response['status'] = "error";
        $this->response['result'] = array(
            "error_id" => "401",
            "error_msg" => $valor
        );
        return $this->response;
    }
}

?>