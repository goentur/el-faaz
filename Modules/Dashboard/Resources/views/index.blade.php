@extends('layouts.app')

@section('content')
<div class="row mb-2 mb-xl-3">
    <div class="col-auto d-none d-sm-block">
        <h3><i class="fa fa-dashboard"></i> DASHBOARD</h3>
    </div>
</div>
<div class="row">
    @for ($i = 1; $i <= 4; $i++) <div class="col-sm-3">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col mt-0">
                        <h5 class="card-title">Sales</h5>
                    </div>

                    <div class="col-auto">
                        <div class="stat text-primary">
                            <i class="align-middle" data-feather="truck"></i>
                        </div>
                    </div>
                </div>
                <h1 class="mt-1 mb-3">2.382</h1>
                <div class="mb-0">
                    <span class="badge badge-primary-light"> <i class="mdi mdi-arrow-bottom-right"></i> -3.65% </span>
                    <span class="text-muted">Since last week</span>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col mt-0">
                        <h5 class="card-title">Visitors</h5>
                    </div>

                    <div class="col-auto">
                        <div class="stat text-primary">
                            <i class="align-middle" data-feather="users"></i>
                        </div>
                    </div>
                </div>
                <h1 class="mt-1 mb-3">14.212</h1>
                <div class="mb-0">
                    <span class="badge badge-success-light"> <i class="mdi mdi-arrow-bottom-right"></i> 5.25% </span>
                    <span class="text-muted">Since last week</span>
                </div>
            </div>
        </div>
    </div>
    @endfor
<div class="col-12">
    <div class="card">
        <div class="card-body">
            <table id="data-user" class="table table-sm table-bordered table-hover dt-responsive">
                <thead>
                    <tr>
                        <th class="mid p-1 text-center w-1">NO</th>
                        <th>EMAIL</th>
                        <th>NAME</th>
                        <th class="mid p-1 text-center w-1">AKSI</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
</div>
@endsection
@push('vendor-js')@endpush
@push('js')@endpush