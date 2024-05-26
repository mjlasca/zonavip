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

class cursos extends fs_controller
{
    
    public $cursos;
    public $allow;
    public $productoshotmart;
    public $chselected;
    public $chmiscursos;
    public $post;

    public function __construct()
    {
        parent::__construct(__CLASS__, 'Vista Usuario', 'contenido', false, false);
    }

    public function private_core()
    {
        $this->chmiscursos = false;
        $this->allow = allowusuario($this->user->nick, $this->page->name);
        if($this->user->admin){
            $this->allow = 1;
        }
        
        if($this->allow != "1"){
            $this->allow = 0;
        }

        

        $this->productoshotmart = new hotmartuser();
        $this->productoshotmart->user = $this->user->nick;
       
        $this->cursos = new hotmartproductos();

        $vec = [];
        $this->chselected = [];

        for( $i = 0; $i < count($this->cursos->get_categoriascursos()) ; $i++){
            if(isset( $_POST["chcategoria".$i]) ){
                $vec[] = $_POST["chcategoria".$i];
                $this->chselected[] = true ;
            }else{
                $this->chselected[] = false ;
            }
        }

        var_dump($this->chselected);

        if(count($vec)>0){
            $this->cursos->categoriacurso = $vec;
        }

        if(isset($_POST["ch_miscursos"])){
            $this->cursos->miscursos = true;  
        }
       
    }


    public function accessscontent($content = ""){
        
        if(  $this->accesoclub3e($content) == 1 )
            return 1;
        if( $this->allow != 0  || $this->aprobaracceso($content) )
            return 1;
            
        return 0;
    }
    

    public function accesoclub3e($content = ""){

        $club3e = new coreclub3e();

        $fecnow = date("Y-m-d");
        if( $club3e->get_user_curse_access($this->user->nick, $content) )
            return 1;
        return 0;
         
    }

    public function aprobaracceso($producto){
        if($producto == "Club de Macros")
            $producto = "1125293";
        if($producto == "Descargas")
            $producto = "1125294";
        $this->productoshotmart->idproducto = $producto;
        return $this->productoshotmart->all_user_producto();
        
    }
  
}
