<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="{{asset('img/favicon.png')}}" rel='icon' type='image/x-icon'/>

    <title>@yield('title', 'Laravel')</title>
    <meta http-equiv="Cache-Control" content="public, max-age=604800">
    <meta http-equiv="Expires" content="Mon, 15 Jan 2027 12:00:00 GMT">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
</head>
<body>

    {{ $slot }}

    <script src="{{ asset('js/auth.js') }}"></script>
</body>
</html>