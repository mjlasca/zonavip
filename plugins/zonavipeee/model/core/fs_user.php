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
namespace FacturaScripts\model;
require 'vendor/autoload.php';

/**
 * Usuario de FacturaScripts. Puede estar asociado a un agente.
 *
 * @author Carlos García Gómez <neorazorx@gmail.com>
 */
class fs_user extends \fs_model
{

    /**
     * Clave primaria. Varchar (12).
     * @var string 
     */
    public $nick;

    /**
     * Contraseña, en sha1
     * @var string 
     */
    public $password;

    /**
     * Email del usuario.
     * @var string 
     */
    public $email;

    /**
     * Clave de sesión. El cliente se la guarda en una cookie,
     * sirve para no tener que guardar la contraseña.
     * Se regenera cada vez que el cliente inicia sesión. Así se
     * impide que dos personas accedan con el mismo usuario.
     * @var string 
     */
    public $log_key;

    /**
     * TRUE -> el usuario ha iniciado sesión
     * No se guarda en la base de datos
     * @var boolean 
     */
    public $logged_on;

    /**
     * Código del agente/empleado asociado
     * @var string 
     */
    public $codagente;

    /**
     * El objeto agente asignado. Hay que llamar previamente la función get_agente().
     * @var agente 
     */
    public $agente;

    /**
     * TRUE -> el usuario es un administrador
     * @var boolean 
     */
    public $admin;

    /**
     * TRUE -> el usuario esta activo
     * @var boolean
     */
    public $enabled;

    /**
     * Fecha del último login.
     * @var string
     */
    public $last_login;

    /**
     * Hora del último login.
     * @var string
     */
    public $last_login_time;

    /**
     * Última IP usada
     * @var string
     */
    public $last_ip;

    /**
     * Último identificador de navegador usado
     * @var string
     */
    public $last_browser;

    /**
     * Página de inicio.
     * @var string
     */
    public $fs_page;

    /**
     * Página de inicio.
     * @var int
     */
    public $activo;

    /**
     * Plantilla CSS predeterminada.
     * @var string
     */
    public $css;
    private $menu;
    public $codverificacion;

    public $nombre;
    public $estadocrm;
    public $pais;
    public $prevpage;
    public $arregconsolidado;
    public $create_date;

    public $getorigen;
    public $getpaises;
    public $getnuevosprospectos;
    public $getingresos;
    public $fecha1;
    public $fecha2;

    public $all_prospectos;
    public $getingresocv;


    public function __construct($data = FALSE)
    {
        parent::__construct('fs_users');

        if ($data) {
            $this->nick = $data['nick'];
            $this->password = $data['password'];
            $this->email = $data['email'];
            $this->log_key = $data['log_key'];

            $this->codagente = NULL;
            if (isset($data['codagente'])) {
                $this->codagente = $data['codagente'];
            }

            $this->admin = $this->str2bool($data['admin']);

            $this->last_login = NULL;
            if ($data['last_login']) {
                $this->last_login = Date('d-m-Y', strtotime($data['last_login']));
            }

            $this->last_login_time = NULL;
            if ($data['last_login_time']) {
                $this->last_login_time = $data['last_login_time'];
            }

            $this->last_ip = $data['last_ip'];
            $this->last_browser = $data['last_browser'];
            $this->fs_page = $data['fs_page'];
            $this->activo = $data['activo'];
            $this->codverificacion = $data['codverificacion'];

            $this->nombre = $data['nombre'];
            $this->estadocrm = $data['estadocrm'];
            $this->pais = $data['pais'];
            $this->prevpage = $data['prevpage'];

            $this->create_date = $data['create_date'];

            $this->css = 'view/css/bootstrap-yeti.min.css';
            if (isset($data['css'])) {
                $this->css = $data['css'];
            }

            $this->enabled = TRUE;
            if (isset($data['enabled'])) {
                $this->enabled = $this->str2bool($data['enabled']);
            }
        } else {
            $this->nick = NULL;
            $this->password = NULL;
            $this->email = NULL;
            $this->log_key = NULL;
            $this->codagente = NULL;
            $this->admin = FALSE;
            $this->enabled = TRUE;
            $this->last_login = NULL;
            $this->last_login_time = NULL;
            $this->last_ip = NULL;
            $this->last_browser = NULL;
            $this->fs_page = NULL;
            $this->codverificacion = NULL;
            $this->activo = 0;
            $this->css = 'view/css/bootstrap-yeti.min.css';
            $this->nombre = NULL;
            $this->estadocrm = NULL;
            $this->pais = NULL;
            $this->prevpage = NULL;
            $this->create_date = date("Y-m-d H:i:s");
        }

        $this->logged_on = FALSE;
        $this->agente = NULL;
    }

