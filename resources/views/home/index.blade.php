@extends('layouts.app')

@section('content')
<div class="col-12">
    <div class="card">
        <div class="card-body text-center">
            <h1>SELAMAT DATANG DI {{ config('app.name') }}</h1>
            <h3>{{ config('app.copyright') }}</h3>
        </div>
    </div>
</div>
@endsection
