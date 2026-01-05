<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <!--begin::Head-->

    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>@yield('title', config('app.name'))</title>

        <!--begin::Accessibility Meta Tags-->
        <meta
            name="viewport"
            content="width=device-width, initial-scale=1.0, user-scalable=yes"
        />
        <meta name="color-scheme" content="light dark" />
        <meta
            name="theme-color"
            content="#007bff"
            media="(prefers-color-scheme: light)"
        />
        <meta
            name="theme-color"
            content="#1a1a1a"
            media="(prefers-color-scheme: dark)"
        />
        <!--end::Accessibility Meta Tags-->

        <!--begin::Primary Meta Tags-->
        <meta name="title" content="@yield('title', config('app.name'))" />
        <meta name="author" content="{{ config('app.name') }}" />
        <meta
            name="description"
            content="@yield('description', config('app.name'))"
        />
        <!--end::Primary Meta Tags-->

        <!--begin::Styles-->
        @vite(['resources/css/adminlte.css', 'resources/js/adminlte.js'])
        @stack('styles')
        <!--end::Styles-->
    </head>
    <!--end::Head-->

    <!--begin::Body-->

    <body class="login-page bg-body-secondary">
        @yield('content')

        <!--begin::Scripts-->
        @stack('scripts')
        <!--end::Scripts-->
    </body>
    <!--end::Body-->
</html>
