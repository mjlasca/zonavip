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
class certificate extends \fs_model
{
    /*
     * Llave primaria
     * @var integer
     */
    public $reg;
    /*
     * certificate name
     * @var string
     */
    public $name;
    /*
     * Html text
     * @var string
     */
    public $body;

    /*
     * Background image
     * @var url
     */
    public $img_bg;

    /*
     * ID product
     * @var int
     */
    public $product_id;

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
        
        parent::__construct('certificate', $data); /// aquí indicamos el NOMBRE DE LA TABLA
        if ($data) {
            $this->reg = $data['reg'];
            $this->name = $data['name']??null;
            $this->body = $data['body']??null;
            $this->img_bg = $data['img_bg']??null;
            $this->ultmod = $data['ultmod']??null;
            $this->estado = $data['estado']??null;
            $this->product_id = $data['product_id']??null;
            
        } else {
            $this->reg = null;
            $this->body = null;
            $this->img_bg = null;
            $this->ultmod = date("Y-m-d h:i:s");
            $this->estado = 1;
            $this->product_id = null;
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

    public function get()
    {
        $sql = "SELECT * FROM " . $this->table_name . " WHERE reg = " . $this->var2str($this->reg) . ";";
        $data = $this->db->select($sql);
        $list = [];
        if ($data) {
            foreach ($data as $u) {
                $list[] = new \certificate($u);
            }
        }

        return $list;
    }


    public function get_all()
    {
        $sql = "SELECT * FROM " . $this->table_name . "";
        $data = $this->db->select($sql);
        $list = [];
        if ($data) {
            foreach ($data as $u) {
                $list[] = new \certificate($u);
            }
        }

        return $list;
    }

    /**
     * Devuelve TRUE si el almacén existe
     * @return boolean
     */
    public function exists()
    {
        return $this->db->select("SELECT * FROM " . $this->table_name . " WHERE reg = " . $this->var2str($this->reg) . ";");
    }

    
    
    /**
     * Guarda los datos en la base de datos
     * @return boolean
     */
    public function save()
    {
    
        $this->clean_cache();
        if ($this->exists()) {

            $sql = "UPDATE " . $this->table_name 
            . " SET name = " . $this->var2str($this->name)
            . ", body = " . $this->var2str($this->body)
            . ", img_bg = " . $this->var2str($this->img_bg)
            . ", ultmod = " . $this->var2str($this->ultmod)
            . ", estado = " . $this->var2str($this->estado)
            . ", product_id = " . $this->var2str($this->product_id)
            . "  WHERE reg = " . $this->var2str($this->reg) . ";";

        }else{
            $sql = "INSERT INTO " . $this->table_name . 
            " (name,body,img_bg,ultmod,estado,product_id) VALUES(" 
            . $this->var2str($this->name)
            . "," . $this->var2str($this->body) 
            . "," . $this->var2str($this->img_bg) 
            . "," . $this->var2str($this->ultmod) 
            . "," . $this->var2str($this->estado) 
            . "," . $this->var2str($this->product_id) 
            .");";
        }
        
        if($this->db->exec($sql)){
            return TRUE;
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
        return $this->db->exec("DELETE FROM " . $this->table_name . " WHERE reg = " . $this->var2str($this->reg));
    }


    /**
     * Limpiamos la caché
     */
    private function clean_cache()
    {
        $this->cache->delete('coreclub3e');
    }

    
}
