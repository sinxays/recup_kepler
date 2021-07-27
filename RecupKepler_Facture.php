<!DOCTYPE html>
<html>

<head>
    <title>Recup API KEPLER FACTURE</title>
    <meta charset="utf-8" />
</head>

<body>
    <h2>Récupération du token</h2>


    <?php



    header('Content-type: text/html; charset=UTF-8');

    /******************************************  CODE MAIN ******************************************/

    include("xlsxwriter.class.php");

    // recup valeur token seulement
    $url = "https://www.kepler-soft.net/api/v3.0/auth-token/";
    $valeur_token = goCurlToken($url);

    sautdeligne();

    ?>



    <span style="color:red"> <?php echo $valeur_token; ?> </span>

    <?php

    echo "<h2>Récupération des données factures</h2>";


    // recup données
    $request_facture = "v3.1/invoice/";
    $request_vehicule = "v3.7/vehicles/";

    $url = "https://www.kepler-soft.net/api/";

    $req_url = $url . "" . $request_facture;
    $req_url_vehicule = $url . "" . $request_vehicule;


    $datas_find = true;
    $array_datas = array();
    $nb_factures = 0;
    $nb_lignes = 0;
    $i = 0;
    $j = 1;

    while ($datas_find == true) {

        //recupere la requete !!!!
        $obj = GoCurl_Facture($valeur_token, $req_url, $j);
        $obj_final = $obj->datas;

        if (!empty($obj_final)) {
            $datas_find = true;



            $array_datas[$i] = ["Date de la facture", "Immatriculation", "Kilométrage", "N° de la facture", "Prix de vente HT", "Acheteur", "Adresse du client", "Code postal du client", "Ville du client", "Pays du client", "Email du client", "Telephone du client", "Telephone portable du client", "Vendeur	Parc", "Destination	Source du client"];

            $i++;

            /*****************    BOUCLE du tableau de données récupérés *****************/
            foreach ($obj_final as $keydatas => $keyvalue) {
                //on boucle par rapport au nombre de bon de commande dans le tableau datas[]


                //get date facture
                $date_facture = $keyvalue->invoiceDate;


                //get numéro de facture
                $num_facture = $keyvalue->number;

                // get prix de vente HT
                $prixHT = $keyvalue->sellPriceWithoutTax;

                //get nom acheteur
                if (isset($keyvalue->owner->firstname)) {
                    $nom_acheteur = $keyvalue->owner->firstname . " " . $keyvalue->owner->lastname;
                } else {
                    $nom_acheteur = $keyvalue->owner->corporateNameContact;
                }

                //adresse du client
                $adresse_client = $keyvalue->customer->addressAddress;

                // Code postal du client
                $cp_client = $keyvalue->customer->addressPostalCode;

                // ville du client
                $ville_client = $keyvalue->customer->addressCity;

                // pays du client
                $pays_client = $keyvalue->customer->addressCountry;


                // email du client
                if (isset($keyvalue->customer->email) && !empty($keyvalue->customer->email)) {
                    $email_client = $keyvalue->customer->email;
                } else {
                    $email_client = '';
                }

                //tel fixe du client
                if (isset($keyvalue->customer->phoneNumber) && !empty($keyvalue->customer->phoneNumber)) {
                    $telfixe_client = $keyvalue->customer->phoneNumber;
                } else {
                    $telfixe_client = '';
                }

                //tel mobile du client
                if (isset($keyvalue->customer->cellPhoneNumber) && !empty($keyvalue->customer->cellPhoneNumber)) {
                    $telmobile_client = $keyvalue->customer->cellPhoneNumber;
                } else {
                    $telmobile_client = '';
                }

                //get nom vendeur
                $nom_vendeur = $keyvalue->seller;
                $nom_vendeur = explode("<", $nom_vendeur);
                $nom_vendeur = $nom_vendeur[0];


                //get destination sortie
                if (isset($keyvalue->destination) && !empty($keyvalue->destination)) {
                    $destination_sortie = $keyvalue->destination;
                } else {
                    $destination_sortie  = '';
                }


                // source du client
                if (isset($keyvalue->customer->knownFrom) && !empty($keyvalue->customer->knownFrom)) {
                    $source_client = $keyvalue->customer->knownFrom;
                } else {
                    $source_client  = '';
                }



                if (isset($keyvalue->items)) {
                    foreach ($keyvalue->items as $key_item => $value_item) {
                        if ($value_item->type == 'vehicle_selling') {

                            $reference_item = $value_item->reference;

                            //  recup infos du véhicule
                            $obj_result = getvehiculeInfo($reference_item, $valeur_token, $req_url_vehicule, false);
                            if (empty($obj_result)) {
                                $obj_result = getvehiculeInfo($reference_item, $valeur_token, $req_url_vehicule, true);
                            }
                            $obj_vehicule = $obj_result[0];

                            //get IMMATRICULATION
                            if (empty($obj_vehicule->licenseNumber)) {
                                $immatriculation = 'N/C';
                            } else {
                                $immatriculation = $obj_vehicule->licenseNumber;
                            }

                            // get kilométrage
                            $kilometrage = $obj_vehicule->distanceTraveled;

                            //get de quel parc
                            $parc = $obj_vehicule->fleet;


                            $array_datas[$i]['date_facture'] = $date_facture;
                            $array_datas[$i]['immatriculation'] = $immatriculation;
                            $array_datas[$i]['kilometrage'] = $kilometrage;
                            $array_datas[$i]['numerofacture'] = $num_facture;
                            $array_datas[$i]['prixHT'] = $prixHT;
                            $array_datas[$i]['acheteur'] = $nom_acheteur;
                            $array_datas[$i]['adresseclient'] = $adresse_client;
                            $array_datas[$i]['CPclient'] = $cp_client;
                            $array_datas[$i]['villeclient'] = $ville_client;
                            $array_datas[$i]['paysclient'] = $pays_client;
                            $array_datas[$i]['emailclient'] = $email_client;
                            $array_datas[$i]['telfixeclient'] = $telfixe_client;
                            $array_datas[$i]['telportableclient'] = $telmobile_client;
                            $array_datas[$i]['vendeur'] = $nom_vendeur;
                            $array_datas[$i]['parc'] = $parc;
                            $array_datas[$i]['destination'] = $destination_sortie;
                            $array_datas[$i]['source'] = $source_client;

                            $nb_lignes++;
                            $i++;
                        }
                    }
                }

                $nb_factures++;
            }
        } else {
            $datas_find = false;
        }
        $j++;
    }


    sautdeligne();

    echo 'nombre de Factures : ' . $nb_factures;

    sautdeligne();

    echo 'nombre de lignes : ' . $nb_lignes;






    $writer = new XLSXWriter();
    $writer->writeSheet($array_datas);
    $writer->writeToFile('test_xlsx_FACTURE.xlsx');








    /*************************************  FIN CODE MAIN ******************************************/













    /*************************************  FONCTIONS ******************************************/

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
        //     "orderFormNumber" => '70670',
        //     "page" => $page
        // );


        // sur un véhicule spécifique
        $dataArray = array(
            "vehicleReference" => 'rcvfq',
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




    function getvehiculeInfo($reference, $token, $url_vehicule, $isNotAvailable_param)
    {

        $ch = curl_init();

        // le token
        $header = array();
        $header[] = 'X-Auth-Token:' . $token;
        $header[] = 'Content-Type:text/html;charset=utf-8';

        //le ou les parametres de l'url
        if ($isNotAvailable_param == true) {
            $dataArray = array(
                "reference" => $reference,
                "state" => 'vehicle.state.sold,vehicle.state.sold_ar,vehicle.state.out,vehicle.state.out_ar',
                "isNotAvailableForSelling" => true
            );
        } else {
            $dataArray = array(
                "reference" => $reference,
                "state" => 'vehicle.state.sold,vehicle.state.sold_ar,vehicle.state.out,vehicle.state.out_ar',
                "isNotAvailableForSelling" => false
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

        if (curl_error($ch)) {
            $result = curl_error($ch);
            print_r($result);
            echo "<br/> erreur";
        }

        curl_close($ch);

        echo "<pre>";
        print_r($result);
        echo "</pre>";

        $obj_vehicule = json_decode($result);

        return $obj_vehicule;
    }




    // json to CSV
    function array2csv($data, $delimiter = ';', $enclosure = '"', $escape_char = "\\")
    {
        $f = fopen('test_datas_factures.csv', 'wr+');
        $entete[] = ["Date de la facture", "Immatriculation", "Kilométrage", "N° de la facture", "Prix de vente HT", "Acheteur", "Adresse du client", "Code postal du client", "Ville du client", "Pays du client", "Email du client", "Telephone du client", "Telephone portable du client", "Vendeur", "Parc", "Destination", "Source du client"];

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