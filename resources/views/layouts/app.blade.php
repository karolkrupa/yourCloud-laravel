@extends('layouts.base')

@section('custom-head')
    {{--<link rel="stylesheet" href="{{ asset('css/folder_page.css') }}">--}}
@endsection

@section('content-base')
<div id="alerts-container">
    @yield('alerts-container')
</div>


<div class="container-fluid" id="container">
    <div class="row">
        <nav class="col-12" id="navbar">
            <div class="row align-items-center h-100">

                <div class="col" id="navbar-left">
                    <div class="dropdown d-inline-block">
                        <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="far fa-user mr-2" style="font-size: 17px"></i>@lang('app.profile')
                        </button>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                            <a class="dropdown-item" href="/settings/general"><i class="fas fa-cog mr-1"></i>@lang('app.settings')</a>
                            <a class="dropdown-item" href="/logout"><i class="fas fa-sign-out-alt mr-1"></i>@lang('app.logout')</a>
                        </div>
                    </div>
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
