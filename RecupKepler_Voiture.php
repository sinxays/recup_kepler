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

    require 'fonctions.php';

    /******************************************  CODE MAIN ******************************************/

    // recup valeur token seulement
    $url = "https://www.kepler-soft.net/api/v3.0/auth-token/";
    $valeur_token = goCurlToken($url);

    sautdeligne();

    ?>



    <span style="color:red"> <?php echo $valeur_token; ?> </span>

    <?php

    echo "<h2>Récupération des données véhcule(s)</h2>";

    sautdeligne();

    // recup données

    $request_bon_de_commande = "v3.1/order-form/";
    $request_facture = "v3.1/invoice/";
    $request_vehicule = "v3.7/vehicles/";

    $url = "https://www.kepler-soft.net/api/";

    //$req_url = $url . "" . $request_bon_de_commande;
    $req_url_vehicule = $url . "" . $request_vehicule;
    //$req_url = $url . "" . $request_vehicule;

    sautdeligne();

    // $obj = GoCurl($valeur_token, $req_url);

    $reference = ' 3vwh1b7';

    $obj_result = getvehiculeInfo($reference, $valeur_token, $req_url_vehicule, false);
    if (empty($obj_result)) {
        $obj_result = getvehiculeInfo($reference, $valeur_token, $req_url_vehicule, true);
    }

    echo '<pre>' . var_dump($obj_result) . '<pre>';

    $type_retour =  gettype($obj_result);

    echo 'le type est ' .$type_retour.'<br/><br/>';

    if ($type_retour !== 'object') {

        $obj_test = $obj_result[0];

        echo gettype($obj_test);

        sautdeligne();

        echo "le VIN est : $obj_test->vin";




        sautdeligne();
        sautdeligne();
    }
    else{
        echo 'tata';
    }



   ?>



</body>

</html>