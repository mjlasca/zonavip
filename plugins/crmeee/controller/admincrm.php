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

 
class admincrm extends fs_controller
{
    public $hotmartuser;
    public $fecha1;
    public $fecha2;
    public $usuarios;
    public $tipofecha;
    public $ingresocv;
    public $invitaciones;
    public $conversion;
    public $contconversiones;
    public $inactividad;
    public $activo;
    public $enlacefiltros;
    


    public function __construct()
    {
        parent::__construct(__CLASS__, 'Administración', 'CRM');
    }

    public function private_core()
    {
        $this->hotmartuser = new hotmartuser();
        $this->usuarios = new fs_user();
        $this->invitaciones = new invitaciones();
        $this->conversion = "";
        $this->contconversiones = 0;
        $this->inactividad = 1;

        $this->enlacefiltros = "";
        

        if(isset($_REQUEST["usuario"])){
            $this->hotmartuser->user = $_REQUEST["usuario"];
        }

        $this->ingresocv = 0;

        if(isset($_REQUEST["ingresocv"])){
            $this->ingresocv = $_REQUEST["ingresocv"];
            $this->usuarios->getingresocv = $_REQUEST["ingresocv"];
            $this->enlacefiltros .="&ingresocv=".$_REQUEST["ingresocv"];
        }

        if(isset($_REQUEST["estado"])){
            $this->hotmartuser->estado = $_REQUEST["estado"];
            $this->enlacefiltros .="&estado=".$_REQUEST["estado"];
        }
        if(isset($_REQUEST["inactividad"])){
            $this->inactividad = $_REQUEST["inactividad"];
            $this->enlacefiltros .="&inactividad=".$_REQUEST["inactividad"];
        }
        if(isset($_REQUEST["tipofecha"])){
            $this->usuarios->tipofecha = $_REQUEST["tipofecha"];
            $this->hotmartuser->ingresocreacion = $_REQUEST["tipofecha"];
            $this->tipofecha = $_REQUEST["tipofecha"];
            $this->enlacefiltros .="&tipofecha=".$_REQUEST["tipofecha"];
        }

        if(isset($_REQUEST["fecha1"])){
            if($_REQUEST["fecha1"] != ""){
                $this->hotmartuser->fecha1 = date_format( new DateTime($_REQUEST["fecha1"]),"Y-m-d H:i:s");
                $this->fecha1 = $_REQUEST["fecha1"] ;
                $this->usuarios->fecha1 = date_format( new DateTime($_REQUEST["fecha1"]),"Y-m-d");
                $this->enlacefiltros .="&fecha1=".$_REQUEST["fecha1"];
            }
                
        }

        if(isset($_REQUEST["fecha2"])){
            if($_REQUEST["fecha2"] != ""){
                $this->hotmartuser->fecha2 = date_format( new DateTime($_REQUEST["fecha2"]),"Y-m-d H:i:s");
                $this->fecha2 = $_REQUEST["fecha2"] ;
                $this->usuarios->fecha2 = date_format( new DateTime($_REQUEST["fecha2"]),"Y-m-d");
                $this->enlacefiltros .="&fecha2=".$_REQUEST["fecha2"];
            }
                
        }
        if(isset($_REQUEST["conversion"])){
            if($_REQUEST["conversion"]){
                $this->conversion = $_REQUEST["conversion"];
                $this->enlacefiltros .="&conversion=".$_REQUEST["conversion"];
            }
        }
        if(isset($_REQUEST["enviarcorreosinactivos"])){
            $this->ciclocorreos();
            $this->enlacefiltros .="&enviarcorreosinactivos=".$_REQUEST["enviarcorreosinactivos"];
        }
        if(isset($_REQUEST["activo"])){
            $this->activo = $_REQUEST["activo"];
            $this->enlacefiltros .="&activo=".$_REQUEST["activo"];
            if($this->activo == "1"){
                $this->hotmartuser->fechacaducidad = " >= DATE('" .date("Y-m-d H:i:s")."')";
            }
            if($this->activo == "0"){
                $this->hotmartuser->fechacaducidad = " <= DATE('" .date("Y-m-d H:i:s")."')";
            }
        }

        

        $this->hotmartuser->all();
        $this->usuarios->all_prospectos();


        if(isset($_GET["descarga"])){
            if($_GET["descarga"] = "clientes"){
                $this->descarga();
            }
        }

        if(isset($_GET["descarga1"])){
                $this->descarga1();
        }



    }

    public function ciclocorreos(){
        foreach($this->usuarios->all_inactivo($this->inactividad) as $correos){
            $this->enviarcorreo( $correos["nick"]);
        }
    }

    public function active_users(){
        $hotuser = new hotmartuser();
        $club3e = new coreclub3e();
        
        
        $arrresult = [];
        


        foreach ($club3e->get_all() as $key => $value) {
            $arrresult[] = [
                'mail' => $value['usuario'],
                'name' => $value['nombre'],
                'product' => 'Club Automatización',
                'validity' => $value['fecha_expira'],
                'active' => $this->compararFechas($value['fecha_expira'])
            ];
        }

        foreach ($hotuser->get_users() as $key => $value) {
            $arrresult[] = [
                'mail' => $value['user'],
                'name' => !empty($value['nombre']) ? $value['nombre'] : "",
                'product' => $value['nombreproducto'],
                'validity' => $value['fechacaducidad'],
                'active' => $this->compararFechas($value['fechacaducidad'])
            ];
        }
        

        return $arrresult;
    }


    function compararFechas($fecha) {
        if(!empty($fecha)){
            $fechaActual = new DateTime();
            $fechaComparar = new DateTime($fecha);
            if ($fechaComparar < $fechaActual) {
                return "NO";
            } elseif ($fechaComparar >= $fechaActual) {
                return "SI";
            }
        }
        return "NO";
    }

