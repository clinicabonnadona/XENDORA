@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row mt-5">
            <div class="col">
                <select class="form-select form-control" aria-label="Default select example">
                    <option selected>Seleccione el Pabellón</option>
                    <option value="1">LIRIO</option>
                    <option value="2">AZUCENA</option>
                    <option value="3">TAMO</option>
                </select>
            </div>

        </div>
        <div class="row mt-2">
            <div class="col">
                <input type="date" class="form-control" placeholder="Last name" aria-label="Last name">
            </div>
        </div>
        <div class="row mt-5">

        </div>
    </div>
@endsection
