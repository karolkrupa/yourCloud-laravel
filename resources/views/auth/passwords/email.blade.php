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
