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
                        <div class="col-lg-6 mb-3">
                            <label for="nama" class="form-label">Nama <span class="text-danger">*</span></label>
                            <input required type="text" class="form-control @error('nama')is-invalid @enderror" value="{{ isset($data)?$data->nama:old('nama') }}" id="nama" name="nama" placeholder="Masukan nama">
                            @error('nama')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                        <div class="col-lg-3 mb-3">
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
                        <div class="col-lg-3 mb-3">
                            <label for="stok" class="form-label">Stok <span class="text-danger">*</span></label>
                            <input required type="text" class="form-control @error('stok')is-invalid @enderror"{{ isset($data)?' disabled':'' }} value="{{ isset($data)?$data->stok:old('stok') }}" id="stok" name="stok" placeholder="Masukan stok" data-inputmask="'alias': 'numeric'">
                            @error('stok')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                        <div class="col-lg-3 mb-3">
                            <label for="hpp" class="form-label">HPP <span class="text-danger">*</span></label>
                            <input required type="text" class="form-control @error('hpp')is-invalid @enderror" value="{{ isset($data)?$data->hpp:old('hpp') }}" id="hpp" name="hpp" placeholder="Masukan nominal hpp" data-inputmask="'alias': 'decimal', 'groupSeparator': ','">
                            @error('hpp')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                        <div class="col-lg-3 mb-3">
                            <label for="harga_jual" class="form-label">Harga Jual <span class="text-danger">*</span></label>
                            <input required type="text" class="form-control @error('harga_jual')is-invalid @enderror" value="{{ isset($data)?$data->harga_jual:old('harga_jual') }}" id="harga_jual" name="harga_jual" placeholder="Masukan nominal harga jual" data-inputmask="'alias': 'decimal', 'groupSeparator': ','">
                            @error('harga_jual')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                        <div class="col-lg-3 mb-3">
                            <label for="harga_anggota" class="form-label">Harga Anggota <span class="text-danger">*</span></label>
                            <input required type="text" class="form-control @error('harga_anggota')is-invalid @enderror" value="{{ isset($data)?$data->harga_anggota:old('harga_anggota') }}" id="harga_anggota" name="harga_anggota" placeholder="Masukan nominal harga anggota" data-inputmask="'alias': 'decimal', 'groupSeparator': ','">
                            @error('harga_anggota')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                        <div class="col-lg-12">
                            <label for="file-manager" class="form-label">Foto <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <a id="file-manager" data-input="foto" data-preview="holder" class="btn btn-primary">
                                    <i class="fa-solid fa-folder-open"></i> Pilih Foto
                                </a>
                                <input id="foto" class="form-control" type="text" readonly value="{{ isset($data)?$data->foto:old('foto') }}" name="foto">
                                @if (isset($data)&&$data->foto!==null)
                                <a href="{{ $data->foto }}" class="btn btn-primary" target="popup" onclick="window.open('{{ $data->foto }}','{{ $data->nama }}','width=800,height=600')"><i class="fa-regular fa-image"></i> Lihat Foto</a>
                                @endif
                            </div>
                            <img id="holder" style="margin-top:15px;max-height:100px;">
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
<script>
    $('#file-manager').filemanager('image');
    $(function() {
        new Choices(document.querySelector("select#pemasok"));
        new Choices(document.querySelector("select#satuan"));
    });
</script>
@endpush