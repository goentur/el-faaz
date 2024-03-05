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
                    <table class="table table-bordered table-hover table-sm">
                        <thead>
                            <tr class="fw-12">
                                <th class="w-1">AKSI</th>
                                <th>BARANG</th>
                                <th class="w-1 text-center">KUANTITAS</th>
                                <th class="w-5 text-end">HARGA</th>
                            </tr>
                        </thead>
                        <tbody id="tabelKeranjang"></tbody>
                    </table>
                    <hr class="my-1 dashed">
                    <table>
                        <tr>
                            <td class="h4 fw-normal">TOTAL</td>
                            <td class="h4 fw-normal w-1" id="textTotal">0</td>
                        </tr>
                    </table>
                    <input type="text" disabled class="form-control form-control-lg my-3" autocomplete="off" required id="inputBayar" placeholder="Bayar ( CTRL + B )" data-inputmask="'alias': 'decimal', 'groupSeparator': ','">
                    <table>
                        <tr>
                            <td class="h4 fw-normal">KEMBALIAN</td>
                            <td class="h4 fw-normal w-1" id="textKembalian">0</td>
                        </tr>
                    </table>
                    <input type="text" disabled class="form-control form-control-lg my-3" autocomplete="off" required id="inputOngkir" placeholder="Ongkos kirim" data-inputmask="'alias': 'decimal', 'groupSeparator': ','">
                    <label for="metodePembayaran" class="form-label mt-3 h4"><i class="fa fa-credit-card"></i> METODE PEMBAYARAN :</label>
                    <select required name="metodePembayaran" id="metodePembayaran" class="form-control">
                        @foreach ($metodePembayarans as $metodePembayaran)
                        <option value="{{ enkrip($metodePembayaran->id) }}">{{ $metodePembayaran->nama }}</option>
                        @endforeach
                    </select>
                    <div class="d-grid gap-2">
                        <button type="submit" id="btnSimpan" class="btn btn-lg btn-primary my-2 "><i class="fa fa-save"></i> SIMPAN</button>
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.lazy/1.7.9/jquery.lazy.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.inputmask/5.0.6/jquery.inputmask.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/recta/dist/recta.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.blockUI/2.70/jquery.blockUI.min.js"></script>
@endpush
@push('js')<script>var keranjangBelanja=[],idAnggota="biasa",namaAnggota=null,totalPenjualan=0,PrinterKey="{{ auth()->user()->kode_printer }}",printer=new Recta(PrinterKey,"1811");function printThermal(a){printer.open().then(function(){printer.align("center").bold(!0).text('{{config("app.copyright")}}').bold(!1).align("left").text("ID    : "+a.id).text("KASIR : {{auth()->user()->name}}").text("TGL   : "+a.tgl+"\n").text(a.barang).bold(!0).text("Total     : "+a.total).text("Bayar     : "+a.bayar).text("Kembalian : "+a.kembalian).align("center").text("\nTERIMA KASIH").cut().print()})}function daftarBarang(a){$.ajax({url:"{{route('penjualan.dataBarang')}}",type:"POST",dataType:"JSON",data:{nama:a},success:function(a){var t="";$.each(a,function(a,e){t+='<div class="product"><div class="card m-0"><img class="card-img-top" src="'+e.foto+'" alt="'+e.nama+'"/><div class="mx-1 my-2"><a class="text-decoration-none text-dark stretched-link fw-11 pilih-barang" data-id="'+e.id+'" data-status="'+e.status+'" title="'+e.pemasok+" | "+e.nama+" ( "+e.ukuran+' )" href="javascript:void(0)"><div class="mb-2"><span class="fw-bold">'+e.pemasok+"</span> - "+e.nama+'</div></a><div class="row fw-10"><div class="col-8"><span class="fw-bold">SIZE :</span> '+e.ukuran+'</div><div class="col-4 text-end"><span class="fw-bold">'+e.stok+"</span> "+e.satuan+"</div></div></div></div></div>"}),$("#daftarBarang").html(t)},error:function(a,t,e){$("#daftarBarang").html('<h1 class="text-center fw-50 mt-5"><i class="fa fa-sad-cry"></i> <i class="fa fa-sad-cry"></i> <i class="fa fa-sad-cry"></i></h1><h3 class="text-center">BARANG TIDAK DITEMUKAN BOS.</h3>'),alertApp("error","Barang tidak ditemukan bos")}})}function keranjang(){var a=$("#tabelKeranjang");function t(){$("#textTotal").html(0),totalPenjualan=0,$.each(keranjangBelanja,function(a,t){totalPenjualan+=t.hargaBarang.replace(/,/g,"")*t.kuantitas}),$("#textTotal").html(currency(totalPenjualan)),keranjangBelanja.length>0&&totalPenjualan>0&&$("#inputBayar").prop("disabled",!1)}$("#inputBayar").prop("disabled",!0),a.empty(),$.each(keranjangBelanja,function(t,e){var r=$("<tr>").append($("<td>").html(`<button type="button" class="btn btn-sm btn-icon waves-effect waves-light btn-danger hapusBarang" data-index="${t}" data-id="${e.idBarang}" data-status="${e.statusBarang}"><i class="fas fa-trash-alt"></i></button>`),$('<td class="fw-12">').text(e.namaBarang),$("<td>").html(`<input type="text" autocomplete="off" class="form-control text-center kuantitasBarang" placeholder="Kuantitas" required value="${e.kuantitas}" data-index="${t}" data-id="${e.idBarang}" data-status="${e.statusBarang}">`),$("<td>").html(`<input type="text" autocomplete="off" class="form-control text-end hargaBarang" placeholder="Harga" required value="${e.hargaBarang}" data-index="${t}">`));a.append(r)}),$(".kuantitasBarang").on("change",function(){var a=$(this).data("index"),t=$(this).data("id"),e=$(this).data("status"),r=parseInt($(this).val());r>0?"siap"==e?$.post("{{route('penjualan.cekStokBarangTersedia')}}",{id:t,kuantitas:0,kuantitasTambahan:r},function(e){e.status?"aman"==e.statusStok?(pesan="Kuantitas berhasil diubah.",keranjangBelanja.forEach(function(a,e){a.idBarang===t&&"po"===a.statusBarang&&(keranjangBelanja.splice(e,1),pesan+=" Barang preOrder dari barang yang diubah kuantitasnya berhasil dihapus.")}),keranjangBelanja[a].kuantitas=r,keranjang(),alertApp("success",pesan)):"melebihi stok"==e.statusStok&&-1===(cariKeranjang=keranjangBelanja.findIndex(a=>a.id===t+"-po"))&&(keranjangBelanja[a].kuantitas=e.stokTersedia,keranjangBelanja.push({id:t+"-po",idBarang:t,statusBarang:"po",namaBarang:"(PO) "+keranjangBelanja[a].namaBarang,hargaBarang:keranjangBelanja[a].hargaBarang,kuantitas:r-e.stokTersedia}),keranjang(),alertApp("success","Stok barang tidak mencukupi, sistem menambahkan kuantitas barang ke preOrder.")):alertApp("error","Barang tidak ditemukan")}).fail(function(a,t,e){alertApp("error",e)}):(keranjangBelanja[a].kuantitas=r,keranjang(),alertApp("success","Kuantitas berhasil diubah.")):($(this).val(null),alertApp("error","Masukan angka"))}),$(".hargaBarang").on("input",function(){(n=parseInt($(this).val()))>0?(keranjangBelanja[$(this).data("index")].hargaBarang=$(this).val(),t()):($(this).val(null),alertApp("error","Masukan angka"))}),$(".hapusBarang").on("click",function(){var a=$(this).data("index"),e=$(this).data("id"),r=$(this).data("status");$.confirm({icon:"fa fa-warning",title:"PERINGATAN!",content:"Apakah anda yakin ingin menghapus data ini?",type:"red",autoClose:"tutup|5000",buttons:{ya:{text:"Ya",btnClass:"btn-red",action:function(){var i="Barang berhasil dihapus dari keranjang.";keranjangBelanja.splice(a,1),"siap"==r&&keranjangBelanja.forEach(function(a,t){a.idBarang===e&&"po"===a.statusBarang&&(keranjangBelanja.splice(t,1),i+=" Bersamaan dengan barang preOrder")}),alertApp("success",i),keranjang(),t()}},tutup:{text:"Tutup"}}})}),$(".hargaBarang").inputmask({alias:"decimal",groupSeparator:","}),$('.kuantitasBarang[data-index="0"]').focus(),t()}$(document).ready(function(){new Choices(document.querySelector("select#metodePembayaran")),daftarBarang(),setTimeout(function(){$("table#tableAnggota").DataTable({bAutoWidth:!1,ordering:!1,responsive:!0,processing:!0,bDestroy:!0,paging:!0,language:{url:"{{ asset('js/id.json') }}"},ajax:{url:"{{ route('penjualan.dataAnggota') }}",type:"POST",dataType:"JSON",error:function(a,t,e){alertApp("error","Data anggota tidak bisa dimuat, silahkan muat ulang halaman ini.")}},columns:[{className:"w-1 text-center"},{className:""},{className:"w-1 text-center"}]})},2e3)}),$("img").lazy({effect:"fadeIn",effectTime:2e3,threshold:0}),$(document).keydown(function(a){a.ctrlKey&&191==a.keyCode?$("#cariBarang").focus():a.ctrlKey&&66==a.keyCode&&$("#inputBayar").focus()}),$(document).on("click",".aksi-anggota",function(){idAnggota=$(this).data("id"),namaAnggota=$(this).data("nama"),$("#namaPembeli").val($(this).data("nama")),$("#modalAnggota").modal("hide")}),$(document).on("keyup","#namaPembeli",function(){namaAnggota=$(this).val()}),$(document).on("change","#pertanyaanAnggota",function(){idAnggota="biasa",namaAnggota=null,$("#namaPembeli").val(null),$(this).is(":checked")?($("#btnCariAnggota").prop("disabled",!1),$("#namaPembeli").prop("readOnly",!0),$("#modalAnggota").modal("show")):($("#btnCariAnggota").prop("disabled",!0),$("#namaPembeli").prop("readOnly",!1))}),$("#cariBarang").on("input",function(a){$(this).val().length>=4?daftarBarang($(this).val()):""===$(this).val()&&daftarBarang()}),$(document).on("click",".pilih-barang",function(){if(""!==namaAnggota&&null!==namaAnggota){var a=$(this).data("id"),t=$(this).data("status"),e=a+"-"+t,r=keranjangBelanja.findIndex(a=>a.id===e);"po"==t?-1!==r?(keranjangBelanja[r].kuantitas++,alertApp("success","Kuantitas berhasil ditambahkan.")):(keranjangBelanja.push({id:e,idBarang:a,statusBarang:t,namaBarang:"(PO) "+$(this).attr("title"),hargaBarang:"",kuantitas:1}),alertApp("success","Barang berhasil ditambahkan kekeranjang.")):-1!==r?-1!==(cariKeranjang=keranjangBelanja.findIndex(t=>t.id===a+"-po"))?(keranjangBelanja[cariKeranjang].kuantitas++,alertApp("success","Kuantitas berhasil ditambahkan kebarang preOrder.")):$.post("{{route('penjualan.cekStokBarangTersedia')}}",{id:$(this).data("id"),kuantitas:keranjangBelanja[r].kuantitas,kuantitasTambahan:1},function(t){t.status?"aman"==t.statusStok?(keranjangBelanja[r].kuantitas++,alertApp("success","Kuantitas berhasil ditambahkan.")):"melebihi stok"==t.statusStok&&-1===(cariKeranjang=keranjangBelanja.findIndex(t=>t.id===a+"-po"))&&(keranjangBelanja[r].kuantitas=t.stokTersedia,keranjangBelanja.push({id:a+"-po",idBarang:a,statusBarang:"po",namaBarang:"(PO) "+keranjangBelanja[r].namaBarang,hargaBarang:keranjangBelanja[r].hargaBarang,kuantitas:1}),alertApp("success","Stok barang tidak mencukupi, sistem menambahkan kuantitas barang ke preOrder.")):alertApp("error","Barang tidak ditemukan"),keranjang()}).fail(function(a,t,e){alertApp("error",e)}):(keranjangBelanja.push({id:e,idBarang:a,statusBarang:t,namaBarang:$(this).attr("title"),hargaBarang:"",kuantitas:1}),alertApp("success","Barang berhasil ditambahkan kekeranjang.")),keranjang()}else $("#namaPembeli").focus(),alertApp("error","Masukan nama atau pilih pembeli")}),$(document).on("keyup","#inputBayar",function(){var a=$(this).val().replace(/,/g,"");a>=0&&(a>totalPenjualan?$("#textKembalian").html(currency(a-totalPenjualan)):$("#textKembalian").html(0),$("#inputOngkir").prop("disabled",!1))}),$("#formPenjualan").submit(function(){loading(),$("#inputBayar").val().replace(/,/g,"")>=0&&$("#inputOngkir").val().replace(/,/g,"")>=0&&totalPenjualan>0?$.post("{{route('penjualan.selesai')}}",{id:idAnggota,nama:namaAnggota,keranjang:keranjangBelanja,bayar:$("#inputBayar").val(),ongkir:$("#inputOngkir").val(),metodePembayaran:$("#metodePembayaran").val(),total:totalPenjualan},function(a){var t=new WebSocket("ws://localhost:1811/socket.io/?token="+PrinterKey+"&EIO=3&transport=websocket");a.status?(alertApp("success",a.message,!0),$("#pertanyaanAnggota").prop("checked",!1),$("#namaPembeli").focus(),$("#namaPembeli").val(null),keranjangBelanja=[],$("#tabelKeranjang").empty(),$("#textTotal").html(0),$("#inputBayar").val(null),$("#inputOngkir").val(null),$("#textKembalian").html(0),$("#btnCariAnggota").prop("disabled",!0),$("#namaPembeli").prop("readOnly",!1),idAnggota="biasa",namaAnggota=null,totalPenjualan=0,daftarBarang(),t.addEventListener("open",function(){printThermal(a.data)}),t.addEventListener("error",a=>{alertApp("error","Printer tidak terhubung",!0)})):alertApp("error",a.message,!0)}).fail(function(a,t,e){alertApp("error",e,!0)}):alertApp("error","Form tidak memenuhi syarat",!0)});</script>@endpush