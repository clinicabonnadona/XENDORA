@extends('layouts.app')

@section('content')
<div class="container-fluid vh-100 p-0 d-flex justify-content-center align-items-center" style="background: hsl(0,0%,96.5%);">
    <div class="card card0 border-0">
        <div class="row d-flex">
            <div class="col-lg-6 d-none d-lg-block">
                <div class="card1 pb-5">
                    <div class="row px-3 d-flex justify-content-center align-items-center mt-4 mb-5 border-line">
                        <img src="{{ asset('/images/new_back_.webp') }}" class="image">
                    </div>
                </div>
            </div>
            <div class="col-lg-6 col-xs-12">
                <div class="card2 card border-0 px-4 py-5 d-flex justify-content-center">
                    <div class="row">
                        <div class="col">
                            <h2 class="mb-4 text-center">
                                <span class="d-block" style="font-size: 20px !important;">
                                    BIENVENIDO A {{ config('app.name', 'Laravel') }}
                                </span>
                            </h2>
                        </div>
                    </div>
                    <hr>
                    <form method="POST" action="{{ route('login') }}">
                        @csrf
                        <div class="row px-3 mt-3">
                            <label class="mb-1">
                                <h6 class="mb-0 text-sm">Usuario o Correo Electrónico:</h6>
                            </label>
                            <input class="mb-4 form-control @error('username') is-invalid @enderror login-form-input" type="text" name="username" placeholder="Ingresa tu usuario o correo electrónico" value="{{ old('username') }}" required autocomplete="username" autofocus>

                            @error('email')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror

                        </div>
                        <div class="row px-3 mb-3">
                            <label class="mb-1">
                                <h6 class="mb-0 text-sm">Contraseña:</h6>
                            </label>
                            <input type="password" name="password" placeholder="Ingresa tu Contraseña" class="form-control @error('password') is-invalid @enderror login-form-input" required id="password" autocomplete="current-password">

                            @error('password')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror

                        </div>
                        <div class="row px-3 mb-4">
                            <div class="custom-control custom-checkbox custom-control-inline">
                                <input id="remember" type="checkbox" name="remember" class="custom-control-input" {{ old('remember') ? 'checked' : '' }}>
                                <label for="remember" class="custom-control-label text-sm">Recordarme</label>
                            </div>
                            <!-- <a href="#" class="ml-auto mb-0 text-sm">Forgot Password?</a> -->
                        </div>
                        <div class="row mb-3 px-3">
                            <button type="submit" class="btn btn-blue btn-primary btn-lg btn-block login-form-button">INICIAR SESIÓN</button>
                        </div>
                    </form>
                    <div class="row px-3 text-center">
                        <div class="col">
                            <h1 class="text-muted">V 1.3.3 de 16-12-2022</h1>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="bg-blue py-4">
            <div class="row px-3">
                <small class="ml-4 ml-sm-5 mb-2">Copyright &copy; 2022. All rights reserved.</small>
                <div class="social-contact ml-4 ml-sm-auto">
                    <span class="fa fa-facebook mr-4 text-sm"></span>
                    <span class="fa fa-google-plus mr-4 text-sm"></span>
                    <span class="fa fa-linkedin mr-4 text-sm"></span>
                    <span class="fa fa-twitter mr-4 mr-sm-5 text-sm"></span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection