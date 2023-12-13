@extends('layouts.app')

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
                <div class="h3"><i class="fa fa-box-open"></i> INFORMASI TRANSAKSI PEMBELIAN</div>
                <table class="table-sm">
                    <tr>
                        <td class="w-4 fw-bold">PENGGUNA</td>
                        <td class="w-1">:</td>
                        <td>{!! $pembelian->user?$pembelian->user->name:'<span class="badge bg-danger">DATA KOSONG</span>' !!}</td>
                    </tr>
                    <tr>
                        <td class="w-4 fw-bold">PEMASOK</td>
                        <td class="w-1">:</td>
                        <td>{!! $pembelian->pemasok?$pembelian->pemasok->nama:'<span class="badge bg-danger">DATA KOSONG</span>' !!}</td>
                    </tr>
                    <tr>
                        <td class="fw-bold">TANGGAL</td>
                        <td>:</td>
                        <td>{{ date('Y-m-d H:i:s', ($pembelian->tanggal + $zonaWaktuPengguna->gmt_offset)) . ' ' . $zonaWaktuPengguna->singkatan }}</td>
                    </tr>
                    <tr>
                        <td class="fw-bold">KETERANGA</td>
                        <td>:</td>
                        <td>{{ $pembelian->keterangan }}</td>
                    </tr>
                </table>
                <div class="h3 mt-3"><i class="fa fa-list-ol"></i> DAFTAR BARANG YANG DIBELI</div>
                <table class="table table-bordered table-sm" id="tableDaftarBarang">
                    <thead>
                        <tr>
                            <th>NO</th>
                            <th>BARANG</th>
                            <th>SATUAN</th>
                            <th>UKURAN</th>
                            <th>KUANTITAS</th>
                            <th>HARGA</th>
                        </tr>
                    </thead>
                </table>
                <table class="table-sm h4">
                    <tr>
                        <td class="text-end" width="99%">TOTAL</td>
                        <td>:</td>
                        <td>{{ rupiah($pembelian->total) }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <div class="h3"><i class="fa fa-list-ol"></i> RIWAYAT ANGSURAN</div>
                <table class="table table-bordered table-sm" id="tableRiwayatAngsuran">
                    <thead>
                        <tr>
                            <th class="w-1">NO</th>
                            <th class="w-6">PENGGUNA</th>
                            <th>METODE PEMBAYARAN</th>
                            <th>TANGGAL</th>
                            <th class="w-1">NOMINAL</th>
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
@endpush
@push('js')<script>function riwayatAngsuran(){$("table#tableRiwayatAngsuran").DataTable({info:!1,ordering:!1,paging:!1,responsive:!0,bAutoWidth:!1,language:{url:"{{ asset('js/id.json') }}"},bDestroy:!0,processing:!0,ajax:{url:"{{ route($attribute['link'].'detail.data.angsuran') }}",type:"POST",data:{id:"{{ enkrip($angsuran->id) }}"},error:function(a,t,n){alertApp("error","Data riwayat angsuran tidak bisa ditampilkan.")}},columns:[{className:"w-1 text-center",data:"no"},{className:"w-5",data:"pengguna"},{data:"metode"},{data:"tanggal"},{className:"w-1 text-end",data:"nominal"},]})}function daftarBarang(){$("table#tableDaftarBarang").DataTable({info:!1,ordering:!1,paging:!1,responsive:!0,bAutoWidth:!1,language:{url:"{{ asset('js/id.json') }}"},bDestroy:!0,processing:!0,ajax:{url:"{{ route($attribute['link'].'daftar.barang') }}",type:"POST",data:{id:"{{ enkrip($pembelian->id) }}"},error:function(a,t,n){alertApp("error","Daftar barang tidak bisa ditampilkan.")}},columns:[{className:"w-1 text-center",data:"no"},{data:"barang"},{data:"satuan"},{data:"ukuran"},{className:"w-1 text-center",data:"kuantitas"},{className:"w-1 text-end",data:"harga"},]})}$(function(){riwayatAngsuran(),daftarBarang()});</script>@endpush
