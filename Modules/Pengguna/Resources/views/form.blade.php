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
                    <div class="row">
                        <div class="col-lg-4 mb-3">
                            <label for="peran" class="form-label">Peran <span class="text-danger">*</span></label>
                            <select required name="peran" id="peran" class="form-control">
                                <option value="">Pilih salah satu</option>
                                @foreach ($roles as $role)
                                <option value="{{ $role->name }}"{{ isset($data)?$data->hasRole($role->name)?' selected':'':'' }}>{{ $role->name }}</option>
                                @endforeach
                            </select>
                            @error('peran')
                            <strong class="text-danger text-validation">{{ $message }}</strong>
                            @enderror
                        </div>
                        <div class="col-lg-4 mb-3">
                            <label for="name" class="form-label">Nama <span class="text-danger">*</span></label>
                            <input required type="text" class="form-control @error('name')is-invalid @enderror" value="{{ isset($data)?$data->name:old('name') }}" id="name" name="name" placeholder="Masukan nama">
                            @error('name')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                        <div class="col-lg-4 mb-3">
                            <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                            <input required type="text" {{ isset($data)?' disabled':'' }} class="form-control @error('email')is-invalid @enderror" value="{{ isset($data)?$data->email:old('email') }}" id="email" name="email" placeholder="Masukan email">
                            @error('email')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                        @if (!isset($data))
                        <div class="col-lg-6 mb-3">
                            <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                            <input required type="password" class="form-control @error('password')is-invalid @enderror" id="password" name="password" placeholder="Masukan password">
                            @error('password')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                        <div class="col-lg-6 mb-3">
                            <label for="password" class="form-label">Ulangi Password <span class="text-danger">*</span></label>
                            <input required type="password" class="form-control @error('password')is-invalid @enderror" id="password" name="password_confirmation" placeholder="Ulangi password">
                        </div>
                        @endif
                        <div class="col-lg-6 mb-3">
                            <label for="zona_waktu" class="form-label">Zona Waktu <span class="text-danger">*</span></label>
                            <select required name="zona_waktu" id="zona_waktu" class="form-control">
                                <option value="">Pilih salah satu</option>
                                @foreach ($zonaWaktus as $zonaWaktu)
                                <option value="{{ $zonaWaktu->id }}" {{ isset($data)&&$data->zona_waktu_id==$zonaWaktu->id?' selected':''}}{{old('zonaWaktu')==$zonaWaktu->id?' selected':'' }}>{{ $zonaWaktu->nama }}</option>
                                @endforeach
                            </select>
                            @error('zona_waktu')
                            <strong class="text-danger text-validation">{{ $message }}</strong>
                            @enderror
                        </div>
                        <div class="col-lg-6 mb-3">
                            <label for="kode_printer" class="form-label">Kode Printer <span class="text-danger">*</span> </label>
                            <input required type="text" class="form-control @error('kode_printer')is-invalid @enderror" value="{{ isset($data)?$data->kode_printer:old('kode_printer') }}" id="kode_printer" name="kode_printer" placeholder="Masukan kode printer" data-inputmask="'alias': 'numeric'">
                            @error('kode_printer')
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
@push('js')<script>$(function() {new Choices(document.querySelector("#peran"));new Choices(document.querySelector("#zona_waktu"));});</script>@endpush