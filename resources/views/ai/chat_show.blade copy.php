@extends('admin.layouts.app')

@section('title', 'AI Chat')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

@php
  $u = auth()->user()->name;
  $employees = DB::table('employees')->where('id', $u)->select('name', 'lastname', 'image')->first();
  $username = $employees->name ?? auth()->user()->name;
  $image = $employees->image ?? 'default.png';
  $avatarJpg = asset('images/employee/'.$image);
  $avatarPng = asset('images/employee/'.$image);
@endphp

<div class="pt-16 md:pt-20 min-h-[calc(100vh-5rem)] bg-gradient-to-b from-slate-50 to-slate-100">
  <!-- Chat header -->
  <header class="sticky top-16 md:top-20 z-10 bg-white/80 backdrop-blur border-b">
    <div class="max-w-5xl mx-auto px-2 py-2 d-flex justify-content-between">
      <div>
        <div class="text-xs text-gray-500">Kunde #{{ $chat->customer_id }}</div>
        <div class="fw-semibold">{{ $chat->title ?? 'Chat '.$chat->id }}</div>
      </div>
      <div class="d-flex gap-2">
        <button id="shareBtn" class="btn   btn-outline-secondary">Share</button>
        <form method="POST" action="{{ route('ai.chats.reset_memory',$chat) }}">
          @csrf
          <button class="btn  btn-outline-secondary">Reset Memory</button>
        </form>
      </div>
    </div>

    <!-- Thinking / status steps -->
  <div id="statusBar" class="mt-1 rounded-3 border bg-white px-3 py-2 small text-primary d-none">
    <div class="d-flex align-items-center mb-1">
      <div class="spinner-border spinner-border-sm me-2"></div>
      <span id="statusTitle">Thinking…</span>
    </div>
    <div id="statusSteps" class="d-flex flex-wrap gap-2 small">
      <div class="step" data-step="0"><span class="badge bg-secondary"><i class="fa fa-dot-circle-o"></i></span> Reading customer data ›</div>
      <div class="step" data-step="1"><span class="badge bg-secondary"><i class="fa fa-dot-circle-o"></i></span> Retrieving related info ›</div>
      <div class="step" data-step="2"><span class="badge bg-secondary"><i class="fa fa-dot-circle-o"></i></span> Calculating / Estimating ›</div>
      <div class="step" data-step="3"><span class="badge bg-secondary"><i class="fa fa-dot-circle-o"></i></span> Composing answer</div>
    </div>
  </div>

  </header>

  <!-- Chat log -->
  <section id="log" class="max-w-5xl mx-auto px-4 py-4 overflow-y-auto" style="max-height:60vh">
    @foreach($chat->messages as $m)
      @php $isUser = $m->role === 'user'; @endphp
      <div class="d-flex mb-3 {{ $isUser ? 'justify-content-end' : '' }}">
        @if(!$isUser)
          <div class="me-2">
            <div class="rounded-circle bg-light text-primary d-flex justify-content-center align-items-center" style="width:36px;height:36px;">
              <i data-feather="cpu"></i>
            </div>
          </div>
        @endif
        <div class="p-3 rounded-3 shadow-sm {{ $isUser ? 'bg-primary text-white' : 'bg-white border' }}" style="max-width:70%;">
          <div class="small text-muted mb-1">
            {{ $isUser ? $username : 'Assistant' }} · {{ optional($m->created_at)->format('d.m.Y H:i') }}
          </div>
          {!! \Illuminate\Support\Str::markdown($m->content, ['html_input' => 'strip', 'allow_unsafe_links' => false]) !!}



        </div>
        @if($isUser)
          <div class="ms-2">
            <img src="{{ $avatarJpg }}" onerror="this.onerror=null;this.src='{{ $avatarPng }}';"
                 class="rounded-circle border" style="width:36px;height:36px;object-fit:cover;">
          </div>
        @endif
      </div>
    @endforeach
  </section>

  <!-- Composer -->
  <footer class="sticky bottom-0 bg-white border-top">
    <div class="max-w-5xl mx-auto p-2 d-flex gap-2 align-items-center">
      
      <!-- Mic -->
      <button id="micBtn" type="button" class="btn btn-outline-secondary d-flex align-items-center justify-content-center" style="width:46px;height:46px;">
        <i class="feather icon-mic"></i>
      </button>

      <!-- Text input -->
      <textarea id="msgInput" rows="1"
        class="form-control flex-grow-1"
        placeholder="Ask something…"></textarea>

      <!-- Send -->
      <button id="sendBtn" class="btn btn-primary d-flex align-items-center gap-1">
        <i class="feather icon-send"></i><span>Send</span>
      </button>
    </div>
  </footer>

