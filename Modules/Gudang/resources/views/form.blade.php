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
                            <label for="barang" class="form-label">Barang<span class="text-danger">*</span></label>
                            <select required name="barang" id="barang" class="form-control">
                                <option value="">Pilih salah satu</option>
                            </select>
                            @error('barang')
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
@push('js')<script>$(function(){new Choices(document.querySelector("select#pemasok"));var e=new Choices("select#barang",{placeholder:!0,placeholderValue:"Pilih salah satu",searchPlaceholderValue:"Masukan nama barang, minimal 3 huruf",noChoicesText:"Tidak ada pilihan",itemSelectText:"Tekan untuk memilih",noResultsText:"Tidak ada pilihan",searchResultLimit:10,removeItems:!0});e.passedElement.element.addEventListener("search",function(a){a.detail.value.length>2&&a.detail.value.length%2&&$.ajax({url:"{{ route('dagangan.data') }}",type:"POST",data:{nama:a.detail.value},dataType:"JSON",success:function(a){e.clearChoices(),e.setChoices(a)},error:function(e,a,t){alertApp("error","Barang Dagangan tidak ditemukan")}})})});</script>@endpush