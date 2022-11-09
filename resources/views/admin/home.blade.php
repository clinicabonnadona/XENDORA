@extends('layouts.appIn')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        @auth()
                            <h3> 👋 BIENVENIDO A XENDORA {{ auth()->user()->name  }} {{ auth()->user()->lastName }}</h3>
                        @endauth
                    </div>
                </div>
            </div>
        </div>
        <div class="row mt-2">
            <div class="col-lg-12">
                <censo-component></censo-component>
                {{-- {{ gethostbyaddr($_SERVER['REMOTE_ADDR']) }}
                {{ $_SERVER['REMOTE_USER'] }}
                {{ getenv('USERDOMAIN') }}
                {{ getenv('USERNAME') }}
                {{ getenv("HOMEDRIVE") . getenv("HOMEPATH") }}
                {{ $_SERVER['LOGON_USER'] }}
                {{ $_SERVER['AUTH_USER'] }} --}}
            </div>
        </div>
    </div>
@endsection
