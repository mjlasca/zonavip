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
class quiz extends \fs_model
{
    /*
     * Llave primaria
     * @var integer
     */
    public $reg;
    /*
     * quiz name
     * @var string
     */
    public $name;
    /*
     * quiz introduction message 
     * @var string
     */
    public $detail;

    /*
     * Time limit for quiz
     * @var time
     */
    public $limit_time;

    /*
     * question number
     * @var int
     */
    public $question_number;

    /*
     * question visible
     * @var int
     */
    public $question_visible;

    /*
     * number question pass quiz
     * @var int
     */
    public $question_pass;

    /*
     * ID product
     * @var int
     */
    public $product_id;

    /*
     * Tiempo para repetir prueba
     * @var int
     */
    public $repeat_quiz;

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
        
        parent::__construct('quizzes', $data); /// aquí indicamos el NOMBRE DE LA TABLA
        if ($data) {
            $this->reg = $data['reg'];
            $this->name = $data['name']??null;
            $this->detail = $data['detail']??null;
            $this->limit_time = $data['limit_time']??null;
            $this->question_number = $data['question_number']??null;
            $this->question_visible = $data['question_visible']??null;
            $this->question_pass = $data['question_pass']??null;
            $this->ultmod = $data['ultmod']??null;
            $this->estado = $data['estado']??null;
            $this->product_id = $data['product_id']??null;
            $this->repeat_quiz = $data['repeat_quiz']??null;
            
        } else {
            $this->reg = null;
            $this->name = null;
            $this->detail = null;
            $this->limit_time = null;
            $this->question_number = null;
            $this->question_visible = null;
            $this->question_pass = null;
            $this->ultmod = date("Y-m-d h:i:s");
            $this->estado = 1;
            $this->product_id = null;
            $this->repeat_quiz = null;
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
     * 
     */
    public function get_to_curse($product_id_)
    {
        $sql = "SELECT reg,product_id FROM " . $this->table_name . " WHERE product_id = " . $this->var2str($product_id_) . ";";
        $data = $this->db->select($sql);
        $list = [];
        if ($data) {
            foreach ($data as $u) {
                $list = new \quiz($u);
            }
        }
        
        return $list;
    }

    public function get()
    {
        $sql = "SELECT * FROM " . $this->table_name . " WHERE reg = " . $this->var2str($this->reg) . " OR product_id = " . $this->var2str($this->product_id) . " ;";
        $data = $this->db->select($sql);
        $list = [];
        if ($data) {
            foreach ($data as $u) {
                $list = new \quiz($u);
            }
        }

        return $list;
    }

    public function get_name()
    {
        $sql = "SELECT * FROM " . $this->table_name . " WHERE name = " . $this->var2str($this->name) . " ORDER BY reg DESC LIMIT 1 ;";
        $data = $this->db->select($sql);
        $list = [];
        if ($data) {
            foreach ($data as $u) {
                $list = new \quiz($u);
            }
        }

        return $list;
    }


    public function get_all()
    {
        $sql = "SELECT " . $this->table_name . ".*, hotmartproductos.nombre as curso FROM " . $this->table_name . " LEFT JOIN hotmartproductos ON " . $this->table_name . ".product_id = hotmartproductos.reg ";
        return $this->db->select($sql);
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
            . ", detail = " . $this->var2str($this->detail)
            . ", limit_time = " . $this->var2str($this->limit_time)
            . ", question_number = " . $this->var2str($this->question_number)
            . ", question_visible = " . $this->var2str($this->question_visible)
            . ", question_pass = " . $this->var2str($this->question_pass)
            . ", ultmod = " . $this->var2str($this->ultmod)
            . ", estado = " . $this->var2str($this->estado)
            . ", product_id = " . $this->var2str($this->product_id)
            . ", repeat_quiz = " . $this->var2str($this->repeat_quiz)
            . "  WHERE reg = " . $this->var2str($this->reg) . ";";

        }else{
            $sql = "INSERT INTO " . $this->table_name . 
            " (name,detail,limit_time,question_number,question_visible,question_pass,ultmod,estado,product_id,repeat_quiz) VALUES(" 
            . $this->var2str($this->name)
            . "," . $this->var2str($this->detail) 
            . "," . $this->var2str($this->limit_time)
            . "," . $this->var2str($this->question_number) 
            . "," . $this->var2str($this->question_visible) 
            . "," . $this->var2str($this->question_pass) 
            . "," . $this->var2str($this->ultmod) 
            . "," . $this->var2str($this->estado) 
            . "," . $this->var2str($this->product_id) 
            . "," . $this->var2str($this->repeat_quiz) 
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
        $this->cache->delete('quiz');
    }

    
}
