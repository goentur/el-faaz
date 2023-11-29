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
                        <div class="col-lg-3 mb-3">
                            <label for="pemasok" class="form-label">Pemasok <span class="text-danger">*</span></label>
                            <select required name="pemasok" id="pemasok" class="form-control">
                                <option value="">Pilih salah satu</option>
                                @foreach ($pemasoks as $pemasok)
                                <option value="{{ $pemasok->id }}" {{ isset($data)&&$data->pemasok_id==$pemasok->id?' selected':''}}{{old('pemasok')==$pemasok->id?' selected':'' }}>{{ $pemasok->nama }}</option>
                                @endforeach
                            </select>
                            @error('pemasok')
                            <strong class="text-danger text-validation">{{ $message }}</strong>
                            @enderror
                        </div>
                        <div class="col-lg-9 mb-3">
                            <label for="barang" class="form-label">Barang <span class="text-danger">*</span></label>
                            <select required name="barang" id="barang" class="form-control">
                                <option value="">Pilih salah satu</option>
                                @foreach ($barangs as $barang)
                                <option value="{{ $barang->id }}" {{ isset($data)&&$data->barang_id==$barang->id?' selected':''}}{{old('barang')==$barang->id?' selected':'' }}>{{ $barang->nama }} - {{ $barang->warna?$barang->warna->nama:'WARNA TIDAK ADA' }}</option>
                                @endforeach
                            </select>
                            @error('barang')
                            <strong class="text-danger text-validation">{{ $message }}</strong>
                            @enderror
                        </div>
                        <div class="col-lg-6">
                            <label for="satuan" class="form-label">Satuan <span class="text-danger">*</span></label>
                            <select required name="satuan" id="satuan" class="form-control">
                                <option value="">Pilih salah satu</option>
                                @foreach ($satuans as $satuan)
                                <option value="{{ $satuan->id }}" {{ isset($data)&&$data->satuan_id==$satuan->id?' selected':''}}{{old('satuan')==$satuan->id?' selected':'' }}>{{ $satuan->nama }}</option>
                                @endforeach
                            </select>
                            @error('satuan')
                            <strong class="text-danger text-validation">{{ $message }}</strong>
                            @enderror
                        </div>
                        <div class="col-lg-6">
                            <label for="ukuran" class="form-label">Ukuran <span class="text-danger">*</span></label>
                            <select required name="ukuran[]" id="ukuran" class="form-control" multiple>
                                <option value="">Pilih lebih dari satu</option>
                                @foreach ($ukurans as $ukuran)
                                <option value="{{ $ukuran->id }}"{{in_array($ukuran->id, old("ukuran") ?: []) ? ' selected': ''}}{{ isset($data)&&$data->ukuran->contains($ukuran->id)? ' selected': '' }}>{{ $ukuran->nama }}</option>
                                @endforeach
                            </select>
                            @error('ukuran')
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
@push('js')<script>$("#file-manager").filemanager("image"),$(function(){new Choices(document.querySelector("select#pemasok")),new Choices(document.querySelector("select#barang")),new Choices(document.querySelector("select#satuan")),new Choices(document.querySelector("select#ukuran"))});</script>@endpush