@extends('layouts.app')

@push('vendor-css')
<link rel="stylesheet" href="{{ asset('css/jquery-confirm.min.css') }}">
@endpush
@section('content')
<div class="row mb-2 mb-xl-3">
    <div class="col-auto d-none d-sm-block">
        <h3 class="d-inline align-middle">Detail data {{ $attribute['title'] }}</h3>
    </div>
    <div class="col-auto ms-auto text-end mt-n1">
        <a href="{{ route($attribute['link'].'index') }}" class="btn btn-primary"><i class="fa fa-arrow-left"></i> KEMBALI DATA</a>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <div class="h3"><i class="fa fa-box-open"></i> INFORMASI TRANSAKSI PENJUALAN</div>
                <table class="table-sm">
                    <tr>
                        <td class="w-4 fw-bold">PENGGUNA</td>
                        <td class="w-1">:</td>
                        <td>{!! $penjualan->user?$penjualan->user->name:'<span class="badge bg-danger">DATA KOSONG</span>' !!}</td>
                    </tr>
                    <tr>
                        <td class="w-4 fw-bold">PEMBELI</td>
                        <td class="w-1">:</td>
                        <td>{!! $penjualan->anggota?$penjualan->anggota->nama:$penjualan->nama_pembeli !!}</td>
                    </tr>
                    <tr>
                        <td class="fw-bold">TANGGAL</td>
                        <td>:</td>
                        <td>{{ formatTanggal($penjualan->tanggal, $zonaWaktuPengguna) }}</td>
                    </tr>
                </table>
                <div class="h3 mt-3"><i class="fa fa-list-ol"></i> DAFTAR BARANG YANG DIJUAL</div>
                <div class="alert alert-info alert-dismissible" role="alert">
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    <div class="alert-message">
                        <h4 class="alert-heading fw-bold">INFORMASI!</h4>
                        <ul>
                            <li class="mb-1">Kolom <b>Status</b> pada tabel barang yang dijual merupakan status barang pada saat dijual.</li>
                            <li class="mb-1">Status <span class="badge bg-success">TERSEDIA</span> berarti barang ketika dijual masih ada stoknya.</li>
                            <li class="mb-1">Status <span class="badge bg-danger">TIDAK</span> berarti barang ketika dijual stoknya sudah habis.</li>
                            <li>Button <button type="button" class="btn btn-sm btn-success"><i class="fa fa-check"></i></button> pada kolom <b>Aksi</b> digunakan untuk memilih barang yang akan diretur.</li>
                        </ul>
                    </div>
                </div>
                <table class="table table-bordered table-sm" id="tableDaftarBarang">
                    <thead>
                        <tr>
                            <th>NO</th>
                            <th>PEMASOK</th>
                            <th>BARANG</th>
                            <th>SATUAN</th>
                            <th>UKURAN</th>
                            <th>STATUS</th>
                            <th>KUANTITAS</th>
                            <th>HARGA</th>
                            <th>AKSI</th>
                        </tr>
                    </thead>
                </table>
                <table class="table-sm h4">
                    <tr>
                        <td class="text-end" width="99%">TOTAL</td>
                        <td>:</td>
                        <td>{{ rupiah($penjualan->total) }}</td>
                    </tr>
                </table>
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <div class="h3"><i class="fa fa-list-ol"></i> DAFTAR BARANG YANG DIRETUR</div>
                <div class="alert alert-info alert-dismissible" role="alert">
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    <div class="alert-message">
                        <h4 class="alert-heading fw-bold">INFORMASI!</h4>
                        <ul>
                            <li class="mb-1">Jika pada kolom <b>Aksi</b> tidak ada tombol hapus, maka barang tersebut sudah dijurnalkan.</li>
                            <li>Button <button type="button" class="btn btn-sm btn-danger"><i class="fas fa-trash-alt"></i></button> pada kolom <b>Aksi</b> digunakan untuk menghapus data barang yang diretur.</li>
                        </ul>
                    </div>
                </div>
                <table class="table table-bordered table-sm" id="tableDaftarBarangRetur">
                    <thead>
                        <tr>
                            <th>NO</th>
                            <th>PENGGUNA</th>
                            <th>TANGGAL</th>
                            <th>PEMASOK</th>
                            <th>BARANG</th>
                            <th>SATUAN</th>
                            <th>UKURAN</th>
                            <th>KUANTITAS</th>
                            <th>HARGA</th>
                            <th>AKSI</th>
                        </tr>
                    </thead>
                </table>
                <table class="table-sm h4">
                    <tr>
                        <td class="text-end" width="99%">TOTAL</td>
                        <td>:</td>
                        <td id="textTotalRetur">0</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="modalRetur" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <form action="javascript:void(0)" id="formRetur" method="post">
                <div class="modal-header">
                    <h3 class="modal-title"><i class="fa fa-file-signature"></i> FORM RETUR</h3>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body m-0">
                    <div class="row">
                        <div class="col-lg-6 col-12">
                            <div class="h4"><i class="fa fa-box-open"></i> INFORMASI BARANG</div>
                            <table class="table-sm">
                                <tr>
                                    <td class="w-1 fw-bold">PEMASOK</td>
                                    <td class="w-1">:</td>
                                    <td id="textPemasok"></td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">BARANG</td>
                                    <td>:</td>
                                    <td id="textBarang"></td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">SATUAN</td>
                                    <td>:</td>
                                    <td id="textSatuan"></td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">UKURAN</td>
                                    <td>:</td>
                                    <td id="textUkuran"></td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">KUANTITAS</td>
                                    <td>:</td>
                                    <td id="textKuantitas"></td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">HARGA</td>
                                    <td>:</td>
                                    <td id="textHarga"></td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-lg-6 col-12">
                            <div class="h4"><i class="fa fa-file-signature"></i> FORM RETUR</div>
                            <input type="number" required disabled class="form-control form-control-lg" id="kuantitas" name="kuantitas" placeholder="Masukan kuantitas barang yang akan diretur">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary" id="btnFormRetur" disabled><i class="fa fa-save"></i> Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
