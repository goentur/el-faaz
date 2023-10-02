<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
     @include('layouts.partials.head')
     <script src="{{ asset('js/settings.js') }}"></script>
     <link href="{{ asset('css/custom.css') }}" rel="stylesheet">
</head>

<body data-theme="default" data-layout="fluid" data-sidebar-position="left" data-sidebar-layout="default">
     <div class="wrapper">
          <nav id="sidebar" class="sidebar js-sidebar{{ auth()->user()->sidebar==='h'?' collapsed':'' }}">
               <div class="sidebar-content js-simplebar">
                    <a class="sidebar-brand" href="javscript:void(0)">
                         <span class="sidebar-brand-text align-middle">
                              {{ config('app.name') }}
                         </span>
                         <svg class="sidebar-brand-icon align-middle" width="32px" height="32px" viewBox="0 0 24 24" fill="none" stroke="#FFFFFF" stroke-width="1.5" stroke-linecap="square" stroke-linejoin="miter" color="#FFFFFF" style="margin-left: -3px">
                              <path d="M12 4L20 8.00004L12 12L4 8.00004L12 4Z"></path>
                              <path d="M20 12L12 16L4 12"></path>
                              <path d="M20 16L12 20L4 16"></path>
                         </svg>
                    </a>

                    <div class="sidebar-user">
                         <div class="d-flex justify-content-center">
                              <div class="flex-shrink-0">
                                   <img src="{{ Avatar::create(auth()->user()->name)->setShape('square')->toBase64() }}" class="avatar img-fluid rounded me-1" alt="{{ auth()->user()->name }}" />
                              </div>
                              <div class="flex-grow-1 ps-2">
                                   <a class="sidebar-user-title dropdown-toggle" href="javascript:void(0)" data-bs-toggle="dropdown">{{ strlen(auth()->user()->name)>= 15 ? substr(auth()->user()->name,0,15).'...':auth()->user()->name }} </a>
                                   <div class="dropdown-menu dropdown-menu-start">
                                        @include('layouts.partials.dropdown')
                                        <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault();document.getElementById('logout-form').submit();"><i class="align-middle me-1" data-feather="power"></i> Log out</a>
                                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
                                   </div>

                                   <div class="sidebar-user-subtitle">{{ strtoupper(auth()->user()->getRoleNames()[0]) }}</div>
                              </div>
                         </div>
                    </div>
                    @include('layouts.partials.menus')
               </div>
          </nav>

          <div class="main">
               <nav class="navbar navbar-expand navbar-light navbar-bg">
                    <a class="sidebar-toggle js-sidebar-toggle" id="settings-side-bar">
                         <i class="hamburger align-self-center"></i>
                    </a>

                    <form class="d-none d-sm-inline-block">
                         <div class="input-group input-group-navbar">
                              <input type="text" class="form-control" placeholder="Cari" aria-label="Cari">
                              <button class="btn" type="button">
                                   <i class="fa fa-search"></i>
                              </button>
                         </div>
                    </form>

                    <div class="navbar-collapse collapse">
                         <ul class="navbar-nav navbar-align">
                              <li class="nav-item dropdown">
                                   <a class="nav-icon dropdown-toggle" href="javscript:void(0)" id="alertsDropdown" data-bs-toggle="dropdown">
                                        <div class="position-relative">
                                             <i class="align-middle" data-feather="bell"></i>
                                             <span class="indicator">1</span>
                                        </div>
                                   </a>
                                   <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end py-0" aria-labelledby="alertsDropdown">
                                        <div class="dropdown-menu-header">
                                             1 New Notifications
                                        </div>
                                        <div class="list-group">
                                             <a href="javascript:void(0)" class="list-group-item">
                                                  <div class="row g-0 align-items-center">
                                                       <div class="col-2">
                                                            <i class="text-danger" data-feather="alert-circle"></i>
                                                       </div>
                                                       <div class="col-10">
                                                            <div class="text-dark">Update completed</div>
                                                            <div class="text-muted small mt-1">Restart server 12 to complete the update.</div>
                                                            <div class="text-muted small mt-1">30m ago</div>
                                                       </div>
                                                  </div>
                                             </a>
                                        </div>
                                        <div class="dropdown-menu-footer">
                                             <a href="javascript:void(0)" class="text-muted">Show all notifications</a>
                                        </div>
                                   </div>
                              </li>
                              <li class="nav-item">
                                   <a class="nav-icon js-fullscreen d-none d-lg-block" href="javascript:void(0)">
                                        <div class="position-relative">
                                             <i class="align-middle" data-feather="maximize"></i>
                                        </div>
                                   </a>
                              </li>
                              <li class="nav-item dropdown">
                                   <a class="nav-icon pe-md-0 dropdown-toggle" href="javascript:void(0)" data-bs-toggle="dropdown">
                                        <img src="{{ Avatar::create(auth()->user()->name)->setShape('square')->toBase64() }}" class="avatar img-fluid rounded" alt="{{ auth()->user()->name }}" />
                                   </a>
                                   <div class="dropdown-menu dropdown-menu-end">
                                        @include('layouts.partials.dropdown')
                                        <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault();document.getElementById('logout-form').submit();"><i class="align-middle me-1" data-feather="power"></i> Log out</a>
                                   </div>
                              </li>
                         </ul>
                    </div>
               </nav>

               <main class="content">
                    <div class="container-fluid p-0">
                         @yield('content')
                    </div>
               </main>
               @include('layouts.partials.footer')
          </div>
     </div>
     @include('layouts.partials.script')
</body>

</html>