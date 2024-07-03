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

use FacturaScripts\model\hotmartproductos;
use FacturaScripts\model\question;
use FacturaScripts\model\quiz;

/**
 * Controlador para modificar el perfil del usuario.
 * @author Mario Lasluisa Castaño <mjlasluisa@gmail.com>
 */

class quizzes extends fs_controller
{
    
    public $quizzes;
    public $products;

    public function __construct()
    {
        parent::__construct(__CLASS__, 'Pruebas', 'Zona Vip', true, true);
    }

    public function private_core()
    {   
        $this->quizzes = new quiz();
        $this->products = new hotmartproductos();
        $this->products = $this->products->all_cursos_quiz();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            
            $json = file_get_contents('php://input');
            $this->saveJson($json);
        }

        if( isset( $_GET['reg_quiz'] )){
            $this->getJson($_GET['reg_quiz']);
        }

        if( isset( $_GET['delete_quiz'] )){
            $this->deleteQuiz($_GET['delete_quiz']);
        }
    }   

    public  function saveJson($json)
    {
        $this->template = FALSE;
        $questions = json_decode($json, true);
        $quizData = $questions['questions']['quiz'];
        $quiz = new quiz();
        if(!empty($quizData['reg']))
            $quiz->reg = $quizData['reg'];
        $quiz->name = $quizData['name'];
        $quiz->limit_time = $quizData['time'];
        $quiz->detail = $quizData['detail'];
        $quiz->question_number = count($questions["questions"]['q']) ?? null;
        $quiz->question_visible = $quizData['question_visible'];
        $quiz->question_pass = $quizData['question_pass'];
        $quiz->product_id = $quizData['product_id'];
        $quiz->repeat_quiz = $quizData['repeat_quiz'];
        if($quiz->save()){
            $quiz = $quiz->get_name();
            $question = new question();
            $question->quiz_id = $quiz->reg;
            $question->delete_quiz_id();
            foreach ($questions["questions"]['q'] as $key => $q) {
                $question = new question();
                $question->quiz_id = $quiz->reg;
                $question->question = $q['question'];
                $question->correct_answer =  $q['correct'];
                $question->recommendation = 'recomendación de prueba';
                if($question->save()){
                    $question = $question->get_question();
                    foreach ($q['answers'] as $k => $a) {
                        $answer = new answer();
                        $answer->question_id = $question->reg;
                        $answer->answer = $a['text'];
                        $answer->save();
                    }
                }
            }
        }else{
            echo json_encode($questions);
        }
        header('Content-Type: application/json');
        echo json_encode($quiz);
        
    }

    private function getJson($reg_quiz) {
        $this->template = FALSE;
        $quiz = new quiz();
        $quiz->reg = $reg_quiz;
        $quiz = $quiz->get();
        $result['quiz'] = $quiz;
        $question = new question();
        $question->quiz_id = $quiz->reg;
        $questions = $question->get_quiz();
        
        $result['questions'] = [];
        foreach ($questions as $key => $q) {
            $result['questions'][$key]['q'] = $q->question;
            $result['questions'][$key]['correct'] = $q->correct_answer;
            $answer = new answer();
            $answer->question_id = $q->reg;
            $answer = $answer->get_question();
            foreach ($answer as $k => $val) {
                $result['questions'][$key]['answer'][] = $val->answer;
            }
        }

        header('Content-Type: application/json');
        echo json_encode($result);

    }

    private function deleteQuiz($reg){
        $quiz = new quiz();
        $quiz->reg = $reg;
        $quiz->delete();
    }
    
}
