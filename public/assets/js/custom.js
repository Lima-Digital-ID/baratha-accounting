$(document).ready(function() {
    // $(".datatable").DataTable();
    $(".select2").select2();
    $(".datepicker").datepicker({
        format: "yyyy-mm-dd"
    });
    
    function formatRupiah(angka) {
        var number_string = angka.toString(),
            sisa = number_string.length % 3,
            rupiah = number_string.substr(0, sisa),
            ribuan = number_string.substr(sisa).match(/\d{3}/g);

        if (ribuan) {
            separator = sisa ? "." : "";
            rupiah += separator + ribuan.join(".");
        }
        return rupiah;
    }

    // get kode induk di tambah kode rekening
    $('#kode_induk').change(function (e) {
        $('#kode_rekening').val(`${$(this).val()}.`);
    });

    $(".getKode").change(function() {
        var tanggal = $(this).val();
        var url = $(this).data("url");

        $.ajax({
            type: "get",
            url: url,
            data: { tanggal: tanggal },
            success: function(data) {
                $("#kode").val(data);
            }
        });
    });

    function addDetail(thisParam) {
        var biggestNo = 0; //setting awal No/Id terbesar
        $(".row-detail").each(function() {
            var currentNo = parseInt($(this).attr("data-no"));
            if (currentNo > biggestNo) {
                biggestNo = currentNo;
            }
        }); //mencari No teresar

        var next = parseInt(biggestNo) + 1; // No selanjutnya ketika ditambah field baru
        var thisNo = thisParam.data("no"); // No pada a href
        var url = $("#urlAddDetail").data("url");
        $.ajax({
            type: "get",
            url: url,
            data: { biggestNo: biggestNo },
            beforeSend: function() {
                $(".loading").addClass("show");
            },
            success: function(response) {
                $(".loading").removeClass("show");
                $(".row-detail[data-no='" + thisNo + "']").after(response);
                $(".select2").select2();

                $(".addDetail[data-no='" + next + "']").click(function(e) {
                    e.preventDefault();
                    addDetail($(this));
                });

                $(".deleteDetail").click(function(e) {
                    e.preventDefault();
                    deleteDetail($(this));
                });
                $(".getSubtotal").keyup(function() {
                    getSubtotal($(this));
                });

                // $(".barang").change(function() {
                //     barang($(this));
                // });

                $(".getTotalQty").keyup(function() {
                    getTotalQty($(this));
                });
                getTotalQty();

                // $(".menu").change(function() {
                //     getDetailMenu($(this));
                // });

                // $(".menu2").change(function() {
                //     pjGetDetailMenu($(this));
                //     pjGetDiskon($(this));
                // });

                // $(".qtyPj").change(function() {
                //     getSubtotalPj($(this));
                // });
            }
        });
    }
    $(".addDetail").click(function(e) {
        e.preventDefault();
        addDetail($(this));
    });
    function deleteDetail(thisParam) {
        var delNo = thisParam.data("no");
        var parent = ".row-detail[data-no='" + delNo + "']";
        var idDetail = $(parent + " .idDetail").val();
        if (thisParam.hasClass("addDeleteId") && idDetail != 0) {
            $(".idDelete").append(
                "<input type='hidden' name='id_delete[]' value='" +
                    idDetail +
                    "'>"
            );
        }
        $(parent).remove();
        getTotal();
        getTotalQty();
    }
    $(".deleteDetail").click(function(e) {
        e.preventDefault();
        deleteDetail($(this));
    });

    function getSubtotal(thisParam) {
        var no = thisParam.closest(".row-detail").data("no");
        var parent = ".row-detail[data-no='" + no + "']";

        var thisval = parseFloat(thisParam.val());
        var other = parseFloat($(parent + " " + thisParam.data("other")).val());
        // console.log(other);
        other = isNaN(other) ? 0 : other;
        var subtotal = thisval * other;
        $(parent + " " + ".subtotal").val(subtotal);
        getTotal();
    }

    function getTotal() {
        var total = 0;
        $(".subtotal").each(function() {
            var subtotalVal = parseInt($(this).val());
            subtotalVal = isNaN(subtotalVal) ? 0 : subtotalVal;
            total = total + subtotalVal;
        });
        $("#total").html(new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(total));
        getTotalPpn(total)
    }

    function getTotalPpn(total) {
        let statusPpn = $('#status_ppn').val();
        let totalPpn = 0;
        if (statusPpn == 'Tanpa') {
            totalPpn = 0;
        }
        else if(statusPpn == 'Belum'){
            totalPpn = 10 / 100 * total;
        }
        else{
            total = (100 / 110) * total;
            totalPpn = 10 / 100 * total;
            $("#total").html(new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(total));
        }
        $("#totalPpn").html(new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(totalPpn));
        $("#grandtotal").html(new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(total + totalPpn));
    }
    
    function getTotalQty() {
        var totalQty = 0;
        $(".getTotalQty").each(function() {
            var getQty = parseFloat($(this).val());
            console.log(getQty);
            getQty = isNaN(getQty) ? 0 : getQty;
            totalQty += getQty;
        });
        $("#totalQty").html(totalQty);
    }

    $(".getSubtotal").keyup(function() {
        getSubtotal($(this));        
    });
    
    $(".getTotalQty").keyup(function() {
        getTotalQty($(this));
        console.log($(this).val())
    });

    $('#kode_barang').change(function () { 
        let url = $(this).data('url');
        let kodeBarang = $(this).val();

        var no = $(this).closest(".row-detail").data("no");
        var parent = ".row-detail[data-no='" + no + "']";

        $.ajax({
            type: "get",
            url: url,
            data: {kodeBarang : kodeBarang},
            dataType: 'JSON',
            success: function (response) {
                $(parent + " " + "#stock").val(response.stock);
                $(parent + " " + "#saldo").val(response.saldo);
            }
        });
    });

});
