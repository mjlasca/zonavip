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

//require 'vendor/autoload.php';
require_once 'plugins/club3e/assets/fpdf/fpdf.php';

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
            $this->generarCertificado();
        }
    }

    public function generarCertificado(){
        $certificate = new certificate();
        $certificate->product_id = $_GET['curse'];
        $certificate = $certificate->get_curse();
        if(!empty($certificate)){
            header('Content-Type: text/html; charset=UTF-8');
            // Crear una instancia del PDF
            $pdf = new PDF('L');
            $pdf->AddPage();
            $pdf->SetFont('Arial');
            $pdf->image = $certificate->img_bg;
            $html = str_replace('[user_name]', $this->user->nombre, $certificate->body);
            $html =  iconv('UTF-8', 'windows-1252',$html);
            $pdf->SetY(100);
            $pdf->WriteHTML($html);
            $pdf->Output('archivo.pdf', 'D');
        }else
            $this->new_error_msg('No han generado el certicado, comunícate con el administrador');
        
    }
}

class PDF extends FPDF
{
    public $image = '/img_zonavip/certificados%20macros%20v3.png';
    // Método para escribir HTML
    function WriteHTML($html)
    {
        // Intérprete de HTML
        $html = str_replace("\n", ' ', $html);
        $a = preg_split('/<(.*)>/U', $html, -1, PREG_SPLIT_DELIM_CAPTURE);
        foreach ($a as $i => $e)
        {
            if ($i % 2 == 0)
            {
                // Texto
                if(!empty($tag) && $tag != 'P')
                    $this->Write(5, '   '.$e);
                else
                    $this->Write(5, '            '.$e);
            }
            else
            {
                // Etiqueta
                if ($e[0] == '/')
                {
                    $this->CloseTag(strtoupper(substr($e, 1)));
                }
                else
                {
                    // Extraer atributos
                    $a2 = explode(' ', $e);
                    $tag = strtoupper(array_shift($a2));
                    $attr = array();
                    foreach ($a2 as $v)
                    {
                        if (preg_match('/([^=]*)=["\']?([^"\']*)/', $v, $a3))
                        {
                            $attr[strtoupper($a3[1])] = $a3[2];
                        }
                    }
                    $this->OpenTag($tag, $attr);
                }
            }
        }
        
    }

    function OpenTag($tag, $attr)
    {
        // Etiquetas de apertura
        $this->SetStyle($tag, true);
        if ($tag == 'A')
        {
            $this->HREF = $attr['HREF'];
        }
        $this->Ln(10);
    }

    function CloseTag($tag)
    {
        // Etiquetas de cierre
        $this->SetStyle($tag, false);
        if ( in_array($tag,['H1','H2','h3']))
        {
            $this->Ln(5);
        }
        if ($tag == 'A')
        {
            $this->HREF = '';
        }
    }

    function SetStyle($tag, $enable)
    {
        // Establecer estilo y tamaño de fuente
        if ($tag == 'B' || $tag == 'I' || $tag == 'U') {
            $this->$tag += ($enable ? 1 : -1);
            $style = '';
            foreach (array('B', 'I', 'U') as $s) {
                if ($this->$s > 0) {
                    $style .= $s;
                }
            }
            $this->SetFont('', $style);
        }

        // Establecer tamaño de fuente para encabezados
        if ($enable) {
            switch ($tag) {
                case 'H1':
                    $this->SetFontSize(54);
                    break;
                case 'H2':
                    $this->SetFontSize(48);
                    break;
                case 'H3':
                    $this->SetFontSize(28);
                    break;
                case 'H4':
                    $this->SetFontSize(18);
                    break;
                case 'H5':
                    $this->SetFontSize(16);
                    break;
                case 'H6':
                    $this->SetFontSize(14);
                    break;
            }
        } else {
            // Restablecer tamaño de fuente estándar después de los encabezados
            if (in_array($tag, array('H1', 'H2', 'H3', 'H4', 'H5', 'H6'))) {
                $this->SetFontSize(12);
            }
        }

    }

    // Función para agregar una imagen de fondo
    function Header()
    {
        // Carga la imagen
        $this->Image($this->image, 0, 0, $this->GetPageWidth(), $this->GetPageHeight());
    }
    
    // Función para agregar un pie de página (opcional)
    function Footer()
    {
        // Posición a 1.5 cm del final
        $this->SetY(-15);
        // Arial italic 8
        $this->SetFont('Arial', 'I', 8);
        // Número de página
        $this->Cell(0, 10, 'Page ' . $this->PageNo(), 0, 0, 'C');
    }
}
