$(document).ready(function() {
    // $(".datatable").DataTable();
    $(".select2").select2();
    $(".datepicker").datepicker({
        format: "yyyy-mm-dd"
    });
    CKEDITOR.replace( 'editor');

    $('#has_submenu').change(function () { 
        if ($(this).val() == '0') {
            $('#halaman').removeAttr('disabled');
            $('#section_id').removeAttr('disabled');
        }
        else{
            $('#halaman').val('');
            $('#no_page').attr('selected', true);
            $('#halaman').attr('disabled', true);
            $('#section_id').attr('disabled', true);
        }
    });

});
