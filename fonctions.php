<?php

use App\Connection;

include "pdo.php";
require_once "bibliotheque_array.php";
include "fonctions_create_table.php";


ini_set('xdebug.var_display_max_depth', 10);
ini_set('xdebug.var_display_max_children', 256);
ini_set('xdebug.var_display_max_data', 1024);

// RECUPERER LE TOKEN 
function goCurlToken($url)
{


    $ch2 = curl_init();

    curl_setopt($ch2, CURLOPT_URL, $url);
    curl_setopt($ch2, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch2, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch2, CURLOPT_POST, true);
    curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch2, CURLOPT_POSTFIELDS, "apiKey=54c4fbe3ee0ed3683a17488371d5e762b9c5f4db6a7bc0507d3518c40bedfe300fbece014e3eac21acd380a142e874c460931659fe922bc1ef170d1f325e499c");

    $result = curl_exec($ch2);

    curl_close($ch2);

    $data = json_decode($result, true);

    return $data['value'];
}



//FONCTIONS POUR RECUPERER LES BDC DE LA VEILLE
function GoCurl_Recup_BDC($token, $url, $page, $num_bdc = '', $date_bdc = '')
{

    $ch = curl_init();

    // le token
    $header = array();
    $header[] = 'X-Auth-Token:' . $token;
    $header[] = 'Content-Type:text/html;charset=utf-8';

    // choper un BC spécifique
    if (isset($num_bdc) && $num_bdc != '') {
        $dataArray = array(
            /* 'state' => array(
            //     'administrative_selling.state.invoiced_edit',
            //     'administrative_selling.state.valid',
            //     'administrative_selling.state.invoiced',
            // ),*/
            "uniqueId" => $num_bdc,
            "page" => $page
        );
    }
    //sinon par date
    else {

        if (isset($date_bdc) && $date_bdc != '') {

            $dataArray = array(
                // "state" => array(
                //     'administrative_selling.state.invoiced_edit',
                //     'administrative_selling.state.valid',
                //     'administrative_selling.state.invoiced',
                // ),
                "orderFormDateFrom" => "$date_bdc",
                "orderFormDateTo" => "$date_bdc",
                "count" => 100,
                "page" => $page
            );
        }
        //si pas de date alors on prend ceux d'hier
        else {
            $date_hier = date('Y-m-d', strtotime('-1 day'));
            $dataArray = array(
                "state" => array(
                    'administrative_selling.state.invoiced_edit',
                    'administrative_selling.state.valid',
                    'administrative_selling.state.invoiced',
                ),
                // "state" => "administrative_selling.state.invoiced",
                "orderFormDateFrom" => "$date_hier",
                "orderFormDateTo" => "$date_hier",
                // "updateDateFrom" => "$date_hier",
                // "updateDateTo" => "$date_hier",
                "count" => 100,
                "page" => $page
            );
        }
    }


    $getURL = $url . '?' . http_build_query($dataArray);

    print_r($getURL);

    // die();

    sautdeligne();

    curl_setopt($ch, CURLOPT_URL, $getURL);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);


    $result = curl_exec($ch);

    if (curl_error($ch)) {
        $result = curl_error($ch);
        print_r($result);
        echo "<br/> erreur";
    }

    curl_close($ch);

    echo gettype($result);
    echo $result;

    $obj = json_decode($result);

    // sautdeligne();
    // echo gettype($obj);
    // die();



    // echo gettype($obj);
    // var_dump($obj);


    // echo '<pre>' . print_r($obj) . '</pre>';

    if (!isset($num_bdc) && $num_bdc == '') {
        echo '<pre> nombre total de BDC : ' . $obj->total . '</pre>';
        echo '<pre> page actuelle :' . $obj->currentPage . '</pre>';
        echo '<pre> BDC par page :' . $obj->perPage . '</pre>';
    }

    return $obj;
}

