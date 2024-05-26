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

class transmision extends fs_controller
{

    public $transmisiones;
    public $productos;
    public $producto;

    public function __construct()
    {
        parent::__construct(__CLASS__, 'Transmisiones', 'Zona Vip', true, true);
    }

    public function private_core()
    {
        $this->transmisiones = new transmisiones();
        $this->productos = new hotmartproductos();

        


        if(isset($_REQUEST["deleteregis"])){
            $this->eliminar($_REQUEST["deleteregis"]);
        }
        if(isset($_POST["nombre"])){
            $this->guardar();
        }
    }


    private function guardar(){
        $trans = new transmisiones();

        
        
        if(isset($_POST["reg"])){
            $trans->reg = $_POST["reg"];
        }
        $trans->nombre = $_POST["nombre"];
        $trans->link = $_POST["link"];
        if(strlen($_POST["hora"]) < 6){
            $_POST["hora"] = $_POST["hora"] .":00";
        }
        $trans->fechainicia = $_POST["fechainicia"]." ".$_POST["hora"];
        $trans->fechasemuestra = $_POST["fechasemuestra"];
        
        $trans->pago = $_POST["pago"];
        $trans->producto = $_POST["producto"];
        if($trans->producto != ""){
            $trans->descripcion = "Club de Macros";
        }
        $trans->useredit = $this->user->nick;
        $trans->ultmod = date("Y-m-d H:i:s");
        if($trans->save()){
            $this->new_message("Registro guardado con éxito");
        }else{
            $this->new_error_msg("Error... no se pudo guardar el registro");
        }
        
    }

    private function eliminar($reg){
        $trans = new transmisiones();
        $trans->reg = $reg;
        $trans->delete();
    }

}
