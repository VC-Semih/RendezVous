$( document ).ready(function() {
    $("#chosed-date").hide();
    $("#verify").hide();

});

function Geeks() {

    $("#form").hide();
    var $lundi;
    var $mardi;
    var $mercredi;
    var $jeudi;

    var valuesss = $("#myselect option:selected").text();


    if(valuesss === "Procuration")
    {
        $lundi = 2;
        $mercredi = 4;
    }if(valuesss === "Passeport")
    {
       $mardi= 1;
       $jeudi= 3;
    }if (valuesss === "Certificat divers")
    {
        $lundi = 2;
        $mercredi = 4;
    }if(valuesss === "Heritage")
    {
        $lundi = 2;
        $mercredi = 4;
    }if(valuesss === "Visa")
    {
        $mardi= 1;
    }


    $("#getRendez-vous").hide();
    $("#chosed-date").show();
    $('#datepicker').datepicker({
        format: "yyyy-mm-dd",
        language: "fr",
        todayHighlight: true,
        daysOfWeekDisabled: [0,$lundi,$mardi,$mercredi,$jeudi, 5,6],
        startDate: new Date()

    }).on('changeDate', getTodayDate);

    function getTodayDate() {
        var value = $('#datepicker').datepicker('getFormattedDate');

        $("#showDate").text(value);
        $("#getRendez-vous").show();

        $("#title-date").show();

        var myDiv = document.getElementById("myDiv");
        $(myDiv).html("");
        $.ajax({
            url: '/heure',
            type: "GET",
            dataType: "json",
            data: 'date=' + value,
            async: true,
            success: function (data) {

                for (let i = 0; i < data.length; i++) {

                    var checkbox = document.createElement('input');

                    checkbox.type = "radio";
                    checkbox.name = "skills";
                    checkbox.className = "heureCheckBox m-2";
                    checkbox.id = "heure";
                    checkbox.value = data[i]['heure'];

                    var label = document.createElement('label');

                    label.htmlFor = "id";

                    label.appendChild(document.createTextNode(data[i]['heure']));

                    myDiv.appendChild(checkbox);
                    myDiv.appendChild(label);

                    $('.check input:checkbox').click(function() {
                        $('.check input:checkbox').not(this).prop('checked', false);
                    });

                    $('input[type=radio][name=skills]').change(function() {
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
    $("#service-rendezvous").text(valuesss);
    $("#date-rendezvous").text($('#datepicker').datepicker('getFormattedDate'));
    $("#heure-rendezvous").text(heure);

}
function rdv()
{
    var homepage = $('#homepage').val();
    var valuesss = $("#myselect option:selected").text();
    $.ajax({
        url: '/rdv',
        type: "POST",
        cache: false,
        dataType: "json",
        data: {
            service: valuesss,
            date: $('#datepicker').datepicker('getFormattedDate'),
            heure: $('input:radio[name="skills"]:checked').val(),
        },
        async: true,
        success: function (data) {
          console.log(data);
          console.log(homepage);
        }
    });
}

function getuser(){

    var urlrdv = $('#urlrendezvous').val();
    var urlredirect = $('#urlredirect').val();

    console.log(urlredirect);
    var user = $('input:radio[name="userchosed"]:checked').val();
    var service = $("#myselect option:selected").text();
    var date = $('#datepicker').datepicker('getFormattedDate');
    var heure =$('input:radio[name="skills"]:checked').val();

    $.ajax({
        url: urlrdv,
        type: 'POST',
        cache: false,
        dataType: 'json',
        data: {
            getUser: user,
            getService: service,
            getDate: date,
            getHeure:heure,
        },
        async: true,
        success: function (data) {
            if(data != null && data != '') {
                location.href = urlredirect;
            }
        }
    });
}

function modifRdv(){

    var urlrdv = $('#urlrendezvous').val();
    var urlredirect = $('#urlredirect').val();
    var user = $('#iduser').val();
    var idrdv = $('#idrdv').val();
    var service = $("#myselect option:selected").text();
    var date = $('#datepicker').datepicker('getFormattedDate');
    var heure =$('input:radio[name="skills"]:checked').val();

    $.ajax({
        url: urlrdv,
        type: 'POST',
        cache: false,
        dataType: 'json',
        data: {
            getUser: user,
            getService: service,
            getDate: date,
            getHeure:heure,
            getRdvId: idrdv,
        },
        async: true,
        success: function (data) {
            if(data != null && data != '') {
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
