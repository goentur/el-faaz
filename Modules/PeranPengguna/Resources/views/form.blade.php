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
                        <div class="col-lg-6 mb-3">
                            <label for="name" class="form-label">Nama <span class="text-danger">*</span></label>
                            <input required type="text" class="form-control @error('name')is-invalid @enderror" value="{{ isset($data)?$data->name:old('name') }}" id="name" name="name" placeholder="Masukan nama">
                            @error('name')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                        <div class="col-lg-6 mb-3">
                            <label for="guard_name" class="form-label">Guard <span class="text-danger">*</span></label>
                            <input required type="text" class="form-control @error('guard_name')is-invalid @enderror" value="{{ isset($data)?$data->guard_name:old('guard_name') }}" id="guard_name" name="guard_name" placeholder="Masukan guard">
                            @error('guard_name')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                        <div class="col-12">
                            <label for="fitur" class="form-label">Fitur Aplikasi <span class="text-danger">*</span></label>
                            <div class="form-group">
                                @foreach ($permissions as $permission)
                                <label class="form-check form-check-inline">
                                    <input type="checkbox" class="form-check-input" {{ isset($data)?$data->hasPermissionTo($permission->name)?' checked':'':'' }} id="{{ $permission->id }}" name="fitur[]" value="{{ $permission->name }}">
                                    <span class="@error('fitur')text-danger @enderror form-check-label">{{ $permission->name }}</span>
                                </label>
                                @endforeach
                            </div>
                            @error('fitur')
                            <strong class="text-danger">{{ $message }}</strong>
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