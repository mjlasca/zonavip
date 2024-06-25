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
class question extends \fs_model
{
    /*
     * Llave primaria
     * @var integer
     */
    public $reg;
    /*
     * question 
     * @var string
     */
    public $question;

    /*
     * text recomendation for question
     * @var string
     */
    public $recommendation;

    /*
     * text recomendation for question
     * @var string
     */
    public $correct_answer;

    public $quiz_id;

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
        
        parent::__construct('questions', $data); /// aquí indicamos el NOMBRE DE LA TABLA
        if ($data) {
            $this->reg = $data['reg'];
            $this->question = $data['question']??null;
            $this->recommendation = $data['recommendation']??null;
            $this->correct_answer = $data['correct_answer']??null;
            $this->quiz_id = $data['quiz_id']??null;
            $this->ultmod = $data['ultmod']??null;
            $this->estado = $data['estado']??null;
            
        } else {
            $this->reg = null;
            $this->question = null;
            $this->recommendation = null;
            $this->correct_answer = null;
            $this->quiz_id = null;
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

    public function get_quiz()
    {
        $sql = "SELECT * FROM " . $this->table_name . " WHERE quiz_id = " . $this->var2str($this->quiz_id) . ";";
        $data = $this->db->select($sql);
        $list = [];
        if ($data) {
            foreach ($data as $u) {
                $list[] = new \question($u);
            }
        }

        return $list;
    }

    public function get_question()
    {
        $sql = "SELECT * FROM " . $this->table_name . " WHERE question = " . $this->var2str($this->question) . " ORDER BY reg DESC LIMIT 1 ;";
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
        $sql = "SELECT * FROM " . $this->table_name . "";
        $data = $this->db->select($sql);
        $list = [];
        if ($data) {
            foreach ($data as $u) {
                $list[] = new \question($u);
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
            . " SET question = " . $this->var2str($this->question)
            . ", recommendation = " . $this->var2str($this->recommendation)
            . ", correct_answer = " . $this->var2str($this->correct_answer)
            . ", quiz_id = " . $this->var2str($this->quiz_id)
            . ", ultmod = " . $this->var2str($this->ultmod)
            . ", estado = " . $this->var2str($this->estado)
            . "  WHERE reg = " . $this->var2str($this->reg) . ";";

        }else{
            $sql = "INSERT INTO " . $this->table_name . 
            " (question,recommendation,quiz_id,ultmod,estado,correct_answer) VALUES(" 
            . $this->var2str($this->question)
            . "," . $this->var2str($this->recommendation)
            . "," . $this->var2str($this->quiz_id)
            . "," . $this->var2str($this->ultmod) 
            . "," . $this->var2str($this->estado) 
            . "," . $this->var2str($this->correct_answer) 
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
     * Elimina todos los registros con un quiz_id específico
     */
    public function delete_quiz_id(){
        $this->clean_cache();
        return $this->db->exec("DELETE FROM " . $this->table_name . " WHERE quiz_id = " . $this->var2str($this->quiz_id));
    }

    /**
     * Limpiamos la caché
     */
    private function clean_cache()
    {
        $this->cache->delete('question');
    }

    
}
