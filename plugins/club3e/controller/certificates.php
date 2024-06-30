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

use FacturaScripts\model\certificate;
use FacturaScripts\model\coreclub3e;

/**
 * Controlador para modificar el perfil del usuario.
 * @author Mario Lasluisa Castaño <mjlasluisa@gmail.com>
 */

class certificates extends fs_controller
{
    
    public $certificates;
    public $preview;
    public $prev;
    public $products;

    public function __construct()
    {
        parent::__construct(__CLASS__, 'Certificados', 'Zona Vip', true, true);
    }

    public function private_core()
    {   
        $this->products = new hotmartproductos();
        $this->products = $this->products->all_cursos_certifcates();

        $this->certificates = new certificate();
        $this->preview = 0;
        $this->prev = [
            'bg' => ''
        ];
        if(isset($_GET['preview'])){
            $this->preview = $_GET['preview'];
            $cer = new certificate();
            $cer->reg = $this->preview;
            $cer = $cer->get();
            $this->prev['bg'] = $cer[0]->img_bg;
            $this->prev['body'] = $cer[0]->body;
        }

        if( isset($_POST['name']) && isset($_POST['img_bg'])){
            $this->save();  
        }

        if(isset($_GET['deleteregis'])){
            $cer = new certificate();
            $cer->reg = $_GET['deleteregis'];
            $cer->delete();
        }

    }   

    private function save() {
        $certificate = new certificate();
        $certificate->reg = $_POST['reg'];
        $certificate->name = $_POST['name'];
        $certificate->img_bg = $_POST['img_bg'];
        $certificate->body = $_POST['body'];
        $certificate->product_id = $_POST['product_id'];

        if($certificate->save()){
            header("Location: ".$this->url());
        }
    }
    
}
