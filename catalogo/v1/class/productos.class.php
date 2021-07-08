<?php
require_once 'conexion/conexion.php';
require_once 'respuestas.class.php';

class productos extends conexion{
    
    private $table = 'tbl_productos';
    private $url = 'https://api.musicpro.hexagram.cl/catalogo/v1/';
    private $V_codigo = '';
    private $V_nombre = '';
    private $V_serie = '';
    private $V_marca = '';
    private $V_categoria = '';
    private $V_precio = '';
    private $V_cantReg = '0';
    private $V_dolar = '0';
    private $V_euro = '0';
    
    private function listarProductos($P_sc, $P_pagina){
        $inicio = 0;
        $cantidad = 10;
        if($P_pagina > 1){
            $inicio = ($cantidad * ($P_pagina - 1)) + 1;
            $cantidad = $cantidad * $P_pagina;
        }
        $queryT = "SELECT 1 FROM ".$this->table." WHERE tbsc_cdg_id = ".$P_sc.";";
        $query = "SELECT
                        tbpr_cdg_codigo As codigo,
                        tbpr_dsc_nombre As nombre,
                        tbpr_cdg_serie As serie,
                        tbpr_dsc_marca As marca,
                        tbpr_nmr_precio AS precio
                  FROM  
                        ".$this->table."
                  WHERE 
                        tbsc_cdg_id = ".$P_sc."
                  LIMIT $inicio,$cantidad;";
        $this->V_cantReg = $this->countReg($queryT);
        $datos = parent::obtenerDatos($query);
        
        $resultArray = array();
        foreach($datos as $key){
            $resultArray[] = $key;
        }
        return $resultArray;
    }
    
    private function consultarStock($P_codigo){
        $query = "SELECT
                        tbti_dsc_nombre AS tienda,
                        npti_nmr_stock As stock
                  FROM 
                        nav_producto_tienda npti,
                        tbl_tienda tbti
                  WHERE
                        tbpr_cdg_codigo = '".$P_codigo."'
                  AND   npti.tbti_cdg_id = tbti.tbti_cdg_id;";
        $datos = parent::obtenerDatos($query);
        
        $resultArray = array();
        foreach($datos as $key){
            $resultArray[] = $key;
        }
        return $resultArray;
    }
    
    private function validarCategoria($P_sc){
        $query = "SELECT
                        1
                  FROM 
                        tbl_subcategoria
                  WHERE
                        tbsc_cdg_id = ".$P_sc.";";
        $datos = parent::valQuery($query);
        return $datos;
    }
    
    private function cambioMoneda(){
        //Consumo API mindicador.cl
    
        $apiUrl = 'https://mindicador.cl/api';
        $curl = curl_init($apiUrl);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $json = curl_exec($curl);
        curl_close($curl);
        
        $dailyIndicators = json_decode($json);
        
        if($dailyIndicators->dolar->valor > 0){
            $this->V_dolar = $dailyIndicators->dolar->valor;
        }else{
            $this->V_dolar = 0;
        }
        
        if($dailyIndicators->euro->valor > 0){
            $this->V_euro = $dailyIndicators->euro->valor;
        }else{
            $this->V_euro = 0;
        }
        return true;
    }
    
