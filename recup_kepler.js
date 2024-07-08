$(document).ready(function () {


    let loader = $("#loader");

    // Initialisez la modal
    var modal_bdc_init = new bootstrap.Modal($("#modal_bdc"));
    var modal_facture_init = new bootstrap.Modal($("#modal_facture"));
    var modal_vh_init = new bootstrap.Modal($("#modal_vehicule"));


    function remove_modal(instance) {
        var modal_facture = bootstrap.Modal.getInstance(instance);
        modal_facture.hide();
        $('.modal').modal('hide');
        $('body').removeClass('modal-open');
        $('.modal-backdrop').remove();

        // Sélectionnez le corps
        var body = $("body");
        // Enlevez l'attribut style
        body.removeAttr("style");
    }

    // BDC choix entre num ou date, effacer l'un ou l'autre selon le click
    $("#DateBdcInput").click(function (e) {
        $("#NumBdcInput").val("");
    });
    $("#NumBdcInput").click(function (e) {
        $("#DateBdcInput").val("");
    });


    // FACTURES choix entre num ou date, effacer l'un ou l'autre selon le click
    $("#FactureInput").click(function (e) {
        $("#DateDactureInput").val("");
    });
    $("#DateDactureInput").click(function (e) {
        $("#FactureInput").val("");
    });

    // VEHICULE choix entre num dossier,chassis ou immat, effacer l'un ou l'autre selon le click
    $("#VhReferenceInput").click(function (e) {
        $("#VhChassisInput").val("");
        $("#VhImmatInput").val("");
    });
    $("#VhChassisInput").click(function (e) {
        $("#VhReferenceInput").val("");
        $("#VhImmatInput").val("");
    });
    $("#VhImmatInput").click(function (e) {
        $("#VhReferenceInput").val("");
        $("#VhChassisInput").val("");
    });

    // Recherche BDC 
    $("#button_rechercher_bdc").click(function (e) {
        loader.show();
        let form_bdc = $("#form_bdc").serialize();
        let num_bdc = $("#NumBdcInput").val();
        let date_bdc = $("#DateBdcInput").val();
        let type_recherche = $("#type_recherche_bdc").val();
        console.log(date_bdc);
        remove_modal($("#modal_bdc"))
        $.ajax({
            url: "req_recup_kepler_recherche.php",
            type: "POST",
            data: { numero_bdc: num_bdc, date_bdc: date_bdc, type_recherche: type_recherche },
            success: function (data) {
                loader.hide();
                $("#area_result").html(data);
            },
            error: function () {
                window.location.replace('index.php');
            }
        });

    });

    // Recherche Facture 
    $("#button_rechercher_facture").click(function (e) {
        loader.show();
        let num_facture = $("#FactureInput").val();
        let date_factures = $("#DateDactureInput").val();
        let type_recherche = $("#type_recherche_facture").val();

        remove_modal($("#modal_facture"))

        $.ajax({
            url: "req_recup_kepler_recherche.php",
            type: "POST",
            data: { num_facture: num_facture, date_factures: date_factures, type_recherche: type_recherche },
            success: function (data) {
                loader.hide();
                $("#area_result").html(data);
               
            },
            error: function () {
                window.location.replace('index.php');
            }
        });
    });

    // Recherche Vehicule 
    $("#button_rechercher_vehicule").click(function (e) {
        loader.show();
        let num_dossier_or_reference = $("#VhReferenceInput").val();
        let vin_chassis = $("#VhChassisInput").val();
        let immatriculation = $("#VhImmatInput").val();
        let type_recherche = $("#type_recherche_vh").val();

        console.log(type_recherche);

        remove_modal($("#modal_vehicule"))

        $.ajax({
            url: "req_recup_kepler_recherche.php",
            type: "POST",
            data: {
                num_dossier_or_reference: num_dossier_or_reference,
                vin_chassis: vin_chassis,
                immatriculation: immatriculation,
                type_recherche: type_recherche
            },
            success: function (data) {
                loader.hide();
                $("#area_result").html(data);
            },
            error: function () {
                window.location.replace('index.php');
            }
        });

    });


        $("#button_fade_sql").click(function (e) {
            e.preventDefault();
            $("#area_result_brut").fadeToggle(500);
        })








});



