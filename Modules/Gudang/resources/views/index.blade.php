@extends('layouts.app')

@push('vendor-css')
<link rel="stylesheet" href="{{ asset('css/jquery-confirm.min.css') }}">
@endpush
@section('content')
<div class="row mb-2 mb-xl-3">
    <div class="col-auto d-none d-sm-block">
        <h3 class="d-inline align-middle">Data {{ $attribute['title'] }}</h3>
    </div>
    <div class="col-auto ms-auto text-end mt-n1">
        @role('developer')
        <a href="{{ route($attribute['linkSampah'].'sampah') }}" class="btn btn-danger"><i class="fa fa-trash-alt"></i> DATA SAMPAH</a>
        @endrole
        <a href="{{ route($attribute['link'].'create') }}" class="btn btn-primary"><i class="fa fa-plus"></i> TAMBAH DATA</a>
    </div>
</div>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header m-0">
                <div class="row">
                    <div class="col-lg-4">
                        <label for="pemasok" class="form-label h4"><i class="fa fa-truck"></i> CARI PEMASOK :</label>
                        <select required name="pemasok" id="pemasok" class="form-control">
                            <option value="">Pilih salah satu</option>
                        </select>
                    </div>
                    <div class="col-lg-6">
                        <label for="pemasok" class="form-label h4">&nbsp;</label>
                        <h1>TOTAL : <span id="txtTotal">0</span></h1>
                    </div>
                    <div class="col-12">
                    <div class="alert alert-info alert-dismissible" role="alert">
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        <div class="alert-message">
                            <h4 class="alert-heading fw-bold">INFORMASI!</h4>
                            <ul>
                                <li><b>STOK</b> minus (-) bukan berarti kesalahan sistem. coba lihat pada riwayat barang apakah ada barang PreOrder atau tidak.</li>
                            </ul>
                        </div>
                    </div></div>
                </div>
            </div>
            <div class="card-body">
                <table id="dataGudang" class="table table-sm table-bordered table-hover dt-responsive">
                    <thead>
                        <tr>
                            <th>NO</th>
                            <th>FOTO</th>
                            <th>PEMASOK</th>
                            <th>NAMA</th>
                            <th>SATUAN</th>
                            <th>UKURAN</th>
                            <th>STOK</th>
                            <th>BELI</th>
                            <th>JUMLAH</th>
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.lazy/1.7.9/jquery.lazy.min.js"></script>
@endpush
@push('js')<script>function dataGudang(a){$("#txtTotal").html(0),$("table#dataGudang").DataTable({ordering:!1,responsive:!0,bAutoWidth:!1,lengthMenu:[25,50,75,100],language:{url:"{{ asset('js/id.json') }}"},bDestroy:!0,processing:!0,ajax:{url:"{{ route('gudang.data') }}",type:"POST",data:{pemasok:a},error:function(a,t,e){alertApp("error","Data gudang tidak bisa dimuat, silahkan pilih pemasok yang lain.")}},columns:[{className:"w-1 text-center top",data:"no"},{className:"w-10 text-center",data:"foto",render:function(a,t,e){return a.status?'<a href="'+a.url+'" target="popup" onclick="window.open(`'+a.url+"`,`"+a.nama+'`,`width=800,height=600`)"><img src="'+a.url+'" alt="'+a.nama+'" class="img-fluid" width="85%"></a>':a.url}},{className:"w-10 top",data:"pemasok"},{className:"top",data:"nama"},{className:"w-10 top",data:"satuan"},{className:"w-10 top",data:"ukuran"},{className:"w-1 text-center top",data:"stok"},{className:"w-1 text-end top",data:"harga"},{className:"w-1 text-end top",data:"jumlah"},{className:"w-1 top text-center",data:"aksi",render:function(a,t,e){return'<a href="'+a.ubah+'" class="btn btn-sm btn-success mb-1"><i class="fa fa-pencil-alt"></i></a><a href="'+a.detail+'" class="btn btn-sm btn-primary mb-1"><i class="fa fa-eye"></i></a> <button class="btn btn-sm btn-danger hapus" data-id="'+a.id+'"><i class="fas fa-trash-alt"></i></button>'}},],initComplete:function(a,t){$("#txtTotal").html(currency(t.total))}})}$(function(){var a=new Choices("select#pemasok",{placeholder:!0,placeholderValue:"Pilih salah satu",searchPlaceholderValue:"Masukan minimal 1 huruf",noChoicesText:"Tidak ada pilihan",itemSelectText:"Tekan untuk memilih",noResultsText:"Tidak ada pilihan",searchResultLimit:10,removeItems:!0});a.passedElement.element.addEventListener("search",function(t){$.ajax({url:"{{ route('pemasok.data') }}",type:"POST",data:{nama:t.detail.value},dataType:"JSON",success:function(t){a.clearChoices(),a.setChoices(t),a.setValue([{value:"{{ enkrip('semua') }}",label:"Tampilkan semua data gudang"}])},error:function(a,t,e){alertApp("error","Pemasok tidak ditemukan")}})})}),$(document).on("click",".hapus",function(){var a=$(this).data("id");$.confirm({icon:"fa fa-warning",title:"PERINGATAN!",content:"Apakah anda yakin ingin menghapus data ini?",type:"red",autoClose:"tutup|5000",buttons:{ya:{text:"Ya",btnClass:"btn-red",action:function(){$.ajax({url:"{{ route($attribute['link'].'destroy',csrf_token()) }}",type:"POST",data:{_method:"DELETE",id:a},dataType:"JSON",success:function(a){a.status?(alertApp("success",a.message),$("table#dataGudang").DataTable().ajax.reload()):alertApp("error",a.message)},error:function(a,t,e){alertApp("error",e)}})}},tutup:{text:"Tutup"}}})}),$("select#pemasok").change(function(){dataGudang($(this).val())}),$("img").lazy({effect:"fadeIn",effectTime:2e3,threshold:0});</script>@endpush
