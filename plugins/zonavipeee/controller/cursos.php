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
  

    public function __construct()
    {
        parent::__construct(__CLASS__, 'Vista Usuario', 'contenido', false, false);
    }

    public function private_core()
    {

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
