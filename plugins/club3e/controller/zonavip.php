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

class zonavip extends fs_controller
{
    public $zonavip_registros;
    public $busqueda;
    public $grupo;
    public $pago;
    public $categoria;
    public $allow;
    public $cumplesin;
    public $historial;
    public $subs;
    public $productoshotmart;
    public $cursos;
    public $transmision;
    public $pagogeneral;
    public $usuarioactivomailerlite;
    public $notfications;
    
    public function __construct()
    {
        parent::__construct(__CLASS__, 'Vista Usuario', 'Zona Vip', true, true);
    }

    public function private_core()
    {
        

        $cumple = new cumple();
        $this->transmision = new transmisiones();
        $this->transmision = $this->transmision->notificacion();
        $this->usuarioactivomailerlite = revisarusermail($this->user->nick);

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

        $this->productoshotmart = new hotmartuser();
        $this->productoshotmart->user = $this->user->nick;
        $this->pagogeneral = new hotmartuser();
        $this->pagogeneral->user = $this->user->nick;

        $this->cursos = new hotmartproductos();

        if($this->pagogeneral->get_user())
            $this->pagogeneral = 1;
        else
            $this->pagogeneral = 0;

        $this->zonavip_registros = new zonavipdb();

        $this->subs = $this->zonavip_registros->sub($this->user->nick);
        
        if($this->subs == 0){
            $this->zonavip_registros->sub($this->user->nick,1);
        }

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

        if(isset($_REQUEST["devuelta"]) && $this->usuarioactivomailerlite == "NO"){
            if($_REQUEST["devuelta"]){
                $us = new fs_user();
                $us = $us->get($_REQUEST["devuelta"]);
                if($us){
                    comprobarusuarioMailerLite($us->nick, $us->nombre, GROUP_ZONAVIP);
                    header('Location: '.$this->url());
                }
            }
        }

        if(isset($_REQUEST["debaja"]) && $this->usuarioactivomailerlite == "NO"){
            if($_REQUEST["debaja"]){
                $us = new fs_user();
                $us = $us->get($_REQUEST["debaja"]);
                if($us){
                    $us->enabled = FALSE;
                    $us->save();
                    header('Location: '.$this->url());
                }
            }
        }

        if($this->user->nombre == "" && $this->usuarioactivomailerlite == "SI"){
            $usu = new fs_user();
            $usu = $usu->get($this->user->nick);
            $this->user->nombre = revisarusermail($this->user->nick,true)["name"];
            $usu->nombre = $this->user->nombre;
            $usu->save();
        }

        if(isset($_REQUEST["enviarcorreo"])){
            $this->enviarcorreo($_REQUEST["enviarcorreo"]);
        }

        if(isset($_GET["nuevomsg"])){
            $this->nuevomensaje($_GET["nuevomsg"]);
        }

        

    }

    public function nuevomensaje($nuevomsg){
        $this->template = FALSE;

        $resp["resp"] = "Respuesta del BOT";

        if(strpos($nuevomsg, "gracias") !== false){
            $resp["resp"] = "Gracias a ti ".$this->user->nombre;
        }

        header('Content-Type: application/json');
        echo json_encode($resp);

    }

    public function accesoclub3e(){

        $club3e = new coreclub3e();

        $fecnow = date("Y-m-d");
        if( $club3e->get_user_active($this->user->nick, $fecnow) )
            return 1;
        return 0;
         
    }

    public function enviarcorreo($mailusuario){
        /// desactivamos la plantilla HTML
        $this->template = FALSE;

        
        // Create the email and send the message
        $to = $mailusuario; // Add your email address in between the "" replacing yourname@yourdomain.com - This is where the form will send a message to.
        $subject = $this->user->nombre." Te ha invitado a la Zona Vip de EspecialiastasEnExcel.com";
        $body = $this->mensajeinvita();
        $header = "From: noreplay@especialistasenexcel.com\n"; // This is the email address the generated message will be from. 
        $header .= "MIME-Version: 1.0\r\n";
        $header .= "Content-Type: text/html; charset=UTF-8\r\n";


        if(mail($to, $subject, $body, $header)){
          $invitacion = new invitaciones();
          $invitacion->paginaorigen = $this->url();
          $invitacion->usuarioinvita = $this->user->nick;
          $invitacion->correoinvitado = $mailusuario;
          $invitacion->ultmod = date("Y-m-d H:i:s");
          $invitacion->save();
        }

    }
    

    public function mensajeinvita(){

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


    public function aprobaracceso($producto){
        if($producto == "Club de Macros")
            $producto = "1125293";
        if($producto == "Descargas")
            $producto = "1125294";
        $this->productoshotmart->idproducto = $producto;
        return $this->productoshotmart->all_user_producto();
    }


}
