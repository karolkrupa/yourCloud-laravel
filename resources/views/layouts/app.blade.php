@extends('layouts.base')

@section('custom-head')
    {{--<link rel="stylesheet" href="{{ asset('css/folder_page.css') }}">--}}
@endsection

@section('content-base')

<div class="container-fluid" id="container">
    <div class="row">
        <nav class="col-12" id="navbar">
            <div class="row align-items-center h-100">

                <div class="col" id="navbar-left">
                    @yield('navbar-left')
                </div>

                <div class="col text-center" id="navbar-center">
                    @section('navbar-center')
                        {{ config('app.name', 'yourCloud') }}
                    @show
                </div>

                <div class="col text-right" id="navbar-right">
                    @yield('navbar-right')
                </div>
            </div>


        </nav>

        <div class="col" id="left-menu">
            @yield('left-menu')
        </div>

        <div class="col" id="content">
            @yield('content')
        </div>
    </div>
</div>

@endsection