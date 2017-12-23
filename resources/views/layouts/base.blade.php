<!doctype html>
<html lang=" {{ app()->getLocale() }} ">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Page Title --}}
    <title>
        @section('title')
            {{ config('app.name', 'yourCloud') }}
        @show
    </title>

    <!-- CSS -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">

    @yield('custom-head')

</head>
<body>

    @yield('content-base')

<!-- JavaScript -->
<script src="{{ asset('js/app.js') }}"></script>

@yield('custom-js')

</body>
</html>