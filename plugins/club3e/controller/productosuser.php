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

use FacturaScripts\model\hotmartproductos;

require 'vendor/autoload.php';

class productosuser extends fs_controller
{
    public $productos;
    public $quiz;
    public $registerquiz;
    

    public function __construct()
    {
        parent::__construct(__CLASS__, 'Vista Usuario', 'productosuser', false, false);
    }

    public function private_core()
    {
        $this->productos  = new hotmartuser();
        $this->productos->user = $this->user->nick;

        $this->quiz = new quiz();

        if(isset($_REQUEST["pdf"])){
            //if($this->productos->get_user_final()){
                $this->generarCertificado($_REQUEST["pdf"]);
            //}
            
        }
    }
    
    public function generarCertificado($grupo = "1125293"){

        /*$historial = new historialvideos();
        $historial->user = $this->user->nick;
        if(count($historial->certificado_producto($grupo)) > 9){*/
            //ob_clean();
            header('Content-type: application/pdf');
            header('Content-Disposition: inline; filename="Certificado3E.pdf"');
            header('Content-Transfer-Encoding: binary');
            header('Accept-Ranges: bytes');
    
            $month = array(1 => "Enero",2 => "Febrero",3 => "Marzo",4 => "Abril",5 => "Mayo",6 => "Jino",7 => "Julio",8 => "Agosto",9 => "Septiembre",10 => "Octubre",11 => "Noviembre",12 => "Diciembre");
    
            $mpdf = new \Mpdf\Mpdf(['format' => 'Letter', 'orientation' => 'L']);
            
            //$css = file_get_contents("plugins/zonavipeee/view/css/pdfstyle.css");
            $html = "<br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>";
            /*$fechainicia = $this->user->create_date;
            if($fechainicia != ""){
                $mes = date("m", strtotime( $fechainicia));
                $arrmes = ["","ENERO","FEBRERO","MARZO","ABRIL","MAYO","JUNIO","JULIO","AGOSTO","SEPTIEMBRE","OCTUBRE","NOVIEMBRE","DICIEMBRE"];
                $fechainicia = "el ".date("j", strtotime( $fechainicia))." de ".$arrmes[$mes]." del ".date("Y", strtotime( $fechainicia));
                $fechainicia = " desde ".$fechainicia;
            }*/
            $html .= "<div class='nameuser'>".$this->user->nombre."</div>";
            $html .= "<div class='datecertificate'><p>Por pertenecer al Club de Macros de <a href='https://especialistasenexcel.com/'>EpecialistasEnExcel.com</a>, cuyo contenido <br>potencia habilidades en el trabajo con<b> Macros en Excel con VBA</b> </p>";
    
            $html .= "<br><br><br><br>";
            $html .= "Se certifica a los ".date("d")." días del mes de ".$month[intval(date("m"))]." del ".date("Y")."</div>";
    
    
            //$mpdf->SetDefaultBodyCSS('background', "url('plugins/zonavipeee/view/assets/img/certificados/1125293.jpg')");
            //$mpdf->WriteHTML($css, \Mpdf\HTMLParserMode::HEADER_CSS);
            $mpdf->WriteHTML($html, \Mpdf\HTMLParserMode::HTML_BODY);
    
            
            $mpdf->Output();
            //ob_end_flush();
        /*}else{
            $this->new_error_msg("No puede generar el certificado hasta ver un mínimo de 10 vídeos del Producto que intenta generar");
        }*/
            
       

        
    }
}
