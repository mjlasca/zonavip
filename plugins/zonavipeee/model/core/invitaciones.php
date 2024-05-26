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
 * invitaciones
 *
 * @author Mario Lasluisa Castaño <mjlasluisa@gmail.com>
 */
class invitaciones extends \fs_model
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
    public $paginaorigen;
    /*
     * usuario asignado
     * @var varchar
     */
    public $usuarioinvita;
    /*
     * usuario asignado
     * @var varchar
     */
    public $correoinvitado;
    
    /*
     * Fecha registro
     * @var datetime
     */
    public $ultmod;

    public $fecha1;
    public $fecha2;
    public $conversion;

    

    public function __construct($data = FALSE)
    {
        
        parent::__construct('invitaciones', $data); /// aquí indicamos el NOMBRE DE LA TABLA
        if ($data) {
            $this->reg = $data['reg'];
            $this->paginaorigen = $data['paginaorigen'];
            $this->usuarioinvita = $data['usuarioinvita'];
            $this->correoinvitado = $data['correoinvitado'];
            $this->ultmod = $data['ultmod'];
            if(isset($data['conversion']))
                $this->conversion = $data['conversion'];
            
        } else {
            $this->reg = null;
            $this->paginaorigen = null;
            $this->usuarioinvita = null;
            $this->correoinvitado = null;
            $this->ultmod = null;
            $this->conversion = null;
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
     * @return \invitaciones|boolean
     */
    public function get($reg)
    {
        
        $sql = "SELECT * FROM " . $this->table_name . " WHERE  reg = " . $this->var2str($reg);
        $data = $this->db->select($sql);
        if ($data) {
            return new \invitaciones($data[0]);
        }
        return FALSE;
        
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
            if (!$this->exists()) {
               
                $sql = "INSERT INTO " . $this->table_name . " (paginaorigen,usuarioinvita,correoinvitado,ultmod) VALUES
                      (" . $this->var2str($this->paginaorigen)
                      . "," . $this->var2str($this->usuarioinvita) 
                      . "," . $this->var2str($this->correoinvitado) 
                    . "," . $this->var2str($this->ultmod) . ");";
                    
                if($this->db->exec($sql)){
                    return TRUE;
                }else{
                    return FALSE;
                }

            }
        }

        return FALSE;
    }

   


    /**
     * Devuelve un array con todos los invitacioneses
     * @return \invitaciones
     */
    public function all()
    {
        $this->clean_cache();
        /// leemos esta lista de la caché
        $listaa = $this->cache->get_array('invitaciones');
        if (empty($listaa)) {

            $filtro = "";

            if($this->fecha1 != ""){
                if($filtro == ""){
                    $filtro = " DATE(t1.ultmod) >= ".$this->fecha1;
                }
            }

            if($this->fecha2 != ""){
                if($filtro == ""){
                    $filtro = " DATE(t1.ultmod) <= ".$this->fecha2;
                }else{
                    $filtro .= " AND DATE(t1.ultmod) <= ".$this->fecha2;
                }
            }

            if($filtro != ""){
                $filtro = " AND ".$filtro;
            }

            /// si no está en caché, leemos de la base de datos
            $sql = "SELECT t1.reg,t1.paginaorigen,t1.usuarioinvita,t1.correoinvitado,t1.ultmod, (SELECT COUNT(*) FROM fs_users WHERE nick = t1.correoinvitado ) as conversion FROM " . $this->table_name ." t1 LEFT JOIN fs_users t2 ON  t1.paginaorigen = t2.nick ".$filtro;
            
            
            $data = $this->db->select($sql);
            if ($data) {
                foreach ($data as $a) {
                    $listaa[] = new \invitaciones($a);
                }
            }
            /// guardamos la lista en caché
            $this->cache->set('invitaciones', $listaa);
        }

        return $listaa;
    }



    /**
     * Elimina el almacén
     * @return bool
     */
    public function delete()
    {
        $this->clean_cache();
        return $this->db->exec("DELETE FROM " . $this->table_name . " WHERE reg = " . $this->var2str($this->reg) . " OR regmensajeresponde = " . $this->var2str($this->reg) . ";");
    }


    /**
     * Limpiamos la caché
     */
    private function clean_cache()
    {
        $this->cache->delete('invitaciones');
    }

    
}
