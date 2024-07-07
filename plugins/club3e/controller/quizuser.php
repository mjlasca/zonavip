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
    public $time_quiz;
    public $finish;
    public $result_detail;
    public $fech_repeat;
    
    public function __construct()
    {
        $this->start = 0;
        $this->question_number = 0;
        $this->time_quiz = NULL;
        $this->finish = false;
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
                $this->questions = $this->questions->get_quiz_user($this->quiz->question_visible);
    
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
            if( $quizuser != false && $this->quizuser->success == 0){
                $this->quizuser = $quizuser;
                $this->start = 1;
            }
            
        }
        
        $this->result_detail['correct_questions'] = 0;
        $this->result_detail['incorrect_questions'] = 0;
        $this->result_detail['total_questions'] = !empty($this->questions) ? count($this->questions) : 0;
        
        if($this->quizuser != false && $this->quizuser->estado == 2){
            $this->finish = true;
            $this->time_quiz = -1;

        }

        if(isset($_POST['qnumber'])){
            $this->question_number = $_POST['qnumber'];
        }

        if(isset($_POST['answer']) && $this->quizuser->estado < 2){
            $this->save();
            if(isset($_POST['qnumber'])){
                //condition if submit finish
                if(count($this->questions) == $_POST['qnumber']){
                    $this->quizuser->success = 1;
                    $this->quizuser->finish_date = date("Y-m-d h:i:s");
                    $this->quizuser->estado = 2;
                    $this->quizuser->save();
                    $this->finish = true;
                    $this->time_quiz = -1;
                }
            }
        }

        if(isset($_GET['start']) && $this->start != 1 ){
            $this->start = 1;
            $this->time_quiz = 0;
            $this->quizuser->init_date = date("Y-m-d h:i:s");
            $this->quizuser->save();
        }

        $this->getArrayAnswer();
        $this->getResult();
        
        if($this->finish == false){
            if(!empty($this->quizuser->init_date)){
                $fecha1 = new DateTime($this->quizuser->init_date);
                $fecha2 = new DateTime(date("Y-m-d h:i:s"));
                $diferencia = $fecha1->diff($fecha2);
                $minutes = (($diferencia->days * 24 * 60) + ($diferencia->h * 60) + $diferencia->i);
                $seconds = ($diferencia->days * 24 * 60 * 60) + ($diferencia->h * 60 * 60) + ($diferencia->i * 60) + $diferencia->s;
                $stringMinutes = strlen($minutes) < 2 ? '0'.$minutes : $minutes ;
                $stringSeconds = strlen($seconds % 60) < 2 ? '0'.($seconds % 60) : ($seconds % 60);
                $this->time_quiz =  $stringMinutes  .':'. $stringSeconds;
                if($minutes >= ($this->quiz->limit_time)){
                    $this->quizuser->estado = 2;
                    $this->quizuser->save();
                    $this->time_quiz = -1;
                    $this->finish = true;
                }
            }
        }

        if(!empty($this->quizuser) && $this->quizuser->success == 2){
            $f1 = new DateTime($this->quizuser->ultmod);
            $f2 = new DateTime(date("Y-m-d h:i:s"));
            $diferencia = $f1->diff($f2);
            $minutes = (($diferencia->days * 24 * 60) + ($diferencia->h * 60) + $diferencia->i);
            $hour =  $minutes / 60;
            if($hour >= $this->quiz->repeat_quiz){
                $this->quizuser->delete();
                header('Location: '.$this->url().'&quiz_id='.$this->quiz->reg);
            }else{
                $this->fech_repeat = round( $this->quiz->repeat_quiz - $hour );
            }
            $this->finish = true;
            $this->time_quiz = -1;
            
        }

    }

    private function getResult(){

        if($this->quizuser && !empty($this->quizuser->finish_date)){
            foreach ($this->questions as $key => $value) {
                if($this->array_answers[$value->reg] == $value->correct_answer)
                    $this->result_detail['correct_questions']++;
                else
                    $this->result_detail['incorrect_questions']++;
            }
            
            if( !empty( $this->quizuser) ){
                if($this->result_detail['correct_questions'] >= $this->quiz->question_pass){
                    $this->quizuser->success = 1;
                    $this->quizuser->save();
                }else{
                    $this->finish = true;
                    $this->time_quiz = -1;
                    $this->quizuser->success = 2;
                    $this->quizuser->save();
                }
            }
        }
    }

    private function getArrayAnswer() {
        if($this->quizuser != false){
            $this->quizuser = $this->quizuser->get_user_curse();
            if(!empty($this->quizuser->array_answer) && $this->quizuser->array_answer != ''){
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
