$(document).ready(function () {
    $("#chosed-date").hide();
    $("#verify").hide();

});

function Geeks() {

    $("#form").hide();
    var $lundi;
    var $mardi;
    var $mercredi;
    var $jeudi;


    var valuesss = $("#myselect option:selected").val();
    switch (valuesss) {
        case '1':
            $lundi = 2;
            $mercredi = 4;
            break;
        case '3':
            $mardi = 1;
            $jeudi = 3;
            break;
        case '5':
            $lundi = 2;
            $mercredi = 4;
            break;
        case '4':
            $lundi = 2;
            $mercredi = 4;
            break;
        case '2':
            $mardi = 1;
            $jeudi = 3;
            break;

        default:
            $mardi = 1;
            $lundi = 2;
            $mercredi = 4;
            $jeudi = 3;
    }

    $("#getRendez-vous").hide();
    $("#chosed-date").show();
    var disableDates = [];
    var url = $('#urlgetlockeddate').val();
    $.ajax({
        url: url,
        type: 'GET',
        cache: false,
        dataType: "json",
        async: false,
        success: function (data) {
            disableDates = data;
        }
    })
    $('#datepicker').datepicker({
        format: "yyyy-mm-dd",
        language: "fr",
        todayHighlight: true,
        daysOfWeekDisabled: [0, $lundi, $mardi, $mercredi, $jeudi, 5, 6],
        datesDisabled: disableDates,
        startDate: new Date()

    }).on('changeDate', getTodayDate);

    function getTodayDate() {
        var value = $('#datepicker').datepicker('getFormattedDate');

        $("#showDate").text(value);
        $("#getRendez-vous").hide();
        $("#title-date").show();

        var valuesss = $("#myselect option:selected").val();
        switch (valuesss) {
            case '1':
                var service = "Procuration";
                break;
            case '3':
                var service = "Passeport";
                break;
            case '5':
                var service = "Certificat divers";
                break;
            case '4':
                var service = "Heritage";
                break;
            case '2':
                var service = "Visa";
                break;

            default:
                var service = "Inconnu";
        }

        var myDiv = document.getElementById("myDiv");
        $(myDiv).html("");
        $.ajax({
            url: '/heure',
            type: "GET",
            dataType: "json",
            data: 'date=' + encodeURIComponent(value) + '&service=' + encodeURIComponent(service),
            async: false,
            success: function (data) {

                for (let i = 0; i < data.length; i++) {

                    var checkbox = document.createElement('input');

                    checkbox.type = "radio";
                    checkbox.name = "skills";
                    checkbox.className = "heureCheckBox btn-check";
                    checkbox.id = "heure" + i;
                    checkbox.value = data[i]['heure'];

                    var label = document.createElement('label');


                    label.htmlFor = "heure" + i;
                    label.className = " m-2 btn btn-secondary";

                    label.appendChild(document.createTextNode(data[i]['heure']));

                    myDiv.appendChild(checkbox);
                    myDiv.appendChild(label);

                    // $('.check input:checkbox').click(function () {
                    //     $('.check input:checkbox').not(this).prop('checked', false);
                    // });

                    $('input[type=radio][name=skills]').click(function () {
                        $(this).prop('checked', true);
                        send(valuesss);
                    });

                }

            }

        });


    }

}

function send(valuesss) {
    var heure = $('input:radio[name="skills"]:checked').val();
    $("#verify").show();
    switch (valuesss) {
        case '1':
            var service = "Procuration";
            break;
        case '3':
            var service = "Passeport";
            break;
        case '5':
            var service = "Certificat divers";
            break;
        case '4':
            var service = "Heritage";
            break;
        case '2':
            var service = "Visa";
            break;

        default:
            var service = "Inconnu";
    }
    $("#service-rendezvous").text(service);
    $("#date-rendezvous").text($('#datepicker').datepicker('getFormattedDate'));
    $("#heure-rendezvous").text(heure);

}

function rdv() {
    var homepage = $('#homepage').val();
    var url = $('#urlrdv').val();

    var valuesss = $("#myselect option:selected").val();
    switch (valuesss) {
        case '1':
            var service = "Procuration";
            break;
        case '3':
            var service = "Passeport";
            break;
        case '5':
            var service = "Certificat divers";
            break;
        case '4':
            var service = "Heritage";
            break;
        case '2':
            var service = "Visa";
            break;

        default:
            var service = "Inconnu";
    }
    $.ajax({
        url: url,
        type: 'POST',
        cache: false,
        dataType: "json",
        data: {
            service: service,
            date: $('#datepicker').datepicker('getFormattedDate'),
            heure: $('input:radio[name="skills"]:checked').val(),
        },
        async: true,
        success: function (data) {
            console.log(data)
            if (data != null && data != '') {
                console.log(data)
                location.href = homepage;
            }
        }
    });
}

function getuser() {

    var urlrdv = $('#urlrendezvous').val();
    var urlredirect = $('#urlredirect').val();
    var user = $('input:radio[name="userchosed"]:checked').val();
    var date = $('#datepicker').datepicker('getFormattedDate');
    var heure = $('input:radio[name="skills"]:checked').val();

    var valuesss = $("#myselect option:selected").val();
    switch (valuesss) {
        case '1':
            var service = "Procuration";
            break;
        case '3':
            var service = "Passeport";
            break;
        case '5':
            var service = "Certificat divers";
            break;
        case '4':
            var service = "Heritage";
            break;
        case '2':
            var service = "Visa";
            break;

        default:
            var service = "Inconnu";
    }

    $.ajax({
        url: urlrdv,
        type: 'POST',
        cache: false,
        dataType: "json",
        data: {
            getUser: user,
            getService: service,
            getDate: date,
            getHeure: heure,
        },
        async: true,
        success: function (data) {
            if (data != null && data != '') {
                location.href = urlredirect;
            }
        }
    });
}

function modifRdv() {

    var urlrdv = $('#urlrendezvous').val();
    var urlredirect = $('#urlredirect').val();
    var user = $('#iduser').val();
    var idrdv = $('#idrdv').val();
    var service = $("#myselect option:selected").text();
    var date = $('#datepicker').datepicker('getFormattedDate');
    var heure = $('input:radio[name="skills"]:checked').val();

    $.ajax({
        url: urlrdv,
        type: 'POST',
        cache: false,
        dataType: "json",
        data: {
            getUser: user,
            getService: service,
            getDate: date,
            getHeure: heure,
            getRdvId: idrdv,
        },
        async: true,
        success: function (data) {
            if (data != null && data != '') {
                location.href = urlredirect;
            }

        }
    });
}

function myFunction() {
    var input, filter, table, tr, td, i, txtValue;
    input = document.getElementById("myInput");
    filter = input.value.toUpperCase();
    table = document.getElementById("myTable");
    tr = table.getElementsByTagName("tr");
    for (i = 0; i < tr.length; i++) {
        td = tr[i].getElementsByTagName("td")[0];
        if (td) {
            txtValue = td.textContent || td.innerText;
            if (txtValue.toUpperCase().indexOf(filter) > -1) {
                tr[i].style.display = "";
            } else {
                tr[i].style.display = "none";
            }
        }
    }
}
