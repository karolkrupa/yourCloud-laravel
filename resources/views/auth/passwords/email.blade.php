{{--@extends('layouts.app')--}}

{{--@section('content')--}}
{{--<div class="container">--}}
    {{--<div class="row">--}}
        {{--<div class="col-md-8 col-md-offset-2">--}}
            {{--<div class="panel panel-default">--}}
                {{--<div class="panel-heading">Reset Password</div>--}}

                {{--<div class="panel-body">--}}
                    {{--@if (session('status'))--}}
                        {{--<div class="alert alert-success">--}}
                            {{--{{ session('status') }}--}}
                        {{--</div>--}}
                    {{--@endif--}}

                    {{--<form class="form-horizontal" method="POST" action="{{ route('password.email') }}">--}}
                        {{--{{ csrf_field() }}--}}

                        {{--<div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">--}}
                            {{--<label for="email" class="col-md-4 control-label">E-Mail Address</label>--}}

                            {{--<div class="col-md-6">--}}
                                {{--<input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" required>--}}

                                {{--@if ($errors->has('email'))--}}
                                    {{--<span class="help-block">--}}
                                        {{--<strong>{{ $errors->first('email') }}</strong>--}}
                                    {{--</span>--}}
                                {{--@endif--}}
                            {{--</div>--}}
                        {{--</div>--}}

                        {{--<div class="form-group">--}}
                            {{--<div class="col-md-6 col-md-offset-4">--}}
                                {{--<button type="submit" class="btn btn-primary">--}}
                                    {{--Send Password Reset Link--}}
                                {{--</button>--}}
                            {{--</div>--}}
                        {{--</div>--}}
                    {{--</form>--}}
                {{--</div>--}}
            {{--</div>--}}
        {{--</div>--}}
    {{--</div>--}}
{{--</div>--}}
{{--@endsection--}}




@extends('layouts.base')

@section('custom-head')
    <link rel="stylesheet" href="{{ asset('css/login_page.css') }}">
@endsection

@section('content')
    <div class="container" >
        <div class="row justify-content-center pt-5">
            <div class="">
                <div class="card" id="login-form">
                    <div class="card-header">
                        {{ $form_title or 'Resetting password' }}
                    </div>

                    <div class="card-body pb-0">

                        @if (session('status'))
                            @component('components.alert')
                                @slot('classes') mb-4 @endslot

                                @slot('type') success @endslot

                                {{ session('status') or 'You will receive a special message containing a link that allows you to change your password.' }}
                            @endcomponent
                        @else
                            @component('components.alert')
                                @slot('classes') mb-4 @endslot

                                @slot('type') info @endslot

                                {{ $user_information or 'You will receive a special message containing a link that allows you to change your password.' }}
                            @endcomponent
                        @endif

                        <form method="POST" action="{{ route('password.email') }}">
                            {{ csrf_field() }}

                            <div class="{{ $errors->has('email')? 'mb-2' : 'mb-3' }}">
                                <input type="email" id="email" name="email" class="form-control {{ $errors->has('email')? 'is-invalid' : '' }}" placeholder="E-mail" value="{{ old('email') }}" required autofocus>
                                @if($errors->has('email'))
                                    <div class="invalid-feedback">
                                        {{ $errors->first('email') }}
                                    </div>
                                @endif
                            </div>

                            <button type="submit" class="btn btn-primary mx-auto d-block" id="btn-submit"><i class="fas fa-arrow-right"></i></button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
