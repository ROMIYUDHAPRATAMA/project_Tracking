<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>@yield('title','Dashboard')</title>
  <meta name="csrf-token" content="{{ csrf_token() }}">

  {{-- Anti-FOUC: pakai preferensi tema yang tersimpan (default light) --}}
  <script>
    (function () {
      try {
        if (localStorage.getItem('pt_theme') === 'dark') {
          document.documentElement.setAttribute('data-theme', 'dark');
        }
      } catch (e) {}
    })();
  </script>

  {{-- Asset umum aplikasi (global) --}}
  @vite([
    'resources/css/app.css',
    'resources/css/dashboard.css',
    'resources/css/profile.css',
    'resources/css/projects.css',
    'resources/js/app.js'
  ])

  {{-- Slot untuk style halaman spesifik (dipush dari view) --}}
  @stack('styles')
</head>
<body class="bg-app">
  <div class="app">
    @include('partials.sidebar')

    <div class="app-content">
      @include('partials.topbar')

      <main class="container">
        @yield('content')
      </main>
    </div>
  </div>

  {{-- Script dari partial / halaman (dipush dari view) --}}
  @stack('scripts')
</body>
</html>
