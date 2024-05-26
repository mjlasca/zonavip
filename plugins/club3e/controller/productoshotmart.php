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

class productoshotmart extends fs_controller
{
    
    public $registros;
    public $productos;

    public function __construct()
    {
        parent::__construct(__CLASS__, 'Productos', 'Zona Vip', true, true);
    }

    public function private_core()
    {
        $this->productos = new hotmartproductos();
        $test = new testcursos();
        $respuestas = new respuestastest();
        
        if(isset($_POST["nombre"])){
            $this->guardar();
        }
        if(isset($_REQUEST["deleteregis"])){
            $this->eliminar($_REQUEST["deleteregis"]);
        }
    }


    private function guardar(){
        $prod = new hotmartproductos();
        
        if(isset($_POST["reg"])){
            $prod->reg = $_POST["reg"];
        }

        $prod->nombre = $_POST["nombre"];
        $prod->idproducto = $_POST["idproducto"];
        $prod->valor = $_POST["valor"];
        $prod->useredit = $this->user->nick;
        $prod->ultmod = date("Y-m-d H:i:s");
        $prod->curso = $_POST["curso"];
        $prod->urlimgcurso = $_POST["urlimg"];
        if(isset($_POST['view']))
            $prod->view = $_POST['view'];
        if($prod->urlimgcurso != ""){ 
            $pos = strpos($prod->urlimgcurso, "d/");    
            if($pos){

                $subca = substr($prod->urlimgcurso, ($pos+2));
                $pos1 = strpos($subca, "/view");    
                $subca1 = substr($subca, 0,($pos1));
                $prod->urlimgcurso = "https://drive.google.com/uc?id=".$subca1;
                
            }
        }

        $prod->linkpago = $_POST["linkpago"];
        $prod->categoriacurso = $_POST["categoriacurso"];
        $prod->duracioncurso = $_POST["duracioncurso"];
        $prod->vigencia = $_POST["vigencia"];
        if(isset($_POST["fechapublicacion"])){
            if($_POST["fechapublicacion"] != ""){
                $f=strtotime($_POST["fechapublicacion"]);
                $newformat=date('Y-m-d',$f);
                $prod->fechapublicacion = $newformat;
            }
        }

        if(isset($_POST["abierto"]))
            $prod->abierto = $_POST["abierto"];
        if(isset($_POST["actualizando"]))
            $prod->actualizando = $_POST["actualizando"];
        if(isset($_POST["cursobaseclub3e"]))
            $prod->cursobaseclub3e = $_POST["cursobaseclub3e"];
        if(isset($_POST["verencursos"]))
            $prod->verencursos = $_POST["verencursos"];

        if($prod->save()){
            $this->new_message("Registro guardado con éxito");
        }else{
            $this->new_error_msg("Error... no se pudo guardar el registro");
        }
        
    }

    private function eliminar($reg){
        $prod = new hotmartproductos();
        $prod->reg = $reg;
        $prod->delete();
    }
    


}
