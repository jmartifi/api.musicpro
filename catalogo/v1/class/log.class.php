<?php
require_once 'conexion/conexion.php';
require_once 'respuestas.class.php';

class log extends conexion{
    
    private $table = 'tbl_log';
    private $txt = '';
    
    public function listaLog($P_pagina = 1){
        $inicio = 0;
        $cantidad = 100;
        if($P_pagina > 1){
            $inicio = ($cantidad * ($P_pagina - 1)) + 1;
            $cantidad = $cantidad * $P_pagina;
        }
        
        $query = "select tblog_cdg_id, tblog_dsc_text from ".$this->table." order by tblog_cdg_id limit $inicio,$cantidad;";
        $datos = parent::obtenerDatos($query);
        return $datos;
    }
    
    public function obtenerLog($P_id){
        $query = "SELECT tblog_cdg_id, tblog_dsc_text FROM ".$this->table." WHERE tblog_cdg_id = $P_id";
        $datos = parent::obtenerDatos($query);
        return $datos;
    }
    
    public function post($json){
        $_respuestas = new respuestas;
        $datos = json_decode($json,true);
        
        if(!isset($datos['txt'])){
            return $_respuestas->error_400();
        }else{
            $this->txt = $datos['txt'];
            $resp = $this->insertarLog();
            if($resp){
                $respuesta = $_respuestas->response;
                $respuesta['result'] = array(
                    "logId" => $resp
                );
                return $respuesta;
            }else{
                return $_respuestas->error_500();
            }
        }
    }
    
    private function insertarLog(){
        $query = "INSERT INTO ".$this->table." VALUES(NULL, '".$this->txt."');";
        $resp = parent::nonQueryId($query);
        if($resp){
            return $resp;
        }else{
            return 0;
        }
    }
}
?>