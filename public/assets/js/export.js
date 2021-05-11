$( document ).ready(function() {
    var today = new Date(new Date().getFullYear(), new Date().getMonth(), new Date().getDate());
    $('#startDate').datepicker({
        format: "yyyy-mm-dd",
        uiLibrary: 'bootstrap4',
        iconsLibrary: 'fontawesome',
        minDate: today,
        maxDate: function () {
            return $('#endDate').val();
        },
    });
    $('#endDate').datepicker({
        format: "yyyy-mm-dd",
        uiLibrary: 'bootstrap4',
        iconsLibrary: 'fontawesome',
        minDate: function () {
            return $('#startDate').val();
        }
    });
});
function getDates()
{
    var exporturl = $("#exporturl").val();
    $.ajax({
        url: exporturl,
        type: 'POST',
        cache: false,
        dataType: 'json',
        data: {
            date_debut: $("#startDate").datepicker("getFormattedDate"),
            date_fin: $("#endDate").datepicker("getFormattedDate"),
        },
        async: true,
        success: function (data) {
        }
    });
}


