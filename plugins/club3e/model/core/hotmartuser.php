<?php
/**
 * This file is part of FacturaScrregts
 * Copyright (C) 2013-2018 Carlos Garcia Gomez <neorazorx@gmail.com>
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

/**
 * hotmartuser
 *
 * @author Mario Lasluisa Castaño <mjlasluisa@gmail.com>
 */
class hotmartuser extends \fs_model
{
    /*
     * Llave primaria
     * @var integer
     */
    public $reg;
    /*
     * id del registro
     * @var int
     */
    public $user;
    /*
     * usuario asignado
     * @var varchar
     */
    public $idproducto;
    /*
     * usuario que registra
     * @var varchar
     */
    public $nombreproducto;
    /*
     * Fecha caducidad
     * @var datetime
     */
    public $estado;
    /*
     * dias suscripcion
     * @var datetime
     */
    public $periocidad;

    /*
     * Fecha caducidad
     * @var datetime
     */
    public $fechagarantia;
    /*
     * Fecha caducidad
     * @var datetime
     */
    public $fechacaducidad;
    /*
     * Fecha registro
     * @var datetime
     */
    public $ultmod;

    public $fecha1;
    public $fecha2;

    public $ingresosclientes;
    public $datoscompletos;
    public $getregistro;
    public $last_login;

    public $ingresocreacion;

    public $getpaises;
    public $getorigen;

    

    public function __construct($data = FALSE)
    {
        
        parent::__construct('hotmartuser', $data); /// aquí indicamos el NOMBRE DE LA TABLA
        if ($data) {
            $this->reg = $data['reg'];
            $this->user = $data['user'];
            $this->idproducto = $data['idproducto'];
            $this->nombreproducto = $data['nombreproducto'];
            $this->estado = $data['estado'];
            $this->periocidad = $data['periocidad'];
            $this->fechagarantia = $data['fechagarantia'];
            $this->fechacaducidad = $data['fechacaducidad'];
            $this->ultmod = $data['ultmod'];
            if(isset($data["last_login"]))
                $this->last_login = $data["last_login"];
            
        } else {
            $this->reg = null;
            $this->user = null;
            $this->idproducto = null;
            $this->nombreproducto = null;
            $this->estado = null;
            $this->periocidad = null;
            $this->fechagarantia = null;
            $this->fechacaducidad = null;
            $this->ultmod = null;
            $this->last_login = null;
        }
    }

    protected function install()
    {
        
    }

    public function model_class_name()
    {
        return __CLASS__;
    }

    public function primary_column()
    {
        return 'reg';
    }

    /**
     * Devuelve el evento con el reg
     * @param int $reg
     * @return \hotmartuser|boolean
     */
    public function get_user()
    {
        
        $sql = "SELECT * FROM " . $this->table_name . " WHERE user = '".$this->user."'  AND   (fechacaducidad >= NOW()) ";
        $data = $this->db->select($sql);
        if ($data) {
            return new \hotmartuser($data[0]);
        }
        return FALSE;
        
    }

    /**
     * Devuelve el evento con el reg
     * @param int $reg
     * @return \hotmartuser|boolean
     */
    public function get_user_final()
    {
        
        $sql = "SELECT * FROM " . $this->table_name . " WHERE user = '".$this->user."'  AND  (fechacaducidad >= NOW()) ";

        $listaa = [];
        $data = $this->db->select($sql);
        if ($data) {
            foreach ($data as $a) {
                $listaa[] = new \hotmartuser($a);
            }
        }

        return $listaa;
        
    }

    /**
     * Devuelve el evento con el reg
     * @param int $reg
     * @return \hotmartuser|boolean
     */
    public function get_user_idproducto($user,$idproducto)
    {
        
        $sql = "SELECT * FROM " . $this->table_name . " WHERE user = '".$user."' AND idproducto = '".$idproducto."' ";
        $data = $this->db->select($sql);
        if ($data) {
            return new \hotmartuser($data[0]);
        }
        return FALSE;
        
    }

    


   
    
    /**
     * Devuelve el evento con el reg
     * @param int $reg
     * @return \hotmartuser|boolean
     */
    public function get($reg)
    {
        
        $sql = "SELECT * FROM " . $this->table_name . " WHERE  reg = " . $this->var2str($reg);
        $data = $this->db->select($sql);
        if ($data) {
            return new \hotmartuser($data[0]);
        }
        return FALSE;
        
    }

   

