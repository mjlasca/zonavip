<?php

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


if (!function_exists('allowusuario')) {

    function allowusuario($user, $pag)
    {
        $fsaccces = new fs_access();

        foreach($fsaccces->all_from_nick($user) as $fs){
            if($fs->fs_page == $pag && $fs->allow_delete ){
                return 1;
            }
        }

        return 0;

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

if (!function_exists('api_comprareembolsada')) {

    function api_comprareembolsada()
    {
        $req = $_POST;

        $compraaprobada = $req["status"];
        $producto = $req["prod"];
        $email = strtolower($req["email"]);
        $usu = new fs_user();

        $productoshotmart = new hotmartproductos();
        $productoshotmart =  $productoshotmart->get_curso($producto);

        if($productoshotmart){
            if($usu->get($email)){
                //asignarusuariopago($email,'zonavip',$producto);
                actualizarhotmaruser($email,"expired",$req["prod"]);
            }
        }
        
    }
}



if (!function_exists('api_compracompletada')) {

    function api_compracompletada()
    {
        $req = $_POST;
        
        $compraaprobada = $req["status"];
        $producto = $req["prod"];
        $email = strtolower($req["email"]);
        $nombre = strtolower($req["name"]);
        $estado = $req["status"];
        

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
            $data["error"] = "No se encuentra el producto compracompletada ".$req["prod"];
            http_response_code(400);
            echo json_encode($data);
        }

        
    }
}


function actualizarhotmaruser($email,$estado,$idproducto){
    $hotmaruser = new hotmartuser();
    $hotmaruser = $hotmaruser->get_user_idproducto($email,$idproducto);
    if($hotmaruser){
        $hotmaruser->ultmod = date("Y-m-d H:i:s");
        /*$gara = $hotmaruser->fechagarantia;
        if($gara > $hotmaruser->ultmod)
            $hotmaruser->fechacaducidad = $fechacaducidad;
        if($reem)
            $hotmaruser->fechacaducidad = $fechacaducidad;*/
        $hotmaruser->estado = $estado;
        if($hotmaruser->save()){
            header('Content-Type: application/json');
            $data["exito"] = true;
            $data["msg"] = "El usuario ".$email." tiene ahora un estado ".$estado." en el producto ".$idproducto;
            http_response_code(200);
            echo json_encode($data);
        }else{
            header('Content-Type: application/json');
            $data["exito"] = false;
            $data["msg"] = "No se pudo procesar la solicitud del usuario ".$email;
            http_response_code(400);
            echo json_encode($data);
        }
    }
    
}

function revisarusermail($mail, $buscarnombre = FALSE){
    $rest = valideSubscriber($mail);
        
    if($rest['status'] !== 200){
        return 'NO';
    }else{
        return 'SI';
    }
    
    
    

    
}


function comprobarusuarioMailerLite($mailusuario,$nombre, $idgrupo = ZonaVIP_Externos){
    $rest = valideSubscriber($mailusuario);
        
    if($rest['status'] !== 200){
        return 'NO';
    }else{
        $data =  json_decode( $rest['data'] );
        $restGroup = CreateSubscriberInGroup($mailusuario,$idgrupo,$nombre);
        if($restGroup['status'] == 200){
            return 'SI';
        }
    }
}

function dardebaja($email){
        
    deleteSuscriberMailerLite($email);

}



function crearusuariopago($email,$nombre = "", $idgrupoMailerlite = ZonaVIP_Externos){
    $usuario = new fs_user();
    $usuario->nick = $email;
    $usuario->set_password("zvipeee");
    $usuario->fs_page = "zonavip";
    $usuario->new_logkey();
    if($usuario->save()){
        comprobarusuarioMailerLite($email,$nombre,$idgrupoMailerlite);
    }
}

function asignarusuariopago1($email,$idproducto, $garantia,$caducidad,$estado,$periocidad,$nombre, $codrol = "zonavip"){
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

        $hotmaruser = new hotmartuser();
        $hotmaruser->idproducto = $idproducto;
        $hotmaruser->user = $email;
        $hotmaruser->periocidad = $periocidad;

        $productoshotmart = new hotmartproductos();

        $productoshotmart =  $productoshotmart->get_curso($idproducto);

        
        

        if($productoshotmart){
            $hotmaruser->nombreproducto = $productoshotmart->nombre;

            //grupo mailerlite club de macros
            $idgrupoMailerlite = ZonaVIP_Externos;        

            if($idproducto == 1795498){
                $idgrupoMailerlite = 109580945;
            }

            $hotmaruser->fechagarantia = $garantia;
            $hotmaruser->fechacaducidad = $caducidad;
            $hotmaruser->estado = $estado;
            $hotmaruser->ultmod = date("Y-m-d H:i:s");
            if($hotmaruser->save()){
                if($nombre != "")
                    comprobarusuarioMailerLite($email,$nombre,$idgrupoMailerlite);
                header('Content-Type: application/json');
                $data["exito"] = true;
                $data["msg"] = "Usuario asignado con éxito";
                http_response_code(200);
                echo json_encode($data);
            }else{
                header('Content-Type: application/json');
                $data["exito"] = false;
                $data["error"] = "No se pudo asignar el usuario al producto";
                http_response_code(400);
                echo json_encode($data);
            }
        }
        else{
            header('Content-Type: application/json');
            $data["exito"] = false;
            $data["error"] = "No se pudo asignar el usuario ya que no existe el producto";
            http_response_code(400);
            echo json_encode($data);
        }
        

        
    }
}

