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

class contenidocurso extends fs_controller
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
    public $curso;
    public $curso_modulos;
    public $checkvideo;
    public $nocontent;

    public function __construct()
    {
        parent::__construct(__CLASS__, 'Vista Usuario', 'contenido', false, false);
    }

    public function private_core()
    {
        $this->post = new zonavipdb();
        $this->curso = new hotmartproductos();
        $this->comentarios = new comentarios();
        $this->checkvideo = new historialvideoscursos();
        
        $this->nocontent = false;

        $this->allow = allowusuario($this->user->nick, $this->page->name);
        if($this->user->admin){
            $this->allow = 1;
        }
        
        if($this->allow != "1"){
            $this->allow = 0;
        }

        $this->productoshotmart = new hotmartuser();
        $this->productoshotmart->user = $this->user->nick;



        if(isset($_REQUEST["curso"])){


            $this->curso =  $this->curso->get_curso($_REQUEST["curso"]);
            $this->curso_modulos = $this->post->cursos_modulos($this->curso->idproducto);

            if($this->curso_modulos){
                $this->nocontent = true;

                if(isset($_REQUEST["post"]))
                    $this->comentarios->regpost = $_REQUEST["post"];    
                else {
                    $_REQUEST["post"] = $this->post->modulos_lecciones("1",$this->curso->idproducto)[0]["reg"];
                    if($this->checkvideo->lastvideo($this->user->nick,$this->curso->idproducto)[0]["ult"] != "" )
                        $_REQUEST["post"] = $this->checkvideo->lastvideo($this->user->nick,$this->curso->idproducto)[0]["ult"];
                }

                $this->post = $this->post->get( $_REQUEST["post"]);
                $this->recomendaciones = new zonavipdb();
                $this->recomendaciones->categoriaFiltro = $this->post->categoria;
                $this->recomendaciones = $this->recomendaciones->recomendaciones($_REQUEST["post"]);

                $zona = new zonavipdb();
                $zona->categoriaFiltro = $this->post->categoria;
                $this->previewpost = $zona->get_preview_curso($this->post->numeroleccion,$this->post->curso);
                $this->nextpost = $zona->get_next_curso($this->post->numeroleccion,$this->post->curso);

            }
            
        }


        if(isset($_REQUEST["getcomentarios"])){
            $this->getcomentarios();
        }

        if(isset($_REQUEST["insertmsg"])){
            $this->insertmsg();
        }

        if(isset($_REQUEST["filet"])){
            $this->savefile($this->curso->idproducto ."-".$this->post->reg,$_FILES["leccionfile"]);
        }

        if(isset($_REQUEST["eliminarcomentario"])){
            $this->eliminarcomentario($_REQUEST["eliminarcomentario"]);
        }

        if(isset($_REQUEST["tiposetvideo"])){
            if($_REQUEST["tiposetvideo"] == 0)
                $this->deletvideocurso($_REQUEST["setvideocurso"]);
                if($_REQUEST["tiposetvideo"] == 1)
                $this->setvideocurso($_REQUEST["setvideocurso"]);
            
        }
        
    }

    public function checkfile(bool $del = false){

        $extensionesContenido = explode(",", $this->post->detalleupload);


        if(count( $extensionesContenido ) > 0){
            foreach ($extensionesContenido as $value) {
                $path = 'plugins/zonavipeee/view/assets/file/' .$this->curso->idproducto ."-".$this->post->reg.'/'.$this->user->nick.$value;
                if (file_exists($path)) {
                    if($del)
                        unlink($path);
                    return true;
                }
            }
        }

        return false;
    }

    private function savefile($post,$file){

        $ruta_fichero_origen = $file['tmp_name'];
        $max_tamanyo = 1024 * 1024 * 8;
        $path = 'plugins/zonavipeee/view/assets/file/' .$post.'/';
        $exten = ".xlsm";

        if($file['type'] == 'application/vnd.ms-excel')
           $exten = ".xls";
        if($file['type'] == 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
           $exten = ".xlsx";
        if($file['type'] == 'application/vnd.ms-excel.sheet.macroEnabled.12')
           $exten = ".xlsm";
        if($file['type'] == 'application/x-zip-compressed')
            $exten = ".zip";
        if( $file['type'] == 'application/octet-stream' )    
            $exten = ".rar";
        if( $file['type'] == 'application/vnd.openxmlformats-officedocument.wordprocessingml.document' )    
            $exten = ".docx";
        if( $file['type'] == 'application/msword' )    
            $exten = ".doc";
        if( $file['type'] == 'application/pdf' )    
            $exten = ".pdf";
        if( $file['type'] == 'text/plain' )    
            $exten = ".txt";
        if( $file['type'] == 'image/jpeg' )    
            $exten = ".jpg";
        if( $file['type'] == 'image/png' )    
            $exten = ".png";    
        if( $file['type'] == 'image/gif' )    
            $exten = ".gif";    
        
        $extensionesContenido = explode(",", $this->post->detalleupload);

        $siextension = false;
        $concatmssg = "";

        if(count( $extensionesContenido ) > 0){
            foreach ($extensionesContenido as $value) {
                $concatmssg .= " ó ".$value. " ";
                if($exten == $value){
                    
                    $siextension = true;
                }
            }
        }


        if(!$siextension){
            $this->new_error_msg("El tipo de archivo no es correcto, debe ser un archivo tipo <h4>".$concatmssg."</h4>");
            return false;
        }


        $this->checkfile(true);


        if (!file_exists($path)) {
            if(!mkdir($path,0777, true) ) {
                $this->new_error_msg("Error al crear directorio");
                return false;
            }
        }

        $ruta_nuevo_destino = $path. $this->user->nick.$exten;

        
        //$this->new_message("<br><br>--> ".$file['type']);
  
        //if ( in_array($file['type'], $extensiones) ) {
            
           
           if ( $file['size']< $max_tamanyo ) {
              
                 if(! move_uploaded_file ( $ruta_fichero_origen, $ruta_nuevo_destino ) ) {
                     $this->new_error_msg("Error al subir el archivo")  ;
                 }else{
                    
                    /*$this->cliente->img = $exten;
                    $this->cliente->save();                  */
                    $this->new_message("Se ha guardado el arhivo con éxito");
                 }
           }else{
               $this->new_error_msg("El archivo es demasiado grande");
           }
        //}
  
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

    public  function setvideocurso($post)
    {
        /// desactivamos la plantilla HTML
        $this->template = FALSE;

        $historial = new historialvideoscursos();
        $historial->regpost = $post;
        $historial->user =$this->user->nick;
        $historial->ultmod = date("Y-m-d H:i:s");
        $historial->curso = $_REQUEST["curso"];
        $data = [];
        $data["state"] = false;
        if($historial->save()){
            $data["state"] = true;
        }
        header('Content-Type: application/json');
        echo json_encode($data);
    }

    public  function deletvideocurso($post)
    {
        /// desactivamos la plantilla HTML
        $this->template = FALSE;
        $historial = new historialvideoscursos();
        $historial->regpost = $post;
        $historial->user =$this->user->nick;
        $data = [];
        $data["state"] = false;
        if($historial->delete()){
            $data["state"] = true;
        }
        header('Content-Type: application/json');
        echo json_encode($data);
    }

    public function insertmsg(){
        /// desactivamos la plantilla HTMLelimi
        $this->template = FALSE;
        $comen = new comentarios();
        $comen->regpost = $_REQUEST["post"];
        $comen->mensaje = $_REQUEST["insertmsg"];
        if(isset($_REQUEST["regmensaje"])){
            $comen->regmensajeresponde = $_REQUEST["regmensaje"];
            
        }
            
        if(isset($_REQUEST["userresponse"])){
            $comen->userresponde = $_REQUEST["userresponse"];
        }   
        
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

                <p>Ingresa a la  https://zonavip.plataformaeducativa.online/ si necesitas seguir respondiendo, en el siguiente enlace lo puedes hacer</p>
                <b><a href = '".$this->url()."&curso=".$this->curso->idproducto."&post=".$this->post->reg."#leccion".$this->post->reg."' style='background-color: green;color:white;padding:5px; font-size:16px;' >Clic aquí para abrir el contenido relacionado</a></b>
                


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

    public function accessscontent(){

        $limit_lesson = ($this->curso->limit_lessons == 1 && $this->post->limit_date != null);
        
        if(  $this->accesoclub3e($limit_lesson) == 1 )
            return 1;
        
        
        if( $this->post->pago == 'SI' && $this->allow != 0  || $this->aprobaracceso($this->post->grupo,$limit_lesson) )
            return 1;
            
        return 0;
    }


    public function accesoclub3e($limit_lesson = false){

        $club3e = new coreclub3e();

        $fecnow = date("Y-m-d");
        $result = $club3e->get_user_curse_access($this->user->nick, $this->productoshotmart->idproducto,$limit_lesson) ;
        if( $result ){
            if($limit_lesson){
                $date1 = new DateTime($this->post->limit_date);
                $date2 = new DateTime($result[0]['fecha_inicia']);
                if($date1 < $date2)
                    return 0;
            }
            return 1;
        }
            
        return 0;
         
    }

    public function aprobaracceso($producto,$limit_lesson = false){

        if($producto == "Club de Macros")
            $producto = "1125293";
        if($producto == "Descargas")
            $producto = "1125294";
        $this->productoshotmart->idproducto = $producto;
        $result = $this->productoshotmart->all_user_producto();
        if($result){
            if($limit_lesson){
                $date1 = new DateTime($this->post->limit_date);
                $date2 = new DateTime($result[0]->ultmod);
                if($date1 < $date2)
                    return false;
            }
            return $result;
        }

        return $result;
        
    }
}