function GoCurl_Recup_BDC_ANNULE($token, $url, $page, $num_bdc = '', $date_bdc = '')
{

    $ch = curl_init();

    // le token
    $header = array();
    $header[] = 'X-Auth-Token:' . $token;
    $header[] = 'Content-Type:text/html;charset=utf-8';

    // choper un BC spécifique
    if (isset($num_bdc) && $num_bdc != '') {
        $dataArray = array(
            "state" => array(
                'administrative_selling.state.canceled'
            ),
            "uniqueId" => $num_bdc,
            "page" => $page
        );
    }
    //sinon par date
    else {

        if (isset($date_bdc) && $date_bdc != '') {

            $dataArray = array(
                "state" => 'administrative_selling.state.canceled',
                "updateDateFrom" => "$date_bdc",
                "updateDateTo" => "$date_bdc",
                "count" => 100,
                "page" => $page
            );
        }
        //si pas de date alors on prend de début avril à hier
        else {
            $date_from = "2024-04-18";
            $date_to = date('Y-m-d', strtotime('-1 day'));
            $dataArray = array(
                "state" => "administrative_selling.state.canceled",
                // "orderFormDateFrom" => "$date_from",
                // "orderFormDateTo" => "$date_to",
                "updateDateFrom" => "$date_from",
                "updateDateTo" => "$date_from",
                "count" => 100,
                "page" => $page
            );
        }
    }


    $getURL = $url . '?' . http_build_query($dataArray);

    print_r($getURL);

    // die();

    sautdeligne();

    curl_setopt($ch, CURLOPT_URL, $getURL);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);


    $result = curl_exec($ch);

    if (curl_error($ch)) {
        $result = curl_error($ch);
        print_r($result);
        echo "<br/> erreur";
    }

    curl_close($ch);

    echo gettype($result);
    echo $result;

    $obj = json_decode($result);

    // sautdeligne();
    // echo gettype($obj);
    // die();



    // echo gettype($obj);
    // var_dump($obj);


    // echo '<pre>' . print_r($obj) . '</pre>';

    if (!isset($num_bdc) && $num_bdc == '') {
        echo '<pre> nombre total de BDC : ' . $obj->total . '</pre>';
        echo '<pre> page actuelle :' . $obj->currentPage . '</pre>';
        echo '<pre> BDC par page :' . $obj->perPage . '</pre>';
    }

    return $obj;
}

// FONCTION INFOS DU VEHICULES
function getvehiculeInfo($reference, $token, $url_vehicule, $state, $is_not_available_for_sell = '')
{

    $ch = curl_init();

    // le token
    $header = array();
    $header[] = 'X-Auth-Token:' . $token;
    $header[] = 'Content-Type:text/html;charset=utf-8';

    switch ($is_not_available_for_sell) {
        case TRUE:

            if (isset($state) && $state == 'arrivage_or_parc') {
                $dataArray = array(
                    "reference" => $reference,
                    "state" => 'vehicle.state.on_arrival,vehicle.state.parc',
                    "isNotAvailableForSelling" => TRUE
                );
            }
            // !!!! si le vh est vendu, vendu AR, en cours, sorti, sorti AR ou annulé alors il faudra mettre l'état obligatoirement si tu veux un retour 
            else {
                $dataArray = array(
                    "reference" => $reference,
                    "state" => 'vehicle.state.sold,vehicle.state.sold_ar,vehicle.state.pending,vehicle.state.out,vehicle.state.out_ar,vehicle.state.canceled',
                    "isNotAvailableForSelling" => TRUE
                );
            }

            break;

        case FALSE:
            if (isset($state) && $state == 'arrivage_or_parc') {
                $dataArray = array(
                    "reference" => $reference,
                    "state" => 'vehicle.state.on_arrival,vehicle.state.parc',
                    "isNotAvailableForSelling" => FALSE
                );
            }
            // !!!! si le vh est vendu, vendu AR, en cours, sorti, sorti AR ou annulé alors il faudra mettre l'état obligatoirement si tu veux un retour 
            else {
                $dataArray = array(
                    "reference" => $reference,
                    "state" => 'vehicle.state.sold,vehicle.state.sold_ar,vehicle.state.pending,vehicle.state.out,vehicle.state.out_ar,vehicle.state.canceled',
                    "isNotAvailableForSelling" => FALSE
                );
            }
            break;

    }


    $data = http_build_query($dataArray);

    $getURL = $url_vehicule . '?' . $data;

    print_r($getURL);

    sautdeligne();

    curl_setopt($ch, CURLOPT_URL, $getURL);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);

    $result = curl_exec($ch);

    if (curl_error($ch)) {
        $result = curl_error($ch);
        print_r($result);
        echo "<br/> erreur";
    }

    var_dump(gettype($result));
    // var_dump($result);

    curl_close($ch);

    // créer un objet à partir du retour qui est un string
    $obj_vehicule = json_decode($result);

    // var_dump($obj_vehicule);

    // la on a un array
    //si on a l'erreur de token authentification alors on relance un token
    if (isset($obj_vehicule->code) && $obj_vehicule->code == 401) {
        $url = "https://www.kepler-soft.net/api/v3.0/auth-token/";
        $valeur_token = goCurlToken($url);
        $obj = getvehiculeInfo($reference, $valeur_token, $url_vehicule, $state);
        $return = $obj;
    }
    //sinon on continue normal
    else {
        //on prend l'object qui est dans l'array
        $return = $obj_vehicule[0];
        // on retourne un objet
    }
    // var_dump($return);
    return $return;



}


