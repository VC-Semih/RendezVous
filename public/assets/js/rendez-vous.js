function Geeks() {



    $("#form").hide();
    var $lundi;
    var $mardi;
    var $mercredi;
    var $jeudi;

    var valuesss = $("#myselect option:selected").text();

    console.log(valuesss);

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
    $('#datepicker').datepicker({
        format: "yyyy-mm-dd",
        language: "fr",
        todayHighlight: true,
        daysOfWeekDisabled: [0,$lundi,$mardi,$mercredi,$jeudi, 5,6],
        startDate: new Date()

    }).on('changeDate', getTodayDate);
    console.log([0,$lundi,$mardi,$mercredi,$jeudi, 5,6]);
    function getTodayDate() {
        var value = $('#datepicker').datepicker('getFormattedDate');

        $("#showDate").text(value);
        $("#getRendez-vous").show();

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


function send(valuesss){
    var heure = $('input:radio[name="skills"]:checked').val();
    console.log(heure);

    $.ajax({
        url: '/rdv',
        type: "POST",
        cache: false,
        dataType: "json",
        data: {
            service: valuesss,
            date:  $('#datepicker').datepicker('getFormattedDate'),
            heure: $('input:radio[name="skills"]:checked').val(),

        },
        async: true,
        success: function (data) {

            console.log($('input:radio[name="skills"]:checked').val())

        }
    });
}
