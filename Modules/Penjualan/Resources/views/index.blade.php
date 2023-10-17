@extends('layouts.app')

@push('vendor-css')
<link rel="stylesheet" href="{{ asset('css/jquery-confirm.min.css') }}">
@endpush
@section('content')
<div class="row g-1">
    <div class="col-lg-8">
        <div class="m-0 mx-2">
            <label for="cariBarang" class="form-label h4"><i class="fa fa-search"></i> CARI BARANG : </label>
            <div class="input-group mb-3">
                <input type="text" id="cariBarang" autocomplete="off" class="form-control form-control-lg" placeholder="Masukan minimal 4 huruf ( CTRL + / )">
                <span class="input-group-text"><i class="align-middle" data-feather="search"></i></span>
            </div>
        </div>
        <div class="row g-3 d-flex justify-content-center mb-2" id="daftarBarang"></div>
    </div>
    <div class="col-lg-4">
        <div class="card border">
            <form action="javascript:void(0)" id="formPenjualan" method="post">
                <div class="card-header">
                    <h3 class="text-center"><i class="fas fa-cart-shopping"></i> KERANJANG</h3>
                    <hr class="m-0">
                </div>
                <div class="m-0 mx-2">
                    <label for="namaPembeli" class="form-label m-0">PEMBELI : </label>
                    <br class="m-0">
                    <label class="form-check form-check-inline">
                        <input class="form-check-input" type="checkbox" id="pertanyaanAnggota">
                        <span class="form-check-label">Apakah pembeli adalah anggota?</span>
                    </label>
                    <div class="input-group mb-2">
                        <button type="button" class="input-group-text" disabled data-bs-toggle="modal" id="btnCariAnggota" data-bs-target="#modalAnggota"><i class="align-middle" data-feather="search"></i></button>
                        <input type="text" class="form-control form-control-lg" autofocus id="namaPembeli" required autocomplete="off" placeholder="Masukan nama pembeli">
                    </div>
                    <table class="table table-bordered table-hover table-sm" id="tabelKeranjang">
                        <thead>
                            <tr class="fw-12">
                                <th class="w-1">AKSI</th>
                                <th>BARANG</th>
                                <th class="w-1">KUANTITAS</th>
                                <th class="w-1">HARGA</th>
                            </tr>
                        </thead>
                    </table>
                    <hr class="my-1 dashed">
                    <table>
                        <tr>
                            <td class="h4 fw-normal">TOTAL</td>
                            <td class="h4 fw-normal w-1" id="textTotal">0</td>
                        </tr>
                    </table>
                    <input type="text" class="form-control form-control-lg my-3" autocomplete="off" required id="inputBayar" placeholder="Bayar ( CTRL + B )" data-inputmask="'alias': 'decimal', 'groupSeparator': ','">
                    <table>
                        <tr>
                            <td class="h4 fw-normal">KEMBALIAN</td>
                            <td class="h4 fw-normal w-1" id="textKembalian">0</td>
                        </tr>
                    </table>
                    <div class="d-grid gap-2">
                        <button type="submit" id="btnSimpan" disabled class="btn btn-lg btn-primary my-2 "><i class="fa fa-save"></i> SIMPAN</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="modal fade" id="modalAnggota" tabindex="-1" role="dialog" data-bs-backdrop="static" data-bs-keyboard="false" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fa fa-search"></i> CARI ANGGOTA</h5>
            </div>
            <div class="modal-body m-0">
                <table class="table table-bordered table-hover table-sm" id="tableAnggota">
                    <thead>
                        <tr>
                            <th class="w-1">AKSI</th>
                            <th>NAMA</th>
                            <th class="w-1">AKSI</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
