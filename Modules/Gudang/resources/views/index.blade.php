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
            <div class="card-header">
                <div class="row">
                    <div class="col-lg-3">
                        <label for="pemasok" class="form-label">Pemasok :</label>
                        <select required name="pemasok" id="pemasok" class="form-control">
                            <option value="">Pilih salah satu</option>
                            <option value="{{ enkrip('semua') }}">Tampilkan semua</option>
                            @foreach ($pemasoks as $pemasok)
                            <option value="{{ enkrip($pemasok->id) }}">{{ $pemasok->nama }}</option>
                            @endforeach
                        </select>
                    </div>
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
@push('js')<script>function dataGudang(a="{{ enkrip('0') }}"){$("table#dataGudang").DataTable({ordering:!1,responsive:!0,bAutoWidth:!1,lengthMenu:[25,50,75,100],language:{url:"{{ asset('js/id.json') }}"},bDestroy:!0,processing:!0,ajax:{url:"{{ route('gudang.data') }}",type:"POST",data:{pemasok:a},error:function(a,t,e){alertApp("error","Data gudang tidak bisa dimuat, silahkan pilih pemasok yang lain.")}},columns:[{className:"w-1 text-center top",data:"no"},{className:"w-10 text-center",data:"foto",render:function(a,t,e){return a.status?'<a href="'+a.url+'" target="popup" onclick="window.open(`'+a.url+"`,`"+a.nama+'`,`width=800,height=600`)"><img src="'+a.url+'" alt="'+a.nama+'" class="img-fluid" width="85%"></a>':a.url}},{className:"w-10 top",data:"pemasok"},{className:"top",data:"nama"},{className:"w-10 top",data:"satuan"},{className:"w-10 top",data:"ukuran"},{className:"w-1 text-center top",data:"stok"},{className:"w-1 text-end top",data:"harga"},{className:"w-1 text-end top",data:"jumlah"},{className:"w-1 top",data:"aksi",render:function(a,t,e){return'<div class="btn-group"><a href="'+a.link+'" class="btn btn-sm btn-icon waves-effect waves-light btn-success"><i class="fa fa-pencil-alt"></i></a><button class="btn btn-sm btn-icon waves-effect waves-light btn-danger hapus" data-id="'+a.id+'"><i class="fas fa-trash-alt"></i></button></div>'}},]})}$(function(){dataGudang(),new Choices(document.querySelector("select#pemasok")),$("select#pemasok").change(function(){dataGudang($(this).val())})}),$(document).on("click",".hapus",function(){var a=$(this).data("id");$.confirm({icon:"fa fa-warning",title:"PERINGATAN!",content:"Apakah anda yakin ingin menghapus data ini?",type:"red",autoClose:"tutup|5000",buttons:{ya:{text:"Ya",btnClass:"btn-red",action:function(){$.ajax({url:"{{ route($attribute['link'].'destroy',csrf_token()) }}",type:"POST",data:{_method:"DELETE",id:a},dataType:"JSON",success:function(a){a.status?(alertApp("success",a.message),$("table#dataGudang").DataTable().ajax.reload()):alertApp("error",a.message)},error:function(a,t,e){alertApp("error",e)}})}},tutup:{text:"Tutup"}}})}),$("img").lazy({effect:"fadeIn",effectTime:2e3,threshold:0});</script>@endpush