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
                            <label for="kode" class="form-label">Kode <span class="text-danger">*</span></label>
                            <input required type="text" class="form-control @error('kode')is-invalid @enderror"{{ isset($data)?' disabled':'' }} value="{{ isset($data)?$data->kode:old('kode') }}" id="kode" name="kode" placeholder="Masukan kode" data-inputmask="'alias': 'numeric'">
                            @error('kode')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                        <div class="col-lg-6">
                            <label for="nama" class="form-label">Nama <span class="text-danger">*</span></label>
                            <input required type="text" class="form-control @error('nama')is-invalid @enderror" value="{{ isset($data)?$data->nama:old('nama') }}" id="nama" name="nama" placeholder="Masukan nama">
                            @error('nama')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                        <div class="col-lg-6">
                            <label for="debet" class="form-label">Debet <span class="text-danger">*</span></label>
                            <input required type="text" class="form-control @error('debet')is-invalid @enderror"@role('developer')  @else readonly @endrole value="{{ isset($data)?$data->debet:old('debet') }}" id="debet" name="debet" placeholder="Masukan nominal debet" data-inputmask="'alias': 'decimal', 'groupSeparator': ','">
                            @error('debet')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                        <div class="col-lg-6">
                            <label for="kredit" class="form-label">Kredit <span class="text-danger">*</span></label>
                            <input required type="text" class="form-control @error('kredit')is-invalid @enderror"@role('developer')  @else readonly @endrole value="{{ isset($data)?$data->kredit:old('kredit') }}" id="kredit" name="kredit" placeholder="Masukan nominal kredit" data-inputmask="'alias': 'decimal', 'groupSeparator': ','">
                            @error('kredit')
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