@extends('layouts.app')

@section('head')
    {{-- Tailwind CDN with prefix and no preflight to avoid Bootstrap conflicts --}}
    <script>
        tailwind = { config: { prefix: 'tw-', corePlugins: { preflight: false } } };
    </script>
    <script src="https://cdn.tailwindcss.com"></script>
@endsection

@section('content')
<div class="tw-min-h-screen tw-bg-gradient-to-br tw-from-slate-900 tw-via-slate-800 tw-to-slate-900 tw-flex tw-items-center tw-justify-center tw-px-4">
  <div class="tw-w-full tw-max-w-md">
    <div class="tw-backdrop-blur tw-bg-white/10 tw-border tw-border-white/10 tw-rounded-2xl tw-shadow-2xl tw-p-6">

      {{-- Header --}}
      <div class="tw-text-center tw-mb-6">
        <div class="tw-flex tw-items-center tw-justify-center tw-gap-2 tw-mb-2">
          <span class="tw-inline-flex tw-items-center tw-justify-center tw-h-10 tw-w-10 tw-rounded-xl tw-bg-emerald-400/20 tw-border tw-border-emerald-300/30">
            <!-- key icon -->
            <svg class="tw-h-5 tw-w-5 tw-text-emerald-300" viewBox="0 0 24 24" fill="none">
              <path d="M15 7a4 4 0 1 0-3.446 3.95L12 15h2l1 2h2l1 2h2l1-2-4-4 .446-4.05A4 4 0 0 0 15 7Z" stroke="currentColor" stroke-width="1.2" />
            </svg>
          </span>
          <h1 class="tw-text-xl tw-font-semibold tw-text-white">Set a new password</h1>
        </div>
        <p class="tw-text-slate-300 tw-text-sm">Make it strong. Your future self will thank you.</p>
      </div>

      {{-- Errors --}}
      @if ($errors->any())
        <div class="tw-mb-4 tw-rounded-xl tw-bg-red-500/10 tw-border tw-border-red-400/30 tw-px-4 tw-py-3">
          <ul class="tw-text-red-200 tw-text-sm tw-space-y-1">
            @foreach ($errors->all() as $e)
              <li>• {{ $e }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      {{-- Form --}}
      <form method="POST" action="{{ route('password.update') }}" id="resetForm" novalidate>
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">
        <input type="hidden" name="email" value="{{ old('email', request('email')) }}">

        {{-- Password --}}
        <label class="tw-text-slate-200 tw-text-sm tw-font-medium">New password</label>
        <div class="tw-relative tw-mt-1">
          <input
            id="password"
            name="password"
            type="password"
            required
            autocomplete="new-password"
            class="tw-w-full tw-rounded-xl tw-bg-slate-900/60 tw-border tw-border-white/10 tw-text-slate-100 tw-px-4 tw-py-3 focus:tw-outline-none focus:tw-ring-2 focus:tw-ring-emerald-400/60"
            placeholder="••••••••" />
          <button type="button" id="togglePw" class="tw-absolute tw-inset-y-0 tw-right-2 tw-my-auto tw-h-8 tw-w-8 tw-rounded-lg tw-text-slate-300 hover:tw-bg-white/10 focus:tw-outline-none" aria-label="Show password">
            <svg id="eyeIcon" class="tw-h-5 tw-w-5 tw-mx-auto" viewBox="0 0 24 24" fill="none">
              <path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12Z" stroke="currentColor" stroke-width="1.2"/>
              <circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="1.2"/>
            </svg>
          </button>
        </div>

        {{-- Strength meter --}}
        <div class="tw-mt-3">
          <div class="tw-flex tw-items-center tw-justify-between tw-text-xs tw-text-slate-300">
            <span>Password strength</span>
            <span id="strengthLabel" class="tw-font-medium">Too weak</span>
          </div>
          <div class="tw-mt-1 tw-h-2 tw-w-full tw-rounded-full tw-bg-white/10">
            <div id="strengthBar" class="tw-h-2 tw-rounded-full tw-bg-red-400 tw-w-[10%] tw-transition-all"></div>
          </div>
          <ul class="tw-mt-3 tw-text-xs tw-text-slate-300 tw-space-y-1">
            <li id="rule-length" class="tw-flex tw-items-center"><span class="tw-rule-dot tw-inline-block tw-h-2 tw-w-2 tw-rounded-full tw-bg-red-400 tw-mr-2"></span> At least 8 characters</li>
            <li id="rule-mix" class="tw-flex tw-items-center"><span class="tw-rule-dot tw-inline-block tw-h-2 tw-w-2 tw-rounded-full tw-bg-red-400 tw-mr-2"></span> Upper & lower case</li>
            <li id="rule-num" class="tw-flex tw-items-center"><span class="tw-rule-dot tw-inline-block tw-h-2 tw-w-2 tw-rounded-full tw-bg-red-400 tw-mr-2"></span> At least one number</li>
            <li id="rule-sym" class="tw-flex tw-items-center"><span class="tw-rule-dot tw-inline-block tw-h-2 tw-w-2 tw-rounded-full tw-bg-red-400 tw-mr-2"></span> At least one symbol</li>
          </ul>
        </div>

        {{-- Confirm --}}
        <label class="tw-text-slate-200 tw-text-sm tw-font-medium tw-mt-5">Confirm password</label>
        <div class="tw-relative tw-mt-1">
          <input
            id="password_confirmation"
            name="password_confirmation"
            type="password"
            required
            autocomplete="new-password"
            class="tw-w-full tw-rounded-xl tw-bg-slate-900/60 tw-border tw-border-white/10 tw-text-slate-100 tw-px-4 tw-py-3 focus:tw-outline-none focus:tw-ring-2 focus:tw-ring-emerald-400/60"
            placeholder="••••••••" />
          <span id="matchBadge" class="tw-absolute tw-right-2 tw-top-1/2 -tw-translate-y-1/2 tw-text-xs tw-rounded-full tw-px-2 tw-py-1 tw-bg-white/10 tw-text-slate-200 tw-hidden">Match</span>
        </div>

        {{-- Submit --}}
        <button type="submit"
            class="tw-mt-6 tw-w-full tw-bg-emerald-400 hover:tw-bg-emerald-300 tw-text-slate-900 tw-font-semibold tw-py-3 tw-rounded-xl tw-transition">
          Save new password
        </button>

        {{-- Small print --}}
        <p class="tw-text-[11px] tw-text-slate-400 tw-text-center tw-mt-3">
          By saving, you’ll be redirected to the login screen.
        </p>
      </form>
    </div>
  </div>
</div>

{{-- Minimal interactivity (no deps) --}}
<script>
  const pwd = document.getElementById('password');
  const confirmPwd = document.getElementById('password_confirmation');
  const strengthBar = document.getElementById('strengthBar');
  const strengthLabel = document.getElementById('strengthLabel');
  const matchBadge = document.getElementById('matchBadge');
  const rules = {
    length: document.getElementById('rule-length'),
    mix: document.getElementById('rule-mix'),
    num: document.getElementById('rule-num'),
    sym: document.getElementById('rule-sym'),
  };

  function assessStrength(v) {
    const checks = {
      length: v.length >= 8,
      mix: /[a-z]/.test(v) && /[A-Z]/.test(v),
      num: /\d/.test(v),
      sym: /[^a-zA-Z0-9]/.test(v),
    };
    let score = Object.values(checks).filter(Boolean).length;

    // UI updates
    Object.entries(checks).forEach(([k, ok]) => {
      const dot = rules[k].querySelector('.tw-rule-dot');
      dot.classList.toggle('tw-bg-emerald-400', ok);
      dot.classList.toggle('tw-bg-red-400', !ok);
    });

    const widths = ['10%','35%','60%','85%','100%'];
    const colors = ['tw-bg-red-400','tw-bg-orange-400','tw-bg-yellow-300','tw-bg-lime-300','tw-bg-emerald-400'];
    const labels = ['Too weak','Weak','Fair','Good','Strong'];

    strengthBar.className = 'tw-h-2 tw-rounded-full tw-transition-all ' + colors[score];
    strengthBar.style.width = widths[score];
    strengthLabel.textContent = labels[score];
  }

  function checkMatch() {
    const match = pwd.value && confirmPwd.value && pwd.value === confirmPwd.value;
    matchBadge.classList.toggle('tw-hidden', !match);
  }

  pwd.addEventListener('input', () => { assessStrength(pwd.value); checkMatch(); });
  confirmPwd.addEventListener('input', checkMatch);

  document.getElementById('togglePw').addEventListener('click', () => {
    const type = pwd.type === 'password' ? 'text' : 'password';
    pwd.type = type;
    // swap eye icon (simple strike-through)
    const eye = document.getElementById('eyeIcon');
    if (type === 'text') {
      eye.innerHTML = '<path d="M3 3l18 18" stroke="currentColor" stroke-width="1.2"/><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12Z" stroke="currentColor" stroke-width="1.2"/>';
    } else {
      eye.innerHTML = '<path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12Z" stroke="currentColor" stroke-width="1.2"/><circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="1.2"/>';
    }
  });

  // Initialize UI
  assessStrength('');
</script>
@endsection