    /**
     * Devuelve TRUE si el almacén existe
     * @return boolean
     */
    public function exists()
    {
        return $this->db->select("SELECT * FROM " . $this->table_name . " WHERE user = " . $this->var2str($this->user) . " AND idproducto = " . $this->var2str($this->idproducto) . ";");
    }

    
    
    
    private function test(){
        return TRUE;
    }

    /**
     * Guarda los datos en la base de datos
     * @return boolean
     */
    public function save()
    {
        if ($this->test()) {
            $this->clean_cache();
                if ($this->exists()) {

                    $sql = "UPDATE " . $this->table_name 
                    . " SET user = " . $this->var2str($this->user)
                    . ", idproducto = " . $this->var2str($this->idproducto)
                    . ", nombreproducto = " . $this->var2str($this->nombreproducto)
                    . ", estado = " . $this->var2str($this->estado)
                    . ", periocidad = " . $this->var2str($this->periocidad)
                    . ", fechagarantia = " . $this->var2str($this->fechagarantia)
                    . ", fechacaducidad = " . $this->var2str($this->fechacaducidad)
                    . ", ultmod = " . $this->var2str($this->ultmod)
                    . "  WHERE user = " . $this->var2str($this->user) . " AND idproducto = " . $this->var2str($this->idproducto) . ";";

                }else{
                    $sql = "INSERT INTO " . $this->table_name . " (user,idproducto,nombreproducto,estado,periocidad,fechagarantia,fechacaducidad,ultmod) VALUES(" . $this->var2str($this->user)
                    . "," . $this->var2str($this->idproducto) 
                    . "," . $this->var2str($this->nombreproducto) 
                    . "," . $this->var2str($this->estado) 
                    . "," . $this->var2str($this->periocidad) 
                    . "," . $this->var2str($this->fechagarantia) 
                    . "," . $this->var2str($this->fechacaducidad) 
                    . "," . $this->var2str($this->ultmod) . ");";
                }
               
                
                

                if($this->db->exec($sql)){
                    return TRUE;
                }else{
                    return FALSE;
                }

            
        }

        return FALSE;
    }



    /**
     * Elimina el almacén
     * @return bool
     */
    public function delete()
    {
        $this->clean_cache();
        return $this->db->exec("DELETE FROM " . $this->table_name . " WHERE reg = " . $this->var2str($this->reg) . ";");
    }

  

