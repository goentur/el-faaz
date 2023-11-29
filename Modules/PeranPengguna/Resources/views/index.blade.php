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
        <a href="{{ route($attribute['link'].'create') }}" class="btn btn-primary"><i class="fa fa-plus"></i> TAMBAH DATA</a>
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
@push('js'){{ $dataTable->scripts() }}<script>$(document).on("click",".hapus",function(){var t=$(this).data("id");$.confirm({icon:"fa fa-warning",title:"PERINGATAN!",content:"Apakah anda yakin ingin menghapus data ini?",type:"red",autoClose:"tutup|5000",buttons:{ya:{text:"Ya",btnClass:"btn-red",action:function(){$.ajax({url:"{{ route($attribute['link'].'destroy',csrf_token()) }}",type:"POST",data:{_method:"DELETE",id:t},dataType:"JSON",success:function(t){t.status?(alertApp("success",t.message),$("#dataTableBuilder").DataTable().ajax.reload()):alertApp("error",t.message)},error:function(t,a,e){alertApp("error",e)}})}},tutup:{text:"Tutup"}}})});</script>@endpush