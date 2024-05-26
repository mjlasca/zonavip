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
 * respuestastest
 *
 * @author Mario Lasluisa Castaño <mjlasluisa@gmail.com>
 */
class respuestastest extends \fs_model
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
    public $idpregunta;
    /*
     * respuesta 
     * @var varchar
     */
    public $respuesta;
    /*
     * user edit
     * @var varchar
     */
    public $orden;
    /*
     * user edit
     * @var varchar
     */
    public $correcta;
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
    

    

    public function __construct($data = FALSE)
    {
        
        parent::__construct('respuestastest', $data); /// aquí indicamos el NOMBRE DE LA TABLA
        if ($data) {
            $this->reg = $data['reg'];
            $this->idcurso = $data['idcurso'];
            $this->idpregunta = $data['idpregunta'];
            $this->respuesta =  $data['respuesta'];
            $this->orden = $data['orden'];
            $this->correcta = $data['correcta'];
            $this->useredit = $data['useredit'];
            $this->ultmod = $data['ultmod'];
            
        } else {
            $this->reg = null;
            $this->idcurso = null;
            $this->idpregunta = null;
            $this->respuesta =  null;
            $this->orden = null;
            $this->correcta = null;
            $this->useredit = null;
            $this->ultmod = date("Y-m-d H:i:s");
            
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
     * @return \respuestastest|boolean
     */
    public function get($reg)
    {
        
        $sql = "SELECT * FROM " . $this->table_name . " WHERE  reg = " . $this->var2str($reg);
        $data = $this->db->select($sql);
        if ($data) {
            return new \respuestastest($data[0]);
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
               
                $sql = "INSERT INTO " . $this->table_name . " (idcurso,idpregunta,respuesta,orden,correcta,useredit,ultmod) VALUES
                      (" . $this->var2str($this->idcurso)
                      . "," . $this->var2str($this->idpregunta) 
                      . "," . $this->var2str($this->respuesta) 
                      . "," . $this->var2str($this->orden) 
                      . "," . $this->var2str($this->correcta) 
                      . "," . $this->var2str($this->useredit) 
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
     * Devuelve un array con todos los respuestastestes
     * @return \respuestastest
     */
    public function all_pregunta()
    {
        $this->clean_cache();
        /// leemos esta lista de la caché
        $listaa = $this->cache->get_array('respuestastest');
        if (empty($listaa)) {
            /// si no está en caché, leemos de la base de datos
            $sql = "SELECT * FROM " . $this->table_name . " WHERE idcurso = '".$this->idcurso."' AND idpregunta = '".$this->idpregunta."'  ORDER BY reg ASC ";
            
            $data = $this->db->select($sql);
            if ($data) {
                foreach ($data as $a) {
                    $listaa[] = new \respuestastest($a);
                }
            }
            /// guardamos la lista en caché
            $this->cache->set('respuestastest', $listaa);
        }

        return $listaa;
    }


    /**
     * Devuelve un array con todos los respuestastestes
     * @return \respuestastest
     */
    public function all_curso()
    {
        $this->clean_cache();
        /// leemos esta lista de la caché
        $listaa = $this->cache->get_array('respuestastest');
        if (empty($listaa)) {
            /// si no está en caché, leemos de la base de datos
            $sql = "SELECT * FROM " . $this->table_name . " WHERE idcurso = '".$this->idcurso."'  ORDER BY reg ASC ";
            
            $data = $this->db->select($sql);
            if ($data) {
                foreach ($data as $a) {
                    $listaa[] = new \respuestastest($a);
                }
            }
            /// guardamos la lista en caché
            $this->cache->set('respuestastest', $listaa);
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
        return $this->db->exec("DELETE FROM " . $this->table_name . " WHERE reg = " . $this->var2str($this->reg) . ";");
    }


    /**
     * Limpiamos la caché
     */
    private function clean_cache()
    {
        $this->cache->delete('respuestastest');
    }

    
}
