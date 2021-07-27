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

    $j = 1;

    $array_datas = array();

    $nb_BC = 0;
    $nb_lignes = 0;
    $i = 0;
    $nb_total_vehicules_selling = 0;

    $datas_find = true;
    $datas_find_vehicule = true;

    while ($datas_find == true) {

        //recupere la requete !!!!
        $obj = GoCurl($valeur_token, $req_url_BC, $j);
        $obj_final = $obj->datas;

        if (!empty($obj_final)) {
            
            sautdeligne();
            sautdeligne();

            /*****************    BOUCLE du tableau de données récupérés *****************/

            $array_datas[$i] = ["N° Bon Commande", "Immatriculation", "RS/Nom Acheteur", "Prix Vente HT", "Prix de vente TTC", "Vendeur Vente", "date du dernier BC", "Destination de sortie", "VIN"];

            $i++;
            foreach ($obj_final as $keydatas => $keyvalue) {
                //on boucle par rapport au nombre de bon de commande dans le tableau datas[]

                if (isset($keyvalue->state)) {
                    $state = html_entity_decode($keyvalue->state);
                    echo "etaaat ==>" . $state;
                    sautdeligne();

                    if ($state == "Facturé édité") {

                        $j_factures = 1;

                        while ($datas_find_vehicule == true) {

                            //recupere la requete !!!!
                            $obj_factures = GoCurl_Facture($valeur_token, $req_url_factures, $j_factures);
                            $obj_final_factures = $obj_factures->datas;


                            if (!empty($obj_final_factures)) {

                                $array_datas[$i] = ["Date de la facture", "Immatriculation", "Kilométrage", "N° de la facture", "Prix de vente HT", "Acheteur", "Adresse du client", "Code postal du client", "Ville du client", "Pays du client", "Email du client", "Telephone du client", "Telephone portable du client", "Vendeur	Parc", "Destination	Source du client"];

                                $i++;

                                /*****************    BOUCLE du tableau de données récupérés *****************/
                                foreach ($obj_final_factures as $keydatas => $keyvalue) {
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
                                $datas_find_vehicule = false;
                            }
                            $j++;
                        }
                    }
                }




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
    $writer->writeToFile('test_xlsx_BDC_1.xlsx');





    /*************************************  FIN CODE MAIN ************************************************************/


    ?>


</body>

</html>