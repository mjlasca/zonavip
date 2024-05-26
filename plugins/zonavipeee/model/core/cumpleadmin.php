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
 * cumpleadmin
 *
 * @author Mario Lasluisa Castaño <mjlasluisa@gmail.com>
 */
class cumpleadmin extends \fs_model
{
    /*
     * Llave primaria
     * @var integer
     */
    public $reg;

    /*
     * id del registro
     * @var int
     */
    public $asunto;
    /*
     * id del registro
     * @var int
     */
    public $enlace;
    /*
     * usuario asignado
     * @var varchar
     */
    public $mensaje;
    /*
     * usuario que registra
     * @var varchar
     */
    public $useredit;
    /*
     * Fecha registro
     * @var datetime
     */
    public $ultmod;

    

    public function __construct($data = FALSE)
    {
        
        parent::__construct('cumpleadmin', $data); /// aquí indicamos el NOMBRE DE LA TABLA
        if ($data) {
            $this->reg = $data['reg'];
            $this->asunto = $data['asunto'];
            $this->enlace = $data['enlace'];
            $this->mensaje = $data['mensaje'];
            $this->useredit = $data['useredit'];
            $this->ultmod = $data['ultmod'];
        } else {
            $this->reg = null;
            $this->asunto = null;
            $this->enlace = null;
            $this->mensaje = null;
            $this->useredit = null;
            $this->ultmod = null;
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
     * @return \cumpleadmin|boolean
     */
    public function get($reg)
    {
        
        $sql = "SELECT * FROM " . $this->table_name . " WHERE  reg = " . $this->var2str($reg);
        $data = $this->db->select($sql);
        if ($data) {
            return new \cumpleadmin($data[0]);
        }
        return FALSE;
        
    }

   

    /**
     * Devuelve TRUE si el almacén existe
     * @return boolean
     */
    public function exists()
    {
        return $this->db->select("SELECT * FROM " . $this->table_name . " WHERE reg = " . $this->var2str($this->reg) . ";");
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
                . " SET asunto = " . $this->var2str($this->asunto)
                . ", enlace = " . $this->var2str($this->enlace)
                . ", mensaje = " . $this->var2str($this->mensaje)
                . ", useredit = " . $this->var2str($this->useredit)
                . ", ultmod = " . $this->var2str($this->ultmod)
                . "  WHERE reg = " . $this->var2str($this->reg) . ";";
            }else{
                $sql = "INSERT INTO " . $this->table_name . " (reg,asunto,enlace,mensaje,useredit,ultmod) VALUES
                (" . $this->var2str($this->reg)
                . "," . $this->var2str($this->asunto) 
                . "," . $this->var2str($this->enlace) 
                . "," . $this->var2str($this->mensaje) 
                . "," . $this->var2str($this->useredit)
                . "," . $this->var2str($this->ultmod) . ");";
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
        return $this->db->exec("DELETE FROM " . $this->table_name . " WHERE reg = " . $this->var2str($this->reg) . ";");
    }
    


    /**
     * Limpiamos la caché
     */
    private function clean_cache()
    {
        $this->cache->delete('cumpleadmin');
    }

    
}
