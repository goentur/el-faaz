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
                            <th>PEMBELI</th>
                            <th>TANGGAL</th>
                            <th>TOTAL</th>
                            <th>STATUS</th>
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
@push('js')<script>var PrinterKey="{{ auth()->user()->kode_printer }}",printer=new Recta(PrinterKey,"1811");function printThermal(t){printer.open().then(function(){printer.align("center").bold(!0).text('{{config("app.copyright")}}').bold(!1).align("left").text("ID    : "+t.id).text("KASIR : {{auth()->user()->name}}").text("TGL   : "+t.tgl+"\n").text(t.barang).bold(!0).text("Total     : "+t.total).text("Bayar     : "+t.bayar).text("Kembalian : "+t.kembalian).align("center").text("\nTERIMA KASIH").cut().print()})}function data(t){$("table#data").DataTable({ordering:!1,responsive:!0,bAutoWidth:!1,lengthMenu:[25,50,75,100],language:{url:"{{ asset('js/id.json') }}"},bDestroy:!0,processing:!0,ajax:{url:"{{ route($attribute['link'].'data') }}",type:"POST",data:{tanggal:t},error:function(t,a,n){alertApp("error","Data riwayat penjualan tidak bisa ditampilkan.")}},columns:[{className:"w-1 text-center",data:"no"},{className:"w-8",data:"pengguna"},{data:"pembeli"},{className:"w-4",data:"tanggal"},{className:"w-1 text-end",data:"total"},{className:"w-1 text-center",data:"status"},{className:"w-1 text-center",data:"aksi",render:function(t,a,n){var e="";return"hutang"==t.type&&(e=' target="_blank"'),'<div class="btn-group"><a href="'+t.link+'"'+e+' class="btn btn-sm btn-primary mb-1"><i class="fa fa-eye"></i></a><button type="button" class="btn btn-sm btn-success mb-1 cetak-nota" data-id="'+t.id+'"><i class="fa fa-print"></i></a></button></div>'}},],initComplete:function(t,a){$("#textTanggalAwal").html(a.awal),$("#textTanggalAkhir").html(a.akhir)}})}$(function(){flatpickr("#tanggalTransaksi",{mode:"range",dateFormat:"d-m-Y",defaultDate:["{{ $tanggalAwal }}","{{ $tanggalAkhir }}"],maxDate:"{{ $tanggalAkhir }}"}),data("{{ $tanggalAwal }} to {{ $tanggalAkhir }}")}),$("#formTanggalTransaksi").submit(function(){var t=$("#tanggalTransaksi").val();t?data(t):alertApp("error","Pilih tanggal transaksi.")}),$(document).on("click",".cetak-nota",function(){var t=t,a=$(this).data("id");$.confirm({icon:"fa fa-warning",title:"PERINGATAN!",content:"Apakah anda yakin ingin mencetak nota lagi?",type:"red",autoClose:"tutup|5000",buttons:{ya:{text:"Ya",btnClass:"btn-red",action:function(){$.post("{{route('penjualan.cetak-nota')}}",{id:a},function(a){var n=new WebSocket("ws://localhost:1811/socket.io/?token="+t+"&EIO=3&transport=websocket");a.status?(n.addEventListener("open",function(){printThermal(a.data)}),n.addEventListener("error",t=>{alertApp("error","Printer tidak terhubung")})):alertApp("error",a.message)}).fail(function(t,a,n){alertApp("error",n)})}},tutup:{text:"Tutup"}}})});</script>@endpush