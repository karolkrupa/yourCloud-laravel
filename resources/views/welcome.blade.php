<!doctype html>
<html lang=" {{ app()->getLocale() }} ">
<head>
    <title>{{ config('app.name', 'yourCloud') }}</title>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- CSS -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">

    {{-- Custom CSS --}}
    <link rel="stylesheet" href="{{ asset('css/login_page.css') }}">
</head>
<body>
    <div class="container" >
        <div class="row justify-content-center pt-5">
            <div class="">
                <div class="card" id="login-form">
                    <div class="card-header">
                        Zaloguj się
                    </div>

                    <div class="card-body pb-0">
                        <form action="">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">

                            <div class="mb-2">
                                <input type="text" class="form-control" placeholder="E-mail">
                                <div class="invalid-feedback">
                                    Please provide a valid city.
                                </div>
                            </div>

                            <div class="mb-2">
                                <input type="password" class="form-control" placeholder="Password">
                                <div class="invalid-feedback">
                                    Please provide a valid city.
                                </div>
                            </div>


                            <button type="submit" class="btn btn-primary mx-auto d-block" id="btn-submit"><i class="fas fa-arrow-right"></i></button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript -->
    <script src="{{ asset('js/app.js') }}"></script>
</body>
</html>