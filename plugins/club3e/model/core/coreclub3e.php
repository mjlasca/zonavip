<?php
/**
 * This file is part of FacturaScrregts
 * Copyright (C) 2013-2018 Carlos Garcia Gomez <neorazorx@gmail.com>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 * 
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
namespace FacturaScripts\model;


/**
 * coreclub3e
 *
 * @author Mario Lasluisa Castaño <mjlasluisa@gmail.com>
 */
class coreclub3e extends \fs_model
{
    /*
     * Llave primaria
     * @var integer
     */
    public $reg;
    /*
     * usuario del registro
     * @var string
     */
    public $usuario;
    /*
     * Nombre del usuario
     * @var string
     */
    public $nombre;
    /*
     * Nombre del usuario
     * @var string
     */
    public $fecha_inicia;
    /*
     * Nombre del usuario
     * @var string
     */
    public $fecha_expira;
    /*
     * Fecha registro
     * @var datetime
     */
    public $ultmod;

    /*
     * usuario asignado
     * @var bool
     */
    public $estado;


    public function __construct($data = FALSE)
    {
        
        parent::__construct('coreclub3e', $data); /// aquí indicamos el NOMBRE DE LA TABLA
        if ($data) {
            $this->reg = $data['reg'];
            $this->usuario = $data['usuario'];
            $this->nombre = $data['nombre'];
            $this->fecha_inicia = $data['fecha_inicia'];
            $this->fecha_expira = $data['fecha_expira'];
            $this->ultmod = $data['ultmod'];
            $this->estado = $data['estado'];
            
        } else {
            $this->reg = null;
            $this->usuario = null;
            $this->nombre = null;
            $this->fecha_inicia = null;
            $this->fecha_expira = null;
            $this->ultmod = date("Y-m-d h:i:s");
            $this->estado = 1;
        }
    }

    protected function install()
    {
        
    }

    public function model_class_name()
    {
        return __CLASS__;
    }

    public function primary_column()
    {
        return 'reg';
    }


    /**
     * Devuelve el evento con el reg
     * @param int $reg
     * @return \coreclub3e|boolean
     */
    public function get_user()
    {
        $listaa = [];
        if (empty($listaa)) {
            $sql = "SELECT reg,usuario, nombre, DATE(fecha_inicia) as fecha_inicia, DATE(fecha_expira) as fecha_expira,estado, ultmod FROM " . $this->table_name . " WHERE estado = 1 AND  usuario = "
             . $this->var2str($this->usuario)." ORDER BY nombre DESC";
            $data = $this->db->select($sql);
            if ($data) {
                foreach ($data as $a) {
                    $listaa[] = new \coreclub3e($a);
                }
            }
        }

        return $listaa;
        
    }
    
    /**
     * Devuelve el evento con el reg
     * @param int $reg
     * @return \coreclub3e|boolean
     */
    public function get_user_active($user, $fec)
    {
        $sql = "SELECT * FROM " . $this->table_name . " WHERE estado = 1 AND DATE(fecha_inicia) <= DATE('".$fec."') AND DATE(fecha_expira) >= DATE('".$fec."') AND  usuario = ". $this->var2str($user)." ORDER BY nombre DESC ";

        return $this->db->select($sql);
    }

    public function get_all()
    {
        $sql = "SELECT * FROM " . $this->table_name . " ORDER BY nombre DESC ";
        return $this->db->select($sql);
    }

    /**
     * Devuelve el evento con el reg
     * @param int $reg
     * @return \coreclub3e|boolean
     */
    public function get_user_curse_access($user, $idcurso)
    {
        $sum = 0;
        $fec = date("Y-m-d");
        $sql = "SELECT * FROM " . $this->table_name . " WHERE estado = 1 AND fecha_inicia <= '".$fec."' AND fecha_expira >= '".$fec."' AND  usuario = ". $this->var2str($user)." ORDER BY nombre DESC ";

        $cclub = $this->db->select($sql);
        if( $cclub ){
            $sql = "SELECT * FROM hotmartproductos WHERE curso = '".$idcurso."' AND ( ( date(fechapublicacion) <= date('".$cclub[0]["fecha_expira"]."') AND  date(fechapublicacion) >= date('".$cclub[0]["fecha_inicia"]."') ) OR cursobaseclub3e = 1 ) ";
            
            if( $this->db->select($sql) ){
                return true;
            }
        }

        return false;
        
    }

   

    /**
     * Devuelve TRUE si el almacén existe
     * @return boolean
     */
    public function exists()
    {
        return $this->db->select("SELECT * FROM " . $this->table_name . " WHERE usuario = " . $this->var2str($this->usuario) . ";");
    }

    
    
    
    private function test(){
        return TRUE;
    }

    /**
     * Guarda los datos en la base de datos
     * @return boolean
     */
    public function save()
    {
        if ($this->test()) {
            $this->clean_cache();
                if ($this->exists()) {

                    $sql = "UPDATE " . $this->table_name 
                    . " SET usuario = " . $this->var2str($this->usuario)
                    . ", nombre = " . $this->var2str($this->nombre)
                    . ", fecha_inicia = " . $this->var2str($this->fecha_inicia)
                    . ", fecha_expira = " . $this->var2str($this->fecha_expira)
                    . ", ultmod = " . $this->var2str($this->ultmod)
                    . ", estado = " . $this->var2str($this->estado)
                    . "  WHERE usuario = " . $this->var2str($this->usuario) . ";";

                }else{
                    $sql = "INSERT INTO " . $this->table_name . 
                    " (usuario,nombre,fecha_inicia, fecha_expira, ultmod,estado) VALUES(" 
                    . $this->var2str($this->usuario)
                    . "," . $this->var2str($this->nombre) 
                    . "," . $this->var2str($this->fecha_inicia) 
                    . "," . $this->var2str($this->fecha_expira) 
                    . "," . $this->var2str($this->ultmod) 
                    . "," . $this->var2str($this->estado) 
                    .");";
                }
               
                

                if($this->db->exec($sql)){
                    return TRUE;
                }else{
                    return FALSE;
                }

            
        }

        return FALSE;
    }



    /**
     * Elimina el almacén
     * @return bool
     */
    public function delete()
    {
        $this->clean_cache();
        return $this->db->exec("DELETE FROM " . $this->table_name . " WHERE reg = " . $this->var2str($this->reg) . " OR regmensajeresponde = " . $this->var2str($this->reg) . ";");
    }


    /**
     * Limpiamos la caché
     */
    private function clean_cache()
    {
        $this->cache->delete('coreclub3e');
    }

    
}
