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
            </div>
        </div>
    </div>
@endsection
