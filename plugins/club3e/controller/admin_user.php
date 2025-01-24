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
 * @author Carlos García Gómez <neorazorx@gmail.com>
 */

class admin_user extends fs_controller
{

    public $agente;
    public $allow_delete;
    public $allow_modify;
    public $user_log;
    public $suser;
    public $cumple;
    public $hotmartuser;
    public $paises;
    public $productos;
    public $club3e_user;
    public $fecclub3e_ini ;
    public $fecclub3e_exp ;

    public function __construct()
    {
        parent::__construct(__CLASS__, 'Usuario', 'admin', TRUE, FALSE);
    }

    public function private_core()
    {
        $this->share_extensions();
        $this->paises = new pais();
        $this->agente = new agente();
        $this->cumple = new cumple();
        $this->hotmartuser = new hotmartuser();
        $this->productos = new hotmartproductos();
        $this->club3e_user = new coreclub3e();
        $this->fecclub3e_ini = "";
        $this->fecclub3e_exp = "";
        
        /// ¿El usuario tiene permiso para eliminar en esta página?
        $this->allow_delete = $this->user->admin;

        /// ¿El usuario tiene permiso para modificar en esta página?
        $this->allow_modify = $this->user->admin;

        $this->suser = FALSE;
        if (isset($_GET['snick'])) {
            $this->suser = $this->user->get(filter_input(INPUT_GET, 'snick'));
        }


        if(isset($_FILES["imagen1"])){
            $this->guardarImagen($_POST["lista"]);
        }

        

        if ($this->suser) {
            $this->page->title = $this->suser->nick;

            

            /// ¿Estamos modificando nuestro usuario?
            if ($this->suser->nick == $this->user->nick) {
                $this->allow_modify = TRUE;
                $this->allow_delete = FALSE;
            }

            if (isset($_POST['nnombre'])) {
                $this->nuevo_empleado();
            } else if (isset($_POST['spassword']) || isset($_POST['scodagente']) || isset($_POST['sadmin'])) {
                $this->modificar_user();
            } else if (fs_filter_input_req('senabled')) {
                $this->desactivar_usuario();
            }

            /// ¿Estamos modificando nuestro usuario?
            if ($this->suser->nick == $this->user->nick) {
                $this->user = $this->suser;
            }

            /// si el usuario no tiene acceso a ninguna página, entonces hay que informar del problema.
            if (!$this->suser->admin) {
                $sin_paginas = TRUE;
                foreach ($this->all_pages() as $p) {
                    if ($p->enabled) {
                        $sin_paginas = FALSE;
                        break;
                    }
                }
                if ($sin_paginas) {
                    $this->new_advice('No has autorizado a este usuario a acceder a ninguna'
                        . ' página y por tanto no podrá hacer nada. Puedes darle acceso a alguna página'
                        . ' desde la pestaña autorizar.');
                }
            }
            $this->hotmartuser->user = $this->suser->nick;
            

            $fslog = new fs_log();
            $this->user_log = $fslog->all_from($this->suser->nick);


            if(isset($_POST["idproducto"] )){
                $this->crearproductohotmart($this->suser->nick);
            }

            if(isset($_POST["fecha_inicia_club3e"]) && isset($_POST["fecha_expira_club3e"])){
                if($_POST["fecha_inicia_club3e"] != "" && $_POST["fecha_expira_club3e"] != "")
                    $this->registro_club3e($this->suser->nick);
            }
            $this->club3e_user->usuario  = $this->suser->nick;
            $consUser = $this->club3e_user->get_user();
            
            if($consUser){
                if($consUser[0]->fecha_inicia != ""){
                    $this->fecclub3e_ini = date('d-m-Y', strtotime( $consUser[0]->fecha_inicia ));
                }
                if($consUser[0]->fecha_expira != ""){
                    $this->fecclub3e_exp = date('d-m-Y', strtotime( $consUser[0]->fecha_expira ));
                }   
    
            }
            
            

        } else {
            $this->new_error_msg("Usuario no encontrado.", 'error', FALSE, FALSE);
        }

        if(isset($_REQUEST["correoclub"])){

            $this->enviarcorreo($_REQUEST["correoclub"],$_REQUEST["mensajeclub"]);
        }

        //$this->guardar_registro();
    }

    public function accesoclub3e(){

        $club3e = new coreclub3e();

        $fecnow = date("Y-m-d");
        if( $club3e->get_user_active($this->user->nick, $fecnow) )
            return 1;
        return 0;
         
    }