function getVehicules_VOreprise_RepTVA($token, $url_vehicule, $date_filtre, $page)
{
    $ch = curl_init();

    // le token
    $header = array();
    $header[] = 'X-Auth-Token:' . $token;
    $header[] = 'Content-Type:text/html;charset=utf-8';


    //le ou les parametres de l'url
    $dataArray = array(
        'dateUpdatedStart' => $date_filtre,
        'count' => 100,
        'page' => $page
    );


    $data = http_build_query($dataArray);

    $getURL = $url_vehicule . '?' . $data;


    print_r($getURL);

    sautdeligne();

    curl_setopt($ch, CURLOPT_URL, $getURL);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);

    $result = curl_exec($ch);

    if (curl_error($ch)) {
        $result = curl_error($ch);
        print_r($result);
        echo "<br/> erreur";
    }

    curl_close($ch);

    // print_r($result);
    // die();

    // créer un array à partir du retour qui est un string
    $obj_vehicule = json_decode($result);

    // $return = $obj_vehicule[0];

    return $obj_vehicule;
}


//FONCTION POUR RECUPERER LES DONNEES
function GoCurl_Facture($token, $url, $page)
{

    $ch = curl_init();

    // le token
    //$token = '7MLGvf689hlSPeWXYGwZUi\/t2mpcKrvVr\/fKORXMc+9BFxmYPqq4vOZtcRjVes9DBLM=';
    $header = array();
    $header[] = 'X-Auth-Token:' . $token;
    $header[] = 'Content-Type:text/html;charset=utf-8';

    // choper une facture spécifique
    // $dataArray = array(
    //     "orderFormNumber" => '82721',
    //     "page" => $page
    // );


    // sur un véhicule spécifique
    // $dataArray = array(
    //     "vehicleReference" => 'rcvfq',
    //     "page" => $page
    // );

    $date_to = date('Y-m-d', strtotime('-1 day'));
    $date_to = "2024-04-09";


    //sur une date
    $dataArray = array(
        "state" => 'invoice.state.edit',
        "invoiceDateFrom" => $date_to,
        "invoiceDateTo" => $date_to,
        // "updateDateFrom" => "2023-12-08",
        // "updateDateTo" => "2023-11-06",
        "count" => "100",
        "page" => $page
    );


    $data = http_build_query($dataArray);

    $getURL = $url . '?' . $data;

    print_r($getURL);

    sautdeligne();

    curl_setopt($ch, CURLOPT_URL, $getURL);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);

    $result = curl_exec($ch);

    if (curl_error($ch)) {
        $result = curl_error($ch);
        print_r($result);
        echo "<br/> erreur";
    }

    curl_close($ch);

    echo "<pre>";
    print_r($result);
    echo "</pre>";

    $obj = json_decode($result);

    return $obj;
}

