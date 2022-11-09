@extends('layouts.appIn')

@section('content')
    <farmacia-envio-ordenes-component :userdata="{{Auth::user()}}"></farmacia-envio-ordenes-component>
@endsection
