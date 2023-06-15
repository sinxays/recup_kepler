<html>

<head>
    <title>Recup API KEPLER BDC FINAL</title>
    <meta charset="utf-8" />
</head>

<?php

//use App\Helpers\Text;
//use App\Model\Post;
//use App\Connection;
//use App\URL;

?>


<body>
    <h2>Récupération du token</h2>


    <?php

    header('Content-type: text/html; charset=UTF-8');

    require('xlsxwriter.class.php');

    include 'fonctions.php';

    /******************************************  CODE MAIN ******************************************/

    ini_set('xdebug.var_display_max_depth', 10);
    ini_set('xdebug.var_display_max_children', 256);
    ini_set('xdebug.var_display_max_data', 1024);

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

    $array_datas[$i] = ["N Bon Commande", "Immatriculation", "RS/Nom Acheteur", "Prix Vente HT", "Prix de vente TTC", "Vendeur Vente", "date du dernier BC", "Destination de sortie", "VIN"];

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


        $num_BDC = '';
         // on reboucle pas si on met une valeur spécifique de bdc afin de récupérer qu'une seule page de l'API
         if ($num_BDC !== '') {
            $datas_find = false;
        }
        //recupere la requete !!!!
        $valeur_token = goCurlToken($url_token);
        $obj = GoCurl_Recup_BDC($valeur_token, $req_url_BC, $j, $num_BDC);
       
        //a ce niveau obj est un object

        //on prends le tableau datas dans obj et ce qui nous fait un array sur obj_final
        $obj_final = $obj->datas;

        var_dump($obj_final);
        // die();

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
                    $state_bdc = html_entity_decode($keyvalue->state);
                    // $state = utf8_decode($keyvalue->state);

                    echo "etaaat ==>" . $state_bdc;
                    sautdeligne();
                }


                //$test = utf8_decode($state);

                //echo $test.'<br/><br/>';

                // si validé , Facturé ou Facturé édité
                if ($state_bdc == "Validé" or $state_bdc == "Facturé" or $state_bdc == "Facturé édité") {

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

                    //déterminer si c'est une vente particvulier ou vente marchand ( particulier ou pro )


                    echo "destination ==> $destination_sortie <br/><br/>";


                    //si c'est particulier 
                    if ($destination_sortie == "VENTE PARTICULIER") {

                        $erreur_vehicule_sorti = false;

                        // echo "<span style='color:red'>" . $erreur_vehicule_sorti . "</span>";

                        //Si il y a des items
                        if (isset($keyvalue->items)) {

                            $presta_Price_HT = array();
                            $presta_price_TTC = array();

                            //on boucle dans les items
                            foreach ($keyvalue->items as $key_item => $value_item) {
                                //si c'est un vehicule_selling

                                echo "_ _ _ _ _ _ _ _ _ _ _ _";
                                sautdeligne();
                                echo "item numéro : $key_item ==> $value_item->type";
                                sautdeligne();


                                if ($value_item->type == 'vehicle_selling') {

                                    $reference_item = $value_item->reference;

                                    $obj_result = '';

                                    //  recup infos du véhicule
                                    $valeur_token = goCurlToken($url_token);
                                    $state_vh = '';
                                    $obj_result = getvehiculeInfo($reference_item, $valeur_token, $req_url_vehicule, $state_vh);
                                    //si on a des resultats c'est que le vh est non dispo à la vente
                                    if (empty($obj_result)) {
                                        $valeur_token = goCurlToken($url_token);
                                        $state_vh = 'arrivage_or_parc';
                                        $obj_result = getvehiculeInfo($reference_item, $valeur_token, $req_url_vehicule, $state_vh);
                                        // echo "dispo à la vente ou pas ????";
                                        sautdeligne();
                                    } else {
                                        echo "véhicule pas disponible à la vente";
                                        sautdeligne();
                                    }

                                    echo "LE TYPE DE OBJ RESULT EST : " . gettype($obj_result) . "<br/><br/>";


                                    $obj_vehicule = array();

                                    $type = gettype($obj_result);

                                    sautdeligne();

                                    // var_dump($type);
                                    // die();

                                    // si c'est bien un objet comme prévu
                                    if ($type == 'object') {

                                        // si le résultat n'est pas vide
                                        if (!empty($obj_result)) {

                                            $obj_vehicule = $obj_result;

                                            if ($state_bdc == "Facturé édité") {
                                                //On ne prend que les véhicules à l'état Vendu ou vendu AR
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

                                                    $vehicule_seul_HT = $value_item->sellPriceWithoutTax;
                                                    $vehicule_seul_TTC =  $value_item->sellPriceWithTax;

                                                    $nb_total_vehicules_selling++;
                                                }
                                                // si facturé édité et que le véhicule n'est ni vendu ni vendu AR
                                                else {
                                                    $vin = "$obj_vehicule->state VIN";
                                                    $immatriculation = "$obj_vehicule->state immatriculation";
                                                    $vehicule_seul_HT = 0;
                                                    $vehicule_seul_TTC = 0;

                                                    $nb_total_vehicules_selling++;
                                                }
                                            }
                                            //si pas facture éditée
                                            else {

                                                // get VIN
                                                $vin = $obj_vehicule->vin;

                                                //get IMMATRICULATION
                                                if (empty($obj_vehicule->licenseNumber)) {
                                                    $immatriculation = 'N/C';
                                                } else {
                                                    $immatriculation = $obj_vehicule->licenseNumber;
                                                }
                                                //immatriculation bon format
                                                $immatriculation = str_replace("-", "", $immatriculation);
                                                //prix du véhicule seul HT
                                                $vehicule_seul_HT = $value_item->sellPriceWithoutTax;
                                                //prix du véhicule seul TTC
                                                $vehicule_seul_TTC = $value_item->sellPriceWithTax;

                                                $nb_total_vehicules_selling++;
                                            }
                                        }
                                        //si le résultat est vide
                                        else {
                                            echo "véhicule sorti";
                                            sautdeligne();
                                            $erreur_vehicule_sorti = true;
                                        }
                                    }
                                }
                                //si c'est pas vehicule selling et donc service selling 
                                else {
                                    echo " - presta service : " . $value_item->name . "<br/>";
                                    $presta_Price_HT[$key_item] = $value_item->sellPriceWithoutTax;
                                    $presta_price_TTC[$key_item] = $value_item->sellPriceWithTax;
                                }
                            }



                            // si il y a ou pas des services selling
                            if (empty($presta_Price_HT)) {
                                $total_presta_HT = 0;
                            } else {
                                $total_presta_HT = array_sum($presta_Price_HT);
                            }
                            if (empty($presta_price_TTC)) {
                                $total_presta_TTC = 0;
                            } else {
                                $total_presta_TTC = array_sum($presta_price_TTC);
                            }

                            // si $erreur_vehicule_sorti == false
                            if (!$erreur_vehicule_sorti) {

                                $i++;

                                echo "+1 ligne </br>";

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

                                // var_dump($array_datas[$i]);

                                $nb_lignes++;
                            }
                        }
                        $nb_BC++;
                    }


                    /****************************************** si c'est une VENTE A MARCHAND ou autre  ***********************************/
                    // else {
                    elseif ($destination_sortie == "VENTE MARCHAND" || $destination_sortie == "BUY BACK") {

                        $erreur_vehicule_sorti = false;

                        //Si il y a des items
                        if (isset($keyvalue->items)) {
                            //on boucle dans les items
                            foreach ($keyvalue->items as $key_item => $value_item) {
                                //on crée une variable qui contiendra le numéro de bdc initial
                                echo "_ _ _ _ _ _ _ _ _ _ _ _ <br/>";

                                $obj_vehicule = array();

                                //si c'est un vehicule_selling
                                if ($value_item->type == 'vehicle_selling') {

                                    $reference_item = $value_item->reference;

                                    //  recup infos du véhicule
                                    $valeur_token = goCurlToken($url_token);
                                    //pas disponible à la vente
                                    $state_vh = '';
                                    $obj_result = getvehiculeInfo($reference_item, $valeur_token, $req_url_vehicule, false, $state_vh);
                                    if (empty($obj_result)) {
                                        $valeur_token = goCurlToken($url_token);
                                        //disponible à la vente
                                        $state_vh = 'arrivage_or_parc';
                                        $obj_result = getvehiculeInfo($reference_item, $valeur_token, $req_url_vehicule, true, $state_vh);
                                        // echo "véhicule diponible à la vente<br/>";
                                    } else {
                                        echo "véhicule pas disponible à la vente<br/>";
                                    }


                                    echo "LE TYPE DE OBJ RESULT EST : " . gettype($obj_result) . "<br/><br/>";

                                    //array = OK ;  si object alors pas OK
                                    $type = gettype($obj_result);
                                    sautdeligne();

                                    //si on obtient un object.
                                    if ($type == 'object') {

                                        // si le résultat n'est pas vide
                                        if (!empty($obj_result)) {

                                            $obj_vehicule = $obj_result;

                                            if ($state_bdc == "Facturé édité") {

                                                //On ne prend que les véhicules à l'état VEndu ou vendu AR
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

                                                    $vehicule_seul_HT = $value_item->sellPriceWithoutTax;
                                                    $vehicule_seul_TTC =  $value_item->sellPriceWithTax;

                                                    $nb_total_vehicules_selling++;
                                                }
                                                // si facturé édité et que le véhicule n'est ni vendu ni vendu AR
                                                else {
                                                    $vin = "$obj_vehicule->state VIN";
                                                    $immatriculation = "$obj_vehicule->state immatriculation";
                                                    $vehicule_seul_HT = "sorti prix HT";
                                                    $vehicule_seul_TTC = "sorti prix TTC";

                                                    $nb_total_vehicules_selling++;
                                                }
                                            }
                                            //si pas facture éditée
                                            else {

                                                // get VIN
                                                $vin = $obj_vehicule->vin;

                                                //get IMMATRICULATION
                                                if (empty($obj_vehicule->licenseNumber)) {
                                                    $immatriculation = 'N/C';
                                                } else {
                                                    $immatriculation = $obj_vehicule->licenseNumber;
                                                }
                                                //immatriculation bon format
                                                $immatriculation = str_replace("-", "", $immatriculation);
                                                //prix du véhicule seul HT
                                                $vehicule_seul_HT = $value_item->sellPriceWithoutTax;
                                                //prix du véhicule seul TTC
                                                $vehicule_seul_TTC = $value_item->sellPriceWithTax;

                                                $nb_total_vehicules_selling++;
                                            }

                                            //total vehicule 
                                            $total_HT = $vehicule_seul_HT;
                                            $total_TTC = $vehicule_seul_TTC;

                                            $i++;

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
                                            echo "+1 ligne </br>";
                                            $nb_lignes++;
                                        } else {
                                            echo "véhicule sorti";
                                            sautdeligne();
                                            $erreur_vehicule_sorti = true;
                                        }
                                    }
                                }
                            }
                        }
                        $nb_BC++;
                    }
                    // si pas vente marchand NI vente PARTICULIER
                    // else {
                    //     echo "erreur ni vente particulier ni vente marchand<br/>";
                    // }

                }
                //si c'est ni validé ni facturé ni facturé édité 
                else {
                    $state_array[] = $state_bdc;
                }
            }
        }
        //si il n'y a pas de données
        else {
            $datas_find = false;
        }
        $j++;
    }


    sautdeligne();

    //on ne compte pas les bdc annulé ou autre 
    $nbre_bdc_nonCompris = count($state_array);
    $nb_BC = $nb_BC - $nbre_bdc_nonCompris;

    echo 'nombre de BC : ' . $nb_BC;

    sautdeligne();

    echo 'nombre de lignes : ' . $nb_lignes;

    sautdeligne();

    echo 'nombre de véhicules vendus : ' . $nb_total_vehicules_selling;

    sautdeligne();

    print_r($state_array);


    $time_post = time();

    sautdeligne();
    sautdeligne();






    // print_r($array_datas);


    // array2csv($array_datas);

    $writer = new XLSXWriter();
    $writer->writeSheet($array_datas);
    $writer->writeToFile('test_xlsx_BDC.xlsx');


    $exec_time = $time_post - $time_pre;
    echo "temps d'exécution ==> " . $exec_time . " secondes";




    /*************************************  FIN CODE MAIN ************************************************************/


    ?>


</body>

</html>