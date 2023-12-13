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
                        <td>{{ date('Y-m-d H:i:s', ($penjualan->tanggal + $zonaWaktuPengguna->gmt_offset)) . ' ' . $zonaWaktuPengguna->singkatan }}</td>
                    </tr>
                </table>
                <div class="h3 mt-3"><i class="fa fa-list-ol"></i> DAFTAR BARANG YANG DIJUAL</div>
                <div class="alert alert-info alert-dismissible" role="alert">
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    <div class="alert-message">
                        <h4 class="alert-heading fw-bold">INFORMASI!</h4>
                        <ul>
                            <li>Kolom <b>Status</b> pada tabel barang yang dijual merupakan status barang pada saat dijual.</li>
                            <li>Status <span class="badge bg-success">TERSEDIA</span> berarti barang ketika dijual masih ada stoknya.</li>
                            <li>Status <span class="badge bg-danger">TIDAK</span> berarti barang ketika dijual stoknya sudah habis.</li>
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
@push('js')<script>function riwayatAngsuran(){total="{{$penjualan->total}}",idAngsuran="{{ $angsuran ? enkrip($angsuran->id) : 0 }}",$("table#tableRiwayatAngsuran").DataTable({info:!1,ordering:!1,paging:!1,responsive:!0,bAutoWidth:!1,language:{url:"{{ asset('js/id.json') }}"},bDestroy:!0,processing:!0,ajax:{url:"{{ route($attribute['link'].'detail.data.angsuran') }}",type:"POST",data:{id:idAngsuran},error:function(a,t,n){alertApp("error","Data riwayat angsuran tidak bisa ditampilkan.")}},columns:[{className:"w-1 text-center",data:"no"},{className:"w-8",data:"pengguna"},{data:"metode"},{data:"tanggal"},{className:"w-1 text-end",data:"nominal"},]})}function daftarBarang(){$("table#tableDaftarBarang").DataTable({info:!1,ordering:!1,paging:!1,responsive:!0,bAutoWidth:!1,language:{url:"{{ asset('js/id.json') }}"},bDestroy:!0,processing:!0,ajax:{url:"{{ route($attribute['link'].'daftar.barang') }}",type:"POST",data:{id:"{{ enkrip($penjualan->id) }}"},error:function(a,t,n){alertApp("error","Daftar barang tidak bisa ditampilkan.")}},columns:[{className:"w-1 text-center",data:"no"},{data:"pemasok"},{data:"barang"},{data:"satuan"},{data:"ukuran"},{className:"w-1 text-center",data:"status"},{className:"w-1 text-center",data:"kuantitas"},{className:"w-1 text-end",data:"harga"},]})}$(function(){riwayatAngsuran(),daftarBarang()});</script>@endpush