    public function fullProductos($P_sc, $P_pagina){
        $_respuestas = new respuestas;
        if(is_numeric($P_sc) && is_numeric($P_pagina)){
            if($this->validarCategoria($P_sc) > 0){
                $datos = $this->listarProductos($P_sc, $P_pagina);
                $respuesta = $_respuestas->responseFull;
                $respuesta['cantidad'] = $this->V_cantReg;
                $valores = $this->cambioMoneda();
                //$this->V_euro = $this->cambioMoneda('dolar')['serie'][0]['valor'];
                //$this->V_dolar = $this->cambioMoneda('euro')['serie'][0]['valor'];                
                $resultArray = array();
                foreach ($datos as $valor) {
                    $resultArray[] = array(
                        "codigo" => $valor['codigo'],
                        "nombre" => $valor['nombre'],
                        "serie" => $valor['serie'],
                        "marca" => $valor['marca'],
                        "precio" => array(
                            "clp" => $valor['precio'],
                            "euro" => round(($valor['precio'] / $this->V_euro)),
                            "dolar" => round(($valor['precio'] / $this->V_dolar))
                        )
                    );
                }
                $respuesta['productos'] = $resultArray;
                $totHojas = $this->V_cantReg / 10;
                if($totHojas > $P_pagina){
                    if($P_pagina > 1){
                        $pagAnt = $P_pagina - 1;
                        $pagSup = $P_pagina + 1;
                        $respuesta['anterior'] = $this->url.'productos?subcategoria='.$P_sc.'&pagina='.$pagAnt;
                        $respuesta['siguiente'] = $this->url.'productos?subcategoria='.$P_sc.'&pagina='.$pagSup;
                    }else{
                        $pagSup = $P_pagina + 1;
                        $respuesta['anterior'] = '';
                        $respuesta['siguiente'] = $this->url.'productos?subcategoria='.$P_sc.'&pagina='.$pagSup;
                    }
                }
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
    
    private function validarRegistro($P_codigo){
        $query = "SELECT
                        1
                  FROM 
                        ".$this->table."
                  WHERE
                        tbpr_cdg_codigo = '".$P_codigo."';";
        $datos = parent::valQuery($query);
        return $datos;
    }
    
    public function post($json){
        $_respuestas = new respuestas;
        $datos = json_decode($json,true);
        
        if(!isset($datos['codigo']) || !isset($datos['categoria']) || !isset($datos['nombre']) || !isset($datos['serie']) || !isset($datos['marca']) || !isset($datos['precio'])){
            return $_respuestas->error_400();
        }else{
            $this->V_codigo =$datos['codigo'];
            if($this->validarRegistro($this->V_codigo) > 0){
                $respuesta =  $_respuestas->error_400();
                $respuesta['result'] = array(
                    "error" => 'Código Existente'
                );
                return $respuesta;
            }else{
                $this->V_codigo = $datos['codigo'];
                $this->V_categoria = $datos['categoria'];
                $this->V_nombre = $datos['nombre'];
                $this->V_serie = $datos['serie'];
                $this->V_marca = $datos['marca'];
                $this->V_precio = $datos['precio'];
                $resp = $this->insertar();
                if($resp > 0){
                    $respuesta = $_respuestas->responseCreate;
                    $respuesta['result'] = array(
                        "codigo" => $this->V_codigo,
                        "categoria" => $this->V_categoria,
                        "nombre" => $this->V_nombre,
                        "serie" => $this->V_serie,
                        "marca" => $this->V_marca,
                        "precio" => $this->V_precio,
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
                       (tbpr_cdg_codigo, tbsc_cdg_id, tbpr_dsc_nombre, tbpr_cdg_serie, tbpr_dsc_marca, tbpr_nmr_precio) VALUES ('".$this->V_codigo."', ".$this->V_categoria.", '".$this->V_nombre."', '".$this->V_serie."', '".$this->V_marca."',".$this->V_precio.");";        
        $resp = parent::nonQuery($query);
        if($resp > 0){
            return $resp;
        }else{
            return 0;
        }
    }
    
    public function put($json, $P_codigo){
        $_respuestas = new respuestas;
        $datos = json_decode($json,true);
        if(!isset($datos['categoria']) || !isset($datos['nombre']) || !isset($datos['serie']) || !isset($datos['marca']) || !isset($datos['precio'])){
            $respuesta = $_respuestas->error_400();
            $respuesta['status'] = 'error';
            $respuesta['response'] = 400;
            return $respuesta;
        }else{
            if($P_codigo){
                if($this->validarRegistro($P_codigo) > 0){
                    $this->V_codigo = $P_codigo;
                    $this->V_categoria = $datos['categoria'];
                    $this->V_nombre = $datos['nombre'];
                    $this->V_serie = $datos['serie'];
                    $this->V_marca = $datos['marca'];
                    $this->V_precio = $datos['precio'];
                    $resp = $this->editar();
                    if($resp){
                        $respuesta = $_respuestas->response;
                        $respuesta['result'] = array(
                            "categoria" => $this->V_categoria,
                            "nombre" => $this->V_nombre,
                            "serie" => $this->V_serie,
                            "marca" => $this->V_marca,
                            "precio" => $this->V_precio
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
                    tbsc_cdg_id = ".$this->V_categoria.", 
                    tbpr_dsc_nombre = '".$this->V_nombre."',
                    tbpr_cdg_serie = '".$this->V_serie."',
                    tbpr_dsc_marca = '".$this->V_marca."',
                    tbpr_nmr_precio = ".$this->V_precio."
                  WHERE
                       tbpr_cdg_codigo = '".$this->V_codigo."';";        
        $resp = parent::nonQuery($query);
        return $resp;
    }
    
    public function detalle($P_codigo){
        $_respuestas = new respuestas;
        if($P_codigo){
            if($this->validarRegistro($P_codigo) > 0){
                $query = "SELECT
                                tbsc_cdg_id As categoria,
                                tbpr_dsc_nombre AS nombre,
                                tbpr_cdg_serie AS serie,
                                tbpr_dsc_marca AS marca,
                                tbpr_nmr_precio As precio
                          FROM 
                                ".$this->table."
                          WHERE
                                tbpr_cdg_codigo = '".$P_codigo."';";
                $datos = parent::obtenerDatos($query);
                $respuesta =  $_respuestas->response;
                $valores = $this->cambioMoneda();
                $stock = $this->consultarStock($P_codigo);
                $resultArray = array();
                foreach ($datos as $valor) {
                    $resultArray[] = array(
                        "categoria" => $valor['categoria'],
                        "nombre" => $valor['nombre'],
                        "serie" => $valor['serie'],
                        "marca" => $valor['marca'],
                        "precio" => array(
                            "clp" => $valor['precio'],
                            "euro" => round(($valor['precio'] / $this->V_euro)),
                            "dolar" => round(($valor['precio'] / $this->V_dolar))
                        ),
                        "stock" => $stock
                    );
                }
                $respuesta['result'] = $resultArray;
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
    
    public function delete($P_codigo){
        $_respuestas = new respuestas;
        if($P_codigo){
            if($this->validarRegistro($P_id) > 0){
                $this->V_codigo = $P_codigo;
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
                       tbpr_cdg_codigo = '".$this->V_codigo."';";        
        $resp = parent::nonQuery($query);
        return $resp;
    }
}
?>