@push('vendor-js')
<script src="{{ asset('js/datatables.js') }}"></script>
<script src="{{ asset('js/jquery-confirm.min.js') }}"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/jquery.lazy/1.7.9/jquery.lazy.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/recta/dist/recta.js"></script>
@endpush
@push('js')<script>
    var idAnggota = "biasa",
        namaAnggota = null,
        totalPenjualan = 0,
        AppKey = 5036724546,
        printer = new Recta(AppKey, "1811");

    function printThermal(a) {
        var t = new WebSocket("ws://localhost:1811/socket.io/?token=" + AppKey + "&EIO=3&transport=websocket");
        t.addEventListener("open", t => {
            printer.open().then(function() {
                printer.align("center").bold(!0).text('{{config("app.copyright")}}').bold(!1).align("left").text("ID    : " + a.id).text("KASIR : {{auth()->user()->name}}").text("TGL   : " + a.tgl).text("").text(a.barang).bold(!0).text("Total     : " + a.total).text("Bayar     : " + a.bayar).text("Kembalian : " + a.kembalian).align("center").text("\nTERIMA KASIH").cut().print()
            })
        }), t.addEventListener("error", a => {
            alertApp("error", "Printer tidak terhubung")
        })
    }

    function daftarBarang(a) {
        $.ajax({
            url: "{{route('penjualan.dataBarang')}}",
            type: "POST",
            dataType: "JSON",
            data: {
                nama: a
            },
            success: function(a) {
                var t = "";
                $.each(a, function(a, n) {
                    t += '<div class="product"><div class="card m-0"><img class="card-img-top" src="' + n.foto + '" alt="' + n.nama + '"/><div class="m-2"><a class="text-decoration-none text-dark stretched-link fw-10 pilih-barang" data-status="' + n.status + '" data-id="' + n.id + '" title="' + n.nama + '" href="javascript:void(0)"><div class="mb-2">' + n.nama + '</div></a><div class="row fw-10"><span class="col-6 text-primary fw-bold">' + n.harga + '</span><span class="col-6 text-end"><span class="fw-bold">' + n.stok + "</span> " + n.satuan + '</span><span class="col-12"><span class="fw-bold">SIZE</span> : ' + n.ukuran + "</span></div></div></div></div>"
                }), $("#daftarBarang").html(t)
            },
            error: function(a, t, n) {
                $("#daftarBarang").html(null), alertApp("error", n)
            }
        })
    }

    function keranjang(a) {
        $("#btnSimpan").prop("disabled", !0), totalPenjualan = 0, $("#textTotal").html(0), $("table#tabelKeranjang").DataTable({
            bAutoWidth: !1,
            ordering: !1,
            responsive: !0,
            processing: !0,
            bDestroy: !0,
            bInfo: !1,
            searching: !1,
            paging: !1,
            ajax: {
                url: "{{ route('penjualan.keranjang') }}",
                type: "POST",
                dataType: "JSON",
                data: {
                    anggota: idAnggota
                }
            },
            columns: [{
                className: "w-1 text-center"
            }, {
                className: ""
            }, {
                className: "w-1"
            }, {
                className: "w-1 fw-bold text-end"
            }],
            initComplete: function(a) {
                a.json.total > 0 && (totalPenjualan = a.json.total, $("#textTotal").html(currency(a.json.total)), totalPenjualan > 0 && ($("#btnSimpan").prop("disabled", !1), $("#inputBayar").focus(), $("#inputBayar").val(0), $("#textKembalian").html(0)))
            }
        })
    }
    $(document).ready(function() {
        daftarBarang(), setTimeout(function() {
            $("table#tableAnggota").DataTable({
                bAutoWidth: !1,
                ordering: !1,
                responsive: !0,
                processing: !0,
                bDestroy: !0,
                paging: !0,
                language: {
                    url: "{{ asset('js/id.json') }}"
                },
                ajax: {
                    url: "{{ route('penjualan.dataAnggota') }}",
                    type: "POST",
                    dataType: "JSON"
                },
                columns: [{
                    className: "w-1 text-center"
                }, {
                    className: ""
                }, {
                    className: "w-1 text-center"
                }]
            })
        }, 2e3)
    }), $("img").lazy({
        effect: "fadeIn",
        effectTime: 2e3,
        threshold: 0
    }), $(document).keydown(function(a) {
        a.ctrlKey && 191 == a.keyCode ? $("#cariBarang").focus() : a.ctrlKey && 66 == a.keyCode && $("#inputBayar").focus()
    }), $(document).on("keydown", "#cariBarang", function(a) {
        let t = $(this).val().length;
        t > 2 && t % 2 == 1 && daftarBarang($(this).val())
    }), $(document).on("click", ".pilih-barang", function() {
        "" !== namaAnggota && null !== namaAnggota ? $.post("{{route('penjualan.keranjang.tambah')}}", {
            id: $(this).data("id"),
            anggota: idAnggota,
            status: $(this).data("status")
        }, function(a) {
            alertApp("success", a.message), keranjang()
        }).fail(function(a, t, n) {
            alertApp("error", n)
        }) : ($("#namaPembeli").focus(), alertApp("error", "Masukan nama atau pilih pembeli"))
    }), $(document).on("click", ".aksi-anggota", function() {
        idAnggota = $(this).data("id"), namaAnggota = $(this).data("nama"), $("#namaPembeli").val($(this).data("nama")), $("#modalAnggota").modal("hide")
    }), $(document).on("keyup", "#namaPembeli", function() {
        namaAnggota = $(this).val()
    }), $(document).on("change", "#pertanyaanAnggota", function() {
        idAnggota = "biasa", namaAnggota = null, $("#namaPembeli").val(null), $(this).is(":checked") ? ($("#btnCariAnggota").prop("disabled", !1), $("#namaPembeli").prop("readOnly", !0), $("#modalAnggota").modal("show")) : ($("#btnCariAnggota").prop("disabled", !0), $("#namaPembeli").prop("readOnly", !1))
    }), $(document).on("keyup", "#inputBayar", function() {
        var a = $(this).val().replace(/,/g, "");
        a >= 0 && (a > totalPenjualan ? $("#textKembalian").html(currency(a - totalPenjualan)) : $("#textKembalian").html(0))
    }), $(document).on("change", ".kuantitas", function() {
        $(this).val() > 0 ? $.post("{{route('penjualan.keranjang.ubah-kuantitas')}}", {
            id: $(this).data("id"),
            kuantitas: $(this).val()
        }, function(a) {
            a.status ? alertApp("success", a.message) : alertApp("error", a.message), keranjang()
        }).fail(function(a, t, n) {
            alertApp("error", n), keranjang()
        }) : (alertApp("error", "Masukan kuantitas lebih dari 0"), $(this).val(1))
    }), $(document).on("click", ".hapus-keranjang", function() {
        var a = $(this).data("id");
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
                        $.post("{{route('penjualan.keranjang.hapus')}}", {
                            id: a
                        }, function(a) {
                            a.status ? (alertApp("success", a.message), keranjang()) : alertApp("error", a.message)
                        }).fail(function(a, t, n) {
                            alertApp("error", n)
                        })
                    }
                },
                tutup: {
                    text: "Tutup"
                }
            }
        })
    }), $("#formPenjualan").submit(function() {
        // var a = new WebSocket("ws://localhost:1811/socket.io/?token=" + AppKey + "&EIO=3&transport=websocket");
        // a.addEventListener("open", function() {
        $.post("{{route('penjualan.selesai')}}", {
            id: idAnggota,
            nama: namaAnggota,
            bayar: $("#inputBayar").val(),
            total: totalPenjualan
        }, function(a) {
            a.status ? (alertApp("success", a.message), keranjang(), $("#pertanyaanAnggota").prop("checked", !1), $("#namaPembeli").focus(), $("#namaPembeli").val(null), $("#inputBayar").val(null), $("#textKembalian").html(0), $("#btnCariAnggota").prop("disabled", !0), $("#namaPembeli").prop("readOnly", !1), idAnggota = "biasa", namaAnggota = null, totalPenjualan = 0
                // , printThermal(a.data), daftarBarang()
            ) : alertApp("error", a.message)
        }).fail(function(a, t, n) {
            alertApp("error", n)
        })
        // }), a.addEventListener("error", a => {
        //     alertApp("error", "Printer tidak terhubung")
        // })
    });
</script>@endpush