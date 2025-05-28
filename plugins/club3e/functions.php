<?php

use function PHPSTORM_META\override;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Psr7\Request;

require 'vendor/autoload.php';
/**
 * This file is part of facturacion_base
 * Copyright (C) 2015-2019 Carlos Garcia Gomez <neorazorx@gmail.com>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Lesser General Public License for more details.
 * 
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */



if (!function_exists('api_compracompletada')) {

    function api_compracompletada()
    {
        $req = $_POST;
        

        $compraaprobada = $req["status"];
        $producto = $req["prod"];
        $email = strtolower($req["email"]);
        $nombre = strtolower($req["name"]);
        $estado = $req["status"];

        if($producto == "2314440")
        {
            if(crear_registro_club3e($req)){
                header('Content-Type: application/json');
                $data["exito"] = true;
                $data["error"] = "Se ha creado el registro en club3e para el usuario ".$email;
                http_response_code(202);
                comprobarusuarioMailerLite($email,$nombre,88271826450384225 );
                echo json_encode($data);
                return true;
            }else{
                header('Content-Type: application/json');
                $data["exito"] = false;
                $data["error"] = "No se ha podido crear el registro para el usuario ".$email;
                http_response_code(400);
                echo json_encode($data);
                return false;
            }
        }

        $usu = new fs_user();
        
        $productoshotmart = new hotmartproductos();
        $productoshotmart =  $productoshotmart->get_curso($producto);


        
        if($productoshotmart){

            $caduca = date("Y-m-d H:i:s",strtotime($req["purchase_date"]."+ ".$productoshotmart->vigencia." days"));
            if($compraaprobada == "completed"){
                if($usu->get($email)){
                    asignarusuariopago1($email,$req["prod"],$req["warranty_date"],$caduca,$estado,$productoshotmart->vigencia,$nombre);
                }else{
                    $idgrupoMailerlite = ZonaVIP_Externos;        

                    if($producto == 1795498){
                        $idgrupoMailerlite = 109580945;
                    }
                    crearusuariopago($email,$nombre,$idgrupoMailerlite);
                    asignarusuariopago1($email,$req["prod"],$req["warranty_date"],$caduca,$estado,$productoshotmart->vigencia,"");
                }
            }
        }else{
            header('Content-Type: application/json');
            $data["exito"] = false;
            $data["error"] = "No se encuentra el producto ".$req["prod"];
            http_response_code(400);
            echo json_encode($data);
        }

        
    }
}

if (!function_exists('api_compraaprobada')) {

    function api_compraaprobada()
    {
        $req = $_POST;
        
        $compraaprobada = $req["status"];
        $producto = $req["prod"];
        $email = strtolower($req["email"]);
        $nombre = strtolower($req["name"]);
        $estado = $req["status"];

        if($producto == "2314440")
        {
            if(crear_registro_club3e($req)){
                header('Content-Type: application/json');
                $data["exito"] = true;
                $data["error"] = "Se ha creado el registro en club3e para el usuario ".$email;
                http_response_code(202);
                comprobarusuarioMailerLite($email,$nombre,88271826450384225 );
                echo json_encode($data);
                return true;
            }else{
                header('Content-Type: application/json');
                $data["exito"] = false;
                $data["error"] = "No se ha podido crear el registro para el usuario ".$email;
                http_response_code(400);
                echo json_encode($data);
                return false;
            }
        }
        

        $usu = new fs_user();

        $productoshotmart = new hotmartproductos();
        $productoshotmart =  $productoshotmart->get_curso($producto);
        


        if($productoshotmart){
            
            $caduca = date("Y-m-d H:i:s",strtotime($req["purchase_date"]."+ ".$productoshotmart->vigencia." days"));

            if($compraaprobada == "approved"){
                    if($usu->get($email)){
                        asignarusuariopago1($email,$req["prod"],$req["warranty_date"],$caduca,$estado,$productoshotmart->vigencia,$nombre);
                    }else{

                        $idgrupoMailerlite = ZonaVIP_Externos;        

                        if($producto == 1795498){
                            $idgrupoMailerlite = 109580945;
                        }
                        crearusuariopago($email,$nombre,$idgrupoMailerlite);
                        asignarusuariopago1($email,$req["prod"],$req["warranty_date"],$caduca,$estado,$productoshotmart->vigencia,"");
                    }
            }else{
                header('Content-Type: application/json');
                $data["exito"] = false;
                $data["error"] = "Estado no approved ".$req["prod"];
                http_response_code(400);
                echo json_encode($data);
            }
        }else{
            header('Content-Type: application/json');
            $data["exito"] = false;
            $data["error"] = "No se encuentra el producto ".$req["prod"];
            http_response_code(400);
            echo json_encode($data);
        }
        
    }
}

