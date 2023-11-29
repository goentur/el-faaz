@extends('layouts.app')

@push('vendor-css')
<link rel="stylesheet" href="{{ asset('css/jquery-confirm.min.css') }}">
@endpush
@section('content')
<div class="row g-1">
    <div class="col-lg-7">
        <div class="m-0 mx-2">
            <label for="cariBarang" class="form-label h4"><i class="fa fa-search"></i> CARI BARANG : </label>
            <div class="input-group mb-3">
                <input type="text" id="cariBarang" autocomplete="off" class="form-control form-control-lg" placeholder="Masukan minimal 4 huruf ( CTRL + / )">
            </div>
        </div>
        <div class="row g-3 d-flex justify-content-center mb-2" id="daftarBarang"></div>
    </div>
    <div class="col-lg-5">
        <div class="card border">
            <form action="javascript:void(0)" id="FormPembelian" method="post">
                <div class="card-header">
                    <h3 class="text-center"><i class="fas fa-cart-shopping"></i> KERANJANG</h3>
                    <hr class="m-0">
                </div>
                <div class="m-0 mx-2">
                    <table class="table table-bordered table-hover table-sm">
                        <thead>
                            <tr class="fw-12">
                                <th class="w-1">AKSI</th>
                                <th>BARANG</th>
                                <th class="w-7 text-center">KUANTITAS</th>
                                <th class="w-5 text-end">HARGA</th>
                            </tr>
                        </thead>
                        <tbody id="tabelKeranjang"></tbody>
                    </table>
                    <table>
                        <tr>
                            <td class="h4 fw-normal">TOTAL</td>
                            <td class="h4 fw-normal w-1" id="textTotal">0</td>
                        </tr>
                    </table>
                    <input type="text" class="form-control form-control-lg my-3" autocomplete="off" required id="inputBayar" placeholder="Bayar ( CTRL + B )" data-inputmask="'alias': 'decimal', 'groupSeparator': ','">
                    <hr class="dashed">
                    <table>
                        <tr>
                            <td class="h4 fw-normal">KEKURANGAN</td>
                            <td class="h4 fw-normal w-1" id="textKekurangan">0</td>
                        </tr>
                    </table>
                    <textarea required name="keterangan" id="keterangan" class="form-control my-3" rows="3" placeholder="Masukan keterangan pembelian."></textarea>
                    <div class="d-grid gap-2">
                        <button type="submit" id="btnSimpan" disabled class="btn btn-lg btn-primary my-2 "><i class="fa fa-save"></i> SIMPAN</button>
                    </div>
                </div>
            </form>
        </div>
        <div class="alert alert-info alert-dismissible" role="alert">
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            <div class="alert-message">
                <h4 class="alert-heading fw-bold">INFORMASI!</h4>
                <ul>
                    <li>Kolom <b>Harga</b> pada tabel keranjang merupakan harga satuan</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
