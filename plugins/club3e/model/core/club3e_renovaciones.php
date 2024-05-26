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
 * club3e_renovaciones
 *
 * @author Mario Lasluisa Castaño <mjlasluisa@gmail.com>
 */
class club3e_renovaciones extends \fs_model
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
    public $fecha_renovacion;
    /*
     * Nombre del usuario
     * @var string
     */
    public $fecha_expira;
    /*
     * Nombre del usuario
     * @var string
     */
    public $registro_pago;
    /*
     * Fecha registro
     * @var datetime
     */
    public $ultmod;

    


    public function __construct($data = FALSE)
    {
        
        parent::__construct('club3e_renovaciones', $data); /// aquí indicamos el NOMBRE DE LA TABLA
        if ($data) {
            $this->reg = $data['reg'];
            $this->usuario = $data['usuario'];
            $this->fecha_renovacion = $data['fecha_renovacion'];
            $this->fecha_expira = $data['fecha_expira'];
            $this->registro_pago = $data['registro_pago'];
            $this->ultmod = $data['ultmod'];
            
        } else {
            $this->reg = null;
            $this->usuario = null;
            $this->fecha_renovacion = null;
            $this->fecha_expira = null;
            $this->registro_pago = null;
            $this->ultmod = date("Y-m-d h:i:s");
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
     * @return \club3e_renovaciones|boolean
     */
    public function get_user($user)
    {
        $listaa = [];
        if (empty($listaa)) {
            $sql = "SELECT * FROM " . $this->table_name . " WHERE estado = 1 AND  destinatario = " . $this->var2str($user)." ORDER BY reg DESC";
            $data = $this->db->select($sql);
            if ($data) {
                foreach ($data as $a) {
                    $listaa[] = new \club3e_renovaciones($a);
                }
            }
        }

        return $listaa;
        
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
            if (!$this->exists()) {
               
                $sql = "INSERT INTO " . $this->table_name . " (usuario,fecha_renovacion,fecha_expira,registro_pago,ultmod) VALUES
                      (" . $this->var2str($this->usuario)
                      . "," . $this->var2str($this->fecha_renovacion) 
                      . "," . $this->var2str($this->fecha_expira) 
                      . "," . $this->var2str($this->registro_pago) 
                    . "," . $this->var2str($this->ultmod) . ");";
                    

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
        return $this->db->exec("DELETE FROM " . $this->table_name . " WHERE reg = " . $this->var2str($this->reg) . " OR regmensajeresponde = " . $this->var2str($this->reg) . ";");
    }


    /**
     * Limpiamos la caché
     */
    private function clean_cache()
    {
        $this->cache->delete('club3e_renovaciones');
    }

    
}
