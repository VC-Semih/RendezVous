$(document).ready(function () {

    $('#btnpdf').attr('disabled', true);
    $('#btnexcel').attr('disabled', true);
    $('#startDate').change(function () {
        if ($('#startDate').val().length != 0) {
            $('#btnpdf,#btnexcel').attr('disabled', false);
        } else {
            $('#btnpdf,#btnexcel').attr('disabled', true);
        }

    })


    var today = new Date(new Date().getFullYear(), new Date().getMonth(), new Date().getDate());
    $('#startDate').datepicker({
        format: "yyyy-mm-dd",
        uiLibrary: 'bootstrap4',
        iconsLibrary: 'fontawesome',
        regional:'fr',
        minDate: today,
        maxDate: function () {
            return $('#endDate').val();
        },
    });
    $('#endDate').datepicker({
        format: "yyyy-mm-dd",
        uiLibrary: 'bootstrap4',
        iconsLibrary: 'fontawesome',
        regional:'fr',
        minDate: function () {
            return $('#startDate').val();
        }
    });


});


