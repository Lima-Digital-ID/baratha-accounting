$(document).ready(function() {
    // $(".datatable").DataTable();
    $(".select2").select2();
    $(".datepicker").datepicker({
        format: "yyyy-mm-dd"
    });
    CKEDITOR.replace( 'editor');

    // get kode induk di tambah kode rekening
    $('#kode_induk').change(function (e) {
        $('#kode_rekening').val(`${$(this).val()}.`);
    });

});
