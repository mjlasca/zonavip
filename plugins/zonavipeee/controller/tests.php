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

/**
 * Controlador para modificar el perfil del usuario.
 * @author Mario Lasluisa Castaño <mjlasluisa@gmail.com>
 */

class tests extends fs_controller
{
    
    public $registros;
    public $productos;
    public $testcurso;

    public function __construct()
    {
        parent::__construct(__CLASS__, 'Tests Cursos', 'Zona Vip', false, false);
    }

    public function private_core()
    {
        $this->productos = new hotmartproductos();
        $this->testcurso = new testcursos();

        
        if(isset($_POST["idcurso"])){
            $this->guardar();
        }
        if(isset($_REQUEST["delete"])){
            $this->eliminar($_REQUEST["delete"]);
        }
        if(isset($_GET["getpreguntas"])){
            $this->getpreguntas($_GET["getpreguntas"]);
        }

        if(isset($_GET["getrespuestas"])){
            $this->getrespuestas($_GET["getrespuestas"],$_GET["idpregunta"]);
        }
    
    }


    private function guardar(){

        $test = new testcursos();
        $test->idcurso = $_POST["idcurso"];
        if(!$test->get_idcurso()){
            for($i = 1; $i <= $_POST["contpreguntas"]; $i++){
                $test = new testcursos();
                $test->idcurso = $_POST["idcurso"];
                $test->pregunta = $_POST["pregunta".$i];
                $test->ultmod = date("Y-m-d H:i:s");
                $test->useredit = $this->user->nick;
                if($test->save()){
                    for($k = 1; $k <= 4 ; $k++){
                        $preguntas = new respuestastest();
                        $preguntas->idcurso = $_POST["idcurso"];
                        $preguntas->idpregunta = $test->reg;
                        $preguntas->orden = $k;
                        $preguntas->respuesta = $_POST["respuesta".$i.$k];
                        if($_POST["correcta".$i] == $k)
                            $preguntas->correcta = 1;
                        $preguntas->ultmod = $test->ultmod;
                        $preguntas->useredit = $test->useredit;
                        $preguntas->save();
                    }
                }else{
                    $this->new_error_msg("Hubo un error al guardar la pregunta ".$i);
                    exit;
                }
                
            }
        }else{
            $this->new_error_msg("El test de éste curso ya existe");
        }
        
    }

    private function eliminar($idcurso){
        $test = new testcursos();
        $test->idcurso = $idcurso;
        if($test->delete())
            $this->new_message("Test del curso eliminado con éxito");
    }


    public function getpreguntas($idcurso){
        $this->template = FALSE;

        $test = new testcursos();
        $test->idcurso = $idcurso;
        header('Content-Type: application/json');
        echo json_encode($test->all_curso());

    }

    public function getrespuestas($idcurso,$regpregunta){
        $this->template = FALSE;

        $respuesta = new respuestastest();
        $respuesta->idcurso = $idcurso;
        $respuesta->idpregunta = $regpregunta;
        header('Content-Type: application/json');
        echo json_encode($respuesta->all_pregunta());

    }

    


}
