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
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
        <div class="card">
            <div class="card-header m-0">
                <div class="row">
                    <div class="col-lg-12">
                        <h1>TOTAL : <span id="txtTotal">0</span></h1>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <form id="itemForm" method="POST" action="javascript:void(0)">
                    <div class="row">
                        <div class="col-lg-3">
                            <label for="akun" class="form-label">Akun <span class="text-danger">*</span></label>
                            <select required name="akun" id="akun" class="form-control form-control-lg">
                                <option value="">Pilih salah satu</option>
                            </select>
                        </div>
                        <div class="col-lg-3">
                            <label for="debet" class="form-label">Debet <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-lg" required id="debet" name="debet" placeholder="Masukan nominal debet" data-inputmask="'alias': 'decimal', 'groupSeparator': ','">
                        </div>
                        <div class="col-lg-3">
                            <label for="kredit" class="form-label">Kredit <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-lg" required id="kredit" name="kredit" placeholder="Masukan nominal kredit" data-inputmask="'alias': 'decimal', 'groupSeparator': ','">
                        </div>
                        <div class="col-lg-3 d-grid">
                            <label class="form-label">&nbsp;</label>
                            <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Tambahkan</button>
                        </div>
                    </div>
                </form>
                <div class="mt-4">
                    <table class="table table-bordered table-hover table-sm">
                        <thead>
                            <tr class="h4">
                                <td>Akun</td>
                                <td align="center" class="w-4">Debet</td>
                                <td align="center" class="w-4">Kredit</td>
                                <td align="center" class="w-1">Aksi</td>
                            </tr>
                        </thead>
                        <tbody id="cartItemsBody">
                        </tbody>
                        <tfoot>
                            <tr class="h3">
                                <td align="right">TOTAL</td>
                                <td align="right" class="w-4" id="txtTotalDebet">0</td>
                                <td align="right" class="w-4" id="txtTotalKredit">0</td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                    <form action="javascript:void(0)" id="formJurnal" method="post">
                        <div class="form-group">
                            <label for="keterangan" class="form-label">Keterangan <span class="text-danger">*</span></label>
                            <textarea required name="keterangan" id="keterangan" class="form-control" rows="3" placeholder="Masukan keterangan penjurnalan."></textarea>
                        </div>
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-lg btn-primary my-2"><i class="fa fa-save"></i> SIMPAN</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@push('vendor-js')
<script src="{{ asset('js/datatables.js') }}"></script>
<script src="{{ asset('js/jquery-confirm.min.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.blockUI/2.70/jquery.blockUI.min.js"></script>
@endpush
@push('js')<script>function data(a="semua",t="{{ enkrip('kosong') }}"){$("#txtTotal").html(0),totalTransaksi=0,penjualan=[],$("table#data").DataTable({ordering:!1,responsive:!0,bAutoWidth:!1,lengthMenu:[25,50,75,100],language:{url:"{{ asset('js/id.json') }}"},bDestroy:!0,processing:!0,ajax:{url:"{{ route($attribute['link'].'data') }}",type:"POST",data:{type:a,tanggal:t},error:function(a,t,e){alertApp("error","Data piutang tidak bisa ditampilkan.")}},columns:[{className:"w-1 text-center",data:"no"},{className:"w-8",data:"pengguna"},{className:"w-8",data:"pembeli"},{className:"w-6",data:"tanggal"},{className:"w-1 text-end",data:"total"},],initComplete:function(a,t){totalTransaksi=t.total,penjualan=t.penjualan,$("#txtTotal").html(currency(t.total)),$("#textTanggalAwal").html(t.awal),$("#textTanggalAkhir").html(t.akhir)}})}$(function(){var a=new Choices("select#akun",{placeholder:!0,placeholderValue:"Pilih salah satu",searchPlaceholderValue:"Masukan nama akun, minimal 2 huruf",noChoicesText:"Tidak ada pilihan",itemSelectText:"Tekan untuk memilih",noResultsText:"Tidak ada pilihan",searchResultLimit:10,removeItems:!0});a.passedElement.element.addEventListener("search",function(t){t.detail.value.length>1&&$.ajax({url:"{{ route('akun.data') }}",type:"POST",data:{nama:t.detail.value},dataType:"JSON",success:function(t){a.clearChoices(),a.setChoices(t)},error:function(a,t,e){alertApp("error","Akun tidak ditemukan")}})});let t=$("#itemForm"),e=$("#formJurnal"),n=$("#akun"),r=$("#debet"),l=$("#kredit");var i=0,s=0;let u=[];function o(){let a=$("#cartItemsBody");a.empty(),i=0,s=0,u.forEach(function(t,e){let n=$("<tr></tr>");n.html(`<td>${t.akun}</td><td align="right">${t.namaDebet}</td><td align="right">${t.namaKredit}</td><td align="center"><button onclick="removeItem(${e})" class="btn btn-danger btn-sm"><i class="fas fa-trash-alt"></i></button></td>`),a.append(n),i+=t.debet,s+=t.kredit}),$("#txtTotalDebet").html(currency(i)),$("#txtTotalKredit").html(currency(s))}t.submit(function(a){var t;a.preventDefault();let e=n.val().trim();if(t=e,u.some(function(a){return a.idAkun===t}))alertApp("error","Akun sudah terdaftar");else{let i=$("#akun option:selected").text(),s=r.val().trim(),d=l.val().trim(),m=parseFloat(s.replace(/,/g,"")),c=parseFloat(d.replace(/,/g,""));totalTransaksi>0&&e&&i&&s&&m>=0&&m<=totalTransaksi&&d&&c>=0&&c<=totalTransaksi?(u.push({idAkun:e,akun:i,namaDebet:s,debet:m,namaKredit:d,kredit:c}),r.val(""),l.val(""),o()):alertApp("error","Nominal Debet dan/atau Kredit tidak sesuai")}}),e.submit(function(a){a.preventDefault(),loading();let t=$("#keterangan").val().trim();u.length>1&&t&&totalTransaksi>0&&i>0&&s>0&&i==s?$.ajax({url:"{{ route('jurnal.penjualan.lunas.simpan') }}",type:"POST",data:{data:u,penjualan:penjualan,keterangan:t},dataType:"JSON",success:function(a){a.status?(u=[],o(),data(),$("#keterangan").val(null),alertApp("success",a.message,!0)):alertApp("error",a.message,!0)},error:function(a,t,e){alertApp("error",e,!0)}}):alertApp("error","Form tidak memenuhi syarat",!0)}),window.removeItem=function(a){$.confirm({icon:"fa fa-warning",title:"PERINGATAN!",content:"Apakah anda yakin ingin menghapus data ini?",type:"red",autoClose:"tutup|5000",buttons:{ya:{text:"Ya",btnClass:"btn-red",action:function(){u.splice(a,1),o(),alertApp("success","Data berhasil dihapus")}},tutup:{text:"Tutup"}}})},flatpickr("#tanggalTransaksi",{mode:"range",dateFormat:"d-m-Y",maxDate:"{{ $tanggalAkhir }}"}),data()}),$(document).on("click","#tampilSemua",function(){data("semua","{{ enkrip('kosong') }}")}),$("#formTanggalTransaksi").submit(function(){var a=$("#tanggalTransaksi").val();a?data("tanggal",a):alertApp("error","Pilih tanggal transaksi.")});</script>@endpush