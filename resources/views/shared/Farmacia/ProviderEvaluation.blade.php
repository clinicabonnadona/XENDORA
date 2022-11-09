@extends('layouts.appIn')

@section('content')
    {{-- <farmacia-recepcion-component :userdata="{{ Auth::user()->id }}"></farmacia-recepcion-component> --}}
    <provider-evaluation-component></provider-evaluation-component>
@endsection
