@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">

        <div class="col-lg-4 vh-100 d-lg-block d-none app-login-img-box border-right"></div>

        <div class="col-lg-8 vh-100 d-flex col-md-12 bg-white justify-content-center align-items-center">

            <div class="mx-auto app-login-box col-sm-12 col-md-10 col-lg-6">
                <h2 class="mb-4 text-center">
                    <span class="d-block" style="font-size: 20px !important;">
                        BIENVENIDO A {{ config('app.name', 'Laravel') }}
                    </span>
                </h2>
                {{-- <div class="app-logo text-center"> --}}
                {{-- <img src="{{ asset('assets/images/space_icon.png') }}" alt="Login_Logo" width="80"> --}}
                {{-- </div> --}}
                <div class="divider row mb-5"></div>
                <div>
                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <div class="form-group row">
                            <div class="col-md-12">
                                <input id="username" 
                                            placeholder="Usuario" 
                                            type="text" 
                                            class="form-control @error('username') is-invalid @enderror login-form-input" 
                                            name="username" 
                                            value="{{ old('username') }}" 
                                            required 
                                            autocomplete="username" 
                                            autofocus
                                >

                                @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-md-12">
                                <input  id="password" 
                                        placeholder="Contraseña" 
                                        type="password" 
                                        class="form-control @error('password') is-invalid @enderror login-form-input" 
                                        name="password" 
                                        required 
                                        autocomplete="current-password">

                                @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-md-12">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>

                                    <label class="form-check-label" for="remember">
                                        {{ __('Recordarme') }}
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="divider row mb-5"></div>

                        <div class="form-group row mb-0">
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-success btn-block login-form-button">
                                    {{ __('INGRESAR...') }}
                                </button>

                                {{-- @if (Route::has('password.request'))
                                        <a class="btn btn-link" href="{{ route('password.request') }}" style="padding-left: 0">
                                {{ __('Olvidaste tu Contraseña?') }}
                                </a>
                                @endif --}}
                            </div>
                        </div>

                        <div class="form-group row mt-3">
                            <div class="col-md-12 text-center">
                                <p>V 1.3.2 de 06-12-2022</p>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection