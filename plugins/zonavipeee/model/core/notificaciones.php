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

use GuzzleHttp\Psr7\Response;

/**
 * notificaciones
 *
 * @author Mario Lasluisa Castaño <mjlasluisa@gmail.com>
 */
class notificaciones extends \fs_model
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
    public $mensaje;
    /*
     * usuario asignado
     * @var varchar
     */
    public $tipo;
    /*
     * usuario asignado
     * @var varchar
     */
    public $accion;
    
    /*
     * usuario asignado
     * @var varchar
     */
    public $destinatario;
    
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
        
        parent::__construct('notificaciones', $data); /// aquí indicamos el NOMBRE DE LA TABLA
        if ($data) {
            $this->reg = $data['reg'];
            $this->mensaje = $data['mensaje'];
            $this->tipo = $data['tipo'];
            $this->accion = $data['accion'];
            $this->destinatario = $data['destinatario'];
            $this->ultmod = $data['ultmod'];
            $this->estado = $data['estado'];
            
        } else {
            $this->reg = null;
            $this->mensaje = null;
            $this->tipo = null;
            $this->accion = null;
            $this->destinatario = null;
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
     * @return \notificaciones|boolean
     */
    public function get_user($user)
    {
        $listaa = [];
        if (empty($listaa)) {
            $sql = "SELECT * FROM " . $this->table_name . " WHERE estado = 1 AND  destinatario = " . $this->var2str($user)." ORDER BY reg DESC";
            $data = $this->db->select($sql);
            if ($data) {
                foreach ($data as $a) {
                    $listaa[] = new \notificaciones($a);
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
               
                $sql = "INSERT INTO " . $this->table_name . " (mensaje,tipo,accion,destinatario,ultmod,estado) VALUES
                      (" . $this->var2str($this->mensaje)
                      . "," . $this->var2str($this->tipo) 
                      . "," . $this->var2str($this->accion) 
                      . "," . $this->var2str($this->destinatario) 
                      . "," . $this->var2str($this->ultmod) 
                    . "," . $this->var2str($this->estado) . ");";
                    
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
     * Deshabilitar
     * @return bool
     */
    public function deshabilitar()
    {
        $this->clean_cache();
        return $this->db->exec("UPDATE notificaciones SET estado = 0 WHERE reg = " .$$this->var2str($this->reg). " ");
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
        $this->cache->delete('notificaciones');
        $this->cache->delete('notificaciones_user');
    }

    
}