@push('vendor-js')
<script src="{{ asset('js/datatables.js') }}"></script>
<script src="{{ asset('js/jquery-confirm.min.js') }}"></script>
@endpush
@push('js')<script>function daftarBarang(){$("table#tableDaftarBarang").DataTable({info:!1,ordering:!1,paging:!1,responsive:!0,bAutoWidth:!1,language:{url:"{{ asset('js/id.json') }}"},bDestroy:!0,processing:!0,ajax:{url:"{{ route($attribute['link'].'daftar.barang') }}",type:"POST",data:{id:"{{ enkrip($penjualan->id) }}"},error:function(a,t,e){alertApp("error","Daftar barang tidak bisa ditampilkan.")}},columns:[{className:"w-1 text-center",data:"no"},{data:"pemasok"},{data:"barang"},{data:"satuan"},{data:"ukuran"},{className:"w-1 text-center",data:"status"},{className:"w-1 text-center",data:"kuantitas"},{className:"w-1 text-end",data:"harga"},{className:"w-1 text-center",data:"aksi"}]})}function daftarBarangRetur(){$("table#tableDaftarBarangRetur").DataTable({info:!1,ordering:!1,paging:!1,responsive:!0,bAutoWidth:!1,language:{url:"{{ asset('js/id.json') }}"},bDestroy:!0,processing:!0,ajax:{url:"{{ route($attribute['link'].'daftar.barang.retur') }}",type:"POST",data:{id:"{{ enkrip($penjualan->id) }}"},error:function(a,t,e){alertApp("error","Daftar barang tidak bisa ditampilkan.")}},columns:[{className:"w-1 text-center",data:"no"},{data:"pengguna"},{data:"tanggal"},{data:"pemasok"},{data:"barang"},{data:"satuan"},{data:"ukuran"},{className:"w-1 text-center",data:"kuantitas"},{className:"w-1 text-end",data:"harga"},{className:"w-1 text-center",data:"aksi"},],initComplete:function(a,t){$("#textTotalRetur").html(currency(t.total))}})}$(function(){daftarBarang(),daftarBarangRetur()}),$(document).on("click",".detail-barang",function(){(penjualanDetail=$(this).data("id"))?$.ajax({url:"{{route($attribute['link'].'detail.barang')}}",type:"POST",dataType:"JSON",data:{penjualan:"{{ enkrip($penjualan->id) }}",penjualanDetail:penjualanDetail},success:function(a){a?($("#textPemasok").html(a.pemasok),$("#textBarang").html(a.barang),$("#textSatuan").html(a.satuan),$("#textUkuran").html(a.ukuran),$("#textKuantitas").html(a.kuantitas),$("#textHarga").html(a.harga),kuantitas=a.kuantitas_retur,a.status?($("#btnFormRetur").removeAttr("disabled","disabled"),$("#kuantitas").removeAttr("disabled","disabled")):($("#btnFormRetur").attr("disabled","disabled"),$("#kuantitas").attr("disabled","disabled")),$("#kuantitas").val(null),$("#modalRetur").modal("show")):alertApp("error","Data barang tidak bisa ditampilkan.")},error:function(a,t,e){alertApp("error",e)}}):alertApp("error","Pilih salah satu barang terlebih dahulu.")}),$("#formRetur").submit(function(){var a=$("#kuantitas").val();parseInt(a)<=kuantitas?$.post("{{route($attribute['link'].'simpan.retur')}}",{penjualan:"{{ enkrip($penjualan->id) }}",penjualanDetail:penjualanDetail,kuantitas:a},function(a){a.status?(alertApp("success",a.message),$("#textTotalRetur").html(0),daftarBarangRetur(),kuantitas=null,penjualanDetail=null,barang=null,$("#textPemasok").html(null),$("#textBarang").html(null),$("#textSatuan").html(null),$("#textUkuran").html(null),$("#textKuantitas").html(null),$("#textHarga").html(null),$("#btnFormRetur").attr("disabled","disabled"),$("#kuantitas").attr("disabled","disabled"),$("#kuantitas").val(null),$("#modalRetur").modal("hide")):alertApp("error",a.message)}).fail(function(a,t,e){alertApp("error",e)}):alertApp("error","Kuantitas pada form retur barang tidak boleh lebih dari ("+kuantitas+").")}),$(document).on("click",".hapus",function(){var a=$(this).data("id"),t=$(this).data("retur");$.confirm({icon:"fa fa-warning",title:"PERINGATAN!",content:"Apakah anda yakin ingin menghapus data ini?",type:"red",autoClose:"tutup|5000",buttons:{ya:{text:"Ya",btnClass:"btn-red",action:function(){$.ajax({url:"{{ route($attribute['link'].'hapus.retur') }}",type:"POST",data:{retur:t,id:a},dataType:"JSON",success:function(a){a.status?(alertApp("success",a.message),$("#textTotalRetur").html(0),daftarBarangRetur()):alertApp("error",a.message)},error:function(a,t,e){alertApp("error",e)}})}},tutup:{text:"Tutup"}}})});</script>@endpush