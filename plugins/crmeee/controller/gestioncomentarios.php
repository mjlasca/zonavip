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

 
class gestioncomentarios extends fs_controller
{
    public $comentarios;
    public $comentarios1;

    public function __construct()
    {
        parent::__construct(__CLASS__, 'Gestión de comentarios', 'CRM');
    }

    public function private_core()
    {
        $this->comentarios = new comentarios();
        $this->comentarios1 = new comentarios();

        if(isset($_REQUEST["insertmsg"])){
            $this->insertmsg();
        }
        if(isset($_REQUEST["eliminarcomentario"])){
            $this->eliminarcomentario($_REQUEST["eliminarcomentario"]);
        }
    }

    


    public function insertmsg(){
        /// desactivamos la plantilla HTMLelimi
        $this->template = FALSE;
        $comen = new comentarios();
        $comen->regpost = $_REQUEST["post"];
        $comen->mensaje = $_REQUEST["insertmsg"];
        if(isset($_REQUEST["regmensaje"]))
            $comen->regmensajeresponde = $_REQUEST["regmensaje"];
        if(isset($_REQUEST["userresponse"]))            
            $comen->userresponde = $_REQUEST["userresponse"];
        $comen->ultmod = date("Y-m-d H:i:s");
        $comen->user = $_REQUEST["user"];
        $comen->save();
        header('Content-Type: application/json');

        echo json_encode($comen);
    }

    public function eliminarcomentario($reg){
        /// desactivamos la plantilla HTML
        $this->template = FALSE;
        $comen = new comentarios();
        $comen->reg = $reg;
        
        header('Content-Type: application/json');
        echo json_encode($comen->delete());
    }

    
}