    public function enviarcorreo($asunto,$mensaje){
        $this->template = FALSE;
        /// desactivamos la plantilla HTML
        $to = "especialistasenexcel1@gmail.com,lenis1@gmail.com,admin@especialistasenexcel.com"; // Add your email address in between the "" replacing yourname@yourdomain.com - This is where the form will send a message to.
        $subject = "Hay una inquietud / ".$asunto;
        $body = "<br>".$mensaje ."<br><br><br> Atentamente, <br><br>" . $this->user->nombre . "<br>".$this->user->nick;
        $header = "From: contact_Club3E@especialistasenexcel.com\n"; // This is the email address the generated message will be from. 
        $header .= "MIME-Version: 1.0\r\n";
        $header .= "Content-Type: text/html; charset=UTF-8\r\n";

        
        $data["success"] = false;


        try{
            mail($to, $subject, $body, $header);
            $data["msg"] = "Correo enviado con éxitos";
            $data["success"] = true;
        }catch(Exception $e){
            $data["msg"] = "Ha habido un error al enviar el correo";
        }

        echo json_encode($data);
        

        
    }


    private function registro_club3e($user_){

        $club3e = new coreclub3e();
        $club3e->usuario = $user_;
        $club3e->nombre = $_POST["nombres"];
        $club3e->fecha_inicia = $_POST["fecha_inicia_club3e"];
        $club3e->fecha_expira = $_POST["fecha_expira_club3e"];
        $club3e->ultmod = date("Y-m-d h:i:s");
        $club3e->estado = 1;
        $club3e->save();

    }


    private function crearproductohotmart($user_){
        
        if(isset($_POST["idproducto"] )){

            if($_POST["idproducto"]){
                $userhtomart = new hotmartuser();
                $userhtomart->user = $user_;
                $userhtomart->idproducto = $_POST["idproducto"] ;
                $hotmartproductos = new hotmartproductos();
                $userhtomart->nombreproducto = $hotmartproductos->get_curso($userhtomart->idproducto)->nombre;
                $userhtomart->estado = "completed";
                $userhtomart->periocidad = 30;
                $userhtomart->fechagarantia = date("Y-m-d h:i:s");
                $userhtomart->fechacaducidad = $_POST["fechacaducidad"]." ".date("h:i:s");
                $userhtomart->ultmod = date("Y-m-d 00:00:01");

                $userhtomart->save();
            }

        }
    }

