<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
     @include('layouts.partials.head')
</head>

<body data-theme="default" data-layout="fluid" data-sidebar-position="left" data-sidebar-layout="default">
     <main class="d-flex w-100 h-100">
          <div class="container d-flex flex-column">
               <div class="row vh-100">
                    <div class="col-sm-10 col-md-8 col-lg-6 mx-auto d-table h-100">
                         <div class="d-table-cell align-middle">
                              <div class="text-center mt-4">
                                   <h1 class="h2">{{ config('app.name') }}</h1>
                                   <p class="lead">{{ config('app.copyright') }}</p>
                              </div>
                              <div class="card">
                                   <div class="card-body">
                                        <div class="m-sm-4">
                                             <div class="text-center">
                                                  <img src="{{ asset('img/icons/logo.png') }}" alt="Logo app" height="100px" />
                                             </div>
                                             @yield('content')
                                        </div>
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