@push('vendor-js')
<script src="{{ asset('js/datatables.js') }}"></script>
<script src="{{ asset('js/jquery-confirm.min.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.lazy/1.7.9/jquery.lazy.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.inputmask/5.0.6/jquery.inputmask.min.js"></script>
@endpush
@push('js')<script>
    var shoppingCart = [],
        totalPembelian = 0;

    function keranjang() {
        var a = $("#tabelKeranjang");

        function t() {
            $("#textTotal").html(0), totalPembelian = 0, $.each(shoppingCart, function(a, t) {
                totalPembelian += t.hargaBarang.replace(/,/g, "") * t.kuantitas
            }), $("#textTotal").html(currency(totalPembelian))
        }
        a.empty(), $.each(shoppingCart, function(t, e) {
            var i = $("<tr>").append($("<td>").html(`<button type="button" class="btn btn-sm btn-icon waves-effect waves-light btn-danger hapusBarang" data-index="${t}"><i class="fas fa-trash-alt"></i></button>`), $('<td class="fw-12">').text(e.namaBarang), $("<td>").html(`<input type="text" autocomplete="off" class="form-control text-center kuantitasBarang" placeholder="Kuantitas" required value="${e.kuantitas}" data-index="${t}">`), $("<td>").html(`<input type="text" autocomplete="off" class="form-control text-end hargaBarang" placeholder="Harga" required value="${e.hargaBarang}" data-index="${t}">`));
            a.append(i)
        }), $(".kuantitasBarang").on("input", function() {
            var a = $(this).data("index"),
                e = parseInt($(this).val());
            e > 0 ? (shoppingCart[a].kuantitas = e, t()) : ($(this).val(null), alertApp("error", "Masukan angka"))
        }), $(".hargaBarang").on("input", function() {
            (n = parseInt($(this).val())) > 0 ? (shoppingCart[$(this).data("index")].hargaBarang = $(this).val(), t()) : ($(this).val(null), alertApp("error", "Masukan angka"))
        }), $(".hapusBarang").on("click", function() {
            var a = $(this).data("index");
            $.confirm({
                icon: "fa fa-warning",
                title: "PERINGATAN!",
                content: "Apakah anda yakin ingin menghapus data ini?",
                type: "red",
                autoClose: "tutup|5000",
                buttons: {
                    ya: {
                        text: "Ya",
                        btnClass: "btn-red",
                        action: function() {
                            alertApp("success", "Barang berhasil dihapus dari keranjang"), shoppingCart.splice(a, 1), keranjang(), t()
                        }
                    },
                    tutup: {
                        text: "Tutup"
                    }
                }
            })
        }), $(".hargaBarang").inputmask({
            alias: "decimal",
            groupSeparator: ","
        }), $('.kuantitasBarang[data-index="0"]').focus(),t()
    }

    function daftarBarang(a) {
        $.ajax({
            url: "{{route('pembelian.dataBarang')}}",
            type: "POST",
            dataType: "JSON",
            data: {
                nama: a
            },
            success: function(a) {
                var t = "";
                $.each(a, function(a, e) {
                    t += '<div class="product"><div class="card m-0"><img class="card-img-top" src="' + e.foto + '" alt="' + e.nama + '"/><div class="mx-1 my-2"><a class="text-decoration-none text-dark stretched-link fw-11 pilih-barang" data-id="' + e.id + '" title="' + e.pemasok + ' - ' + e.nama + '" href="javascript:void(0)"><div class="mb-2"><span class="fw-bold">' + e.pemasok + '</span> - ' + e.nama + '</div></a><div class="row fw-10"><div class="col-8 text-primary fw-bold">' + e.harga + '</div><div class="col-4 text-end"><span class="fw-bold">' + e.stok + "</span> " + e.satuan + '</div><div><span class="fw-bold">SIZE :</span> ' + e.ukuran + "</div></div></div></div></div>"
                }), $("#daftarBarang").html(t)
            },
            error: function(a, t, e) {
                $("#daftarBarang").html(null), alertApp("error", "Barang tidak bisa dimuat")
            }
        })
    }
    $(document).ready(function() {
        daftarBarang()
    }), $("img").lazy({
        effect: "fadeIn",
        effectTime: 2e3,
        threshold: 0
    }), $(document).keydown(function(a) {
        a.ctrlKey && 191 == a.keyCode ? $("#cariBarang").focus() : a.ctrlKey && 66 == a.keyCode && $("#inputBayar").focus()
    }), $("#cariBarang").on("input", function(a) {
        $(this).val().length >= 4 ? (statuspencarian = !0, daftarBarang($(this).val())) : "" === $(this).val() && daftarBarang()
    }), $(document).on("click", ".pilih-barang", function() {
        var a = $(this).data("id"),
            t = shoppingCart.findIndex(t => t.id === a); - 1 !== t ? shoppingCart[t].kuantitas++ : shoppingCart.push({
            id: a,
            namaBarang: $(this).attr("title"),
            hargaBarang: "",
            kuantitas: ""
        }), keranjang()
    }), $("#inputBayar").on("input", function() {
        var a = $(this).val().replace(/,/g, "");
        a >= 0 && (a < totalPembelian ? $("#textKekurangan").html(currency(totalPembelian - a)) : $("#textKekurangan").html(0))
    }), $("#keterangan").on("input", function() {
        shoppingCart.length > 0 && $("#inputBayar").val().length > 0 && $(this).val().length > 0 ? $("#btnSimpan").prop("disabled", !1) : alertApp("error", "Form tidak memenuhi syarat")
    }), $("#FormPembelian").submit(function() {
        console.log(shoppingCart), $.post("{{route('pembelian.selesai')}}", {
            barang: shoppingCart,
            total: totalPembelian,
            bayar: $("#inputBayar").val(),
            keterangan: $("#keterangan").val()
        }, function(a) {
            a.status ? (alertApp("success", a.message), $("#tabelKeranjang").html(""), shoppingCart.empty(), totalPembelian = 0, $("#textTotal").html(0), $("#inputBayar").val(null), $("#textKekurangan").html(0), $("#keterangan").val(null), $("#btnSimpan").prop("disabled", !0)) : alertApp("error", a.message)
        }).fail(function(a, t, e) {
            alertApp("error", e)
        })
    });
</script>@endpush