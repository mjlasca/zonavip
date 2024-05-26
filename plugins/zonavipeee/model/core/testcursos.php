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
 * testcursos
 *
 * @author Mario Lasluisa Castaño <mjlasluisa@gmail.com>
 */
class testcursos extends \fs_model
{
    /*
     * Llave primaria
     * @var integer
     */
    public $reg;
    /*
     * id del curso
     * @var varchar
     */
    public $idcurso;
    /*
     * user edit
     * @var varchar
     */
    public $pregunta;
    /*
     * user edit
     * @var varchar
     */
    public $useredit;
    /**
     * ultmodificación
     * @var datetime
     */
    public $ultmod;

    public $nombre;
    

    

    public function __construct($data = FALSE)
    {
        
        parent::__construct('testcursos', $data); /// aquí indicamos el NOMBRE DE LA TABLA
        if ($data) {
            $this->reg = $data['reg'];
            $this->idcurso = $data['idcurso'];
            $this->pregunta = $data['pregunta'];
            $this->useredit = $data['useredit'];
            $this->ultmod = $data['ultmod'];
            if(isset($data['nombre']))
                $this->nombre = $data['nombre'];
            
        } else {
            $this->reg = null;
            $this->idcurso = null;
            $this->pregunta = null;
            $this->useredit = null;
            $this->ultmod = date("Y-m-d H:i:s");
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
     * @return \testcursos|boolean
     */
    public function get_idcurso()
    {
        
        $sql = "SELECT * FROM " . $this->table_name . " WHERE  idcurso = " . $this->var2str($this->idcurso);
        $data = $this->db->select($sql);
        if ($data) {
            return new \testcursos($data[0]);
        }
        return FALSE;
        
    }

   
    
    /**
     * Devuelve el evento con el reg
     * @param int $reg
     * @return \testcursos|boolean
     */
    public function get($reg)
    {
        
        $sql = "SELECT * FROM " . $this->table_name . " WHERE  reg = " . $this->var2str($reg);
        $data = $this->db->select($sql);
        if ($data) {
            return new \testcursos($data[0]);
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
               
                $sql = "INSERT INTO " . $this->table_name . " (idcurso,pregunta,useredit,ultmod) VALUES
                      (" . $this->var2str($this->idcurso)
                      . "," . $this->var2str($this->pregunta) 
                      . "," . $this->var2str($this->useredit) 
                    . "," . $this->var2str($this->ultmod) . ");";
                    
                if($this->db->exec($sql)){
                    $sql = "SELECT reg FROM ". $this->table_name ." ORDER BY reg DESC ";
                    $this->reg = $this->db->select($sql)[0]["reg"];
                    return TRUE;
                }else{
                    return FALSE;
                }

            }
        }

        return FALSE;
    }

    /**
     * Devuelve un array con todos los testcursoses
     * @return \testcursos
     */
    public function all()
    {
        $this->clean_cache();
        /// leemos esta lista de la caché
        $listaa = $this->cache->get_array('testcursos');
        if (empty($listaa)) {
            /// si no está en caché, leemos de la base de datos
            $sql = "SELECT *, (SELECT t1.nombre FROM hotmartproductos t1 WHERE t1.idproducto = idcurso ) as nombre FROM " . $this->table_name . " GROUP BY idcurso ORDER BY reg DESC ";

            $data = $this->db->select($sql);
            if ($data) {
                foreach ($data as $a) {
                    $listaa[] = new \testcursos($a);
                }
            }
            /// guardamos la lista en caché
            $this->cache->set('testcursos', $listaa);
        }

        return $listaa;
    }


    /**
     * Devuelve un array con todos los testcursoses
     * @return \testcursos
     */
    public function all_curso()
    {
        $this->clean_cache();
        /// leemos esta lista de la caché
        $listaa = $this->cache->get_array('testcursos');
        if (empty($listaa)) {
            /// si no está en caché, leemos de la base de datos
            $sql = "SELECT * FROM " . $this->table_name . " WHERE idcurso = '".$this->idcurso."'  ORDER BY reg ASC ";

            $data = $this->db->select($sql);
            if ($data) {
                foreach ($data as $a) {
                    $listaa[] = new \testcursos($a);
                }
            }
            /// guardamos la lista en caché
            $this->cache->set('testcursos', $listaa);
        }

        return $listaa;
    }

    /**
     * Elimina el almacén
     * @return bool
     */
    public function delete()
    {
        $this->clean_cache();

        if($this->db->exec("DELETE FROM " . $this->table_name . " WHERE idcurso = " . $this->var2str($this->idcurso) . ";")){

            return $this->db->exec("DELETE FROM respuestastest WHERE idcurso = " . $this->var2str($this->idcurso) . ";");
        }

    }


    /**
     * Limpiamos la caché
     */
    private function clean_cache()
    {
        $this->cache->delete('testcursos');
    }

    
}
