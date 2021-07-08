<?php
require_once 'conexion/conexion.php';
require_once 'respuestas.class.php';

class subcategoria extends conexion{
    
    private $table = 'tbl_subcategoria';
    private $V_nombre = '';
    private $V_orden = '';
    private $V_estado = '';
    private $V_categoria = '';
    
    public function listar($P_menu){
        $query = "SELECT
                        tbsc_cdg_id AS id,
                        tbsc_dsc_nombre AS nombre,
                        tbsc_nmr_orden AS orden
                  FROM 
                        ".$this->table."
                  WHERE
                        tbca_cdg_id = ".$P_menu."
                  AND   tbsc_sts_estado = 'ACT'
                  ORDER BY
                        tbsc_nmr_orden ASC;";
        $datos = parent::obtenerDatos($query);
        return $datos;
    }
    
    private function validarId($P_id){
        $query = "SELECT
                        1
                  FROM 
                        ".$this->table."
                  WHERE
                        tbsc_cdg_id = ".$P_id.";";
        $datos = parent::valQuery($query);
        return $datos;
    }
    
    public function detalle($P_id){
        $_respuestas = new respuestas;
        if(is_numeric($P_id)){
            if($this->validarId($P_id) > 0){
                $query = "SELECT
                                tbsc_cdg_id AS id,
                                tbca_cdg_id As categoria,
                                tbsc_dsc_nombre AS nombre,
                                tbsc_nmr_orden AS orden,
                                tbsc_sts_estado As estado
                          FROM 
                                ".$this->table."
                          WHERE
                                tbsc_cdg_id = ".$P_id.";";
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
                        UPPER(tbsc_dsc_nombre) = UPPER('".$P_nombre."');";
        $datos = parent::valQuery($query);
        return $datos;
    }
    
    public function post($json){
        $_respuestas = new respuestas;
        $datos = json_decode($json,true);
        
        if(!isset($datos['nombre']) || !isset($datos['orden']) || !isset($datos['estado']) || !isset($datos['categoria'])){
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
                $this->V_categoria = $datos['categoria'];
                $resp = $this->insertar();
                if($resp > 0){
                    $respuesta = $_respuestas->responseCreate;
                    $respuesta['result'] = array(
                        "id" => $resp,
                        "nombre" => $datos['nombre'],
                        "orden" => $this->V_orden,
                        "estado" => $this->V_estado,
                        "categoría" => $this->V_categoria
                    );
                    return $respuesta;
                }else{
                    return $_respuestas->error_500();
                }
            }
        }
    }
    
    private function insertar(){
        $query = "INSERT INTO ".$this->table."
                       (tbca_cdg_id, tbsc_dsc_nombre, tbsc_nmr_orden, tbsc_sts_estado) VALUES (".$this->V_categoria.", '".$this->V_nombre."', ".$this->V_orden.", '".$this->V_estado."');";        
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
        
        if(!isset($datos['nombre']) || !isset($datos['orden']) || !isset($datos['estado']) || !isset($datos['categoria'])){
            return $_respuestas->error_400();
        }else{
            if(is_numeric($P_id)){
                if($this->validarId($P_id) > 0){
                    $this->V_nombre = utf8_decode($datos['nombre']);
                    $this->V_orden = $datos['orden'];
                    $this->V_estado = $datos['estado'];
                    $this->V_categoria = $datos['categoria'];
                    $this->V_id = $P_id;
                    $resp = $this->editar();
                    if($resp){
                        $respuesta = $_respuestas->response;
                        $respuesta['result'] = array(
                            "id" => $P_id,
                            "menu" => $this->V_categoria,
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
                       tbsc_dsc_nombre = '".$this->V_nombre."', 
                       tbsc_nmr_orden = ".$this->V_orden.",
                       tbsc_sts_estado = '".$this->V_estado."',
                       tbca_cdg_id = ".$this->V_categoria."
                  WHERE
                       tbsc_cdg_id = ".$this->V_id.";";        
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
                       tbsc_cdg_id = ".$this->V_id.";";        
        $resp = parent::nonQuery($query);
        return $resp;
    }
}
?>