    public function descarga1(){
        /// desactivamos la plantilla HTML
        $this->template = FALSE;


        

       $html = "<table>
               <tr>
                    <th >Email</th>
                    <th >Nombre</th>
                    <th >Producto</th>
                    <th >Fecha caducidad</th>
                    <th >Activo</th>
               </tr>
       ";

            foreach ($this->active_users() as $key => $value) {
                $html .= "<tr>
                <td>{$value['mail']}</td>
                <td>{$value['name']}</td>
                <td>{$value['product']}</td>
                <td>{$value['validity']}</td>
                <td>{$value['active']}</td>
                </tr>";
            }
                

           $html .= "</tbody></table>";
        
        header('Content-Type: application/vnd.ms-excel; charset=UTF-8');
        header('Content-Disposition: attachment; filename=Listado usuarios.xls');
        echo $html;       
       
       
   }




    public function descarga(){
         /// desactivamos la plantilla HTML
         $this->template = FALSE;


         header('Content-type:application/xls');
         header('Content-Disposition: attachment; filename=usersList.xls');
 
        $html = "<table>
                <tr>
                    <th>Email</th>
                    <th>Producto</th>
                    <th>Estado</th>
                    <th>Fecha Registro</th>
                    <th>Fecha Garantía</th>
                    <th>Fecha Caducidad</th>
                    <th>Último Ingreso</th>
                </tr>
        ";

            for($i = 0; $i < count($this->hotmartuser->datoscompletos) ; $i++){
                $html .= "
                <tr>
                        <td>".$this->hotmartuser->datoscompletos[$i]->user."</td>
                        <td>".$this->hotmartuser->datoscompletos[$i]->nombreproducto."</td>
                        <td>".$this->hotmartuser->datoscompletos[$i]->estado."</td>
                        <td>".$this->hotmartuser->datoscompletos[$i]->ultmod."</td>
                        <td>".$this->hotmartuser->datoscompletos[$i]->fechagarantia."</td>
                        <td>".substr($this->hotmartuser->datoscompletos[$i]->fechacaducidad, 0, 10)."</td>
                        <td>".$this->hotmartuser->datoscompletos[$i]->last_login."</td>
                    </tr>
                ";
            }

            $html .= "</tbody></table>";
                    
        echo $html;       
        
        
    }

    public function vecestado(){
        $vec = [];
        $vec[] = "approved";
        $vec[] = "completed";
        $vec[] = "canceled";
        $vec[] = "expired";
        return $vec;
    }

    public function contconver(){
        $this->contconversiones++;
    }


    public function enviarcorreo($mailusuario){
        /// desactivamos la plantilla HTML
        $this->template = FALSE;

        
        // Create the email and send the message
        $to = $mailusuario; // Add your email address in between the "" replacing yourname@yourdomain.com - This is where the form will send a message to.
        $subject = $this->user->nombre." Te ha invitado a la Zona Vip de EspecialiastasEnExcel.com";
        $body = $this->mensaje();
        $header = "From: noreplay@especialistasenexcel.com\n"; // This is the email address the generated message will be from. 
        $header .= "MIME-Version: 1.0\r\n";
        $header .= "Content-Type: text/html; charset=UTF-8\r\n";


        echo "---> ".$to;

        /*if(mail($to, $subject, $body, $header)){
          //
        }*/

    }

    public function mensaje(){

        $cadena = "
        <center>
        <div style='background-color:#2e3a59;color:#fff;padding:30px;text-align: left;font-size:18px;max-width:600px'>
        <img widht src='https://especialistasenexcel.com/downloads/icono%20zonavip%20con%20eee.png' alt='Img Zona Vip'>
        <p><h5>
        Saludos,</h5></p>
        <p>este mensaje es una recomendación que te envía ".$this->user->nombre." (".$this->user->nick.") con el fin que aprendas más de Excel y te beneficies con lo que se comparte en la plataforma Zona VIP de EspecialistasEnExcel.com, en el caso que no te interese, por favor haz caso omiso a este correo.</p>
        <p>En la Zona VIP te puedes unir gratuitamente, la cual te permitirá aprender mucho más de Excel y podrás disfrutar de los siguientes beneficios:</p>
        <ol>
        <li>Vídeos y descarga de archivos Excel</li>
        <li>Invitación a webinars en vivo</li>
        <li>Material organizado por categorías </li>
        <li>Otro tipo de actividades que estamos desarrollando</li>
        </ol>

        <p><h5>Importante:</h5><br>
        En la plataforma hay dos tipos de contenido, gratuito y premium, podrás disfrutar del contenido gratuito todo el tiempo que lo desees y adquirir acceso premium en el momento que gustes.</p>

        <p>Para unirte a la comunidad de personas que desean Maximizar su desempeño con Excel, comienza creando una cuenta <b>gratuita</b> siguiendo las indicaciones del sistema, dando clic en el siguiente botón.</p>

        <h3><a href='https://especialistasenexcel.com/zonavip/index.php#registro' alt='Zona Vip 3E' style='padding:10px;background-color:#f7c94d;color:#2e3a59;'>Crear cuenta gratis en Zona VIP de Excel</a></h3>

        <br><br>
        <p>Te esperamos en el equipo, un abrazo virtual.</p>

        <p>Cordialmente,<br>
        Equipo <a href='https://especialistasenexcel.com/' alt='Especialistas En Excel'> EspecialistasEnExcel.com </a>
        </p>
        </div>
        </center>";

        return $cadena;

    }

   
    
}
