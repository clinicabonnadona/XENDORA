<!-- FontAwesome 5.13.1 -->
<link rel="stylesheet" href="{{ asset('css/all.min.css') }}">
<!-- Css Styles -->
<link rel="stylesheet" href="{{ asset('css/app.css') }}">

<style>

</style>


<div class="container h-100">
    <div class="row justify-content-center h-100">
        <div class="col-md-12 align-self-center text-center">
            <div class="card h-50">
                <div class="card-body">
                    <img src="{{ asset('assets/images/403.png') }}" style="max-width: 350px" class="img-thumbnail mx-auto d-block border-0" alt="403 Robot">
                    <h2>
                        {{--{{ $exception->getMessage() }}--}}
                        <strong>403 - Acceso Denegado </strong>
                    </h2>
                    <h4>No Puedes Acceder a este Recurso....</h4>
                </div>
            </div>
        </div>
    </div>
</div>
