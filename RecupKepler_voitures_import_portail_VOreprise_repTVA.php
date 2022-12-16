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


    $obj = getVehicules_VOreprise_RepTVA($valeur_token, $req_url);

    // print_r($obj);
    // die();

    sautdeligne();
    sautdeligne();

    foreach ($obj as $vehicule) {
        $date_creation_vh = $vehicule->createdAt;
        echo $date_creation_vh;
        sautdeligne();
        // $type = $vehicule->typeVoVn->name;
        // if ($type == 'VO REPRISE' || $type == 'REP TVA')
        //     echo $vehicule->reference;
        // sautdeligne();
    }

    sautdeligne();
    sautdeligne();
    
    die();

    print_r($obj);

    $type_retour =  gettype($obj);


    if ($type_retour == 'object') {

        sautdeligne();
        sautdeligne();

        echo $obj->reference;
        // echo gettype($obj_test);


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