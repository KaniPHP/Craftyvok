<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin sign in — Craftyvok</title>
  <link rel="stylesheet" href="{{ asset('styles.css') }}">
</head>
<body class="admin-page">
  <main class="admin-login">
    <a class="brand" href="{{ route('home') }}">
      <img class="brand-logo" src="{{ asset('images/craftyvok-logo.jpg') }}" alt="Craftyvok logo">
      <span class="brand-name">craftyvok<small>ADMIN</small></span>
    </a>
    <h1>Welcome back</h1>
    <p>Sign in to answer gift enquiries and update your catalogue.</p>
    @if($errors->any())<div class="admin-alert" role="alert">{{ $errors->first() }}</div>@endif
    <form method="post" action="{{ url('/admin/login') }}" class="admin-form">
      @csrf
      <label for="email">Admin email</label>
      <input id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="username" required>
      <label for="password">Password</label>
      <input id="password" name="password" type="password" autocomplete="current-password" required>
      <button class="button button-primary" type="submit">Sign in</button>
    </form>
    <a class="admin-back-link" href="{{ route('home') }}">← Back to the storefront</a>
  </main>
</body>
</html>