if (!function_exists('api_compracancelada')) {

    function api_compracancelada()
    {

        $json = file_get_contents('php://input');

        // Converts it into a PHP object
        $req = json_decode($json);

        $email = strtolower($req->userEmail);

        if($req->productId == "2314440"){

            $vigencia = "0";
            //creamos el registro de pago
            $club3e_renova = new club3e_renovaciones();
            $club3e_renova->usuario = strtolower($req["email"]);
            $club3e_renova->fecha_renovacion = date("Y-m-d H:i:s", date("Y-d-m H:i:s") );
            $club3e_renova->fecha_expira = date("Y-m-d H:i:s",strtotime(date("Y-d-m H:i:s")."+ ".$vigencia." days"));
            $club3e_renova->registro_pago = "";
            $club3e_renova->ultmod = date("Y-m-d H:i:s");
            if($club3e_renova->save()){
                $club3e = new coreclub3e();
                $club3e->fecha_inicia = $club3e_renova->fecha_renovacion;
                $club3e->fecha_expira = $club3e_renova->fecha_expira;
                $club3e->estado = 1;
                $club3e->ultmod = $club3e_renova->ultmod;
                if($club3e->save())
                {
                    header('Content-Type: application/json');
                    $data["exito"] = true;
                    $data["error"] = "Se ha creado el registro en club3e para el usuario ".$email;
                    http_response_code(202);
                    echo json_encode($data);
                    return true;
                }else{
                    header('Content-Type: application/json');
                    $data["exito"] = false;
                    $data["error"] = "No se ha podido crear el registro para el usuario ".$email;
                    http_response_code(400);
                    echo json_encode($data);
                    return false;
                }
            }

            
        }
        //$fechacancelacion = $req->cancellationDate;
        
        if($req->productName == "Club de Macros Excel VBA")
            $idpro = "1125293";
        $usu = new fs_user();
        if($usu->get($email)){
            actualizarhotmaruser($email,"canceled",$idpro);
            dardebaja($email);
        }else{
            header('Content-Type: application/json');
            $data["exito"] = false;
            $data["msg"] = "El usuario ".$email." no existe";
            http_response_code(400);
            echo json_encode($data);
        }
            
    }
}


function crear_registro_club3e($req){
    $vigencia = "365";
    //recurrency_period
    if( isset($req['recurrency_period']) && $req['recurrency_period'] != ''){
        $vigencia = $req['recurrency_period'];
        if($vigencia == 360){
            $vigencia = 365;
        }
    }
    //creamos el registro de pago
    $club3e_renova = new club3e_renovaciones();
    $club3e_renova->usuario = strtolower($req["email"]);
    //$club3e_renova->fecha_renovacion = date("Y-m-d H:i:s", strtotime($req["purchase_date"]."-1 days") );
    $club3e_renova->fecha_renovacion = date("Y-m-d H:i:s", strtotime($req["confirmation_purchase_date"]) );
    $club3e_renova->fecha_expira = date("Y-m-d H:i:s",strtotime($req["confirmation_purchase_date"]."+ ".$vigencia." days"));
    $club3e_renova->registro_pago = "";
    $club3e_renova->ultmod = date("Y-m-d H:i:s");
    if($club3e_renova->save()){
        $club3e = new coreclub3e();
        $club3e->usuario = $club3e_renova->usuario;

        if($club3e->get_user()){
            $club3e = $club3e->get_user()[0];

            $fecha1= new DateTime($club3e->fecha_expira);
            $fecha2= new DateTime($club3e_renova->fecha_renovacion);
            $diff = $fecha1->diff($fecha2);

            if($diff->days > 7)//si la diferencia entre la fecha de expiración y la fecha de renovación es mayor a 7, la fecha de inicio cambia y ya no tendría acceso a varias cosas
                $club3e->fecha_inicia = $club3e_renova->fecha_renovacion;

            $club3e->fecha_expira = $club3e_renova->fecha_expira;
            $club3e->ultmod = $club3e_renova->ultmod;

            if($club3e->save()){
                if(isset($req["name"]))
                    crearusuario_zonavip_club3e($club3e->usuario, $req["name"]);
                else
                    crearusuario_zonavip_club3e($club3e->usuario);
                return true;
            }
                
        }else{
            $club3e->fecha_inicia = $club3e_renova->fecha_renovacion;
            $club3e->fecha_expira = $club3e_renova->fecha_expira;
            $club3e->estado = 1;
            $club3e->ultmod = $club3e_renova->ultmod;
            if($club3e->save()){
                if(isset($req["name"]))
                    crearusuario_zonavip_club3e($club3e->usuario, $req["name"]);
                else
                    crearusuario_zonavip_club3e($club3e->usuario);
                return true;
            }
                
        }
        
        return false;
    }
    else{
        return false;
    }

    
}


