@extends('admin.layout')

@section('content')
  <div class="admin-page-heading">
    <div>
      <p class="eyebrow"><span class="eyebrow-line"></span> CUSTOMER QUESTIONS</p>
      <h1>Gift enquiry inbox</h1>
      <p>Reply to customer questions and share gift prices directly.</p>
    </div>
  </div>

  <section class="admin-section admin-enquiry-page">
    @forelse($conversations as $conversation)
      <article class="admin-chat-card">
        <div class="admin-chat-meta"><strong>{{ $conversation->item_name }}</strong><span>{{ $conversation->status }} · {{ $conversation->last_message_at?->format('d M, g:i a') }}</span></div>
        <div class="admin-chat-messages">
          @foreach($conversation->messages as $message)
            <p class="admin-message admin-message-{{ $message->sender }}"><strong>{{ ucfirst($message->sender) }}:</strong> {{ $message->body }}</p>
          @endforeach
        </div>
        <form method="post" action="{{ route('admin.chat.reply', $conversation) }}" class="admin-reply-form">
          @csrf
          <label class="sr-only" for="reply-{{ $conversation->id }}">Reply to {{ $conversation->item_name }}</label>
          <input id="reply-{{ $conversation->id }}" name="message" maxlength="2000" placeholder="Type a price or reply…" required>
          <button type="submit">Send reply</button>
        </form>
      </article>
    @empty
      <p class="admin-empty">New customer chats will appear here.</p>
    @endforelse
  </section>
@endsection
