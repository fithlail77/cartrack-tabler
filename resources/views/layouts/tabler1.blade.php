<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover"/>
    <title>{{ config('app.name', 'RestAPI-Cartrack') }}</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/core@1.0.0-beta20/dist/css/tabler.min.css">
</head>
<body class="layout-fluid">
    <div class="page">
        <!-- Sidebar Menu -->
        <aside class="navbar navbar-vertical navbar-expand-lg navbar-dark">
            <div class="container-fluid">
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#sidebar-menu">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <h1 class="navbar-brand navbar-brand-autodark">
                    <a href="{{ route('dashboard') }}">My Dashboard</a>
                </h1>
                <div class="collapse navbar-collapse" id="sidebar-menu">
                    <ul class="navbar-nav pt-lg-3">
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('dashboard') }}">
                                <span class="nav-link-title">Home</span>
                            </a>
                        </li>

                        <!-- Penerapan Permission pada Menu -->
                        @can('view users')
                        <li class="nav-item">
                            <a class="nav-link" href="#">
                                <span class="nav-link-title">Manajemen User</span>
                            </a>
                        </li>
                        @endcan

                        @can('view reports')
                        <li class="nav-item">
                            <a class="nav-link" href="#">
                                <span class="nav-link-title">Laporan</span>
                            </a>
                        </li>
                        @endcan

                        @can('manage settings')
                        <li class="nav-item">
                            <a class="nav-link" href="#">
                                <span class="nav-link-title">Pengaturan</span>
                            </a>
                        </li>
                        @endcan
                    </ul>
                </div>
            </div>
        </aside>

        <div class="page-wrapper">
            <!-- Header Profile -->
            <header class="navbar navbar-expand-md navbar-light d-none d-lg-flex d-print-none">
                <div class="container-xl justify-content-end">
                    <div class="nav-item dropdown">
                        <a href="#" class="nav-link d-flex lh-1 text-reset p-0" data-bs-toggle="dropdown">
                            <span class="avatar avatar-sm">{{ substr(Auth::user()->name, 0, 2) }}</span>
                            <div class="d-none d-xl-block ps-2">
                                <div>{{ Auth::user()->name }}</div>
                                <div class="mt-1 small text-muted">{{ Auth::user()->roles->pluck('name')->implode(', ') }}</div>
                            </div>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item">Logout</button>
                            </form>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Konten Dinamis -->
            <div class="page-body">
                <div class="container-xl">
                    @yield('content')
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/@tabler/core@1.0.0-beta20/dist/js/tabler.min.js"></script>