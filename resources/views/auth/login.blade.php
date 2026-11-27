<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Shopper login — Craftyvok</title>
  <link rel="stylesheet" href="{{ asset('styles.css') }}">
</head>
<body class="admin-page">
  <header class="account-header">
    <a class="brand" href="{{ route('home') }}">
      <img class="brand-logo" src="{{ asset('images/craftyvok-logo.jpg') }}" alt="Craftyvok logo">
      <span class="brand-name">craftyvok<small>GIFTS WITH A PERSONAL TOUCH</small></span>
    </a>
    <a class="text-link" href="{{ route('home') }}#gifts">All products</a>
  </header>
  <main class="account-page">
    <section class="account-card">
      <p class="eyebrow"><span class="eyebrow-line"></span> YOUR CRAFTYVOK ACCOUNT</p>
      <h1>Welcome back.</h1>
      <p>Sign in to see your Craftyvok orders and delivery updates.</p>
      @if($errors->any())<div class="admin-alert" role="alert">{{ $errors->first() }}</div>@endif
      <form method="post" action="{{ url('/login') }}" class="account-form">
        @csrf
        <label for="email">Email address</label>
        <input id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="email" required>
        <label for="password">Password</label>
        <input id="password" name="password" type="password" autocomplete="current-password" required>
        <button class="button button-primary" type="submit">Login <span aria-hidden="true">→</span></button>
      </form>
      <p class="account-switch">New to Craftyvok? <a href="{{ route('register') }}">Create an account</a></p>
      <a class="admin-back-link" href="{{ route('admin.login') }}">Shop admin sign-in</a>
    </section>
  </main>
</body>
</html>