    private function guardarImagen($laboratorio){

        $ruta_fichero_origen = $_FILES['imagen1']['tmp_name'];
        $extensiones = array(0=>'file/xls',1=>'file/xlsx',2=>'file/xlsm');
        $max_tamanyo = 1024 * 1024 * 8;
        $exten = "xlsm";
        if($_FILES['imagen1']['type'] == 'application/vnd.ms-excel')
           $exten = "xls";
        if($_FILES['imagen1']['type'] == 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
           $exten = "xlsx";
        if($_FILES['imagen1']['type'] == 'application/vnd.ms-excel.sheet.macroEnabled.12')
           $exten = "xlsm";
        $ruta_nuevo_destino = 'plugins/zonavipeee/view/assets/file/' .$laboratorio.'/'. $this->user->nick.'.'.$exten;

        
        //$this->new_message("<br><br>--> ".$_FILES['imagen1']['type']);
  
        //if ( in_array($_FILES['imagen1']['type'], $extensiones) ) {
            
           
           if ( $_FILES['imagen1']['size']< $max_tamanyo ) {
              
                 if(! move_uploaded_file ( $ruta_fichero_origen, $ruta_nuevo_destino ) ) {
                     $this->new_error_msg("Error al subir la imagen")  ;
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


    

    

    public function url()
    {
        if (!isset($this->suser)) {
            return parent::url();
        } else if ($this->suser) {
            return $this->suser->url();
        }

        return $this->page->url();
    }

    public function all_pages()
    {
        $returnlist = array();

        /// Obtenemos la lista de páginas. Todas
        foreach ($this->menu as $m) {
            $m->enabled = FALSE;
            $m->allow_delete = FALSE;
            $returnlist[] = $m;
        }

        /// Completamos con la lista de accesos del usuario
        $access = $this->suser->get_accesses();
        foreach ($returnlist as $i => $value) {
            foreach ($access as $a) {
                if ($value->name == $a->fs_page) {
                    $returnlist[$i]->enabled = TRUE;
                    $returnlist[$i]->allow_delete = $a->allow_delete;
                    break;
                }
            }
        }

        /// ordenamos por nombre
        usort($returnlist, function($val1, $val2) {
            return strcmp($val1->name, $val2->name);
        });

        return $returnlist;
    }

    private function share_extensions()
    {
        foreach ($this->extensions as $ext) {
            if ($ext->type == 'css') {
                if (!file_exists($ext->text)) {
                    $ext->delete();
                }
            }
        }

        $extensions = array(
            array(
                'name' => 'cosmo',
                'page_from' => __CLASS__,
                'page_to' => __CLASS__,
                'type' => 'css',
                'text' => 'view/css/bootstrap-cosmo.min.css',
                'params' => ''
            ),
            array(
                'name' => 'darkly',
                'page_from' => __CLASS__,
                'page_to' => __CLASS__,
                'type' => 'css',
                'text' => 'view/css/bootstrap-darkly.min.css',
                'params' => ''
            ),
            array(
                'name' => 'flatly',
                'page_from' => __CLASS__,
                'page_to' => __CLASS__,
                'type' => 'css',
                'text' => 'view/css/bootstrap-flatly.min.css',
                'params' => ''
            ),
            array(
                'name' => 'sandstone',
                'page_from' => __CLASS__,
                'page_to' => __CLASS__,
                'type' => 'css',
                'text' => 'view/css/bootstrap-sandstone.min.css',
                'params' => ''
            ),
            array(
                'name' => 'united',
                'page_from' => __CLASS__,
                'page_to' => __CLASS__,
                'type' => 'css',
                'text' => 'view/css/bootstrap-united.min.css',
                'params' => ''
            ),
            array(
                'name' => 'yeti',
                'page_from' => __CLASS__,
                'page_to' => __CLASS__,
                'type' => 'css',
                'text' => 'view/css/bootstrap-yeti.min.css',
                'params' => ''
            ),
            array(
                'name' => 'lumen',
                'page_from' => __CLASS__,
                'page_to' => __CLASS__,
                'type' => 'css',
                'text' => 'view/css/bootstrap-lumen.min.css',
                'params' => ''
            ),
            array(
                'name' => 'paper',
                'page_from' => __CLASS__,
                'page_to' => __CLASS__,
                'type' => 'css',
                'text' => 'view/css/bootstrap-paper.min.css',
                'params' => ''
            ),
            array(
                'name' => 'simplex',
                'page_from' => __CLASS__,
                'page_to' => __CLASS__,
                'type' => 'css',
                'text' => 'view/css/bootstrap-simplex.min.css',
                'params' => ''
            ),
            array(
                'name' => 'spacelab',
                'page_from' => __CLASS__,
                'page_to' => __CLASS__,
                'type' => 'css',
                'text' => 'view/css/bootstrap-spacelab.min.css',
                'params' => ''
            ),
        );
        foreach ($extensions as $ext) {
            $fsext = new fs_extension($ext);
            $fsext->save();
        }
    }

    private function nuevo_empleado()
    {
        $age0 = new agente();
        $age0->codagente = $age0->get_new_codigo();
        $age0->nombre = filter_input(INPUT_POST, 'nnombre');
        $age0->apellidos = filter_input(INPUT_POST, 'napellidos');
        $age0->dnicif = filter_input(INPUT_POST, 'ndnicif');
        $age0->telefono = filter_input(INPUT_POST, 'ntelefono');
        $age0->email = strtolower(filter_input(INPUT_POST, 'nemail'));

        if (!$this->user->admin) {
            $this->new_error_msg('Solamente un administrador puede crear y asignar un empleado desde aquí.');
        } else if ($age0->save()) {
            $this->new_message("Empleado " . $age0->codagente . " guardado correctamente.");
            $this->suser->codagente = $age0->codagente;

            if ($this->suser->save()) {
                $this->new_message("Empleado " . $age0->codagente . " asignado correctamente.");
            } else {
                $this->new_error_msg("¡Imposible asignar el agente!");
            }
        } else {
            $this->new_error_msg("¡Imposible guardar el agente!");
        }
    }

    private function modificar_user()
    {
        if (FS_DEMO && $this->user->nick != $this->suser->nick) {
            $this->new_error_msg('En el modo <b>demo</b> sólo puedes modificar los datos de TU usuario.
            Esto es así para evitar malas prácticas entre usuarios que prueban la demo.');
        } else if (!$this->allow_modify) {
            $this->new_error_msg('No tienes permiso para modificar estos datos.');
        } else {
            $user_no_more_admin = FALSE;
            $error = FALSE;
            $spassword = filter_input(INPUT_POST, 'spassword');
            if ($spassword != '') {
                if ($spassword == filter_input(INPUT_POST, 'spassword2')) {
                    if ($this->suser->set_password($spassword)) {
                        $this->new_message('Se ha cambiado la contraseña del usuario ' . $this->suser->nick, TRUE, 'login', TRUE);
                    }
                } else {
                    $this->new_error_msg('Las contraseñas no coinciden.');
                    $error = TRUE;
                }
            }

            if (isset($_POST['email'])) {
                $this->suser->email = strtolower(filter_input(INPUT_POST, 'email'));
            }

            if (isset($_POST['fechacumple'])) {

                if($_POST['fechacumple'] != ""){
                    $cumple = new cumple();
                    $cumple->usercumple =  $this->suser->nick;
                    $fec = new DateTime($_POST['fechacumple']);
                    $cumple->date = date_format($fec,"Y-m-d") ;
                    $cumple->useredit = $this->user->nick;
                    $cumple->ultmod = date("Y-m-d H:i:s");
                    
                    if(!$cumple->save()){
                        //$this->new_error_msg("No se pudo guardar la fecha de cumpleaños");
                    }
                }
                
            }

            if (isset($_POST['scodagente'])) {
                $this->suser->codagente = NULL;
                if ($_POST['scodagente'] != '') {
                    $this->suser->codagente = filter_input(INPUT_POST, 'scodagente');
                }
            }

            /*
             * Propiedad admin: solamente un admin puede cambiarla.
             */
            if ($this->user->admin) {
                /*
                 * El propio usuario no puede decidir dejar de ser administrador.
                 */
                if ($this->user->nick != $this->suser->nick) {
                    /*
                     * Si un usuario es administrador y deja de serlo, hay que darle acceso
                     * a algunas páginas, en caso contrario no podrá continuar
                     */
                    if ($this->suser->admin && ! isset($_POST['sadmin'])) {
                        $user_no_more_admin = TRUE;
                    }
                    $this->suser->admin = isset($_POST['sadmin']);
                }
            }

            $this->suser->creat = $_POST["pais"];

            if(isset($_POST["pais"]))
                $this->suser->pais = $_POST["pais"];

            if(isset($_POST["nombres"]))
                $this->suser->nombre = $_POST["nombres"];

            $this->suser->fs_page = NULL;
            if (isset($_POST['udpage'])) {
                $this->suser->fs_page = filter_input(INPUT_POST, 'udpage');
            }

            if (isset($_POST['css'])) {
                $this->suser->css = filter_input(INPUT_POST, 'css');
            }

            if ($error) {
                /// si se han producido errores, no hacemos nada más
            } else if ($this->suser->save()) {
                if (!$this->user->admin) {
                    /// si no eres administrador, no puedes cambiar los permisos
                } else if (!$this->suser->admin) {
                    /// para cada página, comprobamos si hay que darle acceso o no
                    foreach ($this->all_pages() as $p) {
                        /**
                         * Creamos un objeto fs_access con los datos del usuario y la página.
                         * Si tiene acceso guardamos, sino eliminamos. Así no tenemos que comprobar uno a uno
                         * si ya estaba en la base de datos. Eso lo hace el modelo.
                         */
                        $a = new fs_access(array('fs_user' => $this->suser->nick, 'fs_page' => $p->name, 'allow_delete' => FALSE));
                        if (isset($_POST['allow_delete'])) {
                            $a->allow_delete = in_array($p->name, $_POST['allow_delete']);
                        }

                        if ($user_no_more_admin) {
                            /*
                             * Si un usuario es administrador y deja de serlo, hay que darle acceso
                             * a algunas páginas, en caso contrario no podrá continuar.
                             */
                            $a->save();
                        } else if (!isset($_POST['enabled'])) {
                            /**
                             * No se ha marcado ningún checkbox de autorizado, así que eliminamos el acceso
                             * a todas las páginas. Una a una.
                             */
                            $a->delete();
                        } else if (in_array($p->name, $_POST['enabled'])) {
                            /// la página ha sido marcada como autorizada.
                            $a->save();

                            /// si no hay una página de inicio para el usuario, usamos esta
                            if (is_null($this->suser->fs_page) && $p->show_on_menu) {
                                $this->suser->fs_page = $p->name;
                                $this->suser->save();
                            }
                        } else {
                            /// la página no está marcada como autorizada.
                            $a->delete();
                        }
                    }
                }

                $this->new_message("Datos modificados correctamente.");
            } else {
                $this->new_error_msg("¡Imposible modificar los datos!");
            }
        }
    }

    private function desactivar_usuario()
    {
        if (!$this->user->admin) {
            $this->new_error_msg('Solamente un administrador puede activar o desactivar a un Usuario.');
        } else if ($this->user->nick == $this->suser->nick) {
            $this->new_error_msg('No se permite Activar/Desactivar a uno mismo.');
        } else {
            // Un usuario no se puede Activar/Desactivar a él mismo.
            $this->suser->enabled = (fs_filter_input_req('senabled') == 'TRUE');

            if ($this->suser->save()) {
                if ($this->suser->enabled) {
                    $this->new_message('Usuario activado correctamente.', TRUE, 'login', TRUE);
                } else {
                    $this->new_message('Usuario desactivado correctamente.', TRUE, 'login', TRUE);
                }
            } else {
                $this->new_error_msg('Error al Activar/Desactivar el Usuario');
            }
        }
    }
}
