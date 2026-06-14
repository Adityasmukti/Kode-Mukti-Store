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

    @if(env('META_PIXEL_ID'))
    <script>
    !function(f,b,e,v,n,t,s)
    {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
    n.callMethod.apply(n,arguments):n.queue.push(arguments)};
    if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
    n.queue=[];t=b.createElement(e);t.async=!0;
    t.src=v;s=b.getElementsByTagName(e)[0];
    s.parentNode.insertBefore(t,s)}(window, document,'script',
    'https://connect.facebook.net/en_US/fbevents.js');
    fbq('init', '{{ env("META_PIXEL_ID") }}');
    fbq('track', 'PageView');
    </script>
    <noscript><img height="1" width="1" style="display:none"
    src="https://www.facebook.com/tr?id={{ env('META_PIXEL_ID') }}&ev=PageView&noscript=1"
    /></noscript>
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
