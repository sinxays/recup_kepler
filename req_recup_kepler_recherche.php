<?php

include 'fonctions.php';


if (isset($_POST)) {

    $type_recherche = $_POST['type_recherche'];
    switch ($type_recherche) {
        //RECHERCHE BDC
        case "bdc":
            $page = 1;
            $return_bdc = recup_bdc($_POST['numero_bdc'], $_POST['date_bdc'], $page);
            if (!empty($return_bdc)) {
                // décommenter ci dessous pour voir l'objet final
                // var_dump($return_bdc);
                $result_final = mise_en_forme_result("bdc", $return_bdc);
            } else {
                $result_final = "Pas de résultat";
            }
            // echo $result_final;
            echo $result_final;

            break;


        //RECHERCHE FACTURE
        case "facture":
            $page = 1;
            $result_facture = recup_facture($_POST['num_facture'], $_POST['date_factures'], $page);

            if (!empty($result_facture)) {
                 // décommenter ci dessous pour voir l'objet final
                // var_dump($result_facture);
                $result_final = mise_en_forme_result("facture", $result_facture);
            } else {
                $result_final = "Pas de résultat";
            }
            // echo $result_final;
            echo $result_final;
            break;

        case "vh":
            $page = 1;
            $result_vh = recup_vehicule($_POST['num_dossier_or_reference'], $_POST['vin_chassis'], $_POST['immatriculation'], FALSE, 1);
            //si vide alors sans doute que le véhicule n'ets aps dispo à la vente
            if (empty($result_vh)) {
                $result_vh = recup_vehicule($_POST['num_dossier_or_reference'], $_POST['vin_chassis'], $_POST['immatriculation'], TRUE, 1);
                $result_vh2 = $result_vh;
                $result_final = $result_vh2;
            } else {
                // var_dump($result_vh);
                $result_final = mise_en_forme_result("vh", $result_vh);
            }

            // echo $result_final;
            echo $result_final;


            break;

    }
}