function GoCurl_Facture_edite($token, $url, $page)
{

    $ch = curl_init();

    // le token
    //$token = '7MLGvf689hlSPeWXYGwZUi\/t2mpcKrvVr\/fKORXMc+9BFxmYPqq4vOZtcRjVes9DBLM=';
    $header = array();
    $header[] = 'X-Auth-Token:' . $token;
    $header[] = 'Content-Type:text/html;charset=utf-8';

    // choper une facture spécifique
    // $dataArray = array(
    //     "orderFormNumber" => '70670',
    //     "page" => $page
    // );


    // sur un véhicule spécifique
    // $dataArray = array(
    //     "vehicleReference" => 'rcvfq',
    //     "page" => $page
    // );

    //sur une date
    $dataArray = array(
        "state" => 'invoice.state.edit',
        "invoiceDateFrom" => "2023-12-08",
        // "invoiceDateTo" => "2023-11-06",
        // "updateDateFrom" => "2023-12-08",
        // "updateDateTo" => "2023-11-06",
        "count" => "100",
        "page" => $page


    );


    $data = http_build_query($dataArray);

    $getURL = $url . '?' . $data;

    print_r($getURL);

    sautdeligne();

    curl_setopt($ch, CURLOPT_URL, $getURL);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);

    $result = curl_exec($ch);

    if (curl_error($ch)) {
        $result = curl_error($ch);
        print_r($result);
        echo "<br/> erreur";
    }

    curl_close($ch);

    echo "<pre>";
    print_r($result);
    echo "</pre>";

    $obj = json_decode($result);

    return $obj;
}


// JSON TO CSV
function array2csv($data, $delimiter = ';', $enclosure = '"', $escape_char = "\\")
{
    $f = fopen('test_datas_bdc.csv', 'wr+');
    $entete[] = ["N° Bon Commande", "Immatriculation", "RS/Nom Acheteur", "Prix Vente HT", "Prix de vente TTC", "Vendeur Vente", "date du dernier BC", "Destination de sortie", "VIN"];

    //entete
    foreach ($entete as $item) {
        fputcsv($f, $item, $delimiter, $enclosure, $escape_char);
    }

    foreach ($data as $item) {
        fputcsv($f, $item, $delimiter, $enclosure, $escape_char);
    }
    rewind($f);
    return stream_get_contents($f);
}


