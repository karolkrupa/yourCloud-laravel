@extends('layouts.base')

@section('custom-head')
    <link rel="stylesheet" href="{{ asset('css/loginPage.css') }}">
@endsection

@section('content')
    <div class="container" >
        <div class="row justify-content-center pt-5">
            <div class="">
                <div class="card" id="login-form">
                    <div class="card-header">
                        {{ $form_title or 'Setting the password' }}
                    </div>

                    <div class="card-body pb-0">
                        <form method="POST" action="{{ route('password.request') }}">
                            {{ csrf_field() }}

                            <input type="hidden" name="token" value="{{ $token }}">

                            <div class="{{ $errors->has('email')? 'mb-2' : 'mb-3' }}">
                                <input type="email" id="email" name="email" class="form-control {{ $errors->has('email')? 'is-invalid' : '' }}" placeholder="E-mail" value="{{ old('email') }}" required autofocus>
                                @if($errors->has('email'))
                                    <div class="invalid-feedback">
                                        {{ $errors->first('email') }}
                                    </div>
                                @endif
                            </div>

                            <div class="{{ $errors->has('password')? 'mb-2' : 'mb-3' }}">
                                <input type="password" id="password" name="password" class="form-control {{ $errors->has('password')? 'is-invalid' : '' }}" placeholder="Password" required>
                                @if($errors->has('password'))
                                    <div class="invalid-feedback">
                                        {{ $errors->first('password') }}
                                    </div>
                                @endif
                            </div>

                            <div class="{{ $errors->has('password_confirmation')? 'mb-2' : 'mb-3' }}">
                                <input type="password" id="password" name="password_confirmation" class="form-control {{ $errors->has('password_confirmation')? 'is-invalid' : '' }}" placeholder="Repeat Password" required>
                                @if($errors->has('password_confirmation'))
                                    <div class="invalid-feedback">
                                        {{ $errors->first('password_confirmation') }}
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
