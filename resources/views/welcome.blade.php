<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="theme-color" content="#fff9f1">
  <meta name="description" content="Make every moment memorable with personalised and corporate gifts from Craftyvok. Thoughtful gifting, made easy, with pan-India delivery.">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Craftyvok — Gifts that feel like you</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Playfair+Display:wght@600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('styles.css') }}">
  <script src="{{ asset('script.js') }}" defer></script>
</head>
<body>
  <div class="announcement">
    <span class="announcement-sparkle" aria-hidden="true">✦</span>
    Thoughtful gifts, delivered with love — anywhere in India
    <span class="announcement-sparkle" aria-hidden="true">✦</span>
  </div>

  <header class="site-header">
    <a class="brand" href="#home" aria-label="Craftyvok home">
      <img class="brand-logo" src="{{ asset('images/craftyvok-logo.jpg') }}" alt="Craftyvok logo">
      <span class="brand-name">craftyvok<small>GIFTS WITH A PERSONAL TOUCH</small></span>
    </a>
    <button class="menu-toggle" type="button" aria-expanded="false" aria-controls="primary-nav" aria-label="Open menu">
      <span></span><span></span>
    </button>
    <nav class="primary-nav" id="primary-nav" aria-label="Main navigation">
      <a href="#gifts">All products</a>
      <a href="#collection-love">Love</a>
      <a href="#collection-birthday">Birthday gifts</a>
      <a href="#collection-occasions">Occasions</a>
      <a href="#collection-relationship">Relationship</a>
      <a href="#collection-corporate">Corporate</a>
      <a href="#collection-greetings">Greetings</a>
      <a class="orders-nav-link" href="{{ auth()->check() && !auth()->user()->is_admin ? route('orders.index') : route('login') }}">My Orders</a>
    </nav>
    <div class="header-tools">
      <button class="header-icon search-toggle" type="button" aria-label="Search gifts" aria-expanded="false" aria-controls="site-search">
        <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="10.8" cy="10.8" r="6.8"></circle><path d="m16 16 5 5"></path></svg>
      </button>
      <a class="header-icon" href="{{ auth()->check() && auth()->user()->is_admin ? route('admin.dashboard') : (auth()->check() ? route('orders.index') : route('login')) }}" aria-label="{{ auth()->check() ? 'My account' : 'Login' }}" title="{{ auth()->check() ? 'My account' : 'Login' }}">
        <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="7.5" r="4"></circle><path d="M4.5 21v-2a7.5 7.5 0 0 1 15 0v2z"></path></svg>
      </a>
      <a class="header-icon order-icon" href="{{ auth()->check() && !auth()->user()->is_admin ? route('orders.index') : route('login') }}" aria-label="My orders" title="My orders">
        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 8h16l-1 13H5L4 8z"></path><path d="M8 9V6a4 4 0 0 1 8 0v3"></path></svg>
      </a>
    </div>
  </header>
  <form class="site-search" id="site-search" role="search" hidden>
    <label class="sr-only" for="gift-search">Search gifts</label>
    <input id="gift-search" type="search" placeholder="Search gifts…" autocomplete="off">
    <button type="button" class="search-close" aria-label="Close search">×</button>
  </form>

  <main>
    <section class="hero" id="home">
      <div class="hero-copy">
        <p class="eyebrow"><span class="eyebrow-line"></span> LITTLE BOX. BIG FEELINGS.</p>
        <h1>Make their day.<br><span>Make it personal.</span></h1>
        <p class="hero-description">The best gifts say, “I thought of you.” Find something wonderfully personal for every person, moment and milestone.</p>
        <div class="hero-actions">
          <a class="button button-primary" href="#gifts">Find your kind of gift <span aria-hidden="true">→</span></a>
          <a class="text-link" href="#corporate">Gifting for your team <span aria-hidden="true">↗</span></a>
        </div>
        <div class="hero-proof">
          <div class="proof-icons" aria-hidden="true"><span>🎁</span><span>💝</span><span>✨</span></div>
          <p><strong>10+ years</strong> of making moments matter</p>
        </div>
      </div>
      <div class="hero-art">
        <div class="hero-image-wrap">
          <img src="https://images.unsplash.com/photo-1549465220-1a8b9238cd48?auto=format&amp;fit=crop&amp;w=1100&amp;q=85" alt="A bright pink gift box wrapped with a golden ribbon" fetchpriority="high">
          <div class="image-sticker"><span>made with</span><strong>♥</strong><span>for you</span></div>
        </div>
        <div class="floating-note"><span class="note-icon" aria-hidden="true">✳</span><span>A little extra<br><strong>thought goes a long way</strong></span></div>
        <span class="hero-doodle doodle-one" aria-hidden="true">✷</span>
        <span class="hero-doodle doodle-two" aria-hidden="true">✦</span>
      </div>
      <div class="hero-scroll" aria-hidden="true"><span></span> GOOD THINGS THIS WAY</div>
    </section>

    <section class="ticker" aria-label="What we do">
      <div class="ticker-track">
        <span>PERSONALISED GIFTS</span><b>✳</b><span>MADE TO MAKE THEM SMILE</span><b>✳</b><span>CORPORATE GIFTING</span><b>✳</b><span>DELIVERED PAN INDIA</span><b>✳</b>
        <span aria-hidden="true">PERSONALISED GIFTS</span><b aria-hidden="true">✳</b><span aria-hidden="true">MADE TO MAKE THEM SMILE</span><b aria-hidden="true">✳</b><span aria-hidden="true">CORPORATE GIFTING</span><b aria-hidden="true">✳</b><span aria-hidden="true">DELIVERED PAN INDIA</span><b aria-hidden="true">✳</b>
      </div>
    </section>

    <section class="section gifts-section" id="gifts">
      <div class="section-heading">
        <div>
          <p class="eyebrow"><span class="eyebrow-line"></span> MADE TO MATCH THEIR MOMENT</p>
          <h2>Find their kind<br class="mobile-break"> of gift.</h2>
        </div>
        <p class="section-intro">Browse our collections and find a thoughtful little something for their kind of celebration.</p>
      </div>
      <div class="collection-tabs">
        @foreach($giftCategories as $key => $category)
          <a class="collection-tab {{ $category['section_class'] }}" href="#collection-{{ $key }}"><span>{{ $category['label'] }}</span><span aria-hidden="true">↗</span></a>
        @endforeach
      </div>
      <p class="category-footnote">Have something special in mind? <a href="{{ $instagramProfile }}" target="_blank" rel="noopener noreferrer">Tell us about it on Instagram <span aria-hidden="true">↗</span></a></p>
    </section>

    @foreach($giftCategories as $key => $category)
      <section class="gift-collection {{ $category['section_class'] }}" id="collection-{{ $key }}">
        <div class="collection-heading">
          <div>
            <p class="eyebrow"><span class="eyebrow-line"></span> {{ $category['eyebrow'] }}</p>
            <h2>{{ $category['label'] }}</h2>
            <p class="collection-subtitle">{{ $category['subtitle'] }}</p>
          </div>
          <a class="collection-view-all" href="#gifts">All collections <span aria-hidden="true">↑</span></a>
        </div>
        <div class="category-grid">
          @forelse($giftGroups->get($key, collect()) as $gift)
            <article class="category-card collection-card {{ $category['card_class'] }}">
              <div class="category-image">
                <img src="{{ $gift->image_url ?: 'https://images.unsplash.com/photo-1549465220-1a8b9238cd48?auto=format&fit=crop&w=760&q=80' }}" alt="{{ $gift->name }}" loading="lazy">
                <span class="category-number">{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
              </div>
              <div class="category-info"><div><h3>{{ $gift->name }}</h3><p>{{ $gift->description ?: 'Thoughtfully made for you' }}</p></div></div>
              @if($gift->price !== null)<p class="gift-price">₹{{ number_format((float) $gift->price, 2) }}</p>@endif
              <button type="button" class="gift-chat-button js-chat-open" data-chat-item="{{ $gift->name }}">Ask about this gift <span aria-hidden="true">↗</span></button>
            </article>
          @empty
            <p class="collection-empty">More thoughtful gifts are on their way. <a href="{{ $instagramProfile }}" target="_blank" rel="noopener noreferrer">Ask us on Instagram ↗</a></p>
          @endforelse
        </div>
      </section>
    @endforeach

    <section class="social-section" id="videos">
      <div class="section-heading">
        <div><p class="eyebrow"><span class="eyebrow-line"></span> A LITTLE DOSE OF HAPPY</p><h2>New videos &amp; highlights</h2></div>
        <a class="text-link" href="{{ $instagramProfile }}" target="_blank" rel="noopener noreferrer">See all on Instagram <span aria-hidden="true">↗</span></a>
      </div>
      <div class="social-grid">
        @forelse($videos as $video)
          <article class="social-card">
            <div class="social-embed-wrap">
              @if($video->embed_url)
                <iframe class="instagram-embed" src="{{ $video->embed_url }}" title="Craftyvok Instagram video" loading="lazy" scrolling="no" allow="autoplay; clipboard-write; encrypted-media; picture-in-picture; web-share" allowfullscreen></iframe>
              @elseif($video->thumbnail_url)
                <a class="social-image" href="{{ $video->permalink ?: $instagramProfile }}" target="_blank" rel="noopener noreferrer"><img src="{{ $video->thumbnail_url }}" alt="" loading="lazy"><span class="social-play" aria-hidden="true">▶</span></a>
              @else
                <a class="social-placeholder" href="{{ $video->permalink ?: $instagramProfile }}" target="_blank" rel="noopener noreferrer">Watch on Instagram ↗</a>
              @endif
            </div>
            <p class="social-label">NEW VIDEO <span>{{ $video->published_at?->format('d M Y') }}</span></p>
            <a class="social-caption" href="{{ $video->permalink ?: $instagramProfile }}" target="_blank" rel="noopener noreferrer">{{ \Illuminate\Support\Str::limit($video->caption ?: 'A little inspiration from Craftyvok', 86) }} <span class="social-watch-link">Watch on Instagram ↗</span></a>
          </article>
        @empty
          <div class="social-empty"><span aria-hidden="true">✦</span><p>New Instagram videos will show up here once the account is connected.</p></div>
        @endforelse
      </div>
      @if($highlights->isNotEmpty())
        <div class="highlight-heading"><p class="eyebrow"><span class="eyebrow-line"></span> SAVED STORIES</p></div>
        <div class="highlights-grid">
          @foreach($highlights as $highlight)
            <a class="highlight-card" href="{{ $highlight->permalink ?: $instagramProfile }}" target="_blank" rel="noopener noreferrer">
              @if($highlight->thumbnail_url)<img src="{{ $highlight->thumbnail_url }}" alt="" loading="lazy">@endif
              <span>{{ $highlight->title ?: 'Instagram highlight' }}</span>
            </a>
          @endforeach
        </div>
      @endif
    </section>

    <section class="story-section" id="about">
      <div class="story-photo">
        <img src="https://images.unsplash.com/photo-1544816155-12df9643f363?auto=format&amp;fit=crop&amp;w=1000&amp;q=85" alt="A thoughtfully assembled gift with a personal touch" loading="lazy">
        <div class="story-stamp"><span>THOUGHTFUL</span><strong>by<br>nature</strong><span>ALWAYS</span></div>
      </div>
      <div class="story-copy">
        <p class="eyebrow"><span class="eyebrow-line"></span> A LITTLE ABOUT US</p>
        <h2>We believe a gift should feel like <em>you.</em></h2>
        <p>Not just another thing in a box. A detail that brings back a memory. A name that makes it theirs. A little “I knew you’d love this.”</p>
        <p>For more than 10 years, Craftyvok has helped people and teams turn good intentions into gifts worth remembering.</p>
        <a class="button button-outline" href="{{ $instagramProfile }}" target="_blank" rel="noopener noreferrer">Get to know us <span aria-hidden="true">↗</span></a>
        <span class="story-sparkle" aria-hidden="true">✳</span>
      </div>
    </section>

    <section class="steps-section" id="steps-section">
      <div class="steps-heading">
        <p class="eyebrow"><span class="eyebrow-line"></span> EASY AS ONE, TWO, YOU</p>
        <h2>Thoughtful gifting.<br><span>Without the guesswork.</span></h2>
      </div>
      <div class="steps-grid">
        <article class="step"><span class="step-number">01</span><span class="step-emoji" aria-hidden="true">💭</span><h3>Tell us the story</h3><p>Who’s it for? What are we celebrating? Share the details with us.</p></article>
        <article class="step"><span class="step-number">02</span><span class="step-emoji" aria-hidden="true">🎨</span><h3>Make it personal</h3><p>We’ll help you find a thoughtful gift and add those special touches.</p></article>
        <article class="step"><span class="step-number">03</span><span class="step-emoji" aria-hidden="true">🚚</span><h3>Send the happy</h3><p>We’ll get it gift-ready and deliver across India, with care.</p></article>
      </div>
    </section>

    <section class="corporate-section" id="corporate">
      <div class="corporate-copy">
        <p class="eyebrow"><span class="eyebrow-line"></span> FOR THE PEOPLE WHO MAKE IT HAPPEN</p>
        <h2>Good work deserves<br>a <span>great gift.</span></h2>
        <p>Welcome new teammates, celebrate milestones and say a proper thank-you. We make corporate gifting feel personal, not like another item on the agenda.</p>
        <a class="button button-dark" href="{{ $instagramProfile }}" target="_blank" rel="noopener noreferrer">Let’s talk gifting <span aria-hidden="true">↗</span></a>
        <div class="corporate-squiggle" aria-hidden="true">〰</div>
      </div>
      <div class="corporate-image">
        <img src="https://images.unsplash.com/photo-1512909006721-3d6018887383?auto=format&amp;fit=crop&amp;w=1000&amp;q=85" alt="A collection of gifts ready to bring a team together" loading="lazy">
        <span class="corporate-label">A little appreciation<br>goes a long way <b>♥</b></span>
      </div>
    </section>

    <section class="closing-section">
      <span class="closing-flower" aria-hidden="true">✿</span>
      <p class="eyebrow">YOUR NEXT “THEY LOVED IT!” MOMENT STARTS HERE</p>
      <h2>Let’s find <em>the one.</em></h2>
      <p>Tell us who you’re celebrating. We’ll take it from there.</p>
      <a class="button button-primary" href="{{ $instagramProfile }}" target="_blank" rel="noopener noreferrer">DM us on Instagram <span aria-hidden="true">↗</span></a>
      <span class="closing-sparkle sparkle-left" aria-hidden="true">✦</span>
      <span class="closing-sparkle sparkle-right" aria-hidden="true">✳</span>
    </section>
  </main>

  <section class="review-section" aria-labelledby="review-title">
    <h2 id="review-title">Loved your Craftyvok gift?</h2>
    <p>Your honest review helps someone find their next favourite gift.</p>
    @if($googleReviewUrl)
      <a class="button button-outline" href="{{ $googleReviewUrl }}" target="_blank" rel="noopener noreferrer">Leave a Google review <span aria-hidden="true">↗</span></a>
    @else
      <p class="review-note">Our Google review link is being connected. Please check back soon.</p>
    @endif
  </section>

  <footer class="site-footer">
    <div class="footer-grid">
      <div class="footer-about">
        <a class="footer-brand" href="#home" aria-label="Craftyvok home">
          <img class="footer-logo" src="{{ asset('images/craftyvok-logo.jpg') }}" alt="Craftyvok logo">
          <span>Craftyvok</span>
        </a>
        <p>Thoughtful gifts made to celebrate every special moment, relationship and occasion.</p>
        <p class="footer-experience">Personalised gifting with a personal touch — for over 10 years.</p>
      </div>
      <nav class="footer-column" aria-label="Quick links">
        <h2>Quick Links</h2>
        <a href="#about">About Us</a>
        <a href="#gifts">Shop Gifts</a>
        <button type="button" class="footer-link-button js-chat-open" data-chat-item="Contact Craftyvok">Contact Us</button>
        <a href="#videos">New Videos</a>
        <a href="{{ auth()->check() && !auth()->user()->is_admin ? route('orders.index') : route('login') }}">Track Order</a>
      </nav>
      <nav class="footer-column" aria-label="Policies">
        <h2>Policies</h2>
        <button type="button" class="footer-link-button js-chat-open" data-chat-item="Shipping policy">Shipping Policy</button>
        <button type="button" class="footer-link-button js-chat-open" data-chat-item="Returns and exchanges">Return Policy</button>
        <button type="button" class="footer-link-button js-chat-open" data-chat-item="Privacy policy">Privacy Policy</button>
        <button type="button" class="footer-link-button js-chat-open" data-chat-item="Terms of service">Terms of Service</button>
      </nav>
      <div class="footer-column footer-social">
        <h2>Follow Us</h2>
        <div class="footer-social-links">
          <a href="{{ $instagramProfile }}" target="_blank" rel="noopener noreferrer" aria-label="Craftyvok on Instagram" title="Instagram">
            <svg viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="3" width="18" height="18" rx="5"></rect><circle cx="12" cy="12" r="4"></circle><circle class="social-dot" cx="18" cy="6.5" r="1"></circle></svg>
          </a>
          <a href="https://share.google/ZwqudRvUbKLdJjsRG" target="_blank" rel="noopener noreferrer" aria-label="Craftyvok on Google" title="Google">
            <span class="google-g" aria-hidden="true">G</span>
          </a>
        </div>
      </div>
    </div>
    <div class="footer-bottom">
      <small>© <span id="year"></span> Craftyvok. All rights reserved.</small>
      <small>Made with <span class="footer-heart" aria-label="love">♥</span> for every special moment.</small>
      <a href="{{ route('admin.login') }}">Admin sign-in</a>
    </div>
  </footer>
  <aside class="chat-widget" data-start-url="{{ route('chat.start') }}" data-messages-url="{{ route('chat.messages', ['conversation' => '__ID__']) }}" data-send-url="{{ route('chat.send', ['conversation' => '__ID__']) }}">
    <button class="chat-toggle" type="button" aria-expanded="false" aria-controls="chat-panel"><span aria-hidden="true">✉</span> Ask us about a gift</button>
    <section class="chat-panel" id="chat-panel" aria-label="Gift enquiry chat" hidden>
      <div class="chat-header"><div><strong>Craftyvok chat</strong><small>Ask about gifts and prices</small></div><button class="chat-close" type="button" aria-label="Close chat">×</button></div>
      <div class="chat-messages" aria-live="polite"><p class="chat-welcome">Hi there! Ask us about a gift — no account needed.</p></div>
      <form class="chat-form">
        <label class="sr-only" for="chat-message">Your message</label>
        <input id="chat-message" name="message" maxlength="2000" placeholder="Type your question…" autocomplete="off" required>
        <button type="submit" aria-label="Send message">Send</button>
      </form>
      <p class="chat-status" aria-live="polite"></p>
    </section>
  </aside>
</body>
</html>