    /**
     * Devuelve un array con todos los hotmartuser
     * @return \hotmartuser
     */
    public function all()
    {
        $this->clean_cache();
        /// leemos esta lista de la caché
        $listaa = $this->cache->get_array('hotmartuser');
        if (empty($listaa)) {




            $query = "";
            if($this->user != ""){
                $query = " t1.user = '".$this->user."' ";
            }
            if($this->estado != ""){
                if($query != "")
                    $query .= " AND  t1.estado = '".$this->estado."' ";
                else
                    $query = " t1.estado = '".$this->estado."' ";
            }

            if($this->fecha1 != ""){
                
                if($query != "")
                    $query .= " AND DATE( t1.ultmod ) >= DATE('".$this->fecha1."' ) ";
                else
                    $query = "  DATE( t1.ultmod) >= DATE('".$this->fecha1."') ";
            }

            if($this->fecha2 != ""){

                if($query != "")
                    $query .= " AND DATE( t1.ultmod ) <= DATE('".$this->fecha2."')";
                else
                    $query = " DATE( t1.ultmod ) <= DATE('".$this->fecha2."')";
            }

            if($this->fechacaducidad != ""){
                
                if($query != "")
                    $query .= " AND DATE( t1.fechacaducidad ) ".$this->fechacaducidad."";
                else
                    $query = " DATE( t1.fechacaducidad ) ".$this->fechacaducidad."";
            }

            if($this->ingresocreacion == 2){
                $query = "";
                if($this->user != ""){
                    $query = " t1.user = '".$this->user."' ";
                }
                if($this->estado != ""){
                    if($query != "")
                        $query .= " AND  t1.estado = '".$this->estado."' ";
                    else
                        $query = " t1.estado = '".$this->estado."' ";
                }
    
                if($this->fecha1 != ""){
                    
                    if($query != "")
                        $query .= " AND DATE( t2.last_login ) >= DATE('".$this->fecha1."' ) ";
                    else
                        $query = "  DATE( t2.last_login) >= DATE('".$this->fecha1."') ";
                }
    
                if($this->fecha2 != ""){
    
                    if($query != "")
                        $query .= " AND DATE( t2.last_login ) <= DATE('".$this->fecha2."')";
                    else
                        $query = " DATE( t2.last_login ) <= DATE('".$this->fecha2."')";
                }

                if($this->fechacaducidad != ""){
                
                    if($query != "")
                        $query .= " AND DATE( t1.fechacaducidad ) ".$this->fechacaducidad."";
                    else
                        $query = " DATE( t1.fechacaducidad ) ".$this->fechacaducidad."";
                }
    
                
            }

            if($query != "")
                $query = " AND ".$query;
            
            
            //approved, canceled, billet_printed, refunded, dispute, completed, blocked, chargeback, delayed, expired, wayting_payment
            $sql = "SELECT t1.reg,t1.user,t1.idproducto,t1.nombreproducto,t1.estado,t1.periocidad,t1.fechagarantia,t1.fechacaducidad,t1.ultmod, t2.last_login FROM " . $this->table_name . " t1  INNER JOIN fs_users t2 ON t1.user = t2.nick ".$query;
            
            $data = $this->db->select($sql);
            if ($data) {
                foreach ($data as $a) {
                    $listaa[] = new \hotmartuser($a);
                }
            }


            $sql = "SELECT (SELECT nombre FROM paises WHERE codpais = t2.pais) as pais, COUNT(pais) as cant FROM " . $this->table_name . " t1  INNER JOIN fs_users t2 ON t1.user = t2.nick AND t2.pais IS NOT NULL ".$query." GROUP BY t2.pais ORDER BY t2.pais DESC" ;
            
        
            $this->getpaises = $this->db->select($sql);

            $sql = "SELECT t2.prevpage as prevpage, COUNT(prevpage) as cant FROM " . $this->table_name . " t1  INNER JOIN fs_users t2 ON t1.user = t2.nick AND t2.prevpage IS NOT NULL ".$query." GROUP BY t2.prevpage";
            
            

            $this->getorigen = $this->db->select($sql);


            //"t1.reg,t1.user,t1.idproducto,t1.nombreproducto,t1.estado,t1.periocidad,t1.fechagarantia,t1.fechacaducidad,t1.ultmod";

            $query = "";
                if($this->user != ""){
                    $query = " t1.user = '".$this->user."' ";
                }
                if($this->estado != ""){
                    if($query != "")
                        $query .= " AND  t1.estado = '".$this->estado."' ";
                    else
                        $query = " t1.estado = '".$this->estado."' ";
                }
    
                if($this->fecha1 != ""){
                    
                    if($query != "")
                        $query .= " AND DATE( t2.last_login ) >= DATE('".$this->fecha1."' ) ";
                    else
                        $query = "  DATE( t2.last_login) >= DATE('".$this->fecha1."') ";
                }

                if($this->fechacaducidad != ""){

                    if($query != "")
                        $query .= " AND DATE( t1.fechacaducidad ) ".$this->fechacaducidad."";
                    else
                        $query = " DATE( t1.fechacaducidad ) ".$this->fechacaducidad."";
                }
    
                if($this->fecha2 != ""){
    
                    if($query != "")
                        $query .= " AND DATE( t2.last_login ) <= DATE('".$this->fecha2."')";
                    else
                        $query = " DATE( t2.last_login ) <= DATE('".$this->fecha2."')";
                }

            if($query != "")
                $query = " AND ".$query;


            $sql = "SELECT COUNT(*) as cant FROM " . $this->table_name . " t1 INNER JOIN fs_users t2 ON t1.user = t2.nick  ".$query;
            $this->ingresosclientes = $this->db->select($sql)[0]["cant"];

            

            $query = "";
            if($this->user != ""){
                $query = " t1.user = '".$this->user."' ";
            }
            if($this->estado != ""){
                if($query != "")
                    $query .= " AND  t1.estado = '".$this->estado."' ";
                else
                    $query = " t1.estado = '".$this->estado."' ";
            }

            if($this->fecha1 != ""){
                
                if($query != "")
                    $query .= " AND DATE( t1.ultmod ) >= DATE('".$this->fecha1."' ) ";
                else
                    $query = "  DATE( t1.ultmod) >= DATE('".$this->fecha1."') ";
            }

            if($this->fecha2 != ""){

                if($query != "")
                    $query .= " AND DATE( t1.ultmod ) <= DATE('".$this->fecha2."')";
                else
                    $query = " DATE( t1.ultmod ) <= DATE('".$this->fecha2."')";
            }

            if($this->fechacaducidad != ""){

                if($query != "")
                    $query .= " AND DATE( t1.fechacaducidad ) ".$this->fechacaducidad."";
                else
                    $query = " DATE( t1.fechacaducidad ) ".$this->fechacaducidad."";
            }

            if($query != "")
                $query = " AND ".$query;
                
                
            $sql = "SELECT COUNT(*) as cant FROM " . $this->table_name . " t1 INNER JOIN fs_users t2 ON t1.user = t2.nick  ".$query;
            $this->getregistro = $this->db->select($sql)[0]["cant"];
            

            /// guardamos la lista en caché
            $this->cache->set('hotmartuser', $listaa);
        }

        $this->datoscompletos = $listaa;

        return $listaa;
    }