function get_CVO_by_vendeur($nom_vendeur)
{

    //dans le cas ou ya un é
    $nom_vendeur = str_replace('é', 'e', $nom_vendeur);

    $name_vendeur = get_name_acheteur_vendeur($nom_vendeur);

    $pdo = Connection::getPDO();
    $request = $pdo->query("SELECT cvo.nom_cvo FROM `cvo` 
        LEFT JOIN collaborateurs_payplan as cp ON cp.id_site = cvo.ID 
        WHERE cp.nom = '$name_vendeur' ");
    $cvo = $request->fetch(PDO::FETCH_COLUMN);
    return strtoupper($cvo);

}


function get_name_acheteur_vendeur($nom_complet)
{

    $nb_word_nom_complet = str_word_count($nom_complet);

    switch ($nb_word_nom_complet) {
        //si c'est une agence
        case 1:
            return $nom_complet;
            break;

        //sinon si c'est un collaborateur hors agence
        case 2:
            if ($nom_complet !== null || $nom_complet !== '') {
                $nom_complet_acheteur = $nom_complet;
                $acheteur = explode(" ", strtolower($nom_complet_acheteur));
                // si jamais on a un point à la place de l'espace
                if (empty($acheteur[1])) {
                    $acheteur = explode(".", strtolower($nom_complet_acheteur));
                }
                // $prenom_acheteur = $acheteur[0];
                $nom_acheteur = $acheteur[1];
                return $nom_acheteur;
            } else {
                return '';
            }
            break;
    }
}

function upload_bdc_ventes_uuid($bdc, $uuid, $date_bdc)
{
    $pdo = Connection::getPDO();
    $data = [
        'bdc' => $bdc,
        'uuid' => $uuid,
        'date_bdc' => $date_bdc
    ];
    $sql = "INSERT INTO bdc_ventes (numero_bdc, uuid,date_bdc) VALUES (:bdc, :uuid,:date_bdc)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($data);

}

function get_bdc_from_uuid($uuid)
{
    $pdo = Connection::getPDO();
    $request = $pdo->query("SELECT numero_bdc FROM bdc_ventes WHERE uuid = '$uuid' ");
    $uuid = $request->fetch(PDO::FETCH_COLUMN);
    return $uuid;
}

function get_chassis_from_bdc($bdc)
{
    $pdo = Connection::getPDO_2();
    $request = $pdo->query("SELECT numero_chassis FROM vehicules 
    LEFT JOIN bdcventes ON bdcventes.vehicule_id = vehicules.id
    WHERE bdcventes.numero = '$bdc' ");
    $uuid = $request->fetchAll(PDO::FETCH_ASSOC);
    return $uuid;

}


// SAUT DE LIGNE 
function sautdeligne()
{
    echo "<br/>";
    echo "<br/>";
}


function get_token()
{
    $url_token = "https://www.kepler-soft.net/api/v3.0/auth-token/";

    $ch2 = curl_init();

    curl_setopt($ch2, CURLOPT_URL, $url_token);
    curl_setopt($ch2, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch2, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch2, CURLOPT_POST, true);
    curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch2, CURLOPT_POSTFIELDS, "apiKey=54c4fbe3ee0ed3683a17488371d5e762b9c5f4db6a7bc0507d3518c40bedfe300fbece014e3eac21acd380a142e874c460931659fe922bc1ef170d1f325e499c");

    $result = curl_exec($ch2);

    curl_close($ch2);

    $data = json_decode($result, true);

    return $data['value'];
}


function recup_bdc($num_bdc = '', $date_bdc = '', $page)
{

    $token = get_token();
    $ch = curl_init();

    // le token
    $header = array();
    $header[] = 'X-Auth-Token:' . $token;
    $header[] = 'Content-Type:text/html;charset=utf-8';

    // choper un BC spécifique
    if (isset($num_bdc) && $num_bdc != '') {
        $dataArray = array(
            /* 'state' => array(
            //     'administrative_selling.state.invoiced_edit',
            //     'administrative_selling.state.valid',
            //     'administrative_selling.state.invoiced',
            // ),*/
            "uniqueId" => $num_bdc,
            "page" => 1
        );
    }

    //sinon par date
    if (isset($date_bdc) && $date_bdc != '') {



        $dataArray = array(
            // "state" => array(
            //     'administrative_selling.state.invoiced_edit',
            //     'administrative_selling.state.valid',
            //     'administrative_selling.state.invoiced',
            // ),
            "orderFormDateFrom" => "$date_bdc",
            "orderFormDateTo" => "$date_bdc",
            "count" => 100,
            "page" => $page
        );
    }

    $url = "https://www.kepler-soft.net/api/";
    $request_bon_de_commande = "v3.1/order-form/";
    $req_url_BC = $url . "" . $request_bon_de_commande;

    $getURL = $req_url_BC . '?' . http_build_query($dataArray);

    // print_r($getURL);

    sautdeligne();

    curl_setopt($ch, CURLOPT_URL, $getURL);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);

    $result = curl_exec($ch);

    if (curl_error($ch)) {
        $result = curl_error($ch);
        print_r($result);
        echo "<br/> erreur";
    }

    curl_close($ch);

    // echo gettype($result);
    // echo $result;

    $obj = json_decode($result);
    $result = $obj->datas;
    return $result;
}

function recup_facture($num_facture = '', $date_facture = '', $page)
{
    $token = get_token();
    $ch = curl_init();

    // le token
    $header = array();
    $header[] = 'X-Auth-Token:' . $token;
    $header[] = 'Content-Type:text/html;charset=utf-8';

    //  choper une facture spécifique
    if ($num_facture && $num_facture !== '') {
        $dataArray = array(
            "number" => $num_facture,
            "page" => $page
        );
    }

    // sur un véhicule spécifique
    // $dataArray = array(
    //     "vehicleReference" => 'rcvfq',
    //     "page" => $page
    // );

    //sur une date
    if ($date_facture && $date_facture !== '') {
        $dataArray = array(
            // "state" => 'invoice.state.edit',
            "invoiceDateFrom" => $date_facture,
            "invoiceDateTo" => $date_facture,
            // "updateDateFrom" => "2023-12-08",
            // "updateDateTo" => "2023-11-06",
            "count" => "100",
            "page" => $page
        );
    }

    // URL factures API
    $request_facture = "v3.1/invoice/";
    $url = "https://www.kepler-soft.net/api/";
    $req_url = $url . "" . $request_facture;

    $data = http_build_query($dataArray);

    $getURL = $req_url . '?' . $data;

    // print_r($getURL);

    curl_setopt($ch, CURLOPT_URL, $getURL);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);

    $result = curl_exec($ch);

    if (curl_error($ch)) {
        $result = curl_error($ch);
        print_r($result);
        echo "<br/> erreur";
    }

    curl_close($ch);

    $obj = json_decode($result);
    $result = $obj->datas;
    // var_dump($result);
    return $result;
}

