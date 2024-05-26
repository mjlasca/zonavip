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

class escuela_cursos extends fs_controller
{


    public $cursos;
    public $curso;
    public $estudiantes;
    public $actividades;
    public $curso_se;

    public function __construct()
    {
        parent::__construct(__CLASS__, 'Cursos', 'Escuela', true, true);
    }

    public function private_core()
    {
        $this->cursos = new hotmartproductos();
        $this->curso = new hotmartproductos();
        $this->estudiantes = new hotmartuser();
        $this->actividades = new zonavipdb();

        $this->curso_se = "";

        if(isset($_POST["curso"])){
            $this->curso->idproducto = $_POST["curso"];
            $this->curso = $this->curso->get_curso($this->curso->idproducto);
            $this->estudiantes->idproducto = $this->curso->idproducto;
            $this->curso_se = $_POST["curso"];
        }

        if(isset($_GET['download'])){
            $this->downloadFile($_GET['download']);
        }

    }

    public function activitysearch(string $lab, string $user_, string $exten)
    {
        $extensionesContenido = explode(",", $exten);

        if(count( $extensionesContenido ) > 0){
            foreach ($extensionesContenido as $value) {
                $nombre_fichero = 'plugins/zonavipeee/view/assets/file/' .$lab.'/'. $user_.$value;
                if (file_exists($nombre_fichero)) {
                    return "<a class='btn btn-primary' href='".$this->url()."&download=".$lab.'/'. $user_.$value."' target='_blank'> Descargar </a>";
                }
            }
        }
       
        return "<span class='bg-danger'>NO HA CARGADO LA ACTIVIDAD</span>";

    }

    public function buscarlaboratorio(string $lab, string $user_, string $exten)
    {
        $nombre_fichero = 'plugins/zonavipeee/view/assets/file/' .$lab.'/'. $user_.'.'.$exten;
        if (file_exists($nombre_fichero)) {
            return "<a class='btn btn-primary' href='".$nombre_fichero."' target='_blank'> Descargar </a>";
        }

        return "<span class='bg-danger'>NO HA CARGADO EL LABORATORIO</span>";

    }

    public function downloadFile($keyPath){
        
        
        $filepath = 'plugins/zonavipeee/view/assets/file/' . $keyPath; // Directorio donde se encuentra el archivo
    
        // Verifica si el archivo existe
        if (file_exists($filepath)) {
            // Configura las cabeceras para la descarga
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . str_replace("/","-", $keyPath) . '"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($filepath));
            // Envía el archivo al navegador
            readfile($filepath);
            exit;
        } else {
            echo 'El archivo no existe.';
        }
        
    }



}
