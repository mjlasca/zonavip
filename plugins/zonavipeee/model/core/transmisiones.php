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
 * transmisiones
 *
 * @author Mario Lasluisa Castaño <mjlasluisa@gmail.com>
 */
class transmisiones extends \fs_model
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
    public $link;
    /*
     * usuario asignado
     * @var varchar
     */
    public $nombre;

    public $descripcion;
    /*
     * usuario que registra
     * @var varchar
     */
    public $fechainicia;
    /*
     * Fecha caducidad
     * @var datetime
     */
    public $fechasemuestra;
   /*
     * Fecha caducidad
     * @var datetime
     */
    public $pago; 
    /*
     * Fecha caducidad
     * @var datetime
     */
    public $producto;
    /*
     * Fecha caducidad
     * @var datetime
     */
    public $useredit;
    /*
     * Fecha registro
     * @var datetime
     */
    public $ultmod;

    public $hora;
    

    public function __construct($data = FALSE)
    {
        
        parent::__construct('transmisiones', $data); /// aquí indicamos el NOMBRE DE LA TABLA
        if ($data) {
            $this->reg = $data['reg'];
            $this->link = $data['link'];
            $this->nombre = $data['nombre'];
            $this->fechainicia = $data['fechainicia'];
            $this->fechasemuestra = $data['fechasemuestra'];
            $this->pago = $data['pago'];
            $this->producto = $data['producto'];
            $this->descripcion = $data['descripcion'];
            $this->useredit = $data['useredit'];
            if(isset($data['hora']))
                $this->hora = $data['hora'];
            $this->ultmod = $data['ultmod'];
            
            
        } else {
            $this->reg = null;
            $this->link = null;
            $this->nombre = null;
            $this->fechainicia = null;
            $this->fechasemuestra = null;
            $this->pago = null;
            $this->producto = null;
            $this->descripcion = null;
            $this->hora = null;
            $this->useredit = null;
            $this->ultmod = null;
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
     * @return \transmisiones|boolean
     */
    public function get($reg)
    {
        $sql = "SELECT * FROM " . $this->table_name . " WHERE  reg = " . $this->var2str($reg);
        $data = $this->db->select($sql);
        if ($data) {
            return new \transmisiones($data[0]);
        }

        return FALSE;
    }

    /**
     * Devuelve el evento con el reg
     * @param int $reg
     * @return \transmisiones|boolean
     */
    public function notificacion()
    {

        $this->clean_cache();
        /// leemos esta lista de la caché
        $listaa = $this->cache->get_array('transmisiones');
        if (empty($listaa)) {
            $sql = "SELECT descripcion,pago,IF(producto IS NULL,false,producto) as producto,reg,nombre,link,DATE_FORMAT(fechainicia,'%d-%m-%Y') as fechainicia ,DATE_FORMAT(fechasemuestra,'%d-%m-%Y') as fechasemuestra, TIME(fechainicia) as hora,ultmod,useredit  FROM " . $this->table_name . " WHERE NOW() >= fechasemuestra AND NOW() <= fechainicia  ORDER BY fechainicia ASC ";

            
            $data = $this->db->select($sql);
            if ($data) {
                foreach ($data as $a) {
                    $listaa[] = new \transmisiones($a);
                }
            }
            /// guardamos la lista en caché
            $this->cache->set('transmisiones', $listaa);
        }

        return $listaa;
    }

   

    /**
     * Devuelve TRUE si el almacén existe
     * @return boolean
     */
    public function exists()
    {
        return $this->db->select("SELECT * FROM " . $this->table_name . " WHERE reg = " . $this->var2str($this->reg) . ";");
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
                    . " SET link = " . $this->var2str($this->link)
                    . ", nombre = " . $this->var2str($this->nombre)
                    . ", fechainicia = " . $this->var2str($this->fechainicia)
                    . ", fechasemuestra = " . $this->var2str($this->fechasemuestra)
                    . ", useredit = " . $this->var2str($this->useredit)
                    . ", ultmod = " . $this->var2str($this->ultmod)
                    . ", pago = " . $this->var2str($this->pago)
                    . ", descripcion = " . $this->var2str($this->descripcion)
                    . ", producto = " . $this->var2str($this->producto)
                    . "  WHERE reg = " . $this->var2str($this->reg) . ";";

                }else{
                    $sql = "INSERT INTO " . $this->table_name . " (link,nombre,fechainicia,fechasemuestra,useredit,ultmod,pago,producto,descripcion) VALUES(" . $this->var2str($this->link)
                    . "," . $this->var2str($this->nombre) 
                    . "," . $this->var2str($this->fechainicia) 
                    . "," . $this->var2str($this->fechasemuestra) 
                    . "," . $this->var2str($this->useredit) 
                    . "," . $this->var2str($this->ultmod) 
                    . "," . $this->var2str($this->pago) 
                    . "," . $this->var2str($this->producto) 
                    . "," . $this->var2str($this->descripcion) . ");";
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
     * Devuelve un array con todos los transmisiones
     * @return \transmisiones
     */
    public function all()
    {
        $this->clean_cache();
        /// leemos esta lista de la caché
        $listaa = $this->cache->get_array('transmisiones');
        if (empty($listaa)) {
            
            //approved, canceled, billet_printed, refunded, dispute, completed, blocked, chargeback, delayed, expired, wayting_payment
            $sql = "SELECT descripcion,pago,producto,reg,nombre,link,DATE_FORMAT(fechainicia,'%d-%m-%Y') as fechainicia ,DATE_FORMAT(fechasemuestra,'%d-%m-%Y') as fechasemuestra, TIME(fechainicia) as hora,ultmod,useredit FROM " . $this->table_name . " ";
            
            $data = $this->db->select($sql);
            if ($data) {
                foreach ($data as $a) {
                    $listaa[] = new \transmisiones($a);
                }
            }
            /// guardamos la lista en caché
            $this->cache->set('transmisiones', $listaa);
        }

        return $listaa;
    }
    
    


    /**
     * Limpiamos la caché
     */
    private function clean_cache()
    {
        $this->cache->delete('transmisiones');
    }

    
}
