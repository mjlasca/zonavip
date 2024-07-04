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
    public $question_number;
    public $question_total;
    public $array_questions;
    public $array_answers;
    
    public function __construct()
    {
        $this->start = 0;
        $this->question_number = 0;
        parent::__construct(__CLASS__, 'Quiz', '', false, false);
    }

    public function private_core()
    {
        $this->quizuser = new registerquiz();
        $this->quizuser->user_id = $this->user->nick;

        if( isset($_GET['quiz_id']) || isset($_GET['product_id']) ){

            if( isset($_GET['qnumber']) ){
                $this->question_number = $_GET['qnumber'];
            }
            $this->quizuser->curse_id = $_GET['product_id'] ?? null;
            

            $this->quiz = new quiz();
            $this->quiz->reg = $_GET['quiz_id'] ?? null;
            $this->quiz->product_id = $_GET['product_id'] ?? null;
            $this->quiz = $this->quiz->get();
            if($this->quiz != false){

                if(empty($this->quizuser->curse_id)){
                    $this->quizuser->curse_id = $this->quiz->product_id;
                }
                
                $this->questions = new question();
                $this->questions->quiz_id = $this->quiz->reg;
                $this->questions = $this->questions->get_quiz_user();
    
                $this->answers = [];
                foreach ($this->questions as $key => $q) {
                    
                    $answer = new answer();
                    $answer->question_id = $q->reg;
                    $answer = $answer->get_question();
                    $this->array_answers[$q->reg] = '';
                    foreach ($answer as $k => $val) {
                        $this->answers[$q->reg][] = [ 'reg' => $val->reg, 'answer' => $val->answer];
                    }
                }
                $this->question_total = count($this->questions) - 1;
            }
            $quizuser = $this->quizuser->get_user_curse();
            if( $quizuser != false && $quizuser->success == 0){
                $this->start = 1;
            }
            
        }

        if(isset($_POST['qnumber'])){
            $this->question_number = $_POST['qnumber'];
        }

        if(isset($_POST['answer'])){
            $this->save();
            if(isset($_POST['qnumber'])){
                //condition if submit finish
                if(count($this->questions) == $_POST['qnumber']){
                    $this->quizuser->success = 1;
                    $this->quizuser->save();
                    
                }
            }
        }

        if(isset($_GET['start']) && $this->start != 1 ){
            $this->start = 1;
            $this->quizuser->save();
        }

        
        $this->getArrayAnswer();
    }

    private function getArrayAnswer() {
        $this->quizuser = $this->quizuser->get_user_curse();
        if($this->quizuser != false){
            if($this->quizuser->array_answer != ''){
                $arra = (array)json_decode($this->quizuser->array_answer);
                foreach ($arra as $key => $value) {
                    $this->array_answers[$key] = $value;
                }
            }
        }
    }
    
    
    private function save(){
        $this->quizuser = $this->quizuser->get_user_curse();
        
        if($this->quizuser != false){
            if($this->quizuser->array_answer != ''){
                $arra = (array)json_decode($this->quizuser->array_answer);
                $arra[$_POST['question']] = $_POST['answer'];
                $this->quizuser->array_answer = json_encode($arra);
                $this->quizuser->save();
            }
            else{
                $arra = [
                    $_POST['question'] => $_POST['answer']
                ];
                $this->quizuser->array_answer = json_encode($arra);
                $this->quizuser->save();
            }
        }
    }
    
}
