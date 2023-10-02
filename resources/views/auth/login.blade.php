@extends('auth.app')
@push('css')
<style>
    body {
        opacity: 0;
    }
    body::-webkit-scrollbar {
        width: 0px
    }
</style>
@endpush
@section('content')
<form method="POST" action="{{ route('login') }}">
    @csrf
    <div class="mb-3">
        <label class="form-label fw-bold">Email</label>
        <input class="form-control form-control-lg @error('email') is-invalid @enderror" required type="email" id="email" name="email" value="{{ old('email') }}" placeholder="Masukan email anda" autofocus />
        @error('email')
        <span class="invalid-feedback" role="alert">
            <strong>{{ $message }}</strong>
        </span>
        @enderror
    </div>
    <div class="mb-3">
        <label class="form-label fw-bold">Password</label>
        <input class="form-control form-control-lg @error('password') is-invalid @enderror" required type="password" id="password" name="password" placeholder="Masukan password anda" />
        @error('password')
        <span class="invalid-feedback" role="alert">
            <strong>{{ $message }}</strong>
        </span>
        @enderror
        <small class="float-end">
            <a href="javascript:void(0)">Lupa password?</a>
        </small>
    </div>
    <div class="mb-3">
        <label class="form-label fw-bold">Captcha</label>
        <div class="input-group">
            <div class="input-group-prepend">
                {!! captcha_img('flat') !!}
            </div>
            <input class="form-control form-control-lg @error('captcha') is-invalid @enderror" autocomplete="off" minlength="6" maxlength="6" required type="text" id="captcha" name="captcha" placeholder="Masukan captcha" />
            @error('captcha')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
        </div>
    </div>
    <div>
        <label class="form-check">
            <input class="form-check-input" type="checkbox" id="remember" name="remember"{{ old('remember') ? ' checked' : '' }}>
            <span class="form-check-label">Remember me</span>
        </label>
    </div>
    <div class="text-center mt-3">
        <button type="submit" class="btn btn-lg btn-primary"><i class="fa fa-sign-in-alt"></i> Masuk</button>
    </div>
</form>
@endsection