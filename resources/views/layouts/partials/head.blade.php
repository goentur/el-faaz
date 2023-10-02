<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<meta name="csrf-token" content="{{ csrf_token() }}">
<meta name="description" content="Aplikasi Keuangan El-Faaz Pekalongan">
<meta name="author" content="...">
<meta name="keywords" content="sistem, informasi, keuangan, akuntansi, management">

<link rel="preconnect" href="https://fonts.gstatic.com">
<link rel="shortcut icon" href="{{ asset('img/icons/icon.ico') }}" />

<link rel="canonical" href="{{ url()->current() }}" />

<title>{{ config('app.name') }}</title>

<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap" rel="stylesheet">
<link class="js-stylesheet" href="{{ asset('css/light.css') }}" rel="stylesheet">
@stack('vendor-css')
@stack('css')
