@extends('layouts.app')

@section('content')
<div class="row mb-2 mb-xl-3">
    <div class="col-auto d-none d-sm-block">
        <h3 class="d-inline align-middle">Detail piutang dagang anggota</h3>
    </div>
    <div class="col-auto ms-auto text-end mt-n1">
        <a href="{{ route($attribute['link'].'detail.cetak-tagihan',enkrip($data->id)) }}" target="_blank" class="btn btn-success"><i class="fa fa-print"></i> CETAK TAGIHAN</a>
        <a href="{{ route($attribute['link'].'index') }}" class="btn btn-primary"><i class="fa fa-arrow-left"></i> KEMBALI DATA</a>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <div class="h3 text-center"><i class="fa fa-user"></i> {{ $data->nama }}</div>
                <div class="h3 mt-3"><i class="fa fa-clipboard-list"></i> DAFTAR BARANG YANG DIBELI</div>
                <div class="alert alert-info alert-dismissible" role="alert">
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    <div class="alert-message">
                        <h4 class="alert-heading fw-bold">INFORMASI!</h4>
                        <ul>
                            <li>Kolom <b>Status</b> pada tabel daftar barang yang dibeli merupakan status barang pada saat dijual.</li>
                            <li>Status <span class="badge bg-success">TERSEDIA</span> berarti barang ketika dijual masih ada stoknya.</li>
                            <li>Status <span class="badge bg-danger">TIDAK</span> berarti barang ketika dijual stoknya sudah habis.</li>
                        </ul>
                    </div>
                </div>
                <table class="table table-bordered table-sm" id="daftarBarang">
                    <thead>
                        <tr>
                            <th>NO</th>
                            <th>PENGGUNA</th>
                            <th>PEMASOK</th>
                            <th>BARANG</th>
                            <th>SATUAN</th>
                            <th>UKURAN</th>
                            <th>TANGGAL</th>
                            <th>STATUS</th>
                            <th>KUANTITAS</th>
                            <th>HARGA</th>
                        </tr>
                    </thead>
                </table>
                <table class="table-sm h4">
                    <tr>
                        <td class="text-end" width="99%">TOTAL</td>
                        <td>:</td>
                        <td class="text-end" id="textTotal"></td>
                    </tr>
                    <tr>
                        <td class="text-end" width="99%">BAYAR</td>
                        <td>:</td>
                        <td class="text-end" id="textBayar"></td>
                    </tr>
                    <tr>
                        <td class="text-end" width="99%">KEKURANGAN</td>
                        <td>:</td>
                        <td class="text-end" id="textKekurangan"></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
@push('vendor-js')
<script src="{{ asset('js/datatables.js') }}"></script>
@endpush
@push('js')<script>$(function(){$("table#daftarBarang").DataTable({info:!1,ordering:!1,paging:!1,responsive:!0,bAutoWidth:!1,lengthMenu:[25,50,75,100],language:{url:"{{ asset('js/id.json') }}"},bDestroy:!0,processing:!0,ajax:{url:"{{ route('anggota.detail.daftar-barang') }}",type:"POST",data:{id:"{{enkrip($data->id)}}"},error:function(a,t,e){alertApp("error","Daftar barang yang dibeli tidak bisa ditampilkan.")}},columns:[{className:"w-1 text-center",data:"no"},{data:"pengguna"},{data:"pemasok"},{data:"barang"},{data:"satuan"},{data:"ukuran"},{data:"tanggal"},{className:"w-1 text-center",data:"status"},{className:"w-1 text-center",data:"kuantitas"},{className:"w-1 text-end",data:"harga"},],initComplete:function(a,t){$("#textTotal").html(t.total),$("#textBayar").html(t.bayar),$("#textKekurangan").html(t.kekurangan)}})});</script>@endpush