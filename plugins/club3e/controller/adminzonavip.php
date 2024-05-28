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

class adminzonavip extends fs_controller
{
    
    public $registros;
    public $productos;
    public $offset;
    public $limit;
    
    
    public function __construct()
    {
        parent::__construct(__CLASS__, 'Administración', 'Zona Vip', true, true);
    }

    public function private_core()
    {
        $this->registros = new zonavipdb();
        $this->productos = new hotmartproductos();
        
        $this->offset = 0;
        $this->limit = 20;

        if(isset($_POST["buscar"])){
            $this->registros->busqueda = $_POST["buscar"];
        }

        if(isset($_POST["paga"])){
            $this->registros->pagoFiltro = "SI";
        }
        if(isset($_POST["gratis"])){
            $this->registros->pagoFiltro = "NO";
        }

        if(isset($_POST["grupofiltro"])){
            $this->registros->grupoFiltro = $_POST["grupofiltro"];
        }

        if(isset($_POST["paga"]) && isset($_POST["gratis"])){
            $this->registros->pagoFiltro = "";
        }
        
        if(isset($_POST["nombre"])){
            $this->guardar();
        }

        if(isset($_POST["cantfilas"])){
            $this->limit = $_POST["cantfilas"];
        }

        if(isset($_REQUEST["deleteregis"])){
            $this->eliminar($_REQUEST["deleteregis"]);
        }
    }


    private function guardar(){
        $registro = new zonavipdb();
        
        if(isset($_POST["reg"])){
            $registro->reg = $_POST["reg"];
            $registro->id = $_POST["id"];     
        }
        $registro->nombrevideo = $_POST["nombre"];
        $registro->archivodescarga = $_POST["archivo"];
        $registro->idyoutube = $_POST["idyoutube"];
        $registro->pdf = $_POST["pdf"];
        $registro->useredit = $this->user->nick;
        $registro->ultmod = date("Y-m-d H:i:s");
        
        $registro->pago =  $_POST["pago"];
        $registro->idvimeo =  $_POST["idvimeo"];
        
        $registro->urlminiatura =  $_POST["urlminiatura"];
        if(isset($_POST['limit_date'])){
            $registro->limit_date = $_POST['limit_date'];
        }
        if($registro->urlminiatura != ""){ 
            $pos = strpos($registro->urlminiatura, "d/");    
            if($pos){

                $subca = substr($registro->urlminiatura, ($pos+2));
                $pos1 = strpos($subca, "/view");    
                $subca1 = substr($subca, 0,($pos1));
                $registro->urlminiatura = "https://drive.google.com/uc?id=".$subca1;
                
            }
        }
        
        


        if(isset($_POST["grupo"]))
            $registro->grupo =  $_POST["grupo"];
        $registro->categoria =  $_POST["categoria"];
        $registro->curso =  $_POST["curso"];
        $registro->modulocurso =  $_POST["modulocurso"];
        $registro->leccioncurso =  $_POST["leccioncurso"];
        $registro->numeroleccion =  $_POST["numeroleccion"];
        $registro->nombremodulo =  $_POST["nombremodulo"];
        if(isset($_POST["detalle"]))
            $registro->detalle = str_replace("\n","" ,$_POST["detalle"]);

        if(isset($_POST["publicar"])){
            if($_POST["publicar"] == "1")
                $registro->codestado = 1;
            else
                $registro->codestado = 0;
        }


        if(isset($_POST["upload"])){
            if($_POST["upload"])
                $registro->upload = 1;
            else
                $registro->upload = 0;
        }

        if(isset($_POST["detalleupload"]))
            $registro->detalleupload = $_POST["detalleupload"];
            
        if(isset($_POST["tamanofile"]))
            $registro->tamanofile = $_POST["tamanofile"];

        
        if($registro->save()){
            $this->new_message("Registro guardado con éxito");
        }else{
            $this->new_error_msg("Error... no se pudo guardar el registro");
        }
        
    }

    private function eliminar($reg){
        $registro = new zonavipdb();
        $registro->reg = $reg;
        $registro->delete();
    }
    


}
