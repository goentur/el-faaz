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
                <div class="row">
                    <div class="h3"><i class="fa fa-box-open"></i> INFORMASI TRANSAKSI PENJUALAN</div>
                    <div class="col-lg-6 col-12">
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
                                <td>{{ formatTanggal($penjualan->tanggal,$zonaWaktuPengguna) }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-lg-6 col-12">
                        <table class="table-sm">
                            <tr>
                                <td class="w-4 fw-bold">TOTAL BARANG</td>
                                <td class="w-1">:</td>
                                <td>{{ rupiah($penjualan->total) }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">RETUR</td>
                                <td>:</td>
                                <td>{{ rupiah($penjualan->retur->sum('total')) }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">TOTAL TRANSAKSI</td>
                                <td>:</td>
                                <td>{{ rupiah($penjualan->total - $penjualan->retur->sum('total')) }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">BAYAR</td>
                                <td>:</td>
                                <td class="textBayar">0</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">KEKURANGAN</td>
                                <td>:</td>
                                <td class="textKekurangan">0</td>
                            </tr>
                        </table>
                    </div>
                </div>
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
        <div class="card">
            <div class="card-body">
                <div class="h3"><i class="fa fa-list-ol"></i> DAFTAR BARANG YANG DIRETUR</div>
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
                        </tr>
                    </thead>
                </table>
                <table class="table-sm h4">
                    <tr>
                        <td class="text-end" width="99%">TOTAL</td>
                        <td>:</td>
                        <td>{{ rupiah($penjualan->retur->sum('total')) }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card">
            <div class="card-body">
                <div class="h3"><i class="fa fa-clipboard"></i> FORM ANGSURAN</div>
                <form action="javascript:void(0)" id="formAngsuran" class="row" method="post">
                    <div class="col-lg-6">
                        <label for="metodePembayaran" class="form-label">METODE PEMBAYARAN :</label>
                        <select required name="metodePembayaran" id="metodePembayaran" class="form-control">
                            @foreach ($metodePembayarans as $metodePembayaran)
                            <option value="{{ enkrip($metodePembayaran->id) }}">{{ $metodePembayaran->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-6">
                        <label for="nominal" class="form-label">NOMINAL PEMBAYARAN :</label>
                        <input type="text" class="form-control form-control-lg" autocomplete="off" required id="nominal" placeholder="Nominal pembayaran" data-inputmask="'alias': 'decimal', 'groupSeparator': ','">
                    </div>
                    <div class="col-lg-12 d-grid gap-2">
                        <button type="submit" id="btnSimpan" class="btn btn-lg btn-primary my-2 "><i class="fa fa-save"></i> SIMPAN</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
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
                <table class="table-sm h4">
                    <tr>
                        <td class="text-end" width="99%">TOTAL</td>
                        <td>:</td>
                        <td>{{ rupiah($penjualan->total - $penjualan->retur->sum('total')) }}</td>
                    </tr>
                    <tr>
                        <td class="text-end">BAYAR</td>
                        <td>:</td>
                        <td class="text-end textBayar">0</td>
                    </tr>
                    <tr>
                        <td class="text-end">KEKURANGAN</td>
                        <td>:</td>
                        <td class="text-end textKekurangan">0</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
@push('vendor-js')
<script src="{{ asset('js/datatables.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.blockUI/2.70/jquery.blockUI.min.js"></script>
@endpush
@push('js')<script>function riwayatAngsuran(){total="{{$penjualan->total - $penjualan->retur->sum('total')}}",idAngsuran="{{ enkrip($angsuran->id) }}",$("table#tableRiwayatAngsuran").DataTable({info:!1,ordering:!1,paging:!1,responsive:!0,bAutoWidth:!1,language:{url:"{{ asset('js/id.json') }}"},bDestroy:!0,processing:!0,ajax:{url:"{{ route($attribute['link'].'detail.data.angsuran') }}",type:"POST",data:{id:idAngsuran},error:function(a,t,n){alertApp("error","Data riwayat angsuran tidak bisa ditampilkan.")}},columns:[{className:"w-1 text-center",data:"no"},{className:"w-8",data:"pengguna"},{data:"metode"},{data:"tanggal"},{className:"w-1 text-end",data:"nominal"},],initComplete:function(a,t){(bayar=parseInt(t.bayar))?(kekurangan=parseInt(total)-bayar,$(".textBayar").html(currency(bayar))):kekurangan=parseInt(total),$(".textKekurangan").html(currency(kekurangan))}})}function daftarBarang(){$("table#tableDaftarBarang").DataTable({info:!1,ordering:!1,paging:!1,responsive:!0,bAutoWidth:!1,language:{url:"{{ asset('js/id.json') }}"},bDestroy:!0,processing:!0,ajax:{url:"{{ route($attribute['link'].'daftar.barang') }}",type:"POST",data:{id:"{{ enkrip($penjualan->id) }}"},error:function(a,t,n){alertApp("error","Daftar barang tidak bisa ditampilkan.")}},columns:[{className:"w-1 text-center",data:"no"},{data:"pemasok"},{data:"barang"},{data:"satuan"},{data:"ukuran"},{className:"w-1 text-center",data:"status"},{className:"w-1 text-center",data:"kuantitas"},{className:"w-1 text-end",data:"harga"},]})}function daftarBarangRetur(){$("table#tableDaftarBarangRetur").DataTable({info:!1,ordering:!1,paging:!1,responsive:!0,bAutoWidth:!1,language:{url:"{{ asset('js/id.json') }}"},bDestroy:!0,processing:!0,ajax:{url:"{{ route($attribute['link'].'daftar.barang.retur') }}",type:"POST",data:{id:"{{ enkrip($penjualan->id) }}"},error:function(a,t,n){alertApp("error","Daftar barang tidak bisa ditampilkan.")}},columns:[{className:"w-1 text-center",data:"no"},{data:"pengguna"},{data:"tanggal"},{data:"pemasok"},{data:"barang"},{data:"satuan"},{data:"ukuran"},{className:"w-1 text-center",data:"kuantitas"},{className:"w-1 text-end",data:"harga"}]})}$(function(){new Choices(document.querySelector("select#metodePembayaran")),riwayatAngsuran(),daftarBarang(),daftarBarangRetur()}),$("#formAngsuran").submit(function(){metodePembayaran=$("#metodePembayaran").val(),nominal=$("#nominal").val().replace(/,/g,""),""!==metodePembayaran&&nominal<=kekurangan?(loading(),$.post("{{route($attribute['link'].'detail.tambah-angsuran')}}",{idPenjualan:"{{ enkrip($penjualan->id) }}",idAngsuran:idAngsuran,nominal:nominal,metodePembayaran:metodePembayaran},function(a){a.status?(2==a.type&&$("#btnSimpan").prop("disabled",!0),$("#nominal").val(null),$(".textBayar").html(0),$(".textKekurangan").html(0),alertApp("success",a.message,!0),riwayatAngsuran()):alertApp("error",a.message,!0)}).fail(function(a,t,n){alertApp("error",n,!0)})):($("#nominal").val(null),alertApp("error","Nominal pembayaran lebih besar dari kekurangan"))});</script>@endpush