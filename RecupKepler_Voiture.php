<!DOCTYPE html>
<html>

<head>
    <title>Recup API KEPLER VEHICULE</title>
    <meta charset="utf-8" />
</head>

<body>
    <h2>Récupération du token</h2>


    <?php

    header('Content-type: text/html; charset=UTF-8');

    include 'fonctions.php';

    /******************************************  CODE MAIN ******************************************/

    // recup valeur token seulement
    $url = "https://www.kepler-soft.net/api/v3.0/auth-token/";
    $valeur_token = goCurlToken($url);

    sautdeligne();

    ?>



    <span style="color:red"> <?php echo $valeur_token; ?> </span>

    <?php

    echo "<h2>Récupération des données véhicule(s)</h2>";

    sautdeligne();

    // recup données

    $request_bon_de_commande = "v3.1/order-form/";
    $request_facture = "v3.1/invoice/";
    $request_vehicule = "v3.7/vehicles/";

    $url = "https://www.kepler-soft.net/api/";

    //$req_url = $url . "" . $request_bon_de_commande;
    $req_url = $url . "" . $request_vehicule;
    //$req_url = $url . "" . $request_vehicule;

    sautdeligne();

    // $obj = GoCurl($valeur_token, $req_url);

    $reference ='u41yasu';

    $obj = getvehiculeInfo($reference, $valeur_token, $req_url, true);

    // $obj = get_vehicules_veille($valeur_token,$req_url,false);

    sautdeligne();
    sautdeligne();
    sautdeligne();


    $array_uniqueId = array();
    $array_AcheteurName = array();
    $array_prixHT = array();
    $array_prixTTC = array();
    $array_vendeurName = array();
    $array_dateBC = array();


    $array_datas = array();

    // echo '<pre>' . var_dump($obj) . '<pre>';

    $type_retour =  gettype($obj);

    var_dump($type_retour);


    if ($type_retour == 'object') {

        // echo $obj->brand->reference;
        // echo gettype($obj_test);

        var_dump($obj);


        sautdeligne();
        sautdeligne();

        // echo 'toto';

        sautdeligne();
        sautdeligne();

        // print_r($obj_test);

        sautdeligne();
        sautdeligne();

        // echo 'toto2';

        sautdeligne();
        sautdeligne();

        // echo $obj_test->vin;


        //echo gettype($obj);


        sautdeligne();
        sautdeligne();
    } else {
        echo 'tata';
    }





    //array2csv($array_datas);



    //echo $obj->datas[23]->items[2]->sellPriceWithoutTax;



    //jsonToCSV($obj,"test.csv");

    /*************************************  FIN CODE MAIN ******************************************/




    ?>



</body>

</html>