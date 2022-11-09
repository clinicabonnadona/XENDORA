@extends('layouts.appIn')

@section('content')
    <farmacia-recepcion-component :userdata="{{ Auth::user()->id }}"></farmacia-recepcion-component>
    {{-- <site-in-maintenance></site-in-maintenance> --}}
@endsection
