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

    <script>
        FontAwesomeConfig = { searchPseudoElements: true };
    </script>

</head>
<body>

    @yield('content-base')

<!-- JavaScript -->
    <script src="{{ asset('js/laravelBootstrap.js') }}"></script>
    <script src="{{ asset('js/fontawesome-all.js') }}"></script>
    {{--<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js" integrity="sha384-b/U6ypiBEHpOf/4+1nzFpr53nxSS+GLCkfwBdFNTxtclqqenISfwAzpKaMNFNmj4" crossorigin="anonymous"></script>--}}
    {{--<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/js/bootstrap.min.js" integrity="sha384-h0AbiXch4ZDo7tp9hKZ4TsHbi047NrKGLO3SEJAg45jXxnGIfYzk4Si90RDIqNm1" crossorigin="anonymous"></script>--}}
    <script src="{{ asset('js/app.js') }}"></script>

@yield('custom-js')

</body>
</html>
