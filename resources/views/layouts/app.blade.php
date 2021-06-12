<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name') }}</title>

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Font awesome -->
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet"/>

    <!-- DevExtreme theme -->
    <link href="https://cdn3.devexpress.com/jslib/21.1.3/css/dx.light.css" rel="stylesheet">

    <!-- ChartJS library -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.0.2/chart.min.js"></script>
</head>
<body>
    <div id="app">
        <nav id="header-nav" class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
            @include('layouts.nav')
        </nav>
        <main class="py-4">
            @yield('content')
        </main>
    </div>

    <script src="{{ mix('js/app.js') }}"></script>

    <!-- DevExtreme library -->
    <script src="https://cdn3.devexpress.com/jslib/21.1.3/js/dx.all.js"></script>
    <script src="https://unpkg.com/devextreme-aspnet-data@2.8.2/js/dx.aspnet.data.js"></script>

    @stack('scripts')
</body>
</html>
