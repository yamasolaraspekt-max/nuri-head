<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <title>AI Chats – @yield('title')</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <!-- If served by Laravel Blade, this will be filled -->
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <!-- Tailwind via CDN -->
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    .focus-ring:focus { outline: none; box-shadow: 0 0 0 4px rgba(99,102,241,.35); }
    .modal-open { overflow: hidden; }
  </style>
  @yield('styles')
</head>
<body class="min-h-screen bg-gradient-to-b from-slate-50 to-slate-100 text-slate-800">
  <!-- Header -->
  <header class="sticky top-0 z-10 bg-white/80 backdrop-blur border-b border-slate-200">
    <div class="max-w-7xl mx-auto px-4 py-3 flex items-center justify-between gap-3">
      <div class="flex items-center gap-2">
        <a id="dashboardBtn" href="{{url('/')}}"
           class="rounded-lg border border-slate-300 bg-white px-3 py-1.5 text-sm text-slate-700 hover:bg-slate-50">Dashboard</a>
        <h1 class="text-lg sm:text-xl font-semibold">Nuri Head KI</h1>
      </div>
      <button id="refreshBtn" class="text-sm text-indigo-700 hover:text-indigo-900">Refresh</button>
    </div>
  </header>

  @yield('content')
   
    <script>
          const CONFIGs = {
            DASHBOARD_URL: '/home', 
            }; 
        document.getElementById('dashboardBtn').setAttribute('href', CONFIG.DASHBOARD_URL);
    </script>
  @yield('scripts')

</body>
</html>
