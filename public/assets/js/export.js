$( document ).ready(function() {
    $('#btnpdf').hide();
    $('#btnexcel').hide();

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

    $("#startDate").click(function() {
        $("#btnpdf").show();
        $("#btnexcel").show();
    });





});


