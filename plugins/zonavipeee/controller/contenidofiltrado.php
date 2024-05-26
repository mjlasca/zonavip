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

class contenidofiltrado extends fs_controller
{
    public $zonavip_registros;
    public $busqueda;
    public $grupo;
    public $pago;
    public $categoria;
    public $allow;
    public $cumplesin;
    public $historial;
    public $productoshotmart;
    public $productos;
    public $cursos;
    public $bandera;
    
    public function __construct()
    {
        parent::__construct(__CLASS__, 'Búsqueda', 'Zona Vip', true, true);
    }

    public function private_core()
    {
        $cumple = new cumple();
        $this->productos = new hotmartproductos();
        $this->historial = new historialvideos();
        $this->historial->user = $this->user->nick;

        $this->cumplesin = false;
        $this->allow = allowusuario($this->user->nick, $this->page->name);
        if($this->user->admin){
            $this->allow = 1;
        }
        
        if($this->allow != "1"){
            $this->allow = 0;
        }

        $this->cursos = new hotmartproductos();

        $this->productoshotmart = new hotmartuser();
        $this->productoshotmart->user = $this->user->nick;
        
        $this->zonavip_registros = new zonavipdb();
        if(isset($_REQUEST["busqueda"])){
            $this->zonavip_registros->busqueda = $_REQUEST["busqueda"];
            $this->busqueda =  $_REQUEST["busqueda"];
        }
        if(isset($_REQUEST["pago"])){
            $this->zonavip_registros->pagoFiltro = $_REQUEST["pago"];
            $this->pago =  $_REQUEST["pago"];
        }
        if(isset($_REQUEST["grupo"])){
            $this->zonavip_registros->grupoFiltro = $_REQUEST["grupo"];
            $this->grupo =  $_REQUEST["grupo"];
        }
        if(isset($_REQUEST["categoria"])){
            $this->zonavip_registros->categoriaFiltro = $_REQUEST["categoria"];
            $this->categoria =  $_REQUEST["categoria"];
        }

        if(!$cumple->get_user($this->user->nick)){
            $this->cumplesin = true;
        }

        $this->bandera = false;

        if( isset( $_POST["bandera"] )){
            $this->bandera = $_POST["bandera"];
        }

        

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
