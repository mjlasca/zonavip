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

class contenido extends fs_controller
{
    public $zonavip_registros;
    public $busqueda;
    public $grupo;
    public $pago;
    public $categoria;
    public $allow;
    public $cumplesin;
    public $post;
    public $recomendaciones;
    public $comentarios;
    public $previewpost;
    public $nextpost;
    public $productoshotmart;
    

    public function __construct()
    {
        parent::__construct(__CLASS__, 'Vista Usuario', 'Contenido', false, false);
    }

    public function private_core()
    {
        $this->post = new zonavipdb();
        $this->comentarios = new comentarios();
        
        
        $this->allow = allowusuario($this->user->nick, $this->page->name);
        if($this->user->admin){
            $this->allow = 1;
        }
        
        if($this->allow != "1"){
            $this->allow = 0;
        }

        $this->productoshotmart = new hotmartuser();
        $this->productoshotmart->user = $this->user->nick;

        if(isset($_REQUEST["post"])){
            $this->comentarios->regpost = $_REQUEST["post"];
            $this->post = $this->post->get( $_REQUEST["post"]);
            $this->recomendaciones = new zonavipdb();
            $this->recomendaciones->categoriaFiltro = $this->post->categoria;
            $this->recomendaciones = $this->recomendaciones->recomendaciones($_REQUEST["post"]);

            $zona = new zonavipdb();
            $zona->categoriaFiltro = $this->post->categoria;
            $this->previewpost = $zona->get_preview($_REQUEST["post"]);
            $this->nextpost = $zona->get_next($_REQUEST["post"]);

            $historial = new historialvideos();
            $historial->regpost =$_REQUEST["post"];
            $historial->user =$this->user->nick;
            $historial->ultmod = date("Y-m-d H:i:s");
            $historial->save();
        }


        if(isset($_REQUEST["getcomentarios"])){
            $this->getcomentarios();
        }

        if(isset($_REQUEST["insertmsg"])){
            $this->insertmsg();
        }

        if(isset($_REQUEST["eliminarcomentario"])){
            $this->eliminarcomentario($_REQUEST["eliminarcomentario"]);
        }

        
    }
    
    public  function getcomentarios()
    {
        
        /// desactivamos la plantilla HTML
        $this->template = FALSE;
        $comen = new comentarios();
        $comen->regpost = $_REQUEST["post"];
        header('Content-Type: application/json');

        echo json_encode($comen->all_post());
    }

    public function insertmsg(){
        /// desactivamos la plantilla HTMLelimi
        $this->template = FALSE;
        $comen = new comentarios();
        $comen->regpost = $_REQUEST["post"];
        $comen->mensaje = $_REQUEST["insertmsg"];
        if(isset($_REQUEST["regmensaje"]))
            $comen->regmensajeresponde = $_REQUEST["regmensaje"];
        if(isset($_REQUEST["userresponse"]))            
            $comen->userresponde = $_REQUEST["userresponse"];

        if($comen->regmensajeresponde != ''){
            if(isset($_REQUEST["userresponse"]))
                $this->mailnotificacion($comen,$_REQUEST["userresponse"]);
            else
                $this->mailnotificacion($comen);
            
            
        }
        
        $comen->ultmod = date("Y-m-d H:i:s");
        $comen->user = $_REQUEST["user"];
        $comen->save();
        header('Content-Type: application/json');

        echo json_encode($comen);
    }

    private function notificar($user){
        $not = new notificaciones();
        $not->mensaje = "Han respondido tu comentario en : ".$this->post->nombrevideo;
        $not->tipo = "COMENTARIO";
        $not->accion = $this->url()."&post=".$this->post->reg;
        $not->destinatario = $user;
        $not->save();
    }

   


    private function mailnotificacion(comentarios $comen, string $remitente = ''){

            $co = $comen->get($comen->regmensajeresponde);
            
            if($co){

                $this->notificar($co->user);

                // Create the email and send the message
                $to = $co->user; // Add your email address in between the "" replacing yourname@yourdomain.com - This is where the form will send a message to.
                if($remitente != "")
                    $subject = $remitente ." ha contestado tu mensaje en ".$this->post->nombrevideo;
                else
                    $subject = "Han contestado tu mensaje en ".$this->post->nombrevideo;
                    $body = "<span  style='color:black; font-size:16px;'>Hola! <br> has hecho un comentario y te han respondido.<span><br><br>
                    <span  style='background-color: #ccc;color:black; font-size:14px;'>Tu comentario: </span><br>
                    <p>".$co->mensaje."</p>
                    <br>
                    <span style='background-color: #ccc;color:black; font-size:14px;'> Respuesta : </span><br>
                    <p>".$comen->mensaje."</p>
                    <br>
                
                <p>Ingresa a la  ZonaVip.plataformaeducativa.online si necesitas seguir respondiendo, en el siguiente enlace lo puedes hacer</p>
                <b><a href = '".$this->url()."&post=".$this->post->reg."' style='background-color: green;color:white;padding:5px; font-size:18px;' >Clic aquí para abrir el contenido relacionado</a></b>

                        ";
                $header = "From: ZonaVip3E-Notification@plataformaeducativa.online\n"; // This is the email address the generated message will be from. 
                $header .= "MIME-Version: 1.0\r\n";
                $header .= "Content-Type: text/html; charset=UTF-8\r\n";


                mail($to, $subject, $body, $header);
                    
            }

            
        
    }
    

    public function eliminarcomentario($reg){
        /// desactivamos la plantilla HTML
        $this->template = FALSE;
        $comen = new comentarios();
        $comen->reg = $reg;
        
        header('Content-Type: application/json');
        echo json_encode($comen->delete());
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
