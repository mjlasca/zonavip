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
 * hotmartproductos
 *
 * @author Mario Lasluisa Castaño <mjlasluisa@gmail.com>
 */
class hotmartproductos extends \fs_model
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
    public $nombre;
    /*
     * usuario asignado
     * @var varchar
     */
    public $idproducto;
    /*
     * usuario que registra
     * @var varchar
     */
    public $valor;
    /*
     * Fecha caducidad
     * @var datetime
     */
    public $useredit;
    /*
     * Fecha registro
     * @var datetime
     */
    public $ultmod;
    /**
     * si es curso, colocamos un  código
     * @var varchar
     */
    public $curso;
    /**
     * url img
     * @var varchar
     */
    public $urlimgcurso;
    /**
     * url img
     * @var varchar
     */
    public $linkpago;
    /**
     * url img
     * @var varchar
     */
    public $categoriacurso;
    /**
     * url img
     * @var varchar
     */
    public $duracioncurso;

    /**
     * dias de vigencia del producto
     * @var int
     */
    public $vigencia;

    /**
     * Producto abierto o cerrado
     * @var bool
     */
    public $abierto;

    /**
     * Product in update
     * @var bool
     */
    public $actualizando;


    public function __construct($data = FALSE)
    {
        
        parent::__construct('hotmartproductos', $data); /// aquí indicamos el NOMBRE DE LA TABLA
        if ($data) {
            $this->reg = $data['reg'];
            $this->nombre = $data['nombre'];
            $this->idproducto = $data['idproducto'];
            $this->valor = $data['valor'];
            $this->useredit = $data['useredit'];
            $this->ultmod = $data['ultmod'];
            $this->curso = $data['curso'];
            $this->urlimgcurso = $data['urlimgcurso'];
            $this->linkpago = $data['linkpago'];
            $this->categoriacurso = $data['categoriacurso'];
            $this->duracioncurso = $data['duracioncurso'];
            $this->vigencia = $data['vigencia'];
            $this->abierto = $data['abierto'];
            $this->actualizando = $data['actualizando'];
            
        } else {
            $this->reg = null;
            $this->nombre = null;
            $this->idproducto = null;
            $this->valor = null;
            $this->useredit = null;
            $this->ultmod = date("Y-m-d H:i:s");
            $this->curso = null;
            $this->linkpago = null;
            $this->categoriacurso = null;
            $this->duracioncurso = null;
            $this->vigencia = null;
            $this->abierto = 0;
            $this->actualizando = 0;
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
     * @return \hotmartproductos|boolean
     */
    public function get($reg)
    {
        
        $sql = "SELECT * FROM " . $this->table_name . " WHERE  reg = " . $this->var2str($reg);
        $data = $this->db->select($sql);
        if ($data) {
            return new \hotmartproductos($data[0]);
        }
        return FALSE;
        
    }

    /**
     * Devuelve el evento con el reg
     * @param int $reg
     * @return \hotmartproductos|boolean
     */
    public function get_curso($idproducto_)
    {
        
        $sql = "SELECT * FROM " . $this->table_name . " WHERE  idproducto = " . $this->var2str($idproducto_);
        $data = $this->db->select($sql);
        if ($data) {
            return new \hotmartproductos($data[0]);
        }
        return FALSE;
        
    }

    /**
     * Devuelve un array con todos los hotmartproductos
     * @return \hotmartproductos
     */
    public function all()
    {
        $this->clean_cache();
        /// leemos esta lista de la caché
        $listaa = $this->cache->get_array('hotmartproductos');
        if (empty($listaa)) {
            
            //approved, canceled, billet_printed, refunded, dispute, completed, blocked, chargeback, delayed, expired, wayting_payment
            $sql = "SELECT * FROM " . $this->table_name . "  ";
            
            $data = $this->db->select($sql);
            if ($data) {
                foreach ($data as $a) {
                    $listaa[] = new \hotmartproductos($a);
                }
            }
            /// guardamos la lista en caché
            $this->cache->set('hotmartproductos', $listaa);
        }

        return $listaa;
    }

    /**
     * Devuelve un array con todos los hotmartproductos
     * @return \hotmartproductos
     */
    public function all_nocursos()
    {
        $this->clean_cache();
        /// leemos esta lista de la caché
        $listaa = $this->cache->get_array('hotmartproductos');
        if (empty($listaa)) {
            
            //approved, canceled, billet_printed, refunded, dispute, completed, blocked, chargeback, delayed, expired, wayting_payment
            $sql = "SELECT * FROM " . $this->table_name . " WHERE (curso = '' OR curso IS NULL)  ";
            
            $data = $this->db->select($sql);
            if ($data) {
                foreach ($data as $a) {
                    $listaa[] = new \hotmartproductos($a);
                }
            }
            /// guardamos la lista en caché
            $this->cache->set('hotmartproductos', $listaa);
        }

        return $listaa;
    }


    /**
     * Devuelve un array con todos los hotmartproductos
     * @return \hotmartproductos
     */
    public function all_carrusel()
    {
        $this->clean_cache();
        /// leemos esta lista de la caché
        $listaa = $this->cache->get_array('hotmartproductos');
        if (empty($listaa)) {
            
            //approved, canceled, billet_printed, refunded, dispute, completed, blocked, chargeback, delayed, expired, wayting_payment
            $sql = "SELECT * FROM " . $this->table_name . " WHERE curso != '' AND curso IS NOT NULL  ORDER BY nombre ASC ";
            
            $data = $this->db->select($sql);
            if ($data) {
                foreach ($data as $a) {
                    $listaa[] = new \hotmartproductos($a);
                }
            }
            /// guardamos la lista en caché
            $this->cache->set('hotmartproductos', $listaa);
        }

        return $listaa;
    }



    /**
     * Devuelve TRUE si el almacén existe
     * @return boolean
     */
    public function exists()
    {
        return $this->db->select("SELECT * FROM " . $this->table_name . " WHERE reg = " . $this->var2str($this->reg) . " ;");
    }

    /**
     * Devuelve TRUE si el almacén existe
     * @return boolean
     */
    public function get_categoriascursos()
    {
        $sql = "SELECT UPPER(categoriacurso) as catego FROM " . $this->table_name . " WHERE categoriacurso IS NOT NULL AND categoriacurso != ''    GROUP BY categoriacurso ORDER BY categoriacurso ASC ;";

        return $this->db->select($sql);
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
                    . " SET nombre = " . $this->var2str($this->nombre)
                    . ", idproducto = " . $this->var2str($this->idproducto)
                    . ", valor = " . $this->var2str($this->valor)
                    . ", useredit = " . $this->var2str($this->useredit)
                    . ", ultmod = " . $this->var2str($this->ultmod)
                    . ", curso = " . $this->var2str($this->curso)
                    . ", urlimgcurso = " . $this->var2str($this->urlimgcurso)
                    . ", linkpago = " . $this->var2str($this->linkpago)
                    . ", duracioncurso = " . $this->var2str($this->duracioncurso)
                    . ", categoriacurso = " . $this->var2str($this->categoriacurso)
                    . ", abierto = " . $this->var2str($this->abierto)
                    . ", actualizando = " . $this->var2str($this->actualizando)
                    . ", vigencia = " . $this->var2str($this->vigencia)
                    . "  WHERE reg = " . $this->var2str($this->reg) . ";";

                }else{
                    $sql = "INSERT INTO " . $this->table_name . " (nombre,idproducto,valor,useredit,ultmod,curso,urlimgcurso,linkpago,duracioncurso,abierto,actualizando,vigencia,categoriacurso) VALUES(" . $this->var2str($this->nombre)
                    . "," . $this->var2str($this->idproducto) 
                    . "," . $this->var2str($this->valor) 
                    . "," . $this->var2str($this->useredit) 
                    . "," . $this->var2str($this->ultmod) 
                    . "," . $this->var2str($this->curso) 
                    . "," . $this->var2str($this->urlimgcurso)
                    . "," . $this->var2str($this->linkpago)
                    . "," . $this->var2str($this->duracioncurso)
                    . "," . $this->var2str($this->abierto)
                    . "," . $this->var2str($this->actualizando)
                    . "," . $this->var2str($this->vigencia)
                    . ",UPPER('".$this->categoriacurso . "'));";
                }
               
                

                if($this->db->exec($sql)){
                    return TRUE;
                }else{
                    $this->new_error_msg($sql);

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
        $this->cache->delete('hotmartproductos');
    }

    
}
