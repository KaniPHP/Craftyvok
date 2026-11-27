<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Create account — Craftyvok</title>
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
      <p class="eyebrow"><span class="eyebrow-line"></span> MAKE IT YOURS</p>
      <h1>Create your account.</h1>
      <p>Keep your Craftyvok orders and delivery updates in one place.</p>
      @if($errors->any())<div class="admin-alert" role="alert">{{ $errors->first() }}</div>@endif
      <form method="post" action="{{ url('/register') }}" class="account-form">
        @csrf
        <label for="name">Your name</label>
        <input id="name" name="name" type="text" value="{{ old('name') }}" autocomplete="name" maxlength="120" required>
        <label for="email">Email address</label>
        <input id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="email" required>
        <label for="password">Password</label>
        <input id="password" name="password" type="password" autocomplete="new-password" minlength="8" required>
        <label for="password_confirmation">Confirm password</label>
        <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" minlength="8" required>
        <button class="button button-primary" type="submit">Create account <span aria-hidden="true">→</span></button>
      </form>
      <p class="account-switch">Already have an account? <a href="{{ route('login') }}">Login</a></p>
    </section>
  </main>
</body>
</html>
