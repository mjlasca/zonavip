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
 * comentarios
 *
 * @author Mario Lasluisa Castaño <mjlasluisa@gmail.com>
 */
class comentarios extends \fs_model
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
    public $userresponde;
    /*
     * usuario asignado
     * @var varchar
     */
    public $regmensajeresponde;
    /*
     * usuario asignado
     * @var varchar
     */
    public $regpost;
    /*
     * usuario que registra
     * @var varchar
     */
    public $user;
    /*
     * Fecha registro
     * @var datetime
     */
    public $ultmod;

    public $responde;
    public $nombre;

    

    public function __construct($data = FALSE)
    {
        
        parent::__construct('comentarios', $data); /// aquí indicamos el NOMBRE DE LA TABLA
        if ($data) {
            $this->reg = $data['reg'];
            $this->mensaje = $data['mensaje'];
            $this->regpost = $data['regpost'];
            $this->userresponde = $data['userresponde'];
            $this->regmensajeresponde = $data['regmensajeresponde'];
            $this->user = $data['user'];
            $this->ultmod = $data['ultmod'];
            if(isset($data['responde']))
                $this->responde = $data['responde'];
            if(isset($data['nombre']))
                $this->nombre = $data['nombre'];
            
        } else {
            $this->reg = null;
            $this->mensaje = null;
            $this->regpost = null;
            $this->userresponde = null;
            $this->regmensajeresponde = null;
            $this->ultmod = date("Y-m-d H:i:s");
            $this->user = null;
            $this->responde = null;
            $this->nombre = null;
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
     * @return \comentarios|boolean
     */
    public function get($reg)
    {
        
        $sql = "SELECT * FROM " . $this->table_name . " WHERE  reg = " . $this->var2str($reg);
        $data = $this->db->select($sql);
        if ($data) {
            return new \comentarios($data[0]);
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
            if (!$this->exists()) {
               
                $sql = "INSERT INTO " . $this->table_name . " (mensaje,regpost,userresponde,regmensajeresponde,user,ultmod) VALUES
                      (" . $this->var2str($this->mensaje)
                      . "," . $this->var2str($this->regpost) 
                      . "," . $this->var2str($this->userresponde) 
                      . "," . $this->var2str($this->regmensajeresponde) 
                      . "," . $this->var2str($this->user) 
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
     * Devuelve un array con todos los comentarioses
     * @return \comentarios
     */
    public function all_post()
    {
        $this->clean_cache();
        /// leemos esta lista de la caché
        $listaa = $this->cache->get_array('comentarios');
        if (empty($listaa)) {
            /// si no está en caché, leemos de la base de datos
            $sql = "SELECT *, (SELECT nombre FROM fs_users t1 WHERE user = t1.nick) as nombre FROM " . $this->table_name . " WHERE regpost = '".$this->regpost."'  ORDER BY reg ASC ";
            
            $data = $this->db->select($sql);
            if ($data) {
                foreach ($data as $a) {
                    $listaa[] = new \comentarios($a);
                }
            }
            /// guardamos la lista en caché
            $this->cache->set('comentarios', $listaa);
        }

        return $listaa;
    }

    /**
     * Devuelve un array con todos los comentarioses
     * @return \comentarios
     */
    public function all_responseuser()
    {
        $this->clean_cache();
        /// leemos esta lista de la caché
        $listaa = $this->cache->get_array('comentarios');
        if (empty($listaa)) {
            /// si no está en caché, leemos de la base de datos
            $sql = "SELECT * FROM " . $this->table_name . " t2 WHERE   t2.user = ".$this->user." AND (SELECT COUNT(*) FROM " . $this->table_name . " t1 WHERE t1.regmensajeresponde = t2.reg AND t1.user = ) > 0   ORDER BY t2.reg ASC ";
            
            $data = $this->db->select($sql);
            if ($data) {
                foreach ($data as $a) {
                    $listaa[] = new \comentarios($a);
                }
            }
            /// guardamos la lista en caché
            $this->cache->set('comentarios', $listaa);
        }

        return $listaa;
    }

    /**
     * Devuelve un array con todos los comentarioses
     * @return \comentarios
     */
    public function all_sinrespuesta()
    {
        $this->clean_cache();
        /// leemos esta lista de la caché
        $listaa = $this->cache->get_array('comentarios');
        if (empty($listaa)) {
            /// si no está en caché, leemos de la base de datos
            $sql = "SELECT * FROM " . $this->table_name . " t2 WHERE   t2.regmensajeresponde = '' AND (SELECT COUNT(*) FROM " . $this->table_name . " t1 WHERE t1.regmensajeresponde = t2.reg) = 0   ORDER BY t2.reg ASC ";
            
            $data = $this->db->select($sql);
            if ($data) {
                foreach ($data as $a) {
                    $listaa[] = new \comentarios($a);
                }
            }
            /// guardamos la lista en caché
            $this->cache->set('comentarios', $listaa);
        }

        return $listaa;
    }

    /**
     * Devuelve un array con todos los comentarioses
     * @return \comentarios
     */
    public function all_respondidos()
    {

        $sql = "SELECT t1.mensaje, t1.reg, t1.regpost, t1.regmensajeresponde,t1.userresponde,t1.user,t1.ultmod, t2.mensaje as responde FROM comentarios t1 INNER JOIN comentarios t2 ON t2.reg = t1.regmensajeresponde  ORDER BY t2.reg ASC";
        
        $data = $this->db->select($sql);
        return $data;
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
        $this->cache->delete('comentarios');
    }

    
}
