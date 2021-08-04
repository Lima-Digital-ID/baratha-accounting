$(document).ready(function() {
    // $(".datatable").DataTable();

    var d = new Date();
    var currDate = d.getDate();
    var currMonth = d.getMonth();
    var currYear = d.getFullYear();
    var startDate = new Date(currYear, currMonth, currDate);

    
    $(function () {
        $('[data-toggle="tooltip"]').tooltip()
    });

    $(".select2").select2();

    $(".datepicker").datepicker({
        format: "yyyy-mm-dd"
    });

    $(".datepicker").datepicker("setDate", startDate);

    $(".datepickerDate").datepicker({
        format: "yyyy-mm-dd",
    });

    $("form").submit(function(event) {
        if( !confirm('Apakah anda yakin?') ) {
            event.preventDefault();
        }
        else{
            $(".loading").addClass("show");
        }
    });

    function indoDate(date){
        var year = date.substr(0,4);
        var month = date.substr(5,2);
        var day = date.substr(8,2);
        var newFormat = day+"-"+month+"-"+year 

        return newFormat;
    }
    
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

    $(".getKodeKas").change(function() {
        var tanggal = $("#tanggal").val();
        var tipe = $("#tipe").val();
        var url = $(this).data("url");
        if (tipe !== "" && tanggal !== "") {
            $.ajax({
                type: "get",
                url: url,
                data: { tanggal: tanggal, tipe: tipe },
                success: function(data) {
                    $("#kode").val(data);
                }
            });
        }
    });

    $('#tipe').change(function () { 
        let tipe = $(this).val();
        $('#kode_supplier').attr('disabled', true);
        $('#kode_customer').attr('disabled', true);
        $(".detail-lawan").show();
        if (tipe == 'Masuk') {
            // $('#kode_supplier').val("");
            $('#kode_supplier').val('');
            $('#kode_supplier').attr('disabled', true);
            $('#kode_customer').attr('disabled', false);
        }
        else if(tipe=='Keluar'){
            $('#kode_customer').attr('disabled', true);
            $('#kode_customer').val('');
            $('#kode_supplier').attr('disabled', false);
        }

        $(".hutang-piutang").hide()
        $(".hutang-piutang tbody tr").remove()
        // if(tipe!=''){
        //     $(".detail-lawan").hide();
        //     $(".detail-lawan input, .detail-lawan select").attr('disabled',true)
        // }
        // else{
        //     $(".detail-lawan input, .detail-lawan select").attr('disabled',false)
        //     $(".detail-lawan").show();
        // }

        $(".select2").select2()
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
                
                $(".getHargaSatuan").keyup(function() {
                    // getSubtotal($(this));
                    getHargaSatuan($(this));
                });

                $('.kode_barang').change(function () { 
                    kodeBarang($(this))
                });
                $(".getTotalKas").keyup(function() {
                    getTotalKas($(this));
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
        
        var subtotal = $(parent + " " + ".getSubtotal").val();
        var qty = $(parent + " " + ".getTotalQty").val();
        
        var harga_satuan = subtotal / qty;
        
        $(parent + " " +".harga_satuan").val(harga_satuan);
        // console.log('parent : ' + parent + " " + ".harga_satuan");
        // console.log('subtotal : ' + subtotal);
        // console.log('qty : ' + qty);
        // console.log('harga satuan : ' + harga_satuan);
        getTotal();
    }

    function getTotal() {
        var total = 0;
        $(".subtotal").each(function() {
            var subtotalVal = parseInt($(this).val());
            subtotalVal = isNaN(subtotalVal) ? 0 : subtotalVal;
            total = total + subtotalVal;
        });
        console.log('getTotal : ' + total);
        $("#total").html(new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(total));
        getTotalPpn(total)
    }
    function getTotalPpn(total) {
        console.log('getTotalPpn : ' + total);
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
            // console.log(getQty);
            getQty = isNaN(getQty) ? 0 : getQty;
            totalQty += getQty;
        });
        $("#totalQty").html(totalQty);
    }

    $(".getSubtotal").keyup(function() {
        getSubtotal($(this));
        console.log('subtotal: ' + $(this).val());     
    });
    
    $(".getHargaSatuan").keyup(function() {
        // getHargaSatuan($(this));        
        console.log('getHargaSatuan');        
    });
    
    $(".getTotalQty").keyup(function() {
        getTotalQty($(this));
        // console.log($(this).val())
        console.log('getTotalQty');        
    });

    function kodeBarang(thisParam){
        let url = thisParam.data('url');
        let kodeBarang = thisParam.val();

        var no = thisParam.closest(".row-detail").data("no");
        var parent = ".row-detail[data-no='" + no + "']";

        $.ajax({
            type: "get",
            url: url,
            data: {kodeBarang : kodeBarang},
            dataType: 'JSON',
            success: function (response) {
                $(parent + " " + ".stock").val(response.stock);
                $(parent + " " + ".saldo").val(response.saldo);
            }
        });

    }
    $('.kode_barang').change(function () { 
        kodeBarang($(this))
    });
	$(".confirm-alert").click(function(e) {
		e.preventDefault();
		var url = $(this).attr("href");
		var text = $(this).data('alert')
		swal(
			{
				title: "Apakah anda yakin?",
				text: text,
				type: "warning",
				showCancelButton: true,
				confirmButtonColor: "#3c4099",
				confirmButtonText: 'Submit',
				closeOnConfirm: false
			},
			function() {
				window.location = url;
			}
		);
	});
	$("#submitConfirm").submit(function(e) {
        e.preventDefault();
		var id = $(this).attr("id");
        $(".loading").removeClass("show");
        var info = $(this).data('info')
		swal(
			{
				title: "Apakah Anda Yakin?",
				text: info,
				type: "warning",
				showCancelButton: true,
				confirmButtonColor: "#DD6B55",
				confirmButtonText: "Yakin",
				closeOnConfirm: false
			},
			function() {
				$("#"+id).unbind("submit").submit();
                $(".loading").addClass("show");
			}
		);
	});


    function getTotalKas() {
        var total = 0;
        $(".getTotalKas").each(function() {
            console.log($(this).val());
            var subtotalVal = parseFloat($(this).val());
            subtotalVal = isNaN(subtotalVal) ? 0 : subtotalVal;
            total = total + subtotalVal;
        });
        $("#total").html(new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(total));
    }

    $(".getTotalKas").keyup(function() {
        getTotalKas();        
    });

    $(".getTotalCatering").keyup(function() {

        var thisval = parseFloat($(this).val());
        var other = parseFloat($($(this).data("other")).val());
        // console.log(other);
        other = isNaN(other) ? 0 : other;
        var total = thisval * other;
        $("#total").html(new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(total));
        $("#totalQty").html(new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format($("#qty").val()))
        getTotalPpn(total);
    });
    $(".status_ppn_catering").change(function(){
        var qty = parseInt($("#qty").val())
        var harga_satuan = parseInt($("#harga_satuan").val())
        var total = qty * harga_satuan
        $("#total").html(new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(total));
        $("#totalQty").html(new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format($("#qty").val()))
        getTotalPpn(total)
    })
    $("#status_ppn").change(function(){
        if(!$(this).hasClass('status_ppn_catering')){
            getTotal()
        }
    })
    $(".btn-pembayaran").click(function(e) {
        e.preventDefault();
        
        var param = $(this).data('param')
        $("#kode-hutangpiutang").val(param[0])
        $("#jml-hutangpiutang").val(param[1])
        $("#sisa").val(param[2])
    })
    $(".sendParamToModal").click(function() {
        var data = $(this).data('param')
        $.each(data, function(i,d) {
            if(i%2==0){
                $(data[i+1]).val(d)
            }
        })
    })
    $(".getHutangPiutang").change(function(){
        var tipe = $(this).data('tipe')
        var thisVal = $(this).val()
        var url
        $(".hutang-piutang tbody tr").remove()
        if(thisVal!=""){
        $(".detail-lawan").hide()
        $(".hutang-piutang").show()
            if(tipe=='supplier'){
                url = location.origin+"/pembelian/supplier/getHutangJson"
                $(".titleHutangPiutang").html('Hutang')
            }
            else{
                url = location.origin+"/penjualan/customer/getPiutangJson"
                $(".titleHutangPiutang").html('Piutang')
            }
            $(".loading").addClass("show");

            $.ajax({
                type : 'get',
                dataType : 'json',
                data : {kode : thisVal},
                url : url,
                beforeSend: function() {
                    $(".loading").addClass("show");
                },
                success : function(data){
                    $(".loading").removeClass("show");
                    $.each(data, function(i,v){
                        i++;
                        var sisa = parseInt(v.grandtotal) - parseInt(v.terbayar)
                        $('.hutang-piutang tbody').append(`<tr><td>${i}</td><td><input type='hidden' name='kode_transaksi[]' value='${v.kode_transaksi}'> ${v.kode_transaksi}</td><td>${v.kode}</td><td>${indoDate(v.tanggal)}</td><td>${indoDate(v.jatuh_tempo)}</td><td>${formatRupiah(v.grandtotal)}</td><td>${formatRupiah(sisa)}</td><td><input type='number' class='form-control' name='bayar[]' value='${sisa}'></td></tr>`)
                    })
                }
            })
        }
        else{
            $(".detail-lawan").show()
            $(".hutang-piutang").hide()
        }
    })

    // tarik rekap resto hotel
    $('.tarikData').click(function () { 
        let jenis_bayar = $(this).data('jenis_bayar');
        let total = $(this).data('total');
        let total_ppn = $(this).data('total_ppn');

        $('#jenis_bayar').val(jenis_bayar);
        $('#total').val(total);
        $('#total_ppn').val(total_ppn);
    });
    
    // $("#form-tambah").submit(function(event) {
        
    // });
});
