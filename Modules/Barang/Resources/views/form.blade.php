@extends('layouts.app')

@section('content')
<div class="row mb-2 mb-xl-3">
    <div class="col-auto d-none d-sm-block">
        <h3 class="d-inline align-middle">Form {{ $attribute['title'] }}</h3>
    </div>
    <div class="col-auto ms-auto text-end mt-n1">
        <a href="{{ route($attribute['link'].'index') }}" class="btn btn-primary"><i class="fa fa-arrow-left"></i> KEMBALI DATA</a>
    </div>
</div>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">INFORMASI</h5>
                <h6 class="card-subtitle text-muted">Form yang bertanda (<span class="text-danger">*</span>) <b>wajib</b> diisi.</h6>
            </div>
            <div class="card-body">
                <form action="{{ isset($data)?route($attribute['link'].'update',enkrip($data->id)):route($attribute['link'].'store') }}" method="post">
                    @csrf
                    @isset($data)
                    @method('PUT')
                    @endisset
                    <div class="row mb-3">
                        <div class="col-lg-12">
                            <label for="nama" class="form-label">Nama <span class="text-danger">*</span></label>
                            <input required type="text" class="form-control @error('nama')is-invalid @enderror" value="{{ isset($data)?$data->nama:old('nama') }}" id="nama" name="nama" placeholder="Masukan nama">
                            @error('nama')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Simpan</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
@push('js')
<script src="{{ asset('vendor/laravel-filemanager/js/stand-alone-button.js') }}"></script>
<script>$("#file-manager").filemanager("image"),$(function(){new Choices(document.querySelector("select#warna"))});</script>@endpush