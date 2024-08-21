<!DOCTYPE html>
<html>

<head>
    <meta charset='utf-8'>
    <meta http-equiv='X-UA-Compatible' content='IE=edge'>
    <meta name='viewport' content='width=device-width, initial-scale=1'>
    <title>Recup Kepler</title>

    <!-- Bootstrap -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <!-- perso css -->
    <link rel='stylesheet' type='text/css' media='screen' href='style_recup_kepler.css'>



</head>

<body>
    <div class="header">
        <h1>RECUP KEPLER</h1>
    </div>
    <div class="body">
        <div class="elements_row">
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modal_bdc"
                id="btn_bdc" style="max-width:200px;min-width: 200px;">BDC</button>

            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modal_facture"
                id="btn_factures" style="max-width:200px;min-width: 200px;">Factures</button>

            <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#modal_vehicule"
                id="btn_vehicule" style="max-width:200px;min-width: 200px;">Vehicule</button>
        </div>

        <div class="elements_row">
            <div class="lds-ellipsis" id="loader" style="display:none;">
                <div></div>
                <div></div>
                <div></div>
                <div></div>
            </div>
        </div>

        <div class="result_body">
            <!-- <button id="button_fade_sql" style="display: none;">Résultat Brut</button>
            <span id="area_result_brut" style="display: none;"></span> -->
            <span id="area_result"></span>
        </div>
    </div>




    <!---------------------------------- Modal BDC ---------------------------------->




    <div class="modal" id="modal_bdc" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header" id="my_modal_header_bdc">
                    <h5 class="modal-title" id="exampleModalLongTitle">Rechercher BDC</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal_body">
                    <form id="form_bdc">
                        <span class="info_modal_body">Soit par un numéro, ou tous les BDC d'une date spécifique</span>
                        <br />
                        <br />
                        <div class="form-group">
                            <label for="NumBdcInput">Numero BDC</label>
                            <input type="text" class="form-control" id="NumBdcInput" name="num_bdc_input"
                                style="width: 200px;">
                        </div>

                        <div class="form-group">
                            <label for="DateBdcInput">Date</label>
                            <input type="date" class="form-control" id="DateBdcInput" name="date_bdc_input"
                                style="width: 200px;">
                        </div>
                        <input type="text" name="type_recherche" id="type_recherche_bdc" value="bdc" hidden>
                    </form>
                </div>


                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                    <button type="button" class="btn btn-primary" id="button_rechercher_bdc">Rechercher</button>
                </div>


            </div>
        </div>
    </div>




    <!---------------------------------- Modal Facture ---------------------------------->




    <div class="modal" id="modal_facture" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header" id="my_modal_header_factures">
                    <h5 class="modal-title" id="exampleModalLongTitle">Rechercher Facture(s)</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal_body">
                    <form id="form_factures">
                        <span class="info_modal_body">Soit par un numéro, ou toutes les factures d'une date
                            spécifique</span>
                        <br />
                        <br />
                        <div class="form-group">
                            <label for="FactureInput">Numero Facture</label>
                            <input type="text" class="form-control" id="FactureInput" name="num_facture_input"
                                style="width: 200px;">
                        </div>

                        <div class="form-group">
                            <label for="DateDactureInput">Date</label>
                            <input type="date" class="form-control" id="DateDactureInput" name="date_facture_input"
                                style="width: 200px;">
                        </div>

                        <input type="text" name="type_recherche_facture" id="type_recherche_facture" value="facture"
                            hidden>
                    </form>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                    <button type="button" class="btn btn-primary" id="button_rechercher_facture">Rechercher</button>
                </div>


            </div>
        </div>
    </div>


    <!---------------------------------- Modal Vehicule ---------------------------------->

    <div class="modal" id="modal_vehicule" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header" id="my_modal_header_vehicule">
                    <h5 class="modal-title" id="exampleModalLongTitle">Info Véhicule</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal_body">
                    <form id="form_vehicule">
                        <div class="form-group">
                            <label for="VhReferenceInput">Numéro dossier ou référence </label>
                            <input type="text" class="form-control" id="VhReferenceInput" name="vh_reference_input"
                                style="width: 200px;">
                        </div>
                        <div class="form-group">
                            <label for="VhChassisInput">VIN / Chassis</label>
                            <input type="text" class="form-control" id="VhChassisInput" name="vh_chassis_input"
                                style="width: 200px;">
                        </div>
                        <div class="form-group">
                            <label for="VhImmatInput">Immatriculation</label>
                            <input type="text" class="form-control" id="VhImmatInput" name="vh_immat_input"
                                style="width: 200px;">
                        </div>
                        <input type="text" name="type_recherche" id="type_recherche_vh" value="vh" hidden>
                    </form>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                    <button type="button" class="btn btn-primary" id="button_rechercher_vehicule">Rechercher</button>
                </div>


            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
    <script src='recup_kepler.js'></script>





</body>

</html>