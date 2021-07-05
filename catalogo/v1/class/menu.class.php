<?php
require_once 'conexion/conexion.php';
require_once 'respuestas.class.php';

class menu extends conexion{
    
    private $tableP = 'tbl_menu';
    private $tableS = 'tbl_categoria';
    private $tableSC = 'tbl_subcategoria';
    private $txt = '';
    
    public function listarMenu(){
        $query = "SELECT
                        tbme_cdg_id AS id,
                        tbme_dsc_nombre AS nombre_menu,
                        tbme_nmr_orden AS menu_orden
                  FROM 
                        ".$this->tableP."
                  WHERE
                        tbme_sts_estado = 'ACT'
                  ORDER BY
                        tbme_nmr_orden ASC;";
        $datos = parent::obtenerDatos($query);
        return $datos;
    }
    
    public function listarCategoria(){
        $query = "SELECT
                        tbme_cdg_id AS id,
                        tbme_dsc_nombre AS nombre_menu,
                        tbme_nmr_orden AS menu_orden
                  FROM 
                        tbl_menu
                  WHERE
                        tbme_sts_estado = 'ACT'
                  ORDER BY
                        tbme_nmr_orden ASC;";
        $datos = parent::obtenerDatos($query);
        return $datos;
    }
    
    public function listarSCMenu(){
        $query = "SELECT
                        tbsc_cdg_id AS id,
                        tbsc_dsc_nombre AS nombre_menu,
                        tbsc_nmr_orden AS menu_orden
                  FROM 
                        tbl_subcategoria
                  WHERE
                        tbsc_sts_estado = 'ACT'
                  ORDER BY
                        tbsc_nmr_orden ASC;";
        $datos = parent::obtenerDatos($query);
        return $datos;
    }
}
?>