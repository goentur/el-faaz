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
    <div class="col-lg-4">
        <div class="card">
            <div class="card-body">
                <div class="h3"><i class="fa fa-box-open"></i> INFORMASI BARANG</div>
                <table class="table-sm">
                    <tr>
                        <td class="w-4 fw-bold">PEMASOK</td>
                        <td class="w-1">:</td>
                        <td>{!! $data->pemasok?$data->pemasok->nama:'<span class="badge bg-danger">DATA KOSONG</span>' !!}</td>
                    </tr>
                    <tr>
                        <td class="fw-bold">NAMA</td>
                        <td>:</td>
                        <td>{!! $data->barangDetail&&$data->barangDetail->barang?$data->barangDetail->barang->nama:'<span class="badge bg-danger">DATA KOSONG</span>' !!}</td>
                    </tr>
                    <tr>
                        <td class="fw-bold">WARNA</td>
                        <td>:</td>
                        <td>{!! $data->barangDetail&&$data->barangDetail->warna?$data->barangDetail->warna->nama:'<span class="badge bg-danger">DATA KOSONG</span>' !!}</td>
                    </tr>
                    <tr>
                        <td class="fw-bold">SATUAN</td>
                        <td>:</td>
                        <td>{!! $data->barangDetail&&$data->barangDetail->satuan?$data->barangDetail->satuan->nama:'<span class="badge bg-danger">DATA KOSONG</span>' !!}</td>
                    </tr>
                    <tr>
                        <td class="fw-bold">UKURAN</td>
                        <td>:</td>
                        <td>
                            @forelse ($data->barangDetail->ukuran as $u)
                            @if ($u === $data->barangDetail->ukuran->last())
                            {{ $u->nama }}
                            @else
                            {{ $u->nama . ", " }}
                            @endif
                            @empty
                            <span class="badge bg-danger">DATA KOSONG</span>
                            @endforelse
                        </td>
                    </tr>
                    <tr>
                        <td class="fw-bold">STOK</td>
                        <td>:</td>
                        <td>{{ $data->stok }}</td>
                    </tr>
                    <tr>
                        <td class="fw-bold">HARGA BELI</td>
                        <td>:</td>
                        <td>{{ rupiah($data->harga_beli) }}</td>
                    </tr>
                    <tr>
                        <td class="fw-bold top">FOTO</td>
                        <td class="top">:</td>
                        <td>
                            @if ($data->barangDetail->foto)
                            <img src="{{ asset($data->barangDetail->foto)}}" class="img-fluid shadow-lg border" width="100%" alt="FOTO BARANG">
                            @else
                                <span class="badge bg-danger">FOTO KOSONG</span>
                            @endif
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="h3"><i class="fa fa-reply-all"></i> RIWAYAT BARANG MASUK</div>
                    <div class="alert alert-info alert-dismissible" role="alert">
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        <div class="alert-message">
                            <h4 class="alert-heading fw-bold">INFORMASI!</h4>
                            <ul>
                                <li>Kolom <b>Status</b> pada tabel riwayat barang masuk merupakan status pembelian barang.</li>
                            </ul>
                        </div>
                    </div>
                    <table class="table table-bordered table-sm" id="riwayatMasuk">
                        <thead>
                            <tr>
                                <th class="w-1">NO</th>
                                <th class="w-6">PENGGUNA</th>
                                <th>TANGGAL</th>
                                <th class="w-1">KUANTITAS</th>
                                <th class="w-1">HARGA</th>
                                <th class="w-1">STATUS</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="h3"><i class="fa fa-share"></i> RIWAYAT BARANG KELUAR</div>
                    <div class="alert alert-info alert-dismissible" role="alert">
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        <div class="alert-message">
                            <h4 class="alert-heading fw-bold">INFORMASI!</h4>
                            <ul>
                                <li>Kolom <b>Status</b> pada tabel riwayat barang keluar merupakan status penjualan barang.</li>
                                <li>Kolom <b>Status barang</b> pada tabel riwayat barang keluar merupakan status barang pada saat dijual.</li>
                                <li>Status barang <span class="badge bg-success">TERSEDIA</span> berarti barang ketika dijual masih ada stoknya.</li>
                                <li>Status barang <span class="badge bg-danger">TIDAK</span> berarti barang ketika dijual stoknya sudah habis.</li>
                            </ul>
                        </div>
                    </div>
                    <table class="table table-bordered table-sm" id="riwayatKeluar">
                        <thead>
                            <tr>
                                <th class="w-1">NO</th>
                                <th class="w-6">PENGGUNA</th>
                                <th>TANGGAL</th>
                                <th class="w-1">KUANTITAS</th>
                                <th class="w-1">HARGA</th>
                                <th class="w-5">STATUS BARANG</th>
                                <th class="w-1">STATUS</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@push('vendor-js')
<script src="{{ asset('js/datatables.js') }}"></script>
@endpush
@push('js')<script>function riwayatMasuk(){$("table#riwayatMasuk").DataTable({ordering:!1,responsive:!0,bAutoWidth:!1,lengthMenu:[25,50,75,100],language:{url:"{{ asset('js/id.json') }}"},bDestroy:!0,processing:!0,ajax:{url:"{{ route('gudang.detail.riwayat-masuk') }}",type:"POST",data:{id:"{{enkrip($data->id)}}"},error:function(a,t,e){alertApp("error","Data riwayat masuk tidak bisa ditampilkan.")}},columns:[{className:"w-1 text-center",data:"no"},{className:"w-6",data:"pengguna"},{className:"",data:"tanggal"},{className:"w-1 text-center",data:"kuantitas"},{className:"w-1 text-end",data:"harga"},{className:"w-1 text-center",data:"status"},]})}function riwayatKeluar(){$("table#riwayatKeluar").DataTable({ordering:!1,responsive:!0,bAutoWidth:!1,lengthMenu:[25,50,75,100],language:{url:"{{ asset('js/id.json') }}"},bDestroy:!0,processing:!0,ajax:{url:"{{ route('gudang.detail.riwayat-keluar') }}",type:"POST",data:{id:"{{enkrip($data->id)}}"},error:function(a,t,e){alertApp("error","Data riwayat keluar tidak bisa ditampilkan.")}},columns:[{className:"w-1 text-center",data:"no"},{className:"w-6",data:"pengguna"},{className:"",data:"tanggal"},{className:"w-1 text-center",data:"kuantitas"},{className:"w-1 text-end",data:"harga"},{className:"w-1 text-center",data:"statusBarang"},{className:"w-1 text-center",data:"status"},]})}$(function(){riwayatMasuk(),riwayatKeluar()});</script>@endpush