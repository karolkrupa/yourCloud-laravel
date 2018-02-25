@extends('layouts.app')

@section('custom-head')
    <link rel="stylesheet" href="{{ asset('css/settingsPage.css') }}">
@endsection

@section('navbar-left')
    @parent

    <a href="/" class="btn btn-primary d-inline-block">@lang('settingsView.back_to_drive_btn')</a>
@endsection


@section('left-menu')
    <a href="/settings/general" class="btn active"><i class="fas fa-globe"></i>@lang('settingsView.menu_general')</a>
    <a href="/settings/privacy" class="btn"><i class="fas fa-user-secret"></i>@lang('settingsView.menu_privacy')</a>
    <a href="/settings/customization" class="btn"><i class="fas fa-cubes"></i>@lang('settingsView.menu_customization')</a>
@endsection

@section('content')
    <div class="row pt-3">
        <div class="col-auto">

            <div class="row mt-3">
                <div class="col-12 col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">@lang('settingsView.full_name')</h5>
                            <div class="input-group mb-3">
                                <input id="full-name" type="text" class="form-control" placeholder="@lang('settingsView.full_name_placeholder')" value="{{ Auth::user()->full_name }}">
                                <div class="input-group-append">
                                    <button class="btn btn-secondary" type="button" id="update-full-name">@lang('settingsView.full_name_update')</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">@lang('settingsView.language')</h5>
                            <div class="input-group mb-3">
                                <select class="custom-select" id="selected-language">
                                    @foreach(config('app.languages') as $name => $lang)
                                        <option {{ App::isLocale($lang)? 'selected' : '' }} value="{{ $lang }}">{{ $name }}</option>
                                    @endforeach
                                </select>
                                <div class="input-group-append">
                                    <button class="btn btn-secondary" type="button" id="update-language">@lang('settingsView.language_select')</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">@lang('settingsView.change_password')</h5>
                            <div class="input-group">
                                <input id="password" type="password" class="form-control" placeholder="@lang('settingsView.new_password')">
                                <input id="password-repeat" type="password" class="form-control" placeholder="@lang('settingsView.repeat_password')">
                                <div class="input-group-append">
                                    <button id="update-password" class="btn btn-secondary" type="button">@lang('settingsView.change_password_save')</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mt-5" id="your-cloud-informations">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title mb-1">YourCloud Informations</h5>
                            <p class="card-text mb-0"><b>YourCloud</b> v{{ env('APP_VERSION') }} ({{ env('APP_RELEASE') }})</p>
                            <p class="card-text mb-0">Developed by <a target="_blank" href="https://github.com/Exus1">Exus</a>, the <a target="_blank" href="https://github.com/Exus1/yourCloud-laravel">source code</a> is licensed under the
                                <a target="_blank" href="https://github.com/Exus1/yourCloud-laravel/blob/master/LICENSE">MIT License</a>.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


@section('custom-js')
    @parent

    <script src="{{ asset('js/settingsView.js') }}"></script>
@endsection
