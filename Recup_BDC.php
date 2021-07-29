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

    $time_pre = time();
    $time_token = time();

    set_time_limit(0);

    // recup valeur token seulement
    $url_token = "https://www.kepler-soft.net/api/v3.0/auth-token/";
    $valeur_token = goCurlToken($url_token);
    //$valeur_token_first = $valeur_token;

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

    $state_array = array();

    $array_datas[$i] = ["N° Bon Commande", "Immatriculation", "RS/Nom Acheteur", "Prix Vente HT", "Prix de vente TTC", "Vendeur Vente", "date du dernier BC", "Destination de sortie", "VIN"];

    while ($datas_find == true) {

        /* $time_now = $time();
        $time_now = $time_now - $time_token;

        if($time_now > 1700){
            $valeur_token = goCurlToken($url);
            if($valeur_token !==  $valeur_token_first){
                $time_token = time();
            }
        }
        */

        //recupere la requete !!!!
        $valeur_token = goCurlToken($url_token);
        $obj = GoCurl($valeur_token, $req_url_BC, $j);


        $obj_final = $obj->datas;


        if (!empty($obj_final)) {

            sautdeligne();
            sautdeligne();

            /*****************    BOUCLE du tableau de données récupérés *****************/


            $i++;
            //on boucle par rapport au nombre de bon de commande dans le tableau datas[]
            foreach ($obj_final as $keydatas => $keyvalue) {

                echo ("____________________________________________________________________________________________________________________");
                sautdeligne();

                // on récupere le state du bon de commande 
                if (isset($keyvalue->state)) {
                    $state = html_entity_decode($keyvalue->state);
                    echo "etaaat ==>" . $state;
                    sautdeligne();
                }


                if ($state == "Validé" || $state == "Facturé" || $state == "Facturé édité") {

                    //get ID
                    $bdc = $keyvalue->uniqueId;

                    echo "bon de commande numéro :" . $bdc;
                    sautdeligne();

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


                    // si ya des items
                    if (isset($keyvalue->items)) {

                        $vehicule_seul_HT_test = array();
                        $vehicule_seul_TTC_test = array();

                        //on boucle dans les items
                        foreach ($keyvalue->items as $key_item => $value_item) {

                            // si c'est une voiture et non un service ou prestation alors va chercher les infos du véhicule
                            if ($value_item->type == 'vehicle_selling') {

                                $reference_item = $value_item->reference;

                                //  recup infos du véhicule
                                $valeur_token = goCurlToken($url_token);
                                $obj_result = getvehiculeInfo($reference_item, $valeur_token, $req_url_vehicule, false);
                                if (empty($obj_result)) {
                                    $obj_result = getvehiculeInfo($reference_item, $valeur_token, $req_url_vehicule, true);
                                }

                                //array = OK ;  si object alors pas OK
                                echo "le type est " . gettype($obj_result);
                                sautdeligne();

                                //si on obtient un object alors on remplit la ligne de erreur.
                                if (gettype($obj_result) == 'object') {

                                    $rien = 'erreur';

                                    $array_datas[$i]['uniqueId'] = $bdc;
                                    $array_datas[$i]['immatriculation'] = $rien;
                                    $array_datas[$i]['name'] = $rien;
                                    $array_datas[$i]['prixTotalHT'] = $rien;
                                    $array_datas[$i]['prixTTC'] = $rien;
                                    $array_datas[$i]['nomVendeur'] = $rien;
                                    $array_datas[$i]['dateBC'] = $rien;
                                    $array_datas[$i]['Destination_sortie'] = $rien;
                                    $array_datas[$i]['VIN'] = $rien;
                                } else {

                                    if (!empty($obj_result)) {
                                        $obj_vehicule = $obj_result[0];

                                        if ($state == "Facturé édité") {

                                            if ($obj_vehicule->state == "vehicle.state.sold_ar" or $obj_vehicule->state == "vehicle.state.sold") {

                                                // get VIN
                                                if (isset($obj_vehicule->vin) || !empty($obj_vehicule->vin)) {
                                                    $vin = $obj_vehicule->vin;
                                                } else {
                                                    $vin = "";
                                                }

                                                //get IMMATRICULATION
                                                if (empty($obj_vehicule->licenseNumber)) {
                                                    $immatriculation = 'N/C';
                                                } else {
                                                    $immatriculation = $obj_vehicule->licenseNumber;
                                                }

                                                $immatriculation = str_replace("-", "", $immatriculation);

                                                // On place les valeurs dans les cellules
                                                $array_datas[$i]['uniqueId'] = $bdc;
                                                $array_datas[$i]['immatriculation'] = $immatriculation;
                                                $array_datas[$i]['name'] = $nom_acheteur;
                                                $array_datas[$i]['prixTotalHT'] = $value_item->sellPriceWithoutTax;
                                                $array_datas[$i]['prixTTC'] = $value_item->sellPriceWithTax;
                                                $array_datas[$i]['nomVendeur'] = $nom_vendeur;
                                                $array_datas[$i]['dateBC'] = $date_BC;
                                                $array_datas[$i]['Destination_sortie'] = $destination_sortie;
                                                $array_datas[$i]['VIN'] = $vin;


                                                $vehicule_seul_HT_test[] = $value_item->sellPriceWithoutTax;
                                                $vehicule_seul_TTC_test[] = $value_item->sellPriceWithTax;


                                                $nb_total_vehicules_selling++;
                                                $nb_lignes++;
                                            }
                                        } else {

                                            // get VIN
                                            $vin = $obj_vehicule->vin;

                                            //get IMMATRICULATION
                                            if (empty($obj_vehicule->licenseNumber)) {
                                                $immatriculation = 'N/C';
                                            } else {
                                                $immatriculation = $obj_vehicule->licenseNumber;
                                            }

                                            $immatriculation = str_replace("-", "", $immatriculation);

                                            $vehicule_seul_HT = $value_item->sellPriceWithoutTax;
                                            $vehicule_seul_TTC = $value_item->sellPriceWithTax;

                                            $vehicule_seul_HT_test[] = $value_item->sellPriceWithoutTax;
                                            $vehicule_seul_TTC_test[] = $value_item->sellPriceWithTax;

                                            $nb_total_vehicules_selling++;
                                            $nb_lignes++;
                                        }
                                    }
                                }
                                $i++;
                            } else {
                                //si c'est pas vehicule selling
                                $presta_Price_HT[$key_item] = $value_item->sellPriceWithoutTax;
                                $presta_price_TTC[$key_item] = $value_item->sellPriceWithTax;
                            }

                            // fin foreach //
                        }

                        var_dump($vehicule_seul_TTC_test);
                        var_dump($vehicule_seul_HT_test);

                        $total_presta_HT = array_sum($presta_Price_HT);
                        $total_presta_TTC = array_sum($presta_price_TTC);

                        //total vehicule + prestations 
                        $total_HT = $vehicule_seul_HT + $total_presta_HT;
                        $total_TTC = $vehicule_seul_TTC + $total_presta_TTC;

                        // On place les valeurs dans les cellules
                        $array_datas[$i]['uniqueId'] = $bdc;
                        $array_datas[$i]['immatriculation'] = $immatriculation;
                        $array_datas[$i]['name'] = $nom_acheteur;
                        $array_datas[$i]['prixTotalHT'] = $total_HT;
                        $array_datas[$i]['prixTTC'] = $total_TTC;
                        $array_datas[$i]['nomVendeur'] = $nom_vendeur;
                        $array_datas[$i]['dateBC'] = $date_BC;
                        $array_datas[$i]['Destination_sortie'] = $destination_sortie;
                        $array_datas[$i]['VIN'] = $vin;
                    }

                    $nb_BC++;
                } else {
                    $state_array[] = $state;
                }
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

    print_r($state_array);


    $time_post = time();

    sautdeligne();
    sautdeligne();


    // print_r($array_datas);


    // array2csv($array_datas);

    $writer = new XLSXWriter();
    $writer->writeSheet($array_datas);
    $writer->writeToFile('test_xlsx_BDC_1.xlsx');


    $exec_time = $time_post - $time_pre;
    echo "temps d'excécution ==> " . $exec_time . " secondes";




    /*************************************  FIN CODE MAIN ************************************************************/


    ?>


</body>

</html>