@extends('layouts.app')

@push('vendor-css')
<link rel="stylesheet" href="{{ asset('css/jquery-confirm.min.css') }}">
@endpush
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
                    <div class="col-lg-4">
                        <label class="form-label h3">&nbsp;</label>
                        <div class="d-grid gap-2">
                            <button type="button" class="btn btn-primary btn-lg" id="tampilSemua" type="button"><i class="fa fa-search"></i> TAMPILKAN SEMUA DATA</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <h4 class="text-center">PERIODE TRANSAKSI</h4>
                <h3 class="text-center">
                    <span id="textTanggalAwal">~</span> <b>S.D.</b> <span id="textTanggalAkhir">{{ $tanggalAkhir }}</span>
                </h3>
                <table id="data" class="table table-sm table-bordered table-hover dt-responsive">
                    <thead>
                        <tr>
                            <th>NO</th>
                            <th>PENGGUNA</th>
                            <th>PEMBELI</th>
                            <th>TANGGAL</th>
                            <th>TOTAL</th>
                            <th>BAYAR</th>
                            <th>KEKURANGAN</th>
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
<script src="{{ asset('js/jquery-confirm.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/recta/dist/recta.js"></script>
@endpush
@push('js')<script>var PrinterKey="{{ auth()->user()->kode_printer }}",printer=new Recta(PrinterKey,"1811");function printThermal(a){printer.open().then(function(){printer.align("center").bold(!0).text('{{config("app.copyright")}}').bold(!1).align("left").text("ID    : "+a.id).text("KASIR : {{auth()->user()->name}}").text("TGL   : "+a.tgl+"\n").text(a.barang).bold(!0).text("Total     : "+a.total).text("Bayar     : "+a.bayar).text("Kembalian : "+a.kembalian).align("center").text("\nTERIMA KASIH").cut().print()})}function data(a="semua",t="{{ enkrip('kosong') }}"){$("table#data").DataTable({ordering:!1,responsive:!0,bAutoWidth:!1,lengthMenu:[25,50,75,100],language:{url:"{{ asset('js/id.json') }}"},bDestroy:!0,processing:!0,ajax:{url:"{{ route($attribute['link'].'data') }}",type:"POST",data:{type:a,tanggal:t},error:function(a,t,n){alertApp("error","Data piutang tidak bisa ditampilkan.")}},columns:[{className:"w-1 text-center",data:"no"},{className:"w-8",data:"pengguna"},{className:"w-8",data:"pembeli"},{className:"w-6",data:"tanggal"},{className:"w-1 text-end",data:"total"},{className:"w-1 text-end",data:"bayar"},{className:"w-1 text-end",data:"kekurangan"},{className:"w-1 text-center",data:"aksi",render:function(a,t,n){return'<div class="btn-group"><a href="'+a.link+'" class="btn btn-sm btn-primary mb-1"><i class="fa fa-eye"></i></a><button type="button" class="btn btn-sm btn-success mb-1 cetak-nota" data-id="'+a.id+'"><i class="fa fa-print"></i></a></button>'}},],initComplete:function(a,t){$("#textTanggalAwal").html(t.awal),$("#textTanggalAkhir").html(t.akhir)}})}$(function(){flatpickr("#tanggalTransaksi",{mode:"range",dateFormat:"d-m-Y",maxDate:"{{ $tanggalAkhir }}"}),data()}),$(document).on("click","#tampilSemua",function(){data("semua","{{ enkrip('kosong') }}")}),$("#formTanggalTransaksi").submit(function(){var a=$("#tanggalTransaksi").val();a?data("tanggal",a):alertApp("error","Pilih tanggal transaksi.")}),$(document).on("click",".cetak-nota",function(){var a=a,t=$(this).data("id");$.confirm({icon:"fa fa-warning",title:"PERINGATAN!",content:"Apakah anda yakin ingin mencetak nota lagi?",type:"red",autoClose:"tutup|5000",buttons:{ya:{text:"Ya",btnClass:"btn-red",action:function(){$.post("{{route('penjualan.cetak-nota')}}",{id:t},function(t){var n=new WebSocket("ws://localhost:1811/socket.io/?token="+a+"&EIO=3&transport=websocket");t.status?(n.addEventListener("open",function(){printThermal(t.data)}),n.addEventListener("error",a=>{alertApp("error","Printer tidak terhubung")})):alertApp("error",t.message)}).fail(function(a,t,n){alertApp("error",n)})}},tutup:{text:"Tutup"}}})});</script>@endpush