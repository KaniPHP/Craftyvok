<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>{{ $pageTitle }} — Craftyvok Admin</title>
  <link rel="stylesheet" href="{{ asset('styles.css') }}">
</head>
<body class="admin-page admin-dashboard-page">
  <div class="admin-shell">
    <aside class="admin-sidebar">
      <a class="brand admin-brand" href="{{ route('admin.dashboard') }}">
        <img class="brand-logo" src="{{ asset('images/craftyvok-logo.jpg') }}" alt="Craftyvok logo">
        <span class="brand-name">craftyvok<small>GIFT ADMIN</small></span>
      </a>
      <p class="admin-nav-label">STORE MANAGEMENT</p>
      <nav class="admin-nav" aria-label="Admin pages">
        <a href="{{ route('admin.dashboard') }}" class="{{ $activePage === 'overview' ? 'is-active' : '' }}" @if($activePage === 'overview') aria-current="page" @endif><span class="admin-nav-icon">⌂</span> Overview</a>
        <a href="{{ route('admin.enquiries.index') }}" class="{{ $activePage === 'enquiries' ? 'is-active' : '' }}" @if($activePage === 'enquiries') aria-current="page" @endif><span class="admin-nav-icon">✉</span> Enquiries</a>
        <a href="{{ route('admin.gifts.index') }}" class="{{ $activePage === 'products' ? 'is-active' : '' }}" @if($activePage === 'products') aria-current="page" @endif><span class="admin-nav-icon">▦</span> Products</a>
        <a href="{{ route('admin.orders.index') }}" class="{{ $activePage === 'orders' ? 'is-active' : '' }}" @if($activePage === 'orders') aria-current="page" @endif><span class="admin-nav-icon">▤</span> Orders</a>
        <a href="{{ route('admin.channels.index') }}" class="{{ $activePage === 'channels' ? 'is-active' : '' }}" @if($activePage === 'channels') aria-current="page" @endif><span class="admin-nav-icon">◎</span> Social channels</a>
      </nav>
      <div class="admin-sidebar-bottom">
        <a class="admin-sidebar-site" href="{{ route('home') }}" target="_blank" rel="noopener">↗ <span>View website</span></a>
        <form method="post" action="{{ route('admin.logout') }}">@csrf<button class="admin-sidebar-logout" type="submit"><span>⇥</span> Sign out</button></form>
      </div>
    </aside>

    <div class="admin-dashboard-main">
      <header class="admin-topbar">
        <div>
          <span class="admin-topbar-kicker">CRAFTYVOK / ADMIN</span>
          <span class="admin-topbar-caption">{{ $pageTitle }}</span>
        </div>
        <div class="admin-topbar-user">
          <span class="admin-user-avatar">{{ mb_substr(auth()->user()->name, 0, 1) }}</span>
          <span><strong>{{ auth()->user()->name }}</strong><small>Administrator</small></span>
        </div>
      </header>

      <main class="admin-content">
        @if(session('status'))<div class="admin-success" role="status">{{ session('status') }}</div>@endif
        @if($errors->any())<div class="admin-alert" role="alert">{{ $errors->first() }}</div>@endif
        @yield('content')
      </main>
    </div>
  </div>
</body>
</html>
