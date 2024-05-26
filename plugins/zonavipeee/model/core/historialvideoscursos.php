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
 * historialvideoscursos
 *
 * @author Mario Lasluisa Castaño <mjlasluisa@gmail.com>
 */
class historialvideoscursos extends \fs_model
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
    public $regpost;
    /*
     * usuario asignado
     * @var varchar
     */
    public $nombrevideo;
    
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
    public $curso;

    

    public function __construct($data = FALSE)
    {
        
        parent::__construct('historialvideoscursos', $data); /// aquí indicamos el NOMBRE DE LA TABLA
        if ($data) {
            $this->reg = $data['reg'];
            $this->regpost = $data['regpost'];
            $this->nombrevideo = $data['nombrevideo'];
            $this->user = $data['user'];
            $this->ultmod = $data['ultmod'];
            $this->curso = $data['curso'];
            
        } else {
            $this->reg = null;
            $this->regpost = null;
            $this->nombrevideo = null;
            $this->ultmod = null;
            $this->user = null;
            $this->curso = null;
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
     * @return \historialvideoscursos|boolean
     */
    public function get($reg)
    {
        
        $sql = "SELECT * FROM " . $this->table_name . " WHERE  reg = " . $this->var2str($reg);
        $data = $this->db->select($sql);
        if ($data) {
            return new \historialvideoscursos($data[0]);
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
               
                $sql = "INSERT INTO " . $this->table_name . " (regpost,nombrevideo,user,ultmod,curso) VALUES
                      (" . $this->var2str($this->regpost)
                      . "," . $this->var2str($this->nombrevideo) 
                      . "," . $this->var2str($this->user) 
                      . "," . $this->var2str($this->ultmod)
                    . "," . $this->var2str($this->curso) . ");";
            

                
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
        return $this->db->exec("DELETE FROM " . $this->table_name . " WHERE regpost = " . $this->var2str($this->regpost) . " AND user = " . $this->var2str($this->user) . ";");
    }

    /**
     * Devuelve un array con todos los historialvideoscursoses
     * @return \historialvideoscursos
     */
    public function lastvideo($user__,$curso__)
    {
        /// leemos esta lista de la caché
        
            /// si no está en caché, leemos de la base de datos
            $sql = "SELECT MAX(regpost) as ult FROM " . $this->table_name . " WHERE curso = '".$curso__."' AND user = '".$user__."' ";
            
            $data = $this->db->select($sql);
            
        return $data;
    }
    
    /**
     * Devuelve un array con todos los historialvideoscursoses
     * @return \historialvideoscursos
     */
    public function all_vistos()
    {
        /// leemos esta lista de la caché
        
            /// si no está en caché, leemos de la base de datos
            $sql = "SELECT t2.reg, t2.nombrevideo, t2.idyoutube, t2.idvimeo,t2.pago,t2.grupo FROM " . $this->table_name . " t1 INNER JOIN zonavipdb t2 ON t1.user = '".$this->user."' AND t2.reg = t1.regpost GROUP BY t1.regpost ORDER BY MAX(t1.ultmod) DESC LIMIT 8  ";
            $data = $this->db->select($sql);
            
        return $data;
    }

     /**
     * Devuelve un array con todos los historialvideoscursoses
     * @return \historialvideoscursos
     */
    public function certificado_producto($grupo)
    {
            /// si no está en caché, leemos de la base de datos
            $sql = "SELECT t1.regpost FROM " . $this->table_name . " t1 INNER JOIN zonavipdb t2 ON t1.user = '".$this->user."' AND t2.reg = t1.regpost AND t2.grupo = '".$grupo."' GROUP BY t1.regpost  ";

            $data = $this->db->select($sql);
            
        return $data;
    }


    /**
     * Devuelve un array con todos los historialvideoscursoses
     * @return \historialvideoscursos
     */
    public function populares()
    {
            $sql = "SELECT t2.reg, t2.nombrevideo, t2.idyoutube, t2.idvimeo,t2.grupo,t2.pago,  COUNT( t1.regpost ) as popular FROM " . $this->table_name . " t1 INNER JOIN zonavipdb t2 ON  t2.reg = t1.regpost GROUP BY t1.regpost ORDER BY popular DESC limit 8";
            $data = $this->db->select($sql);
            
        return $data;
    }

    /**
     * Devuelve un array con todos los historialvideoscursoses
     * @return \historialvideoscursos
     */
    public function get_checkcurso($regpost,$user)
    {
            $sql = "SELECT COUNT(*) as visto FROM " . $this->table_name . " WHERE regpost = '".$regpost."' AND user =  '".$user."' ";
            $data = $this->db->select($sql)[0]["visto"];
            
        return $data;
    }



    /**
     * Limpiamos la caché
     */
    private function clean_cache()
    {
        $this->cache->delete('historialvideoscursos');
    }

    
}
