<?php
use  Firebase\JWT\JWT;
require_once 'respuestas.class.php';

class token {
    
    private $V_key = '';
    private $V_token = '';
    private $V_time = '';
    private $V_jwt = '';
    
    public function crearToken(){        

        require_once('php-jwt-master/src/JWT.php');
        
        $this->V_time = time();
        $this->V_key = 'D3mr3Pi1o7Aje$';
        $this->V_token = array(
            'iat' =>  $this->V_time,
            'exp' =>  $this->V_time + (60 * 1)
        );

        try{
            $_respuesta = new respuestas;
            $this->V_jwt = JWT::encode($this->V_token, $this->V_key);
            $respuesta = $_respuesta->response;
            $respuesta['result'] = array(
                "token" => $this->V_jwt
            );
            return $respuesta;
        }catch (Exception $e){
            return 0;
        }
    }
}
?>