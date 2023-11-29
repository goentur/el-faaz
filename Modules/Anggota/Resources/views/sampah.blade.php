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
                {{ $dataTable->table(['class' => 'table table-bordered table-hover table-sm']) }}
            </div>
        </div>
    </div>
</div>
@endsection
@push('vendor-js')
<script src="{{ asset('js/datatables.js') }}"></script>
<script src="{{ asset('js/jquery-confirm.min.js') }}"></script>
@endpush
@push('js'){{ $dataTable->scripts() }}<script>function peringatan(a,n,t){$.confirm({icon:"fa fa-warning",title:"PERINGATAN!",content:a,type:"red",autoClose:"tutup|5000",buttons:{ya:{text:"Ya",btnClass:"btn-red",action:function(){$.ajax({url:n,type:"POST",data:{id:t},dataType:"JSON",success:function(a){a.status?(alertApp("success",a.message),$("#dataTableBuilder").DataTable().ajax.reload()):alertApp("error",a.message)},error:function(a,n,t){alertApp("error",t)}})}},tutup:{text:"Tutup"}}})}$(document).on("click",".memulihkan",function(){peringatan("Apakah anda yakin ingin memulihkan data ini?","{{ route($attribute['linkSampah'].'memulihkan') }}",$(this).data("id"))}),$(document).on("click",".permanen",function(){peringatan("Apakah anda yakin ingin menghapus data ini secara permanen?","{{ route($attribute['linkSampah'].'permanen') }}",$(this).data("id"))});</script>@endpush