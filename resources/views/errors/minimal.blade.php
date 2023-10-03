<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    @include('layouts.partials.head')
    <script src="{{ asset('js/settings.js') }}"></script>
    <link href="{{ asset('css/custom.css') }}" rel="stylesheet">
</head>

<body data-theme="default" data-layout="fluid" data-sidebar-position="left" data-sidebar-layout="default">
	<main class="d-flex w-100 h-100">
		<div class="container d-flex flex-column">
			<div class="row vh-100">
				<div class="col-sm-10 col-md-8 col-lg-6 col-xl-5 mx-auto d-table h-100">
					<div class="d-table-cell align-middle">

						<div class="text-center">
							<p class="display-2 m-0">@yield('code') |</p>
							<p class="lead fw-normal m-0 mb-5">@yield('message')</p>
                            <div class="col-12 d-grid gap-2">
                                <a class="btn btn-primary btn-lg" href="{{ route('home.index') }}"><i class="fa fa-arrow-left"></i> Kembali</a>
                            </div>
						</div>

					</div>
				</div>
			</div>
		</div>
	</main>
    @include('layouts.partials.script')
</body>

</html>