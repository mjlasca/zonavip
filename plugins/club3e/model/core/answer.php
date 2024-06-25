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
 * coreclub3esss
 *
 * @author Mario Lasluisa Castaño <mjlasluisa@gmail.com>
 */
class answer extends \fs_model
{
    /*
     * Llave primaria
     * @var integer
     */
    public $reg;
    /*
     * answer 
     * @var string
     */
    public $answer;

    /*  
     * foreight key,  question table
     * @var int
     */
    public $question_id;

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
        
        parent::__construct('answers', $data); /// aquí indicamos el NOMBRE DE LA TABLA
        if ($data) {
            $this->reg = $data['reg'];
            $this->answer = $data['answer']??null;
            $this->question_id = $data['question_id']??null;
            $this->ultmod = $data['ultmod']??null;
            $this->estado = $data['estado']??null;
            
        } else {
            $this->reg = null;
            $this->answer = null;
            $this->question_id = null;
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

    public function get_question()
    {
        $sql = "SELECT * FROM " . $this->table_name . " WHERE question_id = " . $this->var2str($this->question_id) . ";";
        $data = $this->db->select($sql);
        $list = [];
        if ($data) {
            foreach ($data as $u) {
                $list[] = new \answer($u);
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
                $list[] = new \answer($u);
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
            . " SET answer = " . $this->var2str($this->answer)
            . ", question_id = " . $this->var2str($this->question_id)
            . ", ultmod = " . $this->var2str($this->ultmod)
            . ", estado = " . $this->var2str($this->estado)
            . "  WHERE reg = " . $this->var2str($this->reg) . ";";

        }else{
            $sql = "INSERT INTO " . $this->table_name . 
            " (answer,question_id,ultmod,estado) VALUES(" 
            . $this->var2str($this->answer)
            . "," . $this->var2str($this->question_id) 
            . "," . $this->var2str($this->ultmod) 
            . "," . $this->var2str($this->estado) 
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
        $this->cache->delete('answer');
    }

    
}
