<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'Kode Mukti'))</title>
    <meta name="description" content="@yield('meta_description', 'Ultimate ChatGPT Mastery & Prompt Swipe File — Bundle 15.000+ prompt siap pakai untuk marketing, bisnis, dan produktivitas.')">

    <link rel="icon" type="image/png" sizes="32x32" href="/favicon.png">
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">

    @vite('resources/css/app.css')
    @stack('styles')

    @if(env('UMAMI_WEBSITE_ID'))
    <link rel="preconnect" href="{{ env('UMAMI_HOST_URL') }}">
    <script defer src="{{ env('UMAMI_HOST_URL') }}/script.js" data-website-id="{{ env('UMAMI_WEBSITE_ID') }}"></script>
    @endif
</head>
<body>
    <div class="page-wrapper">
        {{-- Navbar --}}
        <header class="navbar">
            <div class="container navbar-inner">
                <a href="{{ route('landing') }}" class="navbar-brand">
                    <img src="/images/logo.png"
                         alt="Kode Mukti"
                         width="40" height="40"
                         class="navbar-logo"
                         loading="eager">
                    <span class="navbar-name">Kode Mukti</span>
                </a>
            </div>
        </header>

        {{-- Flash Messages --}}
        @if (session('success'))
            <div class="alert alert-success" role="alert">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-error" role="alert">
                {{ session('error') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-error" role="alert">
                <ul class="error-list">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Main Content --}}
        <main class="main-content">
            @yield('content')
        </main>
    </div>

    @stack('scripts')
</body>
</html>
