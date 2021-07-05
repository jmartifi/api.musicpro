<?php
use Firebase\JWT\JWT;
use Firebase\JWT\ExpiredException;
require_once('php-jwt-master/src/JWT.php');
require_once('php-jwt-master/src/ExpiredException.php');
require_once 'respuestas.class.php';

class token {
    
    private $V_key = '';
    private $V_token = '';
    private $V_time = '';
    private $V_jwt = '';
    private $V_decoded = '';
    
    protected function validateToken($P_token){       
        try{
            $key = 'D3mr3Pi1o7Aje$';
            return JWT::decode($P_token, $key, array('HS256'));
        }catch (\Exception $e){
            return false;
        }
    }
    
    public function verifyToken($P_token){
        $key='D3mr3Pi1o7Aje$';
        $token = $P_token;
        $_respuestas = new respuestas;
        
        if($this->validateToken($token) == false){
            $respuesta = $_respuestas->error_401();
            return $respuesta;
        }else{
            $data = JWT::decode($token,$key,array('HS256'));
            $respuesta = $_respuestas->response;
            $respuesta['result'] = array(
                "sts" => true ,
                "datos" => $data
            );
            return $respuesta;
        }
    }
}
?>