@extends('layouts.base')

@section('custom-head')
    <link rel="stylesheet" href="{{ asset('css/loginPage.css') }}">
@endsection

@section('content-base')
<div class="container" >
    <div class="row justify-content-center pt-5">
        <div class="">
            <div class="card" id="login-form">
                <div class="card-header">
                    {{ $form_title or 'Login In' }}
                </div>

                <div class="card-body pb-0">
                    <form method="POST" action="{{ route('login') }}">
                        {{ csrf_field() }}

                        <div class="{{ $errors->has('email')? 'mb-2' : 'mb-3' }}">
                            <input type="email" id="email" name="email" class="form-control {{ $errors->has('email')? 'is-invalid' : '' }}" placeholder="E-mail" value="{{ old('email') }}" required autofocus>
                            @if($errors->has('email'))
                            <div class="invalid-feedback">
                                {{ $errors->first('email') }}
                            </div>
                            @endif
                        </div>

                        <div class="{{ $errors->has('password')? 'mb-2' : 'mb-2' }}">
                            <input type="password" name="password" class="form-control {{ $errors->has('password')? 'is-invalid' : '' }}" placeholder="Password" required>
                            <div class="invalid-feedback">
                                {{ $errors->first('password') }}
                            </div>
                        </div>

                        <a href="{{ route('password.request') }}"><small>Forgot Your Password?</small></a>


                        <button type="submit" class="btn btn-primary mx-auto d-block" id="btn-submit"><i class="fas fa-arrow-right"></i></button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
