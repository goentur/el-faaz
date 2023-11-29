@extends('layouts.app')

@push('vendor-css')
<link rel="stylesheet" href="{{ asset('css/jquery-confirm.min.css') }}">
@endpush
@section('content')
<div class="row mb-2 mb-xl-3">
    <div class="col-auto d-none d-sm-block">
        <h3 class="d-inline align-middle">Data sampah {{ $attribute['title'] }}</h3>
    </div>
    <div class="col-auto ms-auto text-end mt-n1">
        <a href="{{ route($attribute['link'].'index') }}" class="btn btn-primary"><i class="fa fa-arrow-left"></i> KEMBALI DATA</a>
    </div>
</div>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <table id="sampahDataGudang" class="table table-sm table-bordered table-hover dt-responsive">
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
@push('js')<script>function peringatan(a,t,n){$.confirm({icon:"fa fa-warning",title:"PERINGATAN!",content:a,type:"red",autoClose:"tutup|5000",buttons:{ya:{text:"Ya",btnClass:"btn-red",action:function(){$.ajax({url:t,type:"POST",data:{id:n},dataType:"JSON",success:function(a){a.status?(alertApp("success",a.message),$("table#sampahDataGudang").DataTable().ajax.reload()):alertApp("error",a.message)},error:function(a,t,n){alertApp("error",n)}})}},tutup:{text:"Tutup"}}})}function sampahDataGudang(){$("table#sampahDataGudang").DataTable({ordering:!1,responsive:!0,bAutoWidth:!1,lengthMenu:[25,50,75,100],language:{url:"{{ asset('js/id.json') }}"},bDestroy:!0,processing:!0,ajax:{url:"{{ route('gudang.sampah.data') }}",type:"POST"},columns:[{className:"w-1 text-center top",data:"no"},{className:"w-10 text-center",data:"foto",render:function(a,t,n){return a.status?'<a href="'+a.url+'" target="popup" onclick="window.open(`'+a.url+"`,`"+a.nama+'`,`width=800,height=600`)"><img src="'+a.url+'" alt="'+a.nama+'" class="img-fluid" width="85%"></a>':a.url}},{className:"w-10 top",data:"pemasok"},{className:"top",data:"nama"},{className:"w-10 top",data:"satuan"},{className:"w-10 top",data:"ukuran"},{className:"w-1 text-center top",data:"stok"},{className:"w-1 text-end top",data:"harga"},{className:"w-1 text-end top",data:"jumlah"},{className:"w-1 top",data:"aksi",render:function(a,t,n){return'<div class="btn-group"><button class="btn btn-sm btn-icon waves-effect waves-light btn-success memulihkan" data-id="'+a.id+'"><i class="fas fa-recycle"></i></button><button class="btn btn-sm btn-icon waves-effect waves-light btn-danger permanen" data-id="'+a.id+'"><i class="fas fa-trash-alt"></i></button></div>'}},]})}$(function(){sampahDataGudang()}),$(document).on("click",".memulihkan",function(){peringatan("Apakah anda yakin ingin memulihkan data ini?","{{ route($attribute['linkSampah'].'memulihkan') }}",$(this).data("id"))}),$(document).on("click",".permanen",function(){peringatan("Apakah anda yakin ingin menghapus data ini secara permanen?","{{ route($attribute['linkSampah'].'permanen') }}",$(this).data("id"))}),$("img").lazy({effect:"fadeIn",effectTime:2e3,threshold:0});</script>@endpush