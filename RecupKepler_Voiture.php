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



    <span style="color:red">
        <?php echo $valeur_token; ?>
    </span>

    <?php

    echo "<h2>Récupération des données véhicule(s)</h2>";

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
    
    $reference = '1dgcyoho';
    $state = '';

    $obj = getvehiculeInfo($reference, $valeur_token, $req_url, $state, FALSE);

    //si vide alors sans doute que le véhicule n'ets aps dispo à la vente
    if (empty ($obj)) {
        $result = getvehiculeInfo($reference, $valeur_token, $req_url, $state, TRUE);
        $obj = $result;
    }

    $type_retour = gettype($obj);

    var_dump($type_retour);

    var_dump($obj);



    // echo '<pre>' . var_dump($obj) . '<pre>';
    if ($type_retour == 'object') {
        // echo $obj->brand->reference;
        // echo gettype($obj_test);
    } else {
        echo 'pas un objet';
    }



    /*************************************  FIN CODE MAIN ******************************************/




    ?>



</body>

</html>