function recup_vehicule($dossier_ref = '', $vin_chassis = '', $immatriculation = '', $is_not_available_for_sell = '', $page)
{

    // le token
    $token = get_token();

    switch ($is_not_available_for_sell) {
        case TRUE:
            if ($dossier_ref && $dossier_ref !== '') {
                $dataArray = array(
                    "reference" => $dossier_ref,
                    "state" => 'vehicle.state.sold,vehicle.state.sold_ar,vehicle.state.pending,vehicle.state.out,vehicle.state.out_ar,vehicle.state.canceled,vehicle.state.on_arrival,vehicle.state.parc',
                    "isNotAvailableForSelling" => TRUE
                );
            } elseif ($vin_chassis && $vin_chassis !== '') {
                $dataArray = array(
                    "vin" => $vin_chassis,
                    "state" => 'vehicle.state.sold,vehicle.state.sold_ar,vehicle.state.pending,vehicle.state.out,vehicle.state.out_ar,vehicle.state.canceled,vehicle.state.on_arrival,vehicle.state.parc',
                    "isNotAvailableForSelling" => TRUE
                );
            } elseif ($immatriculation && $immatriculation !== '') {
                $dataArray = array(
                    "licenseeNumber" => $immatriculation,
                    "state" => 'vehicle.state.sold,vehicle.state.sold_ar,vehicle.state.pending,vehicle.state.out,vehicle.state.out_ar,vehicle.state.canceled,vehicle.state.on_arrival,vehicle.state.parc',
                    "isNotAvailableForSelling" => TRUE
                );
            }
            break;

        case FALSE:
            if ($dossier_ref && $dossier_ref !== '') {
                $dataArray = array(
                    "reference" => $dossier_ref,
                    "state" => 'vehicle.state.sold,vehicle.state.sold_ar,vehicle.state.pending,vehicle.state.out,vehicle.state.out_ar,vehicle.state.canceled,vehicle.state.on_arrival,vehicle.state.parc',
                    "isNotAvailableForSelling" => FALSE
                );
            } elseif ($vin_chassis && $vin_chassis !== '') {
                $dataArray = array(
                    "vin" => $vin_chassis,
                    "state" => 'vehicle.state.sold,vehicle.state.sold_ar,vehicle.state.pending,vehicle.state.out,vehicle.state.out_ar,vehicle.state.canceled,vehicle.state.on_arrival,vehicle.state.parc',
                    "isNotAvailableForSelling" => FALSE
                );
            } elseif ($immatriculation && $immatriculation !== '') {
                $dataArray = array(
                    "licenseeNumber" => $immatriculation,
                    "state" => 'vehicle.state.sold,vehicle.state.sold_ar,vehicle.state.pending,vehicle.state.out,vehicle.state.out_ar,vehicle.state.canceled,vehicle.state.on_arrival,vehicle.state.parc',
                    "isNotAvailableForSelling" => FALSE
                );
            }
            break;
    }
    $request_vehicule = "v3.7/vehicles/";
    $url = "https://www.kepler-soft.net/api/";

    $url_vehicule = $url . "" . $request_vehicule;

    $data = http_build_query($dataArray);

    $getURL = $url_vehicule . '?' . $data;

    // print_r($getURL);

    sautdeligne();

    $ch = curl_init();
    $header = array();
    $header[] = 'X-Auth-Token:' . $token;
    $header[] = 'Content-Type:text/html;charset=utf-8';

    curl_setopt($ch, CURLOPT_URL, $getURL);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);

    $result = curl_exec($ch);

    if (curl_error($ch)) {
        $result = curl_error($ch);
        print_r($result);
        echo "<br/> erreur";
    }

    var_dump(gettype($result));
    // var_dump($result);

    curl_close($ch);

    // créer un objet à partir du retour qui est un string
    $obj_vehicule = json_decode($result);

    //on prend l'object qui est dans l'array
    $return = $obj_vehicule[0];
    // on retourne un objet

    // var_dump($return);
    return $return;

}



