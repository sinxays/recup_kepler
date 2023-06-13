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

    $date_filtre = date("Y-m-d", strtotime("-1 day"));
    // $date_filtre = date("Y-m-d");
    // $date_filtre = date("2023-01-14");

    $data_find = true;
    $page = 1;
    $compteur_nb_vh_cree = 0;

    while ($data_find == true) {


        // $obj = getVehicules_VOreprise_RepTVA($valeur_token, $req_url, $date_filtre);
        $obj = getVehicules_VOreprise_RepTVA($valeur_token, $req_url, $date_filtre, $page);

        if (!empty($obj)) {

            // var_dump($obj);
            // die();

            sautdeligne();
            sautdeligne();

            foreach ($obj as $vehicule) {
                $date_creation_vh = $vehicule->createdAt;
                $date_creation_formatted_tmp = explode("T", $date_creation_vh);
                $date_creation_formatted = $date_creation_formatted_tmp[0];
                $reference = $vehicule->reference;
                $immatriculation = $vehicule->licenseNumber;

                // echo "<br/> $date_creation_formatted || $immatriculation || $reference <br/>";

                if ($date_creation_formatted == $date_filtre) {
                    echo " véhicule crée le <span style='color:red'> $date_creation_formatted </span>";
                    echo " || ";
                    echo "référence est <span style='color:red'>$reference</span>";
                    echo " || ";
                    echo "immatriculation est <span style='color:red'>$immatriculation</span>";
                    sautdeligne();
                    $compteur_nb_vh_cree ++;
                }
                // $type = $vehicule->typeVoVn->name;
                // if ($type == 'VO REPRISE' || $type == 'REP TVA')
                //     echo $vehicule->reference;
                // sautdeligne();
            }
        } else {
            $data_find = false;
        }
        $page++;
    }

    echo "nombre de véhicule crée le $date_filtre : $compteur_nb_vh_cree";



    // if ($type_retour == 'object') {
    //     sautdeligne();
    //     echo $obj->reference;
    // } else {
    //     echo 'tata';
    // }





    //array2csv($array_datas);



    //echo $obj->datas[23]->items[2]->sellPriceWithoutTax;



    //jsonToCSV($obj,"test.csv");

    /*************************************  FIN CODE MAIN ******************************************/




    ?>



</body>

</html>