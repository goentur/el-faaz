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
                        <div class="col-lg-9 mb-3">
                            <label for="nama" class="form-label">Nama <span class="text-danger">*</span></label>
                            <input required type="text" class="form-control @error('nama')is-invalid @enderror" value="{{ isset($data)?$data->nama:old('nama') }}" id="nama" name="nama" placeholder="Masukan nama">
                            @error('nama')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                        <div class="col-lg-3 mb-3">
                            <label for="warna" class="form-label">Warna <span class="text-danger">*</span></label>
                            <select required name="warna" id="warna" class="form-control">
                                <option value="">Pilih salah satu</option>
                                @foreach ($warnas as $warna)
                                <option value="{{ $warna->id }}" {{ isset($data)&&$data->warna_id==$warna->id?' selected':''}}{{old('warna')==$warna->id?' selected':'' }}>{{ $warna->nama }}</option>
                                @endforeach
                            </select>
                            @error('warna')
                            <strong class="text-danger text-validation">{{ $message }}</strong>
                            @enderror
                        </div>
                        <div class="col-lg-12">
                            <label for="file-manager" class="form-label">Foto <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <a href="javascript:void(0)" id="file-manager" data-input="foto" data-preview="holder" class="btn btn-primary">
                                    <i class="fa-solid fa-folder-open"></i> Pilih Foto
                                </a>
                                <input id="foto" class="form-control" required type="text" readonly value="{{ isset($data)?asset($data->foto):old('foto') }}" name="foto">
                                @if (isset($data)&&$data->foto!==null)
                                <a href="{{ asset($data->foto) }}" class="btn btn-primary" target="popup" onclick="window.open('{{ asset($data->foto) }}','{{ $data->nama }}','width=800,height=600')"><i class="fa-regular fa-image"></i> Lihat Foto</a>
                                @endif
                            </div>
                            @error('foto')
                            <strong class="text-danger text-validation">{{ $message }}</strong>
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