    /**
     * Inserta valores por defecto a la tabla, en el proceso de creación de la misma.
     * @return string
     */
    protected function install()
    {
        $this->clean_cache(TRUE);
        
          
        
        /// Esta tabla tiene claves ajenas a agentes y fs_pages
        new \agente();
        new \fs_page();

        $this->new_message('Se ha creado el usuario <b>admin</b> con la contraseña <b>admin</b>.');
        if ($this->db->select("SELECT * FROM agentes WHERE codagente = '1';")) {
            return "INSERT INTO " . $this->table_name . " (nick,password,log_key,codagente,admin,enabled)
            VALUES ('admin','" . sha1('admin') . "',NULL,'1',TRUE,TRUE);";
        }

        return "INSERT INTO " . $this->table_name . " (nick,password,log_key,codagente,admin,enabled)
            VALUES ('admin','" . sha1('admin') . "',NULL,NULL,TRUE,TRUE);";
    }

    public function url()
    {
        if (is_null($this->nick)) {
            return 'index.php?page=admin_users';
        }

        return 'index.php?page=admin_user&snick=' . $this->nick;
    }

    /**
     * Devuelve el agente/empleado asociado
     * @return boolean|agente
     */
    public function get_agente()
    {
        if (isset($this->agente)) {
            return $this->agente;
        } else if (is_null($this->codagente)) {
            return FALSE;
        }

        $agente_model = new \agente();
        $agente = $agente_model->get($this->codagente);
        if ($agente) {
            $this->agente = $agente;
            return $this->agente;
        }

        $this->codagente = NULL;
        $this->save();
        return FALSE;
    }

    public function get_agente_fullname()
    {
        $agente = $this->get_agente();
        if ($agente) {
            return $agente->get_fullname();
        }

        return $this->nick;
    }

    public function get_agente_url()
    {
        $agente = $this->get_agente();
        if ($agente) {
            return $agente->url();
        }

        return '#';
    }

    /**
     * Devuelve el menú del usuario, el conjunto de páginas a las que tiene acceso.
     * @param boolean $reload
     * @return array
     */
    public function get_menu($reload = FALSE)
    {
        if (!isset($this->menu) || $reload) {
            $this->menu = array();
            $page = new \fs_page();

            if ($this->admin || FS_DEMO) {
                $this->menu = $page->all();
            } else {
                $access = new \fs_access();
                $access_list = $access->all_from_nick($this->nick);
                foreach ($page->all() as $p) {
                    foreach ($access_list as $a) {
                        if ($p->name == $a->fs_page) {
                            $this->menu[] = $p;
                            break;
                        }
                    }
                }
            }
        }
        return $this->menu;
    }

    /**
     * Devuelve TRUE si el usuario tiene acceso a la página solicitada.
     * @param string $page_name
     * @return boolean
     */
    public function have_access_to($page_name)
    {
        $status = FALSE;
        foreach ($this->get_menu() as $m) {
            if ($m->name == $page_name) {
                $status = TRUE;
                break;
            }
        }

        return $status;
    }

    /**
     * Devuelve TRUE si el usuario tiene permiso para eliminar elementos en la página solicitada.
     * @param string $page_name
     * @return boolean
     */
    public function allow_delete_on($page_name)
    {
        if ($this->admin || FS_DEMO) {
            return TRUE;
        }

        $status = FALSE;
        foreach ($this->get_accesses() as $a) {
            if ($a->fs_page == $page_name) {
                $status = $a->allow_delete;
                break;
            }
        }
        return $status;
    }

    /**
     * Devuelve la lista de accesos permitidos del usuario.
     * @return type
     */
    public function get_accesses()
    {
        $access = new \fs_access();
        return $access->all_from_nick($this->nick);
    }

