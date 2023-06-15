<!DOCTYPE html>
<html>

<head>
    <title>Recup API KEPLER BDC</title>
    <meta charset="utf-8" />
</head>

<body>
    <h2>Récupération du token</h2>


    <?php

    header('Content-type: text/html; charset=UTF-8');

    require('xlsxwriter.class.php');

    include 'fonctions.php';

    /******************************************  CODE MAIN ******************************************/

    // recup valeur token seulement
    $url = "https://www.kepler-soft.net/api/v3.0/auth-token/";
    $valeur_token = goCurlToken($url);

    sautdeligne();

    ?>



    <span style="color:red"> <?php echo $valeur_token; ?> </span>

    <?php

    echo "<h2>Récupération des données BDC</h2>";

    sautdeligne();

    // recup données

    $request_bon_de_commande = "v3.1/order-form/";
    $request_facture = "v3.1/invoice/";
    $request_vehicule = "v3.7/vehicles/";

    $url = "https://www.kepler-soft.net/api/";

    $req_url_BC = $url . "" . $request_bon_de_commande;
    $req_url_vehicule = $url . "" . $request_vehicule;
    $req_url_factures = $url . "" . $request_facture;
    //$req_url = $url . "" . $request_vehicule;

    

    sautdeligne();


    $datas_find = true;
    $j = 1;

    $array_datas = array();

    $nb_BC = 0;
    $nb_lignes = 0;
    $i = 0;
    $nb_total_vehicules_selling = 0;

    $array_datas[$i] = ["N° Bon Commande", "Immatriculation", "RS/Nom Acheteur", "Prix Vente HT", "Prix de vente TTC", "Vendeur Vente", "date du dernier BC", "Destination de sortie", "VIN"];

    $i++;

    while ($datas_find == true) {

        //recupere la requete !!!!
        $obj = GoCurl($valeur_token, $req_url_BC, $j);
        $obj_final = $obj->datas;

        if (!empty($obj_final)) {
            $datas_find = true;

            sautdeligne();
            sautdeligne();

            /*****************    BOUCLE du tableau de données récupérés *****************/

            //$array_datas[$i] = ["N° Bon Commande", "Immatriculation", "RS/Nom Acheteur", "Prix Vente HT", "Prix de vente TTC", "Vendeur Vente", "date du dernier BC", "Destination de sortie", "VIN"];

            $i++;
            foreach ($obj_final as $keydatas => $keyvalue) {
                //on boucle par rapport au nombre de bon de commande dans le tableau datas[]

                //get ID
                $bdc = $keyvalue->uniqueId;

                //get nom acheteur
                if (isset($keyvalue->owner->firstname)) {
                    $nom_acheteur = $keyvalue->owner->firstname . " " . $keyvalue->owner->lastname;
                } else {
                    $nom_acheteur = $keyvalue->owner->corporateName;
                }

                //get nom vendeur
                $nom_vendeur = $keyvalue->seller;
                $nom_vendeur = explode("<", $nom_vendeur);
                $nom_vendeur = $nom_vendeur[0];

                //GET DATE BC
                $date_BC =  substr($keyvalue->date, 0, 10);
                $date_BC = str_replace("-", "/", $date_BC);

                //get destination sortie
                if (empty($keyvalue->destination)) {
                    $destination_sortie  = '';
                } else {
                    $destination_sortie = $keyvalue->destination;
                }

                // $test1[] = $keyvalue->uniqueId;
                // $test2[] = $keydatas;

                if (isset($keyvalue->items)) {

                    foreach ($keyvalue->items as $key_item => $value_item) {
                        if ($value_item->type == 'vehicle_selling') {

                            $reference_item = $value_item->reference;

                            //  recup infos du véhicule
                            $obj_result = getvehiculeInfo($reference_item, $valeur_token, $req_url_vehicule, false);
                            if (empty($obj_result)) {
                                $obj_result = getvehiculeInfo($reference_item, $valeur_token, $req_url_vehicule, true);
                            }


                            if (gettype($obj_result) == 'object') {

                                $rien = 'object';

                                $array_datas[$i]['uniqueId'] = $rien;
                                $array_datas[$i]['immatriculation'] = $rien;
                                $array_datas[$i]['name'] = $rien;
                                $array_datas[$i]['prixTotalHT'] = $rien;
                                $array_datas[$i]['prixTTC'] = $rien;
                                $array_datas[$i]['nomVendeur'] = $rien;
                                $array_datas[$i]['dateBC'] = $rien;
                                $array_datas[$i]['Destination_sortie'] = $rien;
                                $array_datas[$i]['VIN'] = $rien;
                            } else {
                                $obj_vehicule = $obj_result[0];


                                // get VIN
                                $vin = $obj_vehicule->vin;

                                //get IMMATRICULATION
                                if (empty($obj_vehicule->licenseNumber)) {
                                    $immatriculation = 'N/C';
                                } else {
                                    $immatriculation = $obj_vehicule->licenseNumber;
                                }

                                $immatriculation = str_replace("-", "", $immatriculation);


                                $array_datas[$i]['uniqueId'] = $bdc;
                                $array_datas[$i]['immatriculation'] = $immatriculation;
                                $array_datas[$i]['name'] = $nom_acheteur;
                                $array_datas[$i]['prixTotalHT'] = $value_item->sellPriceWithoutTax;
                                $array_datas[$i]['prixTTC'] = $value_item->sellPriceWithTax;
                                $array_datas[$i]['nomVendeur'] = $nom_vendeur;
                                $array_datas[$i]['dateBC'] = $date_BC;
                                $array_datas[$i]['Destination_sortie'] = $destination_sortie;
                                $array_datas[$i]['VIN'] = $vin;
                            }


                            // $test3[] = $value_item->reference;
                            $nb_total_vehicules_selling++;
                            $nb_lignes++;
                            $i++;
                        }
                    }
                }

                $nb_BC++;
            }
        } else {
            $datas_find = false;
        }
        $j++;
    }


    sautdeligne();

    echo 'nombre de BC : ' . $nb_BC;

    sautdeligne();

    echo 'nombre de lignes : ' . $nb_lignes;

    sautdeligne();

    echo 'nombre de lignes : ' . $nb_total_vehicules_selling;


    sautdeligne();
    sautdeligne();


    // print_r($array_datas);


    // array2csv($array_datas);

    $writer = new XLSXWriter();
    $writer->writeSheet($array_datas);
    $writer->writeToFile('test_xlsx_BDC_test.xlsx');





    /*************************************  FIN CODE MAIN ************************************************************/






    /************************************************  FONCTIONS *****************************************************/

    //fonction pour récupérer le token avec la clé API
    function goCurlToken($url)
    {
        $ch2 = curl_init();

        $header = array();

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


    //fonction pour récupérer les données
    function GoCurl($token, $url, $page)
    {

        $ch = curl_init();

        // le token
        //$token = '7MLGvf689hlSPeWXYGwZUi\/t2mpcKrvVr\/fKORXMc+9BFxmYPqq4vOZtcRjVes9DBLM=';
        $header = array();
        $header[] = 'X-Auth-Token:' . $token;
        $header[] = 'Content-Type:text/html;charset=utf-8';

        // le ou les parametres de l'url
        $dataArray = array(
            "state" => 'administrative_selling.state.valid',
            "updateDateFrom" => "2022-12-01",
            "count" => "100",
            "page" => $page
        );

        // $dataArray = array(
        //     "state" => 'administrative_selling.state.already_sold',
        //     "updateDateFrom" => "2020-09-01",
        //     "count" => 100,
        //     "page" => $page
        // );

        // choper un BC spécifique
        // $dataArray = array(
        //     "orderFormNumber" => "70670",
        //     "page" => $page
        // );


        $data = http_build_query($dataArray);

        $getURL = $url. '?' . $data;

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

        $obj = json_decode($result);

        echo '<pre>' . print_r($result) . '</pre>';

        return $obj;
    }



    // json to CSV
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


    //récupérer juste la valeur du token
    function recup_token_value($chaine)
    {
        $try = explode(",", $chaine);

        $try2 = explode(":", $try[0]);

        $try3 = str_replace("\"", "", $try2[1]);

        return $try3;
    }



    function sautdeligne()
    {
        echo "<br/>";
        echo "<br/>";
    }

    ?>


</body>

</html>