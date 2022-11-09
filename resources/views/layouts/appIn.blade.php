<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>

    <!-- FontAwesome 5.13.1 -->
    <link rel="stylesheet" href="{{ asset('css/all.min.css') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300&display=swap" rel="stylesheet">

    <!-- Styles -->
    <!-- <link href="{{ asset('css/app.css') }}" rel="stylesheet"> -->
    <link href="{{ asset('css/styles.css') }}" rel="stylesheet" />
    <link href="{{ asset('css/customs.css') }}" rel="stylesheet" />
</head>
<body class="sb-nav-fixed">
<div id="app">

    @auth()
        @include('partials.navbar')

        @include('partials.sidebar')
    @endauth

    @guest()
        <main class="py-4">
            @yield('content')
        </main>
    @endguest


</div>


<script type="text/javascript">
    @auth
        window.Permissions = {!! json_encode(Auth::user()->allPermissions, true) !!}
    @else
        window.Permissions = []
    @endauth
</script>

<script src="https://code.jquery.com/jquery-3.5.1.min.js" crossorigin="anonymous"></script>
<script src="{{ asset('js/scripts.js') }}" defer></script>
</body>
</html>