    public function show_last_login()
    {
        if (is_null($this->last_login)) {
            return '-';
        }

        return Date('d-m-Y', strtotime($this->last_login)) . ' ' . $this->last_login_time;
    }

    public function set_password($pass = '')
    {
        $pass = trim($pass);
        if (mb_strlen($pass) > 1 && mb_strlen($pass) <= 32) {
            $this->password = sha1($pass);
            return TRUE;
        }

        $this->new_error_msg('La contraseña debe contener entre 1 y 32 caracteres.');
        return FALSE;
    }
    /*
     * Modifica y guarda la fecha de login si tiene una diferencia de más de 5 minutos
     * con la fecha guardada, así se evita guardar en cada consulta
     */

    public function update_login()
    {
        $ltime = strtotime($this->last_login . ' ' . $this->last_login_time);
        if (time() - $ltime >= 300) {
            $this->last_login = Date('d-m-Y');
            $this->last_login_time = Date('H:i:s');

            if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                $this->last_ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
            } else {
                $this->last_ip = $_SERVER['REMOTE_ADDR'];
            }

            $this->last_browser = $_SERVER['HTTP_USER_AGENT'];
            $this->save();
        }
    }

    /**
     * Genera una nueva clave de login, para usar en lugar de la contraseña (via cookie),
     * esto impide que dos o más personas utilicen el mismo usuario al mismo tiempo.
     */
    public function new_logkey()
    {
        if (is_null($this->log_key) || ! FS_DEMO) {
            $this->log_key = sha1(strval(rand()));
        }

        $this->logged_on = TRUE;
        $this->last_login = Date('d-m-Y');
        $this->last_login_time = Date('H:i:s');

        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $this->last_ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $this->last_ip = $_SERVER['REMOTE_ADDR'];
        }

        $this->last_browser = $_SERVER['HTTP_USER_AGENT'];
    }

    public function get($nick = '')
    {
        $data = $this->db->select("SELECT * FROM " . $this->table_name . " WHERE LOWER(nick) = LOWER(" . $this->var2str($nick) . ");");
        if ($data) {
            return new \fs_user($data[0]);
        }

        return FALSE;
    }

    public function exists()
    {
        if (is_null($this->nick)) {
            return FALSE;
        }

        return $this->db->select("SELECT * FROM " . $this->table_name . " WHERE LOWER(nick) = LOWER(" . $this->var2str($this->nick) . ");");
    }

    public function test()
    {
        $this->nick = trim($this->nick);
        $this->last_browser = $this->no_html($this->last_browser);

        

        return TRUE;
    }

    public function save()
    {
        if ($this->test()) {
            $this->clean_cache();

            if ($this->exists()) {
                $sql = "UPDATE " . $this->table_name . " SET password = " . $this->var2str($this->password)
                    . ", email = " . $this->var2str($this->email)
                    . ", log_key = " . $this->var2str($this->log_key)
                    . ", codagente = " . $this->var2str($this->codagente)
                    . ", admin = " . $this->var2str($this->admin)
                    . ", enabled = " . $this->var2str($this->enabled)
                    . ", last_login = " . $this->var2str($this->last_login)
                    . ", last_ip = " . $this->var2str($this->last_ip)
                    . ", last_browser = " . $this->var2str($this->last_browser)
                    . ", last_login_time = " . $this->var2str($this->last_login_time)
                    . ", fs_page = " . $this->var2str($this->fs_page)
                    . ", codverificacion = " . $this->var2str($this->codverificacion)
                    . ", css = " . $this->var2str($this->css)
                    . ", nombre = " . $this->var2str($this->nombre)
                    . ", estadocrm = " . $this->var2str($this->estadocrm)
                    . ", pais = " . $this->var2str($this->pais)
                    . ", prevpage = " . $this->var2str($this->prevpage)
                    . ", activo = " . $this->var2str($this->activo)
                    . ", create_date = " . $this->var2str($this->create_date)
                    . "  WHERE nick = " . $this->var2str($this->nick) . ";";
            } else {
                $sql = "INSERT INTO " . $this->table_name . " (nick,password,email,log_key,codagente,admin,enabled,last_login,last_login_time,last_ip,last_browser,fs_page,codverificacion,css,nombre,estadocrm,pais,prevpage,create_date) VALUES
               (" . $this->var2str($this->nick)
                    . "," . $this->var2str($this->password)
                    . "," . $this->var2str($this->email)
                    . "," . $this->var2str($this->log_key)
                    . "," . $this->var2str($this->codagente)
                    . "," . $this->var2str($this->admin)
                    . "," . $this->var2str($this->enabled)
                    . "," . $this->var2str($this->last_login)
                    . "," . $this->var2str($this->last_login_time)
                    . "," . $this->var2str($this->last_ip)
                    . "," . $this->var2str($this->last_browser)
                    . "," . $this->var2str($this->fs_page)
                    . "," . $this->var2str($this->codverificacion)
                    . "," . $this->var2str($this->css)
                    . "," . $this->var2str($this->nombre)
                    . "," . $this->var2str($this->estadocrm)
                    . "," . $this->var2str($this->pais)
                    . "," . $this->var2str($this->prevpage)
                    . "," . $this->var2str($this->create_date) . ");";
            }
            
            return $this->db->exec($sql);
        }

        return FALSE;
    }

    public function delete()
    {
        $this->clean_cache();
        return $this->db->exec("DELETE FROM " . $this->table_name . " WHERE nick = " . $this->var2str($this->nick) . ";");
    }

    public function clean_cache($full = FALSE)
    {
        $this->cache->delete('m_fs_user_all');

        if ($full) {
            $this->clean_checked_tables();
        }
    }

    /**
     * Devuelve la lista completa de usuarios de FacturaScripts.
     * @return \fs_user
     */
    public function all()
    {
        /// consultamos primero en la cache
        $userlist = $this->cache->get_array('m_fs_user_all');

        if (empty($userlist)) {
            /// si no está en la cache, consultamos la base de datos
            $data = $this->db->select("SELECT * FROM " . $this->table_name . " ORDER BY lower(nick) ASC;");
            if ($data) {
                foreach ($data as $u) {
                    $userlist[] = new \fs_user($u);
                }
            }

            /// guardamos en cache
            $this->cache->set('m_fs_user_all', $userlist);
        }

        return $userlist;
    }

    public function all_inactivo($meses){

        $fecha_actual = date("Y-m-d");
        $fecha_actual = date("Y-m-d",strtotime($fecha_actual."- ".$meses." month")); 

        $sql = "SELECT t1.prevpage,t1.nick,t1.enabled,t1.last_login,t1.create_date,t1.nombre,t1.estadocrm,(SELECT nombre FROM paises WHERE codpais = t1.pais) as pais FROM " . $this->table_name . " t1 WHERE DATE(t1.last_login) <= '".$fecha_actual."' ";

        return $this->db->select($sql);
    }

    

   /**
     * Devuelve la lista completa de usuarios de FacturaScripts.
     * @return \fs_user
     */
    public function all_prospectos()
    {

        $filtro = "";

        if($this->fecha1 != ""){
            if($this->tipofecha == 1){
                if($filtro != "")
                    $filtro .= " AND t1.create_date >= '".$this->fecha1."' ";
                else
                    $filtro = " AND t1.create_date >= '".$this->fecha1."' ";
            }else{
                if($filtro != "")
                    $filtro .= " AND t1.last_login >= '".$this->fecha1."' ";
                else
                    $filtro = " AND t1.last_login >= '".$this->fecha1."' ";
            }

        }

        if($this->fecha2 != ""){
            if($this->tipofecha == 1){
                if($filtro != "")
                    $filtro .= " AND t1.create_date <= '".$this->fecha2."' ";
                else
                    $filtro = " AND t1.create_date <= '".$this->fecha2."' ";
            }else{
                if($filtro != "")
                    $filtro .= " AND t1.last_login <= '".$this->fecha2."' ";
                else
                    $filtro = " AND t1.last_login <= '".$this->fecha2."' ";
            }
        }


        if($this->getingresocv != ""){
            if($filtro != "")
                    $filtro .= " AND t1.activo >= '".$this->getingresocv."' ";
                else
                    $filtro = " AND t1.activo >= '".$this->getingresocv."' ";
        }

        $sql = "SELECT t1.prevpage,t1.nick,t1.enabled,t1.last_login,t1.create_date,t1.nombre,t1.estadocrm,(SELECT nombre FROM paises WHERE codpais = t1.pais) as pais FROM " . $this->table_name . " t1 WHERE (SELECT COUNT(user) FROM hotmartuser WHERE t1.nick = user) = 0 ".$filtro;
            

        
        $this->all_prospectos = $this->db->select($sql);

        $sql = "SELECT (SELECT nombre FROM paises WHERE codpais = t1.pais) as pais, COUNT(pais) as cant FROM " . $this->table_name . " t1 WHERE t1.pais IS NOT NULL AND  (SELECT COUNT(user) FROM hotmartuser WHERE t1.nick = user) = 0 ".$filtro . " GROUP BY t1.pais ORDER BY t1.pais DESC" ;

        
        $this->getpaises = $this->db->select($sql);

        $sql = "SELECT t1.prevpage as prevpage, COUNT(t1.prevpage) as cant FROM " . $this->table_name . " t1 WHERE t1.prevpage  IS NOT NULL AND  (SELECT COUNT(user) FROM hotmartuser WHERE t1.nick = user) = 0 ".$filtro . " GROUP BY t1.prevpage " ;

        
        
        $this->getorigen = $this->db->select($sql);

        $filtro = "";

        if($this->fecha1 != ""){
                if($filtro != "")
                    $filtro .= " AND create_date >= '".$this->fecha1."' ";
                else
                    $filtro = " AND create_date >= '".$this->fecha1."' ";
        }

        if($this->fecha2 != ""){
            
                if($filtro != "")
                    $filtro .= " AND create_date <= '".$this->fecha2."' ";
                else
                    $filtro = " AND create_date <= '".$this->fecha2."' ";
            
        }
        if($this->getingresocv != ""){
            if($filtro != "")
                    $filtro .= " AND activo >= '".$this->getingresocv."' ";
                else
                    $filtro = " AND activo >= '".$this->getingresocv."' ";
        }


        $sql = "SELECT COUNT(*) as cant FROM " . $this->table_name . " t1 WHERE  (SELECT COUNT(user) FROM hotmartuser WHERE t1.nick = user) = 0 ".$filtro ;

        
        $this->getnuevosprospectos = $this->db->select($sql)[0]["cant"];

        $filtro = "";

        if($this->fecha1 != ""){
            if($filtro != "")
                $filtro .= " AND last_login >= '".$this->fecha1."' ";
            else
                $filtro = " AND last_login >= '".$this->fecha1."' ";
        }

        if($this->fecha2 != ""){
            
            if($filtro != "")
                $filtro .= " AND last_login <= '".$this->fecha2."' ";
            else
                $filtro = " AND last_login <= '".$this->fecha2."' ";
            
        }
        if($this->getingresocv != ""){
            if($filtro != "")
                    $filtro .= " AND activo >= '".$this->getingresocv."' ";
                else
                    $filtro = " AND activo >= '".$this->getingresocv."' ";
        }


        $sql = "SELECT COUNT(*) as cant FROM " . $this->table_name . " t1 WHERE  (SELECT COUNT(user) FROM hotmartuser WHERE t1.nick = user) = 0 ".$filtro ;

        $this->getingresos = $this->db->select($sql)[0]["cant"];
        

        

        return $this->all_prospectos;
    }




    /**
     * Devuelve la lista completa de usuarios activados de FacturaScripts.
     * @return \fs_user
     */
    public function useractive($act)
    {
        $data = $this->db->exec("UPDATE " . $this->table_name . " SET activo = '".$act."'  WHERE nick = '".$this->nick."';");
        return $data;
    }


    function revisarusermail(){
        
        $rest = valideSubscriber($this->nick);
        
        if($rest['status'] !== 200){
            return 'NO';
        }else{
            $data =  json_decode( $rest['data'] );
            $restGroup = CreateSubscriberInGroup($this->nick,GROUP_ZONAVIP);
            if($restGroup['status'] == 200){
                return 'SI';
            }
        }
    }


    /**
     * Devuelve la lista completa de usuarios activados de FacturaScripts.
     * @return \fs_user
     */
    public function all_enabled()
    {
        $userlist = array();

        $data = $this->db->select("SELECT * FROM " . $this->table_name . " WHERE enabled = TRUE ORDER BY lower(nick) ASC;");
        if ($data) {
            foreach ($data as $u) {
                $userlist[] = new \fs_user($u);
            }
        }

        return $userlist;
    }
}
