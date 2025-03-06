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
 * zonavipdb
 *
 * @author Mario Lasluisa Castaño <mjlasluisa@gmail.com>
 */
class zonavipdb extends \fs_model
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
    public $id;
    /*
     * Nombre del banco
     * @var string
     */
    public $nombrevideo;
    /*
     * Descrregción del banco
     * @var string
     */
    public $idyoutube;
    /*
     * Usuario que edita el registro
     * @var string
     */
    public $archivodescarga;
    /*
     * fecha y hora de la última edición
     * @var ultmod
     */
    public $pdf;
    /*
     * Código del estado
     * @var codestado
     */
    public $useredit;
    /*
     * Código del estado
     * @var codestado
     */
    public $ultmod;
    /*
     * Código del estado
     * @var codestado
     */
    public $codestado;
    /*
     * Tipo de contenido de pago o gratuito
     * @var codestado
     */
    public $pago;
    /*
     * Id del vídeo en viemo para colocar el iframe
     * @var codestado
     */
    public $idvimeo;
    /*
     * Grupo de vídeos o contenidos
     * @var codestado
     */
    public $grupo;
    /*
     * Categoría de vídeos o contenidos
     * @var codestado
     */
    public $categoria;
    /*
     * Código del estado
     * @var codestado
     */
    public $busqueda;
    /*
     * Filtro por grupo
     * @var codestado
     */
    public $grupoFiltro;
    /*
     * filtro por pago o no pago
     * @var codestado
     */
    public $pagoFiltro;
    /*
     * Filtro por categorìa
     * @var codestado
     */
    public $categoriaFiltro;

    /*
     * url img
     * @var codestado
     */
    public $urlminiatura;
    /**
     * Si es curso colocamos el código del curso
     * @var varchar 
     */
    public $curso;
    /**
     * modulo del curso
     * @var int
     */
    public $modulocurso;
    /**
     * leccion
     * @var int
     */
    public $leccioncurso;
    
    /**
     * leccion
     * @var varchar
     */
    public $detalle;

    public $upload;
    public $detalleupload;

    public $nombreproducto;
    public $linkpago;
    public $nombremodulo;
    public $numeroleccion;
    public $limit_date;

    public function __construct($data = FALSE)
    {
        
        parent::__construct('zonavipdb', $data); /// aquí indicamos el NOMBRE DE LA TABLA
        if ($data) {
            $this->reg = $data['reg'];
            $this->id = $data['id'];
            $this->nombrevideo = $data['nombrevideo'];
            $this->archivodescarga = $data['archivodescarga'];
            $this->pdf = $data['pdf'];
            $this->idyoutube = $data['idyoutube'];
            $this->useredit = $data['ultmod'];
            $this->ultmod = $data['ultmod'];
            $this->codestado = $data['codestado'];
            $this->pago = $data['pago'];
            $this->idvimeo = $data['idvimeo'];
            $this->grupo = $data['grupo'];
            $this->categoria = $data['categoria'];
            $this->urlminiatura = $data['urlminiatura'];
            $this->curso = $data['curso'];
            $this->modulocurso = $data['modulocurso'];
            $this->leccioncurso = $data['leccioncurso'];
            $this->detalle = $data['detalle'];
            if(isset($data['nombreproducto']))
                $this->nombreproducto = $data['nombreproducto'];
            if(isset($data['linkpago']))
                $this->linkpago = $data['linkpago'];
            $this->nombremodulo = $data['nombremodulo'];
            $this->numeroleccion = $data['numeroleccion'];
            $this->upload = $data['upload'];
            $this->detalleupload = $data['detalleupload'];
            $this->limit_date = $data['limit_date'] ?? null;
            
        } else {
            $this->reg = null;
            $this->id = null;
            $this->nombrevideo = null;
            $this->archivodescarga = null;
            $this->pdf = null;
            $this->idyoutube = null;
            $this->useredit = null;
            $this->ultmod = null;
            $this->codestado = 1;
            $this->pago = null;
            $this->idvimeo = null;
            $this->grupo = null;
            $this->categoria = null;
            $this->urlminiatura = null;
            $this->curso = null;
            $this->modulocurso = null;
            $this->leccioncurso = null;
            $this->detalle = null;
            $this->nombreproducto = null;
            $this->linkpago = null;
            $this->nombremodulo = null;
            $this->numeroleccion = null;
            $this->upload = null;
            $this->detalleupload = null;
            $this->limit_date = null;
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
     * Devuelve el registro con el id específico
     * @param int $id
     * @return \zonavipdb|boolean
     */
    public function get_id($id)
    {
        
        $sql = "SELECT * FROM " . $this->table_name . " WHERE  id = " . $this->var2str($id);
        $data = $this->db->select($sql);
        if ($data) {
            /*$this->reg = $reg;
            $this->peso = 1;
            $this->save();*/
            return new \zonavipdb($data[0]);
        }
        return FALSE;
        
    }

    /**
     * Devuelve el registro con el id específico
     * @param int $id
     * @return \zonavipdb|boolean
     */
    public function get_consecutivo_id()
    {
        
        $sql = "SELECT MAX(id) as id FROM " . $this->table_name ;
        $data = $this->db->select($sql);
        if ($data) {
            
            return $data[0]["id"] + 1;
        }
        return 0;
        
    }


    
    
    
    
    /**
     * Devuelve el evento con el reg
     * @param int $reg
     * @return \zonavipdb|boolean
     */
    public function get($reg)
    {
        
        $sql = "SELECT t1.*,t2.nombre as nombreproducto,t2.linkpago FROM " . $this->table_name . "  t1 LEFT JOIN  hotmartproductos t2 ON t1.grupo = t2.idproducto WHERE t1.codestado=1 AND t1.reg = " . $this->var2str($reg);
        $data = $this->db->select($sql);
        if ($data) {
            /*$this->reg = $reg;
            $this->peso = 1;
            $this->save();*/
            return new \zonavipdb($data[0]);
        }
        return FALSE;
        
    }

    /**
     * Devuelve las categorías que hay
     * @return \zonavipdb|boolean
     */
    public function get_actividades_cursos($curso_)
    {
        
        $sql = "SELECT reg,nombrevideo,curso,detalleupload FROM " . $this->table_name . " WHERE curso = '".$curso_."' AND upload = 1 ;";
        $data = $this->db->select($sql);
        
        if ($data) {
            return $data;
        }
        return FALSE;
        
    }

     /**
     * Devuelve las categorías que hay
     * @return \zonavipdb|boolean
     */
    public function get_categorias()
    {
        
        $sql = "SELECT UPPER(categoria) as categoria FROM " . $this->table_name . " WHERE (curso = '' OR curso IS NULL) GROUP BY UPPER(categoria) ORDER BY categoria ASC;";
        $data = $this->db->select($sql);
        
        if ($data) {
            return $data;
        }
        return FALSE;
        
    }

    /**
     * Devolverse de video a video en la misma categoría
     * @return \zonavipdb|boolean
     */
    public function get_preview_curso($leccion,$curso, $moducurso)
    {
        
        $sql = "SELECT * FROM " . $this->table_name . " WHERE  modulocurso = '".$moducurso."' AND codestado=1  AND  lower(curso) = lower('".$curso."')  AND   numeroleccion < '".$leccion."' ORDER BY numeroleccion DESC limit 1;";
        $data = $this->db->select($sql);
        
        if ($data) {
            return $data[0];
        }else{
            $sql = "SELECT * FROM " . $this->table_name . " WHERE modulocurso < '".$moducurso."' AND codestado=1  AND  lower(curso) = lower('".$curso."')   ORDER BY numeroleccion DESC limit 1;";
            return  $this->db->select($sql)[0] ?? FALSE;
        }
        return FALSE;
        
    }

         /**
     * Pasar de video a video en la misma categoría
     * @return \zonavipdb|boolean
     */
    public function get_next_curso($leccion,$curso, $moducurso)
    {
        
        $sql = "SELECT * FROM " . $this->table_name . " WHERE  modulocurso = '".$moducurso."' AND  codestado=1  AND  lower(curso) = lower('".$curso."')  AND   numeroleccion > '".$leccion."' ORDER BY numeroleccion ASC limit 1;";
        $data = $this->db->select($sql);
        if ($data) {
            return $data[0];
        }else{
            $sql = "SELECT * FROM " . $this->table_name . " WHERE modulocurso > '".$moducurso."' AND codestado=1  AND  lower(curso) = lower('".$curso."')   ORDER BY numeroleccion ASC limit 1;";
            return  $this->db->select($sql)[0] ?? FALSE;
        }
        return FALSE;
        
    }


     /**
     * Devolverse de video a video en la misma categoría
     * @return \zonavipdb|boolean
     */
    public function get_preview($reg)
    {
        
        $sql = "SELECT * FROM " . $this->table_name . " WHERE codestado=1  AND  lower(categoria) = lower('".$this->categoriaFiltro."')  AND   reg < '".$reg."' ORDER BY reg DESC limit 1;";
        $data = $this->db->select($sql);
        
        if ($data) {
            return $data[0];
        }
        return FALSE;
        
    }

         /**
     * Pasar de video a video en la misma categoría
     * @return \zonavipdb|boolean
     */
    public function get_next($reg)
    {
        
        $sql = "SELECT * FROM " . $this->table_name . " WHERE codestado=1  AND  lower(categoria) = lower('".$this->categoriaFiltro."')  AND   reg > '".$reg."' limit 1;";
        $data = $this->db->select($sql);
        
        if ($data) {
            return $data[0];
        }
        return FALSE;
        
    }

    /**
     * Devuelve TRUE si el almacén existe
     * @return boolean
     */
    public function sub($user,$op = 0)
    {
        if($op == 0){
            $cons = $this->db->select("SELECT * FROM fs_users WHERE nick = " . $this->var2str($user) . " AND (subsyoutube = 0 OR subsyoutube IS NULL) ;");
            if($cons)
                return 0;
            return 1;
        }
            
        if($op == 1){
            $cons = $this->db->exec("UPDATE fs_users SET subsyoutube = 1 WHERE nick = " . $this->var2str($user) );
            return $cons;
        }
            
    }

    /**
     * Devuelve TRUE si el almacén existe
     * @return boolean
     */
    public function exists()
    {
        if (is_null($this->reg)) {
            return FALSE;
        }

        return $this->db->select("SELECT * FROM " . $this->table_name . " WHERE reg = " . $this->var2str($this->reg) . ";");
    }

    
    
    
    private function test(){
        return TRUE;
    }

    private function autoModuleLeccion(){
        if( !empty($this->curso) && empty($this->modulocurso)){
            $sql = "SELECT MAX(modulocurso) as maxmod FROM zonavipdb WHERE curso = '$this->curso'";
            $res = $this->db->select($sql);
            if($res){
                $this->modulocurso = $res[0]['maxmod'];
                $sql = "SELECT MAX(numeroleccion) as maxlecc FROM zonavipdb WHERE curso = '$this->curso' AND modulocurso = '$this->modulocurso'";
                $res1 = $this->db->select($sql);
                if($res1){
                    $this->numeroleccion  = ($res1[0]['maxlecc'] + 2);
                }
            }
        }
    }

    /**
     * Guarda los datos en la base de datos
     * @return boolean
     */
    public function save()
    {
        if ($this->test()) {
            $this->clean_cache();
            $this->autoModuleLeccion();
            if ($this->exists()) {
                $sql = "UPDATE " . $this->table_name 
                    . " SET nombrevideo = " . $this->var2str($this->nombrevideo)
                    . ", archivodescarga = " . $this->var2str($this->archivodescarga)
                    . ", pdf = " . $this->var2str($this->pdf)
                    . ", idyoutube = " . $this->var2str($this->idyoutube)
                    . ", pago = " . $this->var2str($this->pago)
                    . ", idvimeo = " . $this->var2str($this->idvimeo)
                    . ", grupo = " . $this->var2str($this->grupo)
                    . ", categoria = " . $this->var2str($this->categoria)
                    . ", ultmod = " . $this->var2str($this->ultmod)
                    . ", urlminiatura = " . $this->var2str($this->urlminiatura)
                    . ", curso = " . $this->var2str($this->curso)
                    . ", modulocurso = " . $this->var2str($this->modulocurso)
                    . ", leccioncurso = " . $this->var2str($this->leccioncurso)
                    . ", codestado = " . $this->var2str($this->codestado)
                    . ", detalle = " . $this->var2str($this->detalle)
                    . ", nombremodulo = " . $this->var2str($this->nombremodulo)
                    . ", numeroleccion = " . $this->var2str($this->numeroleccion)
                    . ", upload = " . $this->var2str($this->upload)
                    . ", detalleupload = " . $this->var2str($this->detalleupload)
                    . ", limit_date = " . $this->var2str($this->limit_date)
                    . "  WHERE reg = " . $this->var2str($this->reg) . ";";
            } else {
                $sql = "INSERT INTO " . $this->table_name . " (id,nombrevideo,archivodescarga,pdf,idyoutube,useredit,ultmod,codestado,pago,idvimeo,grupo,categoria,urlminiatura,curso,modulocurso,leccioncurso,nombremodulo,numeroleccion,detalle,upload,detalleupload,limit_date) VALUES
                      (" . $this->var2str($this->get_consecutivo_id())
                      . "," . $this->var2str($this->nombrevideo) 
                      . "," . $this->var2str($this->archivodescarga) 
                      . "," . $this->var2str($this->pdf) 
                      . "," . $this->var2str($this->idyoutube) 
                      . "," . $this->var2str($this->useredit) 
                      . "," . $this->var2str($this->ultmod) 
                      . "," . $this->var2str($this->codestado) 
                      . "," . $this->var2str($this->pago) 
                      . "," . $this->var2str($this->idvimeo) 
                      . "," . $this->var2str($this->grupo) 
                      . "," . $this->var2str($this->categoria) 
                      . "," . $this->var2str($this->urlminiatura) 
                      . "," . $this->var2str($this->curso) 
                      . "," . $this->var2str($this->modulocurso) 
                      . "," . $this->var2str($this->leccioncurso) 
                      . "," . $this->var2str($this->nombremodulo) 
                      . "," . $this->var2str($this->numeroleccion) 
                      . "," . $this->var2str($this->detalle) 
                      . "," . $this->var2str($this->upload) 
                      . "," . $this->var2str($this->detalleupload) 
                    . "," . $this->var2str($this->limit_date) . ");";
            }

            if($this->db->exec($sql)){
                $this->update_nombre_modulo();
                return TRUE;
            }else{
                return FALSE;
            }
        }

        return FALSE;
    }

    public function update_nombre_modulo(){
        if($this->nombremodulo != ""){
            
            $sql = "UPDATE " . $this->table_name 
            . " SET nombremodulo = " . $this->var2str($this->nombremodulo)
            . "  WHERE  curso = " . $this->var2str($this->curso) ." AND modulocurso = " . $this->var2str($this->modulocurso) ." ;";

            $this->db->exec($sql);

        }

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
     * Limpiamos la caché
     */
    private function clean_cache()
    {
        $this->cache->delete('zonavipdb');
    }

    /**
     * Devuelve un array con todos los zonavipdbes
     * @return \zonavipdb
     */
    public function recomendaciones($reg)
    {
        $buscando = "";
        if($this->categoriaFiltro != ""){
                $buscando = " AND  lower(categoria) = lower('".$this->categoriaFiltro."') ";
        }

        /// leemos esta lista de la caché
        $listaa = [];
        if (empty($listaa)) {
            /// si no está en caché, leemos de la base de datos
            $data = $this->db->select("SELECT * FROM " . $this->table_name . " WHERE codestado=1 ".$buscando." AND  reg <> '".$reg."' ORDER BY RAND() DESC limit 3;");
            if ($data) {
                foreach ($data as $a) {
                    $listaa[] = new \zonavipdb($a);
                }
            }
        }
        return $listaa;
    }

    /**
     * Devuelve un array con todos los zonavipdbes
     * @return \zonavipdb
     */
    public function cursos_modulos($curso__)
    {
        $sql = "SELECT t2.*, (SELECT t1.nombremodulo FROM zonavipdb t1 WHERE t1.curso = t2.curso AND t1.modulocurso = t2.modulocurso AND t1.nombremodulo != '' LIMIT 1 ) as nombremodulo FROM " . $this->table_name . " t2 WHERE  t2.curso = '".$curso__."' AND t2.codestado=1  GROUP BY t2.modulocurso;";

        return  $data = $this->db->select($sql);
    }


    /**
     * Devuelve un array con todos los zonavipdbes
     * @return \zonavipdb
     */
    public function modulos_lecciones($modulo__,$curso__)
    {
        $sql = "SELECT * FROM " . $this->table_name . " WHERE  curso = '".$curso__."' AND modulocurso = '".$modulo__."' AND codestado=1 ORDER BY numeroleccion ASC;";

        return  $data = $this->db->select($sql);
    }

    /**
     * Devuelve un array con todos los zonavipdbes
     * @return \zonavipdb
     */
    public function all_books()
    {
        $this->clean_cache();
        /// leemos esta lista de la caché
        $listaa = $this->cache->get_array('zonavipdb');
        if (empty($listaa)) {
            /// si no está en caché, leemos de la base de datos
            $data = $this->db->select("SELECT * FROM " . $this->table_name . " WHERE (curso = '' OR curso IS NULL) AND  codestado=1 AND  categoria = 'EBOOK'  ORDER BY reg DESC LIMIT 8;");

            if ($data) {
                foreach ($data as $a) {
                    $listaa[] = new \zonavipdb($a);
                }
            }

            /// guardamos la lista en caché
            $this->cache->set('zonavipdb', $listaa);
        }

        return $listaa;
    }

    /**
     * Devuelve un array con todos los zonavipdbes
     * @return \zonavipdb
     */
    public function all_nuevos()
    {
        $this->clean_cache();
        /// leemos esta lista de la caché
        $listaa = $this->cache->get_array('zonavipdb');
        if (empty($listaa)) {
            /// si no está en caché, leemos de la base de datos
            $data = $this->db->select("SELECT * FROM " . $this->table_name . " WHERE (curso = '' OR curso IS NULL) AND  codestado=1  ORDER BY reg DESC LIMIT 8;");

            if ($data) {
                foreach ($data as $a) {
                    $listaa[] = new \zonavipdb($a);
                }
            }

            /// guardamos la lista en caché
            $this->cache->set('zonavipdb', $listaa);
        }

        return $listaa;
    }

    /**
     * Devuelve un array con todos los zonavipdbes
     * @return \zonavipdb
     */
    public function all_aleatorio()
    {
        $this->clean_cache();
        /// leemos esta lista de la caché
        $listaa = $this->cache->get_array('zonavipdb');
        if (empty($listaa)) {
            /// si no está en caché, leemos de la base de datos
            $data = $this->db->select("SELECT * FROM " . $this->table_name . " WHERE (curso = '' OR curso IS NULL) AND codestado=1  ORDER BY rand() DESC LIMIT 16;");
            if ($data) {
                foreach ($data as $a) {
                    $listaa[] = new \zonavipdb($a);
                }
            }

            /// guardamos la lista en caché
            $this->cache->set('zonavipdb', $listaa);
        }

        return $listaa;
    }

    /**
     * Devuelve un array con todos los zonavipdbes
     * @return \zonavipdb
     */
    public function all($limit = "", $offset = "")
    {
        $this->clean_cache();
        $buscando = "";
        if($this->busqueda != ""){
            
            $buscando = " AND ( t1.id = '".$this->busqueda."' OR lower(nombrevideo) LIKE lower('%".$this->busqueda."%') )";
        }
        if($this->pagoFiltro != ""){
            if($buscando == ""){
                $buscando = " AND  t1.pago = '".$this->pagoFiltro."' ";
            }else{
                $buscando .= " AND  t1.pago = '".$this->pagoFiltro."' ";
            }
        }
        if($this->grupoFiltro != ""){
            if($buscando == ""){
                $buscando = " AND  t1.grupo = '".$this->grupoFiltro."' ";
            }else{
                $buscando .= " AND  t1.grupo = '".$this->grupoFiltro."' ";
            }
        }
        if($this->categoriaFiltro != ""){
            if($buscando == ""){
                $buscando = " AND  lower(t1.categoria) = lower('".$this->categoriaFiltro."') ";
            }else{
                $buscando .= " AND  lower(t1.categoria) = lower('".$this->categoriaFiltro."') ";
            }
        }

        if($limit != ""){
                $limit = " LIMIT ".$offset.",".$limit." ";
        }

        /// leemos esta lista de la caché
        $listaa = $this->cache->get_array('zonavipdb');
        if (empty($listaa)) {
            /// si no está en caché, leemos de la base de datos
            $sql = "SELECT t1.*,t2.nombre as nombreproducto,t2.linkpago FROM " . $this->table_name . " t1 LEFT JOIN  hotmartproductos t2 ON t1.grupo = t2.idproducto WHERE (t1.curso = '' OR t1.curso IS NULL) AND t1.codestado=1 ".$buscando." ORDER BY t1.reg DESC ".$limit.";";
            $data = $this->db->select($sql);

            

            if ($data) {
                foreach ($data as $a) {
                    $listaa[] = new \zonavipdb($a);
                }
            }

            /// guardamos la lista en caché
            $this->cache->set('zonavipdb', $listaa);
        }

        return $listaa;
    }

    /**
     * Devuelve un array con todos los zonavipdbes
     * @return \zonavipdb
     */
    public function all_admin($limit = "", $offset = "")
    {
        $this->clean_cache();
        $buscando = "";
        if($this->busqueda != ""){
            
            $buscando = " AND ( t1.id = '".$this->busqueda."' OR lower(nombrevideo) LIKE lower('%".$this->busqueda."%') )";
        }
        if($this->pagoFiltro != ""){
            if($buscando == ""){
                $buscando = " AND  t1.pago = '".$this->pagoFiltro."' ";
            }else{
                $buscando .= " AND  t1.pago = '".$this->pagoFiltro."' ";
            }
        }
        if($this->grupoFiltro != ""){
            if($buscando == ""){
                $buscando = " AND  t1.grupo = '".$this->grupoFiltro."' ";
            }else{
                $buscando .= " AND  t1.grupo = '".$this->grupoFiltro."' ";
            }
        }
        if($this->categoriaFiltro != ""){
            if($buscando == ""){
                $buscando = " AND  lower(t1.categoria) = lower('".$this->categoriaFiltro."') ";
            }else{
                $buscando .= " AND  lower(t1.categoria) = lower('".$this->categoriaFiltro."') ";
            }
        }

        if($limit != ""){
                $limit = " LIMIT ".$offset.",".$limit." ";
        }

        /// leemos esta lista de la caché
        $listaa = $this->cache->get_array('zonavipdb');
        if (empty($listaa)) {
            /// si no está en caché, leemos de la base de datos
            $sql = "SELECT t1.*,t2.nombre as nombreproducto,t2.linkpago FROM " . $this->table_name . " t1 LEFT JOIN  hotmartproductos t2 ON grupo = t2.idproducto WHERE t1.codestado >= 0 ".$buscando." ORDER BY t1.reg DESC ".$limit.";";
            $data = $this->db->select($sql);

            

            if ($data) {
                foreach ($data as $a) {
                    $listaa[] = new \zonavipdb($a);
                }
            }

            /// guardamos la lista en caché
            $this->cache->set('zonavipdb', $listaa);
        }

        return $listaa;
    }
    
    
    
    
    
}
