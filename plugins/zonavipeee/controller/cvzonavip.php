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

class cvzonavip extends fs_controller
{

    public function __construct()
    {
        parent::__construct(__CLASS__, 'Vista Usuario', 'CV', false, false);
    }

    public function private_core()
    {
        $this->crearfuncionesapi("api_compraaprobada");
        $this->crearfuncionesapi("api_compracompletada");
        $this->crearfuncionesapi("api_compracancelada");
        $this->crearfuncionesapi("api_comprareembolsada");

        $this->registrocarta();

    }


    public function registrocarta(){
        $user = new fs_user();
        $user = $user->get($this->user->nick);
        $user->activo = $user->activo + 1;
        $user->save();
    }

    public function crearfuncionesapi($desc){
        $fsext = new fs_extension();
        $fsext->name = $desc; /// nombre único para esta extensión
        $fsext->from = __CLASS__;
        $fsext->type = 'api';
        $fsext->text = $desc; /// nombre de la función que queremos añadir
        $fsext->save();
    }
    
}
