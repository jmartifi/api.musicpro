<?php
require_once 'conexion/conexion.php';
require_once 'respuestas.class.php';

class menu extends conexion{
    
    private $table = 'tbl_categoria';
    private $V_nombre = '';
    private $V_orden = '';
    private $V_estado = '';
    private $V_menu = '';
    
    public function listar($P_menu){
        $query = "SELECT
                        tbca_cdg_id AS id,
                        tbca_dsc_nombre AS nombre,
                        tbca_nmr_orden AS orden
                  FROM 
                        ".$this->table."
                  WHERE
                        tbme_cdg_id = ".$P_menu."
                  AND   tbca_sts_estado = 'ACT'
                  ORDER BY
                        tbca_nmr_orden ASC;";
        $datos = parent::obtenerDatos($query);
        return $datos;
    }
    
    private function validarId($P_id){
        $query = "SELECT
                        1
                  FROM 
                        ".$this->table."
                  WHERE
                        tbca_cdg_id = ".$P_id.";";
        $datos = parent::valQuery($query);
        return $datos;
    }
    
    public function detalle($P_id){
        $_respuestas = new respuestas;
        if(is_numeric($P_id)){
            if($this->validarId($P_id) > 0){
                $query = "SELECT
                                tbca_cdg_id AS id,
                                tbme_cdg_id As menu,
                                tbca_dsc_nombre AS nombre,
                                tbca_nmr_orden AS orden
                          FROM 
                                ".$this->table."
                          WHERE
                                tbca_cdg_id = ".$P_id.";";
                $datos = parent::obtenerDatos($query);
                $respuesta =  $_respuestas->response;
                $respuesta['result'] = $datos;
                return $respuesta;
            }else{
                $respuesta['status'] = 'error';
                $respuesta['response'] = 404;
                $respuesta['result'] = array(
                    "error" => 'sin registro'
                );
                return $respuesta;
            }
        }else{
            $respuesta =  $_respuestas->error_400();
            return $respuesta;
        }
    }
    
    private function validarRegistro($P_nombre){
        $query = "SELECT
                        1
                  FROM 
                        ".$this->table."
                  WHERE
                        UPPER(tbca_dsc_nombre) = UPPER('".$P_nombre."');";
        $datos = parent::valQuery($query);
        return $datos;
    }
    
    public function post($json){
        $_respuestas = new respuestas;
        $datos = json_decode($json,true);
        
        if(!isset($datos['nombre']) || !isset($datos['orden']) || !isset($datos['estado']) || !isset($datos['menu'])){
            return $_respuestas->error_400();
        }else{
            $this->V_nombre = utf8_decode($datos['nombre']);
            if($this->validarRegistro($this->V_nombre) > 0){
                $respuesta =  $_respuestas->error_400();
                $respuesta['result'] = array(
                    "error" => 'Registro Existente'
                );
                return $respuesta;
            }else{
                $this->V_orden = $datos['orden'];
                $this->V_estado = $datos['estado'];
                $this->V_menu = $datos['menu'];
                $resp = $this->insertarMenu();
                if($resp > 0){
                    $respuesta = $_respuestas->responseCreate;
                    $respuesta['result'] = array(
                        "id" => $resp,
                        "nombre" => $this->V_nombre,
                        "orden" => $this->V_orden,
                        "estado" => $this->V_estado
                    );
                    return $respuesta;
                }else{
                    return $_respuestas->error_500();
                }
            }
        }
    }
    
    private function insertarMenu(){
        $query = "INSERT INTO ".$this->table."
                       (tbme_cdg_id, tbca_dsc_nombre, tbca_nmr_orden, tbca_sts_estado) VALUES (".$this->V_menu.", '".$this->V_nombre."', ".$this->V_orden.", '".$this->V_estado."');";        
        $resp = parent::nonQueryId($query);
        if($resp > 0){
            return $resp;
        }else{
            return 0;
        }
    }
    
    public function put($json, $P_id){
        $_respuestas = new respuestas;
        $datos = json_decode($json,true);
        
        if(!isset($datos['nombre']) || !isset($datos['orden']) || !isset($datos['estado']) || !isset($datos['menu'])){
            return $_respuestas->error_400();
        }else{
            if(is_numeric($P_id)){
                if($this->validarId($P_id) > 0){
                    $this->V_nombre = utf8_decode($datos['nombre']);
                    $this->V_orden = $datos['orden'];
                    $this->V_estado = $datos['estado'];
                    $this->V_menu = $datos['menu'];
                    $this->V_id = $P_id;
                    $resp = $this->editar();
                    if($resp > 0){
                        $respuesta = $_respuestas->response;
                        $respuesta['result'] = array(
                            "id" => $P_id,
                            "menu" => $this->V_menu,
                            "nombre" => $datos['nombre'],
                            "orden" => $this->V_orden,
                            "estado" => $this->V_estado
                        );
                        return $respuesta;
                    }else{
                        return $_respuestas->error_500();
                    }
                }else{
                    $respuesta =  $_respuestas->response;
                    $respuesta['status'] = 'error';
                    $respuesta['response'] = 404;
                    $respuesta['result'] = array(
                        "error" => 'sin registro'
                    );
                    return $respuesta;
                }
            }else{
                $respuesta =  $_respuestas->error_400();
                return $respuesta;
            }
        }
    }
    
    private function editar(){
        $query = "UPDATE ".$this->table." SET
                       tbca_dsc_nombre = '".$this->V_nombre."', 
                       tbca_nmr_orden = ".$this->V_orden.",
                       tbca_sts_estado = '".$this->V_estado."',
                       tbme_cdg_id = ".$this->V_menu."
                  WHERE
                       tbca_cdg_id = ".$this->V_id.";";        
        $resp = parent::nonQuery($query);
        return $resp;
    }
    
    public function delete($P_id){
        $_respuestas = new respuestas;
        
        if(is_numeric($P_id)){
            if($this->validarId($P_id) > 0){
                $this->V_id = $P_id;
                $resp = $this->eliminar();
                if($resp){
                    $respuesta = $_respuestas->error_204();
                    return $respuesta;
                }else{
                    return $_respuestas->error_500();
                }
            }else{
                $respuesta =  $_respuestas->response;
                $respuesta['status'] = 'error';
                $respuesta['response'] = 404;
                $respuesta['result'] = array(
                    "error" => 'sin registro'
                );
                return $respuesta;
            }
        }else{
            $respuesta =  $_respuestas->error_400();
            return $respuesta;
        }
    }
    
    private function eliminar(){
        $query = "DELETE FROM ".$this->table."
                  WHERE
                       tbca_cdg_id = ".$this->V_id.";";        
        $resp = parent::nonQuery($query);
        return $resp;
    }
}
?>