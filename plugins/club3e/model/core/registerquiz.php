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
class registerquiz extends \fs_model
{
    /*
     * Llave primaria
     * @var integer
     */
    public $reg;

    /**
     * id curso
     * @var int
     */
    public $curse_id;

    /**
     * preguntas correctas
     * @var int
     */
    public $correct_questions;

    /**
     * preguntas incorrectas
     * @var int
     */
    public $incorrect_questions;

    /**
     * preguntas null
     * @var int
     */
    public $null_questions;

    /**
     * tiempo que se demoró
     * @var time
     */
    public $quiz_time;


    /**
     * fecha y hora que inició la prueba
     * @var datetime
     */
    public $init_date;

    /**
     * fecha y hora que finalizó la prueba la prueba
     * @var datetime
     */
    public $finish_date;

    /**
     * pasó o no el quiz
     * @var bool
     */
    public $success;

    /**
     * Usuario
     * @var mail
     */
    public $user_id;

    /**
     * array answers
     * @var string
     */
    public $array_answer;

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
        
        parent::__construct('registerquizzes', $data); /// aquí indicamos el NOMBRE DE LA TABLA
        if ($data) {
            $this->reg = $data['reg'];
            $this->curse_id = $data['curse_id']??null;
            $this->correct_questions = $data['correct_questions']??null;
            $this->incorrect_questions = $data['incorrect_questions']??null;
            $this->null_questions = $data['null_questions']??null;
            $this->quiz_time = $data['quiz_time']??null;
            $this->success = $data['success']??null;
            $this->user_id = $data['user_id']??null;
            $this->array_answer = $data['array_answer']??null;
            $this->init_date = $data['init_date']??null;
            $this->finish_date = $data['finish_date']??null;
            $this->ultmod = $data['ultmod']??null;
            $this->estado = $data['estado']??null;
            
        } else {
            $this->reg = null;
            $this->curse_id = null;
            $this->correct_questions = null;
            $this->incorrect_questions = null;
            $this->null_questions = null;
            $this->quiz_time = null;
            $this->success = null;
            $this->ultmod = date("Y-m-d h:i:s");
            $this->estado = 1;
            $this->user_id = null;
            $this->array_answer = null;
            $this->init_date = null;
            $this->finish_date = null;
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
                $list = new \registerquiz($u);
            }
        }

        return $list;
    }

    public function get_user_curse()
    {
        $sql = "SELECT * FROM " . $this->table_name . " WHERE user_id = " . $this->var2str($this->user_id) . " AND curse_id = " . $this->var2str($this->curse_id)." ;";
        
        $data = $this->db->select($sql);
        $list = [];
        if ($data) {
            foreach ($data as $u) {
                $list = new \registerquiz($u);
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
                $list[] = new \registerquiz($u);
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
            . " SET curse_id = " . $this->var2str($this->curse_id)
            . ", correct_questions = " . $this->var2str($this->correct_questions)
            . ", incorrect_questions = " . $this->var2str($this->incorrect_questions)
            . ", null_questions = " . $this->var2str($this->null_questions)
            . ", quiz_time = " . $this->var2str($this->quiz_time)
            . ", success = " . $this->var2str($this->success)
            . ", ultmod = " . $this->var2str($this->ultmod)
            . ", estado = " . $this->var2str($this->estado)
            . ", user_id = " . $this->var2str($this->user_id)
            . ", array_answer = " . $this->var2str($this->array_answer)
            . ", init_date = " . $this->var2str($this->init_date)
            . ", finish_date = " . $this->var2str($this->finish_date)
            . "  WHERE reg = " . $this->var2str($this->reg) . ";";

        }else{
            $sql = "INSERT INTO " . $this->table_name . 
            " (curse_id,correct_questions,incorrect_questions,null_questions,quiz_time,success,ultmod,estado,user_id,array_answer,init_date,finish_date) VALUES(" 
            . $this->var2str($this->curse_id)
            . "," . $this->var2str($this->correct_questions) 
            . "," . $this->var2str($this->incorrect_questions)
            . "," . $this->var2str($this->null_questions) 
            . "," . $this->var2str($this->quiz_time) 
            . "," . $this->var2str($this->success) 
            . "," . $this->var2str($this->ultmod) 
            . "," . $this->var2str($this->estado) 
            . "," . $this->var2str($this->user_id)
            . "," . $this->var2str($this->array_answer)
            . "," . $this->var2str($this->init_date)
            . "," . $this->var2str($this->finish_date)
            .");";
        }
        
        if($this->db->exec($sql)){
            if(!empty($this->finish_date))
                $this->clean_cache();
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
        return $this->db->exec("DELETE FROM " . $this->table_name . " WHERE reg = " . $this->var2str($this->reg));
        $this->clean_cache();
    }


    /**
     * Limpiamos la caché
     */
    private function clean_cache()
    {
        $this->cache->delete('registerquiz');
        $this->cache->delete('getquiz');
    }

    
}
