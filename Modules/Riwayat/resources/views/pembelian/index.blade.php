@extends('layouts.app')

@section('content')
<div class="row mb-2 mb-xl-3">
    <div class="col-auto d-none d-sm-block">
        <h3 class="d-inline align-middle">Data {{ $attribute['title'] }}</h3>
    </div>
</div>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="row mb-2">
                    <div class="col-lg-4">
                        <form action="javascript:void(0)" id="formTanggalTransaksi" method="post">
                            <label class="form-label h3"><i class="fa fa-calendar-alt"></i> TANGGAL TRANSAKSI :</label>
                            <div class="input-group">
                                <input type="text" class="form-control form-control-lg" required id="tanggalTransaksi" placeholder="Pilih tanggal transaksi" />
                                <button type="submit" class="btn btn-primary" type="button"><i class="fa fa-search"></i></button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <h4 class="text-center">PERIODE TRANSAKSI</h4>
                <h3 class="text-center">
                    <span id="textTanggalAwal">{{ $tanggalAwal }}</span> <b>S.D.</b> <span id="textTanggalAkhir">{{ $tanggalAkhir }}</span>
                </h3>
                <table id="data" class="table table-sm table-bordered table-hover dt-responsive">
                    <thead>
                        <tr>
                            <th>NO</th>
                            <th>PENGGUNA</th>
                            <th>PEMASOK</th>
                            <th>TANGGAL</th>
                            <th>TOTAL</th>
                            <th>KETERANGAN</th>
                            <th>AKSI</th>
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
@push('js')<script>function data(a){$("table#data").DataTable({ordering:!1,responsive:!0,bAutoWidth:!1,lengthMenu:[25,50,75,100],language:{url:"{{ asset('js/id.json') }}"},bDestroy:!0,processing:!0,ajax:{url:"{{ route($attribute['link'].'data') }}",type:"POST",data:{tanggal:a},error:function(a,t,n){alertApp("error","Data riwayat pembelian tidak bisa ditampilkan.")}},columns:[{className:"w-1 text-center",data:"no"},{className:"w-8",data:"pengguna"},{className:"w-8",data:"pemasok"},{className:"w-6",data:"tanggal"},{className:"w-1 text-end",data:"total"},{data:"keterangan"},{className:"w-1 text-center",data:"aksi",render:function(a,t,n){return'<a href="'+a+'" class="btn btn-sm btn-primary mb-1"><i class="fa fa-eye"></i></a>'}},],initComplete:function(a,t){$("#textTanggalAwal").html(t.awal),$("#textTanggalAkhir").html(t.akhir)}})}$(function(){flatpickr("#tanggalTransaksi",{mode:"range",dateFormat:"d-m-Y",defaultDate:["{{ $tanggalAwal }}","{{ $tanggalAkhir }}"],maxDate:"{{ $tanggalAkhir }}"}),data("{{ $tanggalAwal }} to {{ $tanggalAkhir }}")}),$("#formTanggalTransaksi").submit(function(){var a=$("#tanggalTransaksi").val();a?data(a):alertApp("error","Pilih tanggal transaksi.")});</script>@endpush