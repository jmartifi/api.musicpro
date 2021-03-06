<?php
require_once 'conexion/conexion.php';
require_once 'respuestas.class.php';

class menu extends conexion{
    
    private $table = 'tbl_menu';
    private $tableC = 'tbl_categoria';
    private $tableSC = 'tbl_subcategoria';
    private $V_nombre = '';
    private $V_orden = '';
    private $V_estado = '';
    
    public function listarMenu(){
        $query = "SELECT
                        tbme_cdg_id AS id,
                        tbme_dsc_nombre AS nombre_menu,
                        tbme_nmr_orden AS menu_orden
                  FROM 
                        ".$this->table."
                  WHERE
                        tbme_sts_estado = 'ACT'
                  ORDER BY
                        tbme_nmr_orden ASC;";
        $datos = parent::obtenerDatos($query);
        return $datos;
    }
    
    public function listarCategoria($P_menu){
        $query = "SELECT
                        tbca_cdg_id AS id,
                        tbca_dsc_nombre AS nombre_categoria,
                        tbca_nmr_orden AS categoria_orden
                  FROM 
                        ".$this->tableC."
                  WHERE
                        tbme_cdg_id = ".$P_menu."
                  AND   tbca_sts_estado = 'ACT'
                  ORDER BY
                        tbca_nmr_orden ASC;";
        $datos = parent::obtenerDatos($query);
        return $datos;
    }
    
    public function listarSCategoria($P_scategoria){
        $query = "SELECT
                        tbsc_cdg_id AS id,
                        tbsc_dsc_nombre AS nombre_subCategoria,
                        tbsc_nmr_orden AS subCategoria_orden
                  FROM 
                        tbl_subcategoria
                  WHERE
                        tbca_cdg_id = ".$P_scategoria."
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
                        tbme_cdg_id = ".$P_id.";";
        $datos = parent::valQuery($query);
        return $datos;
    }
    
    public function detalleMenu($P_id){
        $_respuestas = new respuestas;
        if(is_numeric($P_id)){
            if($this->validarId($P_id) > 0){
                $query = "SELECT
                                tbme_cdg_id AS id,
                                tbme_dsc_nombre AS nombre_menu,
                                tbme_nmr_orden AS menu_orden
                          FROM 
                                ".$this->table."
                          WHERE
                                tbme_cdg_id = ".$P_id.";";
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
                        UPPER(tbme_dsc_nombre) = UPPER('".$P_nombre."');";
        $datos = parent::valQuery($query);
        return $datos;
    }
    
    public function post($json){
        $_respuestas = new respuestas;
        $datos = json_decode($json,true);
        
        if(!isset($datos['nombre']) && !isset($datos['orden']) && !isset($datos['estado'])){
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
                $resp = $this->insertarMenu();
                if($resp > 0){
                    $respuesta = $_respuestas->responseCreate;
                    $respuesta['result'] = array(
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
                       (tbme_dsc_nombre, tbme_nmr_orden, tbme_sts_estado) VALUES ('".$this->V_nombre."', ".$this->V_orden.", '".$this->V_estado."');";        
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
        
        if(is_numeric($P_id)){
            if($this->validarId($P_id) > 0){
                $this->V_nombre = utf8_decode($datos['nombre']);
                $this->V_orden = $datos['orden'];
                $this->V_estado = $datos['estado'];
                $this->V_id = $P_id;
                $resp = $this->editarMenu();
                if($resp){
                    $respuesta = $_respuestas->response;
                    $respuesta['result'] = array(
                        "id" => $P_id,
                        "nombre" => $this->V_nombre,
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
    
    private function editarMenu(){
        $query = "UPDATE ".$this->table." SET
                       tbme_dsc_nombre = '".$this->V_nombre."', 
                       tbme_nmr_orden = ".$this->V_orden.",
                       tbme_sts_estado = '".$this->V_estado."'
                  WHERE
                       tbme_cdg_id = ".$this->V_id.";";        
        $resp = parent::nonQuery($query);
        return $resp;
    }
    
    public function delete($P_id){
        $_respuestas = new respuestas;
        
        if(is_numeric($P_id)){
            if($this->validarId($P_id) > 0){
                $this->V_id = $P_id;
                $resp = $this->eliminarMenu();
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
    
    private function eliminarMenu(){
        $query = "DELETE FROM ".$this->table."
                  WHERE
                       tbme_cdg_id = ".$this->V_id.";";        
        $resp = parent::nonQuery($query);
        return $resp;
    }
}
?>