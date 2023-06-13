<?php


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
function GoCurl_Recup_BDC($token, $url, $page)
{

    $ch = curl_init();

    // le token
    $header = array();
    $header[] = 'X-Auth-Token:' . $token;
    $header[] = 'Content-Type:text/html;charset=utf-8';

    // $dataArray = array(
    //     "updateDateFrom" => "2022-01-01",
    //     "state" => [
    //         "administrative_selling.state.invoiced_edit",
    //         "administrative_selling.state.valid",
    //         "administrative_selling.state.invoiced"
    //     ],
    //     "count" => 100,
    //     "page" => $page
    // );


    // $dataArray2 = array(
    //     'orderFormDateFrom' => '2022-12-01',
    //     'state' => array(
    //         'administrative_selling.state.invoiced_edit',
    //         'administrative_selling.state.valid',
    //         'administrative_selling.state.invoiced',
    //     ),
    //     'count' => 100,
    //     'page' => $page
    // );

    $dataArray = array(
        "state" => 'administrative_selling.state.valid',
        "orderFormDateFrom" => "2023-06-12",
        "orderFormDateTo" => "2023-06-12",        
        "count" => 100,
        "page" => $page
    );


    // choper un BC spécifique
    $dataArray_unique = array(
        // 'state' => array(
        //     'administrative_selling.state.invoiced_edit',
        //     'administrative_selling.state.valid',
        //     'administrative_selling.state.invoiced',
        // ),
        "uniqueId" => "79735",
        "page" => $page
    );


    $getURL = $url . '?' . http_build_query($dataArray);

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

    // echo gettype($result);
    // echo $result;
    // die();

    $obj = json_decode($result);

    // sautdeligne();
    // echo gettype($obj);
    // die();



    // echo gettype($obj);
    // var_dump($obj);


    //  echo '<pre>' . print_r(Utf8_ansi($result)) . '</pre>';

    // echo '<pre>' . print_r($obj) . '</pre>';

    echo '<pre> nombre total de BDC : ' . $obj->total . '</pre>';
    echo '<pre> page actuelle :' . $obj->currentPage . '</pre>';
    echo '<pre> BDC par page :' . $obj->perPage . '</pre>';

    return $obj;
}

// FONCTION INFOS DU VEHICULES
function getvehiculeInfo($reference, $token, $url_vehicule, $isNotAvailable_param,$state)
{

    $ch = curl_init();

    // le token
    $header = array();
    $header[] = 'X-Auth-Token:' . $token;
    $header[] = 'Content-Type:text/html;charset=utf-8';

    if ($state == 'arrivage_or_parc') {

        //le ou les parametres de l'url

        // !!!! si le vh est vendu, vendu AR, en cours, sorti, sorti AR ou annulé alors il faudra mettre l'état obligatoirement si tu veux un retour 
        $dataArray = array(
            "reference" => $reference,
            // "isNotAvailableForSelling" => $isNotAvailable_param
        );
    } else {

        $dataArray = array(
            "reference" => $reference,
            "state" => 'vehicle.state.sold,vehicle.state.sold_ar,vehicle.state.pending,vehicle.state.out,vehicle.state.out_ar,vehicle.state.canceled',
            // "isNotAvailableForSelling" => $isNotAvailable_param
        );
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

    var_dump($result);
    die();

    if (curl_error($ch)) {
        $result = curl_error($ch);
        print_r($result);
        echo "<br/> erreur";
    }

    curl_close($ch);

    // echo gettype($result);
    // sautdeligne();
    // echo $result;

    // créer un array à partir du retour qui est un string
    $obj_vehicule = json_decode($result);

    $return = $obj_vehicule[0];

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
        'dateUpdatedStart' =>  $date_filtre,
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
    $dataArray = array(
        "orderFormNumber" => '70670',
        "page" => $page
    );

    //à partir de quelle date
    // $dataArray = array(
    //     "state" => 'invoice.state.edit',
    //     "invoiceDateFrom" => "2021-03-10",
    //     "count" => "100",
    // );


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





// SAUT DE LIGNE 
function sautdeligne()
{
    echo "<br/>";
    echo "<br/>";
}



function Utf8_ansi($valor = '')
{
    $utf8_ansi2 = array(
        "\u00c0" => "À",
        "\u00c1" => "Á",
        "\u00c2" => "Â",
        "\u00c3" => "Ã",
        "\u00c4" => "Ä",
        "\u00c5" => "Å",
        "\u00c6" => "Æ",
        "\u00c7" => "Ç",
        "\u00c8" => "È",
        "\u00c9" => "É",
        "\u00ca" => "Ê",
        "\u00cb" => "Ë",
        "\u00cc" => "Ì",
        "\u00cd" => "Í",
        "\u00ce" => "Î",
        "\u00cf" => "Ï",
        "\u00d1" => "Ñ",
        "\u00d2" => "Ò",
        "\u00d3" => "Ó",
        "\u00d4" => "Ô",
        "\u00d5" => "Õ",
        "\u00d6" => "Ö",
        "\u00d8" => "Ø",
        "\u00d9" => "Ù",
        "\u00da" => "Ú",
        "\u00db" => "Û",
        "\u00dc" => "Ü",
        "\u00dd" => "Ý",
        "\u00df" => "ß",
        "\u00e0" => "à",
        "\u00e1" => "á",
        "\u00e2" => "â",
        "\u00e3" => "ã",
        "\u00e4" => "ä",
        "\u00e5" => "å",
        "\u00e6" => "æ",
        "\u00e7" => "ç",
        "\u00e8" => "è",
        "\u00e9" => "é",
        "\u00ea" => "ê",
        "\u00eb" => "ë",
        "\u00ec" => "ì",
        "\u00ed" => "í",
        "\u00ee" => "î",
        "\u00ef" => "ï",
        "\u00f0" => "ð",
        "\u00f1" => "ñ",
        "\u00f2" => "ò",
        "\u00f3" => "ó",
        "\u00f4" => "ô",
        "\u00f5" => "õ",
        "\u00f6" => "ö",
        "\u00f8" => "ø",
        "\u00f9" => "ù",
        "\u00fa" => "ú",
        "\u00fb" => "û",
        "\u00fc" => "ü",
        "\u00fd" => "ý",
        "\u00ff" => "ÿ"
    );
    return strtr($valor, $utf8_ansi2);
}
