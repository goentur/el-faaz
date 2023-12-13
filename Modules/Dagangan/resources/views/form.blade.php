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
                        <div class="col-lg-12 mb-3">
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
                        <div class="col-lg-6 mb-3">
                            <label for="barang" class="form-label">Barang <span class="text-danger">*</span></label>
                            <select required name="barang" id="barang" class="form-control">
                                <option value="">Pilih salah satu</option>
                                @foreach ($barangs as $barang)
                                <option value="{{ $barang->id }}" {{ isset($data)&&$data->barang_id==$barang->id?' selected':''}}{{old('barang')==$barang->id?' selected':'' }}>{{ $barang->nama }}</option>
                                @endforeach
                            </select>
                            @error('barang')
                            <strong class="text-danger text-validation">{{ $message }}</strong>
                            @enderror
                        </div>
                        <div class="col-lg-6 mb-3">
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
                        <div class="col-lg-6 mb-3">
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
                        <div class="col-lg-6 mb-3">
                            <label for="ukuran" class="form-label">Ukuran <span class="text-danger">*</span></label>
                            <select required name="ukuran[]" id="ukuran" class="form-control" multiple>
                                <option value="">Pilih lebih dari satu</option>
                                @foreach ($ukurans as $ukuran)
                                <option value="{{ $ukuran->id }}" {{in_array($ukuran->id, old("ukuran") ?: []) ? ' selected': ''}}{{ isset($data)&&$data->ukuran->contains($ukuran->id)? ' selected': '' }}>{{ $ukuran->nama }}</option>
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
@push('vendor-js')
<script src="{{ asset('vendor/laravel-filemanager/js/stand-alone-button.js') }}"></script>
@endpush
@push('js')<script>$("#file-manager").filemanager("image"),$(function(){new Choices(document.querySelector("select#barang")),new Choices(document.querySelector("select#satuan")),new Choices(document.querySelector("select#warna")),new Choices(document.querySelector("select#ukuran"))});</script>@endpush