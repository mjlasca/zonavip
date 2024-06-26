<?php
/*
 * This file is part of FacturaScripts
 * Copyright (C) 2013-2017  Carlos Garcia Gomez  neorazorx@gmail.com
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

use FacturaScripts\model\answer;
use FacturaScripts\model\question;
use FacturaScripts\model\quiz;

/**
 * Controlador para modificar el perfil del usuario.
 * @author Mario Lasluisa Castaño <mjlasluisa@gmail.com>
 */

class quizuser extends fs_controller
{
    
    public $quizuser;
    public $quiz;
    public $questions;
    public $answers;
    public $start;

    public function __construct()
    {
        $this->start = 0;
        parent::__construct(__CLASS__, 'Quiz', '', false, false);
    }

    public function private_core()
    {
        
        if( isset($_GET['quiz_id']) ){
            $this->quiz = new quiz();
            $this->quiz->reg = $_GET['quiz_id'];
            $this->quiz = $this->quiz->get();

            $this->questions = new question();
            $this->questions->quiz_id = $this->quiz->reg;
            $this->questions = $this->questions->get_quiz();

            $this->answers = [];
            foreach ($this->questions as $key => $q) {
                $answer = new answer();
                $answer->question_id = $q->reg;
                $answer = $answer->get_question();
                foreach ($answer as $k => $val) {
                    $this->answers[$key][] = $val->answer;
                }
            }
        }

        if(isset($_POST['reg_quiz']) && $_POST['reg_quiz'] != ""){
            $this->save($_POST['reg_quiz']);
        }

        if(isset($_GET['start'])){
            $this->start = 1;
        }
    }
    
    
    private function save(){
        
    }
    
}