function crearusuario_zonavip_club3e($email,$nombre = ""){
    $usuario = new fs_user();
    $usuario->nick = $email;
    $usuario->set_password("club3202");
    $usuario->fs_page = "zonavip";
    $usuario->new_logkey();
    $usuario->nombre = $nombre;
    if($usuario->save()){
        
        return asignarusuario_club3e($usuario->nick);
    }

    return false;
}

function asignarusuario_club3e($email, $codrol = "zonavip"){
    
    try{
        $rolesUser = new fs_rol_user();
        $rolesUser->codrol = $codrol;
        $rolesUser->fs_user = $email;
        
        if($rolesUser->save()){
    
            $rol = new fs_rol_access();
            $rol = $rol->all_from_rol($codrol);
            
            foreach($rol as $r){
                $access = new fs_access();
                $access->fs_page = $r->fs_page;
                $access->fs_user = $email;
                $access->allow_delete = $r->allow_delete;
                if(!$access->save()){
                    return false;
                }    
            }
        }

        return true;

    }catch(Exception $ex){

        return false;

    }
    
    
            
        
            
}


function dataRequest(){

    $arr['headers'] = [
        'Content-Type' => 'application/json',
        'Accept' => 'application/json',
        'Authorization' => 'Bearer '.TOKEN_
    ];

    $arr['basic_uri'] = BASIC_API_URI;

    return $arr;
}



/**
 * client guzzle 
 * services mailerlite
 */

function valideSubscriber($email){
    
    $client = new Client();
    $request = new Request('GET', dataRequest()['basic_uri']."/subscribers/$email", dataRequest()['headers']);

    $data = [];

    try {
        $res = $client->sendAsync($request)->wait();
        $status_code = $res->getStatusCode();
        $res = $res->getBody()->getContents();
        $data = [
            'status' => $status_code,
            'data' => $res
        ];
    } catch (ClientException $e) {
        $response = $e->getResponse();
        $status_code = $response->getStatusCode();
        $data = [
            'status' => $status_code,
        ];
    }

    return $data;
}

function deleteSuscriberMailerLite($email){
    $rest = valideSubscriber($email);
        
    if($rest['status'] == 200)
    {
        $data =  json_decode( $rest['data'] );
        $client = new Client();
        $request = new Request('DELETE', dataRequest()['basic_uri']."/subscribers/$data->data->id", dataRequest()['headers']);
    }

}

function CreateSubscriberInGroup($email,$group,$name = ""){

    $client = new Client();
    $body = '{
        "email": "'.$email.'",
        "status" : "active",
        "name" : "'.$name.'",
        "groups": [
          "'.$group.'"
        ]
      }';
    $request = new Request('POST', dataRequest()['basic_uri'].'/subscribers', dataRequest()['headers'], $body);
    

    $data = [];

    try {
        $res = $client->sendAsync($request)->wait();
        $status_code = $res->getStatusCode();
        $res = $res->getBody()->getContents();
        $data = [
            'status' => $status_code,
            'data' => $res
        ];
    } catch (ClientException $e) {
        $response = $e->getResponse();
        $status_code = $response->getStatusCode();
        $data = [
            'status' => $status_code,
        ];
    }

    return $data;
}

function AddSubscriberInGroup($idSubscriber, $group_id){

    $client = new Client();
    
    $request = new Request('POST', dataRequest()['basic_uri']."/subscribers/$idSubscriber/groups/$group_id", dataRequest()['headers']);
    
    $data = [];

    try {
        $res = $client->sendAsync($request)->wait();
        $status_code = $res->getStatusCode();
        $res = $res->getBody()->getContents();
        $data = [
            'status' => $status_code,
            'data' => $res
        ];
    } catch (ClientException $e) {
        $response = $e->getResponse();
        $status_code = $response->getStatusCode();
        $data = [
            'status' => $status_code,
        ];
    }

    return $data;
}


