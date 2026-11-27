@extends('admin.layout')

@section('content')
  <div class="admin-page-heading">
    <div>
      <p class="eyebrow"><span class="eyebrow-line"></span> CONNECTED CHANNELS</p>
      <h1>Social channels</h1>
      <p>Check your Instagram content and Google review connection.</p>
    </div>
  </div>

  <div class="admin-stats admin-channel-stats" aria-label="Social media content counts">
    <article class="admin-stat-card admin-stat-red"><span class="admin-stat-icon">◎</span><span class="admin-stat-label">Videos &amp; Reels</span><strong>{{ number_format($videoCount) }}</strong><small>Instagram videos</small></article>
    <article class="admin-stat-card admin-stat-yellow"><span class="admin-stat-icon">▧</span><span class="admin-stat-label">Photos</span><strong>{{ number_format($photoCount) }}</strong><small>Instagram images</small></article>
    <article class="admin-stat-card admin-stat-pink"><span class="admin-stat-icon">✦</span><span class="admin-stat-label">Highlights</span><strong>{{ number_format($highlightCount) }}</strong><small>Saved Instagram stories</small></article>
  </div>

  <section class="admin-panel admin-integration-note">
    <p class="admin-panel-kicker">INSTAGRAM</p>
    <h2>Keep your feed in sync</h2>
    <p>Instagram videos and highlights sync every 30 minutes after you add your Meta Graph API account details to <code>.env</code> and keep Laravel’s scheduler running.</p>
    <p>To sync once now, run <code>php artisan craftyvok:sync-instagram</code>.</p>
  </section>
  <section class="admin-panel admin-integration-note">
    <p class="admin-panel-kicker">GOOGLE REVIEWS</p>
    <h2>Send customers to your review page</h2>
    <p>Add your official review link as <code>GOOGLE_REVIEW_URL</code> in <code>.env</code>. Customers submit their reviews directly to Google.</p>
    @if(config('services.google_reviews.url'))
      <a class="admin-row-action" href="{{ config('services.google_reviews.url') }}" target="_blank" rel="noopener">Open Google review link ↗</a>
    @else
      <p class="admin-help">No Google review link is configured yet.</p>
    @endif
  </section>
@endsection