    /**
     * Devuelve un array con todos los hotmartuser
     * @return \hotmartuser
     */
    public function get_users()
    {
        $sql = "SELECT t1.reg,t2.nombre,t1.user,t1.idproducto,t1.nombreproducto,t1.estado,t1.periocidad,t1.fechagarantia,t1.fechacaducidad,t1.ultmod, t2.last_login FROM " . $this->table_name . " t1  INNER JOIN fs_users t2 ON t1.user = t2.nick ";
            
        return $this->db->select($sql);
    }

    /**
     * Devuelve un array con todos los hotmartuser
     * @return \hotmartuser
     */
    public function all_user()
    {
        $this->clean_cache();
        /// leemos esta lista de la caché
        $listaa = $this->cache->get_array('hotmartuser');
        if (empty($listaa)) {
            
            //approved, canceled, billet_printed, refunded, dispute, completed, blocked, chargeback, delayed, expired, wayting_payment
            $sql = "SELECT *, IF(fechacaducidad > NOW(), 'VIGENTE','CADUCO') as estado FROM " . $this->table_name . " WHERE user = '".$this->user."'  ";

            $data = $this->db->select($sql);
            if ($data) {
                foreach ($data as $a) {
                    $listaa[] = new \hotmartuser($a);
                }
            }
            /// guardamos la lista en caché
            $this->cache->set('hotmartuser', $listaa);
        }

        return $listaa;
    }

    /**
     * Devuelve un array con todos los hotmartuser
     * @return \hotmartuser
     */
    public function all_user_producto()
    {
        $this->clean_cache();
        /// leemos esta lista de la caché
        $listaa = $this->cache->get_array('hotmartuser');
        if (empty($listaa)) {
            
            //approved, canceled, billet_printed, refunded, dispute, completed, blocked, chargeback, delayed, expired, wayting_payment
            $sql = "SELECT * FROM " . $this->table_name . " WHERE user = '".$this->user."' AND idproducto = '".$this->idproducto."' AND (fechacaducidad >= NOW()) AND (estado = 'approved' OR estado = 'completed' ) ";


            $data = $this->db->select($sql);
            if ($data) {
                foreach ($data as $a) {
                    $listaa[] = new \hotmartuser($a);
                }
            }
            /// guardamos la lista en caché
            $this->cache->set('hotmartuser', $listaa);
        }

        return $listaa;
    }

    /**
     * Devuelve un array con todos los hotmartuser
     * @return \hotmartuser
     */
    public function all_estudiante()
    {
            
        $sql = "SELECT t1.*,t2.nombre FROM " . $this->table_name . " t1 INNER JOIN fs_users t2 ON t1.user = t2.nick WHERE  t1.idproducto = '".$this->idproducto."' AND (t1.fechacaducidad >= NOW()) AND (t1.estado = 'approved' OR t1.estado = 'completed' ) ORDER BY t2.nombre ASC ";

        $this->new_message("SQL ".$sql);

        $data = $this->db->select($sql);
        return $data;
    }
    
    


    /**
     * Limpiamos la caché
     */
    private function clean_cache()
    {
        $this->cache->delete('hotmartuser');
    }

    
}
