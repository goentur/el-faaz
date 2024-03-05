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
                <input type="text" id="cariBarang" disabled autocomplete="off" class="form-control form-control-lg" placeholder="Masukan minimal 4 huruf ( CTRL + / )">
            </div>
        </div>
        <div class="row g-3 d-flex justify-content-center mb-2" id="daftarBarang"></div>
    </div>
    <div class="col-lg-5">
        <div class="card border">
            <form action="javascript:void(0)" id="FormPembelian" method="post">
                <div class="card-header">
                    <label for="pemasok" class="form-label h4"><i class="fa fa-truck"></i> CARI PEMASOK : </label>
                    <select required name="pemasok" id="pemasok" class="form-control">
                        <option value="">Pilih salah satu</option>
                    </select>
                </div>
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
                    <li>Kolom <b>Harga</b> pada tabel keranjang merupakan harga satuan.</li>
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.blockUI/2.70/jquery.blockUI.min.js"></script>
@endpush
@push('js')<script>var keranjangBelanja=[],pemasok=null,totalPembelian=0;function keranjang(){var a=$("#tabelKeranjang");function t(){$("#textTotal").html(0),totalPembelian=0,$.each(keranjangBelanja,function(a,t){totalPembelian+=t.hargaBarang.replace(/,/g,"")*t.kuantitas}),$("#textTotal").html(currency(totalPembelian))}a.empty(),$.each(keranjangBelanja,function(t,e){var r=$("<tr>").append($("<td>").html(`<button type="button" class="btn btn-sm btn-icon waves-effect waves-light btn-danger hapusBarang" data-index="${t}"><i class="fas fa-trash-alt"></i></button>`),$('<td class="fw-12">').text(e.namaBarang),$("<td>").html(`<input type="text" autocomplete="off" class="form-control text-center kuantitasBarang" placeholder="Kuantitas" required value="${e.kuantitas}" data-index="${t}">`),$("<td>").html(`<input type="text" autocomplete="off" class="form-control text-end hargaBarang" placeholder="Harga" required value="${e.hargaBarang}" data-index="${t}">`));a.append(r)}),$(".kuantitasBarang").on("input",function(){var a=$(this).data("index"),e=parseInt($(this).val());e>0?(keranjangBelanja[a].kuantitas=e,t()):($(this).val(null),alertApp("error","Masukan angka"))}),$(".hargaBarang").on("input",function(){(n=parseInt($(this).val()))>0?(keranjangBelanja[$(this).data("index")].hargaBarang=$(this).val(),t()):($(this).val(null),alertApp("error","Masukan angka"))}),$(".hapusBarang").on("click",function(){var a=$(this).data("index");$.confirm({icon:"fa fa-warning",title:"PERINGATAN!",content:"Apakah anda yakin ingin menghapus data ini?",type:"red",autoClose:"tutup|5000",buttons:{ya:{text:"Ya",btnClass:"btn-red",action:function(){alertApp("success","Barang berhasil dihapus dari keranjang"),keranjangBelanja.splice(a,1),keranjang(),t()}},tutup:{text:"Tutup"}}})}),$(".hargaBarang").inputmask({alias:"decimal",groupSeparator:","}),$('.kuantitasBarang[data-index="0"]').focus(),t()}function daftarBarang(a,t){$.ajax({url:"{{route('pembelian.dataBarang')}}",type:"POST",dataType:"JSON",data:{pemasok:a,nama:t},success:function(a){var t="";$.each(a,function(a,e){t+='<div class="product"><div class="card m-0"><img class="card-img-top" src="'+e.foto+'" alt="'+e.nama+'"/><div class="mx-1 my-2"><a class="text-decoration-none text-dark stretched-link fw-11 pilih-barang" data-id="'+e.id+'" data-harga="'+e.harga+'" title="'+e.pemasok+" - "+e.nama+" ( "+e.ukuran+' )" href="javascript:void(0)"><div class="mb-2"><span class="fw-bold">'+e.pemasok+"</span> - "+e.nama+' </div></a><div class="row fw-10"><div class="col-8 text-primary fw-bold">'+e.harga+'</div><div class="col-4 text-end"><span class="fw-bold">'+e.stok+"</span> "+e.satuan+'</div><div><span class="fw-bold">SIZE :</span> '+e.ukuran+"</div></div></div></div></div>"}),$("#daftarBarang").html(t)},error:function(a,t,e){$("#daftarBarang").html('<h1 class="text-center fw-50 mt-5"><i class="fa fa-sad-cry"></i> <i class="fa fa-sad-cry"></i> <i class="fa fa-sad-cry"></i></h1><h3 class="text-center">BARANG TIDAK DITEMUKAN BOS.</h3>'),alertApp("error","Barang tidak ditemukan bos")}})}$(document).ready(function(){var a=new Choices("select#pemasok",{searchPlaceholderValue:"Masukan minimal 1 huruf",noChoicesText:"Tidak ada pilihan",itemSelectText:"Tekan untuk memilih",noResultsText:"Tidak ada pilihan",searchResultLimit:10,removeItems:!0});a.passedElement.element.addEventListener("search",function(t){$.ajax({url:"{{ route('pemasok.data') }}",type:"POST",data:{nama:t.detail.value},dataType:"JSON",success:function(t){a.clearChoices(),a.setChoices(t)},error:function(a,t,e){alertApp("error","Pemasok tidak ditemukan")}})}),$("#FormPembelian").submit(function(){loading(),$.post("{{route('pembelian.selesai')}}",{pemasok:pemasok,barang:keranjangBelanja,total:totalPembelian,keterangan:$("#keterangan").val()},function(t){t.status?(alertApp("success",t.message,!0),pemasok=null,keranjangBelanja=[],totalPembelian=0,$("#cariBarang").prop("disabled",!0),$("#tabelKeranjang").empty(),$("#textTotal").html(0),$("#keterangan").val(null),$("#btnSimpan").prop("disabled",!0),$("#daftarBarang").html(""),a.clearStore(),a.setValue(["Pilih salah satu",null])):alertApp("error",t.message,!0)}).fail(function(a,t,e){alertApp("error",e,!0)})})}),$("select#pemasok").change(function(){keranjangBelanja=[],$("#tabelKeranjang").empty(),daftarBarang(pemasok=$(this).val()),$("#cariBarang").prop("disabled",!1)}),$("img").lazy({effect:"fadeIn",effectTime:2e3,threshold:0}),$(document).keydown(function(a){a.ctrlKey&&191==a.keyCode&&$("#cariBarang").focus()}),$("#cariBarang").on("input",function(a){$(this).val().length>=4?daftarBarang(pemasok,$(this).val()):""===$(this).val()&&daftarBarang(pemasok)}),$(document).on("click",".pilih-barang",function(){var a=$(this).data("id"),t=$(this).data("harga"),e=keranjangBelanja.findIndex(t=>t.id===a);-1!==e?keranjangBelanja[e].kuantitas++:keranjangBelanja.push({id:a,namaBarang:$(this).attr("title"),hargaBarang:0!==t?t:"",kuantitas:""}),keranjang()}),$("#keterangan").on("input",function(){keranjangBelanja.length>0&&$(this).val().length>0?$("#btnSimpan").prop("disabled",!1):alertApp("error","Form tidak memenuhi syarat")});</script>@endpush