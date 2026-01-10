<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<!--begin::Head-->

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>@yield('title', config('app.name'))</title>

    <!--begin::Accessibility Meta Tags-->
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes" />
    <meta name="color-scheme" content="light dark" />
    <meta name="theme-color" content="#007bff" media="(prefers-color-scheme: light)" />
    <meta name="theme-color" content="#1a1a1a" media="(prefers-color-scheme: dark)" />
    <!--end::Accessibility Meta Tags-->

    <!--begin::Primary Meta Tags-->
    <meta name="title" content="@yield('title', config('app.name'))" />
    <meta name="author" content="{{ config('app.name') }}" />
    <meta name="description" content="@yield('description', config('app.name'))" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <!--end::Primary Meta Tags-->

    <!--begin::Styles-->
    @vite(['resources/css/adminlte.css'])
    @stack('styles')
    <!--end::Styles-->
</head>
<!--end::Head-->

<!--begin::Body-->

<body class="layout-fixed sidebar-expand-lg bg-body-tertiary">
    <!--begin::App Wrapper-->
    <div class="app-wrapper">
        <!--begin::Header-->
        @include('partials.adminlte.header')
        <!--end::Header-->

        <!--begin::Sidebar-->
        @include('partials.adminlte.sidebar')
        <!--end::Sidebar-->

        <!--begin::App Main-->
        <main class="app-main">
            <!--begin::App Content Header-->
            <div class="app-content-header">
                <!--begin::Container-->
                <div class="container-fluid">
                    <!--begin::Row-->
                    <div class="row">
                        <div class="col-sm-6">
                            <h3 class="mb-0">
                                @yield('page-title', 'Dashboard')
                            </h3>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-end">
                                @yield('breadcrumb')
                            </ol>
                        </div>
                    </div>
                    <!--end::Row-->
                </div>
                <!--end::Container-->
            </div>
            <!--end::App Content Header-->

            <!--begin::App Content-->
            <div class="app-content">
                <!--begin::Container-->
                <div class="container-fluid">
                    @yield('content')
                </div>
                <!--end::Container-->
            </div>
            <!--end::App Content-->
        </main>
        <!--end::App Main-->

        <!--begin::Footer-->
        @include('partials.adminlte.footer')
        <!--end::Footer-->
    </div>
    <!--end::App Wrapper-->

    <!--begin::Scripts-->
    @stack('pre-scripts')
    @vite(['resources/js/adminlte.js'])
    <script type="module">
        @php($sessionData = [
            'success' => session('success'),
            'error' => session('error'),
            'info' => session('info'),
            'warning' => session('warning'),
        ]);

        let sessionMessages = @json($sessionData);

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr(
                    'content',
                ),
            },
        });

        if (sessionMessages?.success)
            toastr.success(sessionMessages.success);
        if (sessionMessages?.error) toastr.error(sessionMessages.error);
        if (sessionMessages?.info) toastr.info(sessionMessages.info);
        if (sessionMessages?.warning)
            toastr.warning(sessionMessages.warning);

        document
            .getElementById('logoutButton')
            ?.addEventListener('click', function (e) {
                document.getElementById('logoutForm').submit();
            });
    </script>
    @stack('scripts')
    <!--end::Scripts-->
</body>
<!--end::Body-->

</html>