</div>
@endsection
 @section('script')
<script src="https://unpkg.com/feather-icons"></script>
<script>
if (window.feather) feather.replace();

(() => {
  const USER_NAME   = @json($username);
  const USER_AVATAR = @json($avatarJpg);
  const POST_URL    = @json(url('/ai/chats/'.$chat->id.'/message'));
  const CSRF        = @json(csrf_token());

  // -------- UI helpers --------
  function addBubble(role, content) {
    const isUser = role === 'user';
    const html = `
      <div class="d-flex mb-3 ${isUser ? 'justify-content-end' : ''}">
        ${!isUser ? `
          <div class="me-2">
            <div class="rounded-circle bg-light text-primary d-flex justify-content-center align-items-center" style="width:36px;height:36px;">
              <i data-feather="cpu"></i>
            </div>
          </div>` : ''}
        <div class="p-3 rounded-3 shadow-sm ${isUser ? 'bg-primary text-white' : 'bg-white border'}" style="max-width:70%;">
          <div class="small text-muted mb-1">
            ${isUser ? USER_NAME : 'Assistant'} · ${new Date().toLocaleString()}
          </div>
          <div>${(content || '').replace(/\n/g,'<br>')}</div>
        </div>
        ${isUser ? `
          <div class="ms-2">
            <img src="${USER_AVATAR}" class="rounded-circle border" style="width:36px;height:36px;object-fit:cover;">
          </div>` : ''}
      </div>`;
    $('#log').append(html);
    if (window.feather) feather.replace();
    $('#log').scrollTop($('#log')[0].scrollHeight);
  }

  function addTypingBubble() {
    const id = 'loader-' + Date.now();
    const html = `
      <div class="d-flex mb-3" id="${id}">
        <div class="me-2">
          <div class="rounded-circle bg-light text-primary d-flex justify-content-center align-items-center" style="width:36px;height:36px;">
            <i data-feather="cpu"></i>
          </div>
        </div>
        <div class="p-3 rounded-3 border bg-white" style="max-width:70%;">
          <div class="small text-muted mb-1">Assistant · typing…</div>
          <div id="stream-${id}"></div>
        </div>
      </div>`;
    $('#log').append(html);
    if (window.feather) feather.replace();
    $('#log').scrollTop($('#log')[0].scrollHeight);

    const $container = $(`#${id}`);
    const $stream    = $(`#stream-${id}`);
    return {
      id,
      update(html) {
        $stream.html(html);
        $('#log').scrollTop($('#log')[0].scrollHeight);
      },
      remove() { $container.remove(); }
    };
  }

  // -------- Status bar --------
  function setStep(i, state) {
    const $steps = $('#statusSteps .step');
    if (i < 0 || i >= $steps.length) return;
    const $b = $steps.eq(i).find('.badge');
    $b.removeClass('bg-secondary bg-primary bg-success');
    if (state === 'active') $b.addClass('bg-primary');
    if (state === 'done')   $b.addClass('bg-success');
    if (state === 'reset')  $b.addClass('bg-secondary');
  }
  function showStatus() {
    $('#statusBar').removeClass('d-none');
    $('#statusSteps .badge').removeClass('bg-primary bg-success').addClass('bg-secondary');
    setStep(0, 'active');
  }
  function hideStatus() {
    setTimeout(() => $('#statusBar').addClass('d-none'), 800);
  }
  // gentle auto-advance until first tokens; after first token we jump to last step
  let progressTimer = null;
  function autoProgress(interval = 900) {
    const $steps = $('#statusSteps .step');
    let i = 0;
    progressTimer = setInterval(() => {
      setStep(i, 'done');
      if (i + 1 < $steps.length) setStep(i + 1, 'active');
      i++;
      if (i >= $steps.length - 1) { clearInterval(progressTimer); }
    }, interval);
  }
  function finalizeProgress() {
    if (progressTimer) clearInterval(progressTimer);
    // Jump to composing step as soon as tokens arrive
    setStep(2, 'done');
    setStep(3, 'active');
  }

  // -------- Streaming POST (SSE over fetch) --------
  async function streamChat(message) {
    // Kick UI
    addBubble('user', message);
    showStatus();
    autoProgress();
    const typing = addTypingBubble();

    let controller = new AbortController();
    let firstChunk = true;
    let fullText   = '';

    try {
      const res = await fetch(POST_URL, {
        method: 'POST',
        headers: {
          'Content-Type'  : 'application/json',
          'X-CSRF-TOKEN'  : CSRF,
          'Accept'        : 'text/event-stream',
          'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({ message }),
        credentials: 'same-origin',
        signal: controller.signal
      });

      if (!res.ok || !res.body) {
        typing.remove();
        hideStatus();
        addBubble('assistant', `⚠️ Server error ${res.status}`);
        return;
      }

      const reader = res.body.getReader();
      const decoder = new TextDecoder();
      let buffer = '';

      while (true) {
        const { done, value } = await reader.read();
        if (done) break;

        buffer += decoder.decode(value, { stream: true });

        // Parse SSE frames by empty-line separation
        const events = buffer.split('\n\n');
        buffer = events.pop() || ''; // keep partial

        for (const evt of events) {
          const line = evt.split('\n').find(l => l.startsWith('data: '));
          if (!line) continue;
          try {
            const payload = JSON.parse(line.slice(6)); // after "data: "
            if (payload.chunk) {
              if (firstChunk) { finalizeProgress(); firstChunk = false; }
              fullText += payload.chunk;
              typing.update(fullText.replace(/\n/g, '<br>'));
            }
            if (payload.done) {
              typing.remove();
              setStep(3, 'done');
              hideStatus();
              addBubble('assistant', fullText);
            }
          } catch(e) {
            // ignore JSON parse blips from partial lines
          }
        }
      }
    } catch (err) {
      typing.remove();
      hideStatus();
      // Special-casing common backend timeouts to give a helpful hint
      const msg = String(err && err.message || err);
      const hint = msg.includes('AbortError') ? 'Request cancelled.' :
                   'If this keeps happening, try a lighter model (llama3.2:1b) or raise backend timeout.';
      addBubble('assistant', '⚠️ Stream failed. ' + hint);
    }
  }

  // -------- Send button / Enter key --------
  $(document).on('click', '#sendBtn', () => {
    const $inp = $('#msgInput');
    const txt = $inp.val().trim();
    if (!txt) return;
    $inp.val('');
    streamChat(txt);
  });

  $('#msgInput').on('keydown', function(e){
    if (e.key === 'Enter' && !e.shiftKey) {
      e.preventDefault();
      $('#sendBtn').click();
    }
  });

  // -------- Mic (unchanged, tidy) --------
  (function mic() {
    const SR = window.SpeechRecognition || window.webkitSpeechRecognition;
    let rec = null, listening = false;

    function toggleMic() {
      if (!SR) { alert('Speech recognition not supported in this browser.'); return; }
      if (listening) { try { rec.stop(); } catch(_){} return; }

      rec = new SR();
      rec.lang = 'en-US';
      rec.interimResults = true;
      rec.continuous = false;
      listening = true;

      $('#micBtn').removeClass('btn-outline-secondary').addClass('btn-danger').html('<i data-feather="square"></i>');
      feather.replace();

      let finalText = '';
      rec.onresult = (e) => {
        let interim = '';
        for (let i = e.resultIndex; i < e.results.length; ++i) {
          const t = e.results[i][0].transcript;
          if (e.results[i].isFinal) finalText += t; else interim += t;
        }
        $('#msgInput').val((finalText + ' ' + interim).trim());
      };
      const cleanup = () => {
        listening = false;
        $('#micBtn').removeClass('btn-danger').addClass('btn-outline-secondary').html('<i data-feather="mic"></i>');
        feather.replace();
        const txt = $('#msgInput').val().trim();
        if (txt) $('#sendBtn').click();
      };
      rec.onerror = cleanup;
      rec.onend   = cleanup;
      try { rec.start(); } catch(_) { cleanup(); }
    }

    $('#micBtn').on('click', toggleMic);
  })();
})();
</script>
@endsection