function mise_en_forme_result($type_recherche, $data)
{
    $return = "";
    $nb_ligne = 1;
    switch ($type_recherche) {
        case "bdc":
            $return .= "<table class='my_table_bdc'>";
            $return .= "<thead>";
            $return .= creation_header_thead(array(" ","Uuid", "Numéro BDC", "Etat", "Date", "Vendeur", "Prix total TTC"));
            $return .= "</thead>";
            foreach ($data as $bdc) {
                $return .= "<tr>";
                $return .= "<td> " . $nb_ligne . " </td>";
                $return .= "<td> " . $bdc->uuid . " </td>";
                $return .= "<td> " . $bdc->number . " </td>";
                $return .= "<td> " . $bdc->state . " </td>";
                $date = new DateTime($bdc->date);
                $date_bdc = $date->format('d/m/Y');
                $return .= "<td> " . $date_bdc . " </td>";
                $return .= "<td> " . $bdc->seller . " </td>";
                $return .= "<td> " . $bdc->sellPriceWithTax . " </td>";
                $return .= "</tr>";
                $nb_ligne++;
            }
            $return .= "</table>";
            break;

        case "facture":
            $return .= "<table class='my_table_facture'>";
            $return .= "<thead>";
            $return .= creation_header_thead(array(" ","Uuid", "Numéro facture", "Etat", "Date", "Vendeur", "Prix total TTC", "BDC lié"));
            $return .= "</thead>";
            foreach ($data as $facture) {

                //on prend que les VO***
                if (strpos($facture->number, 'VO') !== FALSE) {
                    $return .= "<tr>";
                    $return .= "<td> " . $nb_ligne . " </td>";
                    $return .= "<td> " . $facture->uuid . " </td>";
                    $return .= "<td> " . $facture->number . " </td>";
                    $return .= "<td> " . $facture->state . " </td>";
                    $date = new DateTime($facture->invoiceDate);
                    $date_facturation = $date->format('d/m/Y');
                    $return .= "<td> " . $date_facturation . " </td>";
                    $return .= "<td> " . $facture->seller . " </td>";
                    $return .= "<td> " . $facture->sellPriceWithTax . " </td>";
                    $return .= "<td> " . $facture->orderForm->number . " </td>";
                    $return .= "</tr>";
                    $nb_ligne++;
                }
            }
            $return .= "</table>";
            break;

        case "vh":
            $return .= "<table class='my_table_vh'>";
            $return .= "<thead>";
            $return .= creation_header_thead(array(" ","Uuid", "réference kepler", "Etat", "Marque", "Modele", "Version", "Type VO/VN", "Type", "VIN", "Immatriculation"));
            $return .= "</thead>";

            $vh = $data;
            $return .= "<tr>";
            $return .= "<td> " . $vh->uuid . " </td>";
            $return .= "<td> " . $vh->reference . " </td>";
            $return .= "<td> " . $vh->state . " </td>";
            $return .= "<td> " . $vh->brand->name . " </td>";
            $return .= "<td> " . $vh->model->name . " </td>";
            $return .= "<td> " . $vh->version->name . " </td>";
            $return .= "<td> " . $vh->typeVoVn->name . " </td>";
            $return .= "<td> " . $vh->vehicleType->name . " </td>";
            $return .= "<td> " . $vh->vin . " </td>";
            $return .= "<td> " . $vh->licenseNumber . " </td>";
            $return .= "</tr>";
            $return .= "</table>";
            break;
    }

    return $return;
}