function asignarusuariopago($email, $codrol = 'ClubMacros',$codrolviejo = 'zonavip'){
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

        $rolesUser = new fs_rol_user();
        $rolesUser->codrol = $codrolviejo;
        $rolesUser->fs_user = $email;
        $rolesUser->delete();
        return true;
    }
}







/*

array(42){["callback_type"]=>string(1)"1"["hottok"]=>string(37)"yCN6tCNPXoKGimUk3eEKRPOE9jSWTr3123520"["aff"]=>string(0)""["aff_name"]=>string(0)""["currency"]=>string(3)"BRL"["transaction"]=>string(15)"HP1121336654889"["xcod"]=>string(0)""["payment_type"]=>string(11)"credit_card"["payment_engine"]=>string(7)"hotmart"["status"]=>string(8)"approved"["prod"]=>string(5)"00000"["prod_name"]=>string(22)"Produtotestpostback2"["producer_name"]=>string(18)"ProducerTestName"["producer_document"]=>string(11)"12345678965"["producer_legal_nature"]=>string(14)"PessoaFÃ­sica"["transaction_ext"]=>string(16)"HP11315117833431"["purchase_date"]=>string(20)"2017-11-27T11:49:04Z"["confirmation_purchase_date"]=>string(20)"2017-11-27T11:49:06Z"["currency_code_from"]=>string(3)"BRL"["currency_code_from_"]=>string(3)"BRL"["original_offer_price"]=>string(7)"1500.00"["productOfferPaymentMode"]=>string(15)"pagamento_unico"["warranty_date"]=>string(20)"2017-12-27T00:00:00Z"["receiver_type"]=>string(6)"SELLER"["installments_number"]=>string(2)"12"["cms_marketplace"]=>string(6)"149.50"["cms_vendor"]=>string(7)"1350.50"["off"]=>string(4)"test"["price"]=>string(7)"1500.00"["full_price"]=>string(7)"1500.00"["subscriber_code"]=>string(8)"I9OT62C3"["signature_status"]=>string(6)"active"["subscription_status"]=>string(6)"active"["name_subscription_plan"]=>string(14)"planodeteste"["has_co_production"]=>string(5)"false"["email"]=>string(41)"testeComprador271101postman15@example.com"["name"]=>string(15)"TesteComprador"["first_name"]=>string(5)"Teste"["last_name"]=>string(9)"Comprador"["phone_checkout_local_code"]=>string(9)"999999999"["phone_checkout_number"]=>string(2)"00"["sck"]=>string(0)""}


array(38){["callback_type"]=>string(1)"1"["hottok"]=>string(37)"yCN6tCNPXoKGimUk3eEKRPOE9jSWTr3123520"["aff"]=>string(0)""["aff_name"]=>string(0)""["currency"]=>string(3)"BRL"["transaction"]=>string(16)"HP09915534436216"["xcod"]=>string(0)""["payment_type"]=>string(11)"credit_card"["payment_engine"]=>string(7)"hotmart"["status"]=>string(7)"expired"["prod"]=>string(5)"00000"["prod_name"]=>string(22)"Produtotestpostback2"["producer_name"]=>string(18)"ProducerTestName"["producer_document"]=>string(11)"12345678965"["producer_legal_nature"]=>string(14)"PessoaFÃ­sica"["transaction_ext"]=>string(16)"HP09915534436216"["purchase_date"]=>string(20)"2017-11-27T11:49:04Z"["confirmation_purchase_date"]=>string(20)"2017-11-27T11:49:06Z"["currency_code_from"]=>string(3)"BRL"["currency_code_from_"]=>string(3)"BRL"["original_offer_price"]=>string(7)"1500.00"["productOfferPaymentMode"]=>string(15)"pagamento_unico"["warranty_date"]=>string(20)"2017-12-27T00:00:00Z"["receiver_type"]=>string(6)"SELLER"["installments_number"]=>string(2)"12"["cms_marketplace"]=>string(6)"149.50"["cms_vendor"]=>string(7)"1350.50"["off"]=>string(4)"test"["price"]=>string(7)"1500.00"["full_price"]=>string(7)"1500.00"["has_co_production"]=>string(5)"false"["email"]=>string(41)"testeComprador271101postman15@example.com"["name"]=>string(15)"TesteComprador"["first_name"]=>string(5)"Teste"["last_name"]=>string(9)"Comprador"["phone_checkout_local_code"]=>string(9)"999999999"["phone_checkout_number"]=>string(2)"00"["sck"]=>string(0)""}

*/