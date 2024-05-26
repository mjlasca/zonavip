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
 * cumple
 *
 * @author Mario Lasluisa Castaño <mjlasluisa@gmail.com>
 */
class cumple extends \fs_model
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
    public $date;
    /*
     * usuario asignado
     * @var varchar
     */
    public $usercumple;
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

    /*
     * Último envío año que se celebró
     * @var int
     */
    public $ultenvio;

    public function __construct($data = FALSE)
    {
        
        parent::__construct('cumple', $data); /// aquí indicamos el NOMBRE DE LA TABLA
        if ($data) {
            $this->reg = $data['reg'];
            $this->date = $data['date'];
            $this->usercumple = $data['usercumple'];
            $this->useredit = $data['useredit'];
            $this->ultmod = $data['ultmod'];
            $this->ultenvio = $data['ultenvio'];
        } else {
            $this->reg = null;
            $this->date = null;
            $this->usercumple = null;
            $this->useredit = null;
            $this->ultmod = null;
            $this->ultenvio = null;
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
     * @return \cumple|boolean
     */
    public function get_user($user)
    {
        
        $sql = "SELECT * FROM " . $this->table_name . " WHERE usercumple = '".$user."'";
        $data = $this->db->select($sql);
        if ($data) {
            return new \cumple($data[0]);
        }
        return FALSE;
        
    }


   
    
    /**
     * Devuelve el evento con el reg
     * @param int $reg
     * @return \cumple|boolean
     */
    public function get($reg)
    {
        
        $sql = "SELECT * FROM " . $this->table_name . " WHERE  reg = " . $this->var2str($reg);
        $data = $this->db->select($sql);
        if ($data) {
            return new \cumple($data[0]);
        }
        return FALSE;
        
    }

   

    /**
     * Devuelve TRUE si el almacén existe
     * @return boolean
     */
    public function exists()
    {
        return $this->db->select("SELECT * FROM " . $this->table_name . " WHERE usercumple = " . $this->var2str($this->usercumple) . ";");
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
            if (!$this->exists()) {
               
                $sql = "INSERT INTO " . $this->table_name . " (date,usercumple,useredit,ultmod,ultenvio) VALUES
                      (" . $this->var2str($this->date)
                      . "," . $this->var2str($this->usercumple) 
                      . "," . $this->var2str($this->useredit) 
                      . "," . $this->var2str($this->ultmod)
                    . "," . $this->var2str($this->ultenvio) . ");";
            

                if($this->db->exec($sql)){
                    return TRUE;
                }else{
                    return FALSE;
                }

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
     * Devuelve un array con todos los cumplees
     * @return \cumple
     */
    public function all($mes, $principiosemana, $finsemana)
    {
        $this->clean_cache();
        /// leemos esta lista de la caché
        $listaa = $this->cache->get_array('cumple');
        if (empty($listaa)) {
            /// si no está en caché, leemos de la base de datos
            $sql = "SELECT * FROM " . $this->table_name . " WHERE MONTH(date) = '".$mes."' AND DAY(date) BETWEEN '".$principiosemana."' AND '".$finsemana."'  AND ( ultenvio <> YEAR(NOW()) OR ultenvio IS NULL ) ";
            
            $data = $this->db->select($sql);
            if ($data) {
                foreach ($data as $a) {
                    $listaa[] = new \cumple($a);
                }
            }

            /// guardamos la lista en caché
            $this->cache->set('cumple', $listaa);
        }

        return $listaa;
    }


    /**
     * Actualiza los años de los cumpleañeros, el ultenvio representa el último año celebrado
     * @return \cumple
     */
    public function update_cumple($mes, $principiosemana, $finsemana)
    {
        $sql = "UPDATE  " . $this->table_name . " SET ultenvio  = YEAR(NOW())  WHERE (MONTH(date) = '".$mes."' AND DAY(date) BETWEEN '".$principiosemana."' AND '".$finsemana."')  AND ( ultenvio <> YEAR(NOW()) OR ultenvio IS NULL )";

        return $this->db->exec($sql);

    }

    /**
     * Limpiamos la caché
     */
    private function clean_cache()
    {
        $this->cache->delete('cumple');
    }

    
}
