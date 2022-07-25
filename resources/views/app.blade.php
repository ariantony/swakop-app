<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title inertia>{{ config('app.name', 'Laravel') }}</title>

        <!-- Favicon -->
        <link rel="shortcut icon" href="{{ url('assets/images/favicon.png') }}" type="image/x-icon">

        <!-- Fonts -->
        <link rel="stylesheet" href="{{ url('assets/fonts/nunito.css') }}">

        <!-- Icons -->
        <link rel="stylesheet" href="{{ url('assets/boxicons/css/boxicons.css') }}">

        <!-- Scripts -->
        @routes
        @vite('resources/js/app.js')
        @inertiaHead
    </head>
    <body class="font-sans antialiased">
        @inertia
    </body>
</html>
