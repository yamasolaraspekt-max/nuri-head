@extends('admin.layouts.app')
@section('title') Lohn Vollkosten @stop

@section('style')
  <!-- Tailwind utilities only (no preflight to avoid Bootstrap conflicts) -->
  <script>
    window.tailwind = { config: { corePlugins: { preflight: false } } };
  </script>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    /* Decorative helpers */
    .will-tilt { transform-style: preserve-3d; transition: transform .2s ease, box-shadow .2s ease; }
    .will-tilt.tilting { box-shadow: 0 16px 40px rgba(0,0,0,.18); }
    .shine::after{
      content:""; position:absolute; inset:-1px; border-radius:1.25rem; pointer-events:none;
      background: radial-gradient(400px 140px at var(--mx,50%) var(--my,50%), rgba(124,58,237,.18), transparent 60%);
      mix-blend-mode:screen; opacity:.85; transition: opacity .2s ease;
    }
  </style>
@endsection

@section('content')
<div class="app-content content">
  <div class="content-overlay"></div>
  <div class="header-navbar-shadow"></div>

  <div class="content-wrapper">
    <div class="content-header row"></div>
    <div class="content-body">

      <!-- Header / actions -->
      <div class="flex items-center justify-between gap-4 mb-4 px-2">
        <h4 class="m-0 font-semibold text-lg">Mitarbeiter Lohn Vollkosten</h4>
        <a class="btn btn-outline-primary btn-lg"
           href="{{ url('upload_salary/'.request()->id) }}">
          Aktualisierung
        </a>
      </div>

      <!-- Card grid -->
      <div class="px-2">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 2xl:grid-cols-4 gap-6">
          @foreach($data as $item)
            @php
              $avatar = asset('images/employee/'.$item->image);
              $monat  = e($item->salary_month).'.'.e($item->salary_year);
              // Numbers
              $perHour    = number_format((float)$item->wege_per_hour, 2, ',', '.'); // Lohn pro Stunde
              $prodHour   = number_format((float)$item->productive_hour_wege, 2, ',', '.'); // Stundenlohn nach Produktivstunde
              $totalMonth = number_format((float)$item->total_monthly_salary, 2, ',', '.'); // Monatlich gesamt
            @endphp

            <article class="relative bg-white rounded-2xl p-5 shadow will-tilt shine">
              <!-- Top row -->
              <div class="flex items-start justify-between gap-3">
                <div class="flex items-center gap-3">
                  <img src="{{ $avatar }}" class="w-12 h-12 rounded-full object-cover" alt="avatar">
                  <div>
                    <div class="font-semibold leading-tight">
                      {{ $item->name }} {{ $item->lastname }}
                    </div>
                    <div class="text-xs text-gray-500">
                      <i class="feather icon-calendar"></i>
                      {{ $monat }}
                    </div>
                  </div>
                </div>

                <!-- € per hour badge -->
                <span class="inline-flex items-center gap-1 rounded-full px-3 py-1 text-xs font-medium bg-purple-100 text-purple-700">
                  € {{ $perHour }} / h
                </span>
              </div>

              <!-- Main number -->
              <div class="mt-4">
                <div class="text-sm text-gray-500">Monatlicher Lohn (gesamt)</div>
                <div class="text-2xl font-extrabold tracking-tight">€ {{ $totalMonth }}</div>
              </div>

              <!-- Four paragraphs -->
              <div class="mt-4 space-y-3 text-sm">
                <p class="flex gap-3">
                  <span class="grid place-items-center w-7 h-7 rounded-lg bg-gray-100 text-gray-700">
                    <i class="feather icon-clock"></i>
                  </span>
                  <span>
                    <strong>Produktivzeit:</strong>
                    {{ $item->productive_hour }} Std/Jahr
                  </span>
                </p>

                <p class="flex gap-3">
                  <span class="grid place-items-center w-7 h-7 rounded-lg bg-gray-100 text-gray-700">
                    <i class="feather icon-trending-up"></i>
                  </span>
                  <span>
                    <strong>Produktivlohn:</strong>
                    € {{ $prodHour }} / h
                  </span>
                </p>

                <p class="flex gap-3">
                  <span class="grid place-items-center w-7 h-7 rounded-lg bg-gray-100 text-gray-700">
                    <i class="feather icon-activity"></i>
                  </span>
                  <span>
                    <strong>Krankheit:</strong>
                    {{ $item->sick_leave }} Tage ({{ $item->sick_leave_hour }} Std)
                  </span>
                </p>

                <p class="flex gap-3">
                  <span class="grid place-items-center w-7 h-7 rounded-lg bg-gray-100 text-gray-700">
                    <i class="feather icon-sun"></i>
                  </span>
                  <span>
                    <strong>Urlaub:</strong>
                    {{ $item->holiday }} Tage ({{ $item->holiday_hour }} Std)
                  </span>
                </p>
              </div>

              <!-- Divider -->
              <div class="mt-4 h-px bg-gray-100"></div>

              <!-- Actions -->
              <div class="mt-4 flex items-center justify-between">
                <div class="flex gap-2">
                  <a href="{{ url('upload_salary/'.$item->id) }}"
                     class="inline-flex items-center gap-2 rounded-full px-4 py-2 text-sm font-medium bg-purple-600 text-white hover:bg-purple-500">
                    <i class="feather icon-refresh-ccw"></i> Aktualisierung
                  </a>
                </div>
                <div class="flex gap-2">
                  <button type="button"
                          class="btn btn-icon btn-icon rounded-circle btn-danger"
                          data-toggle="modal" data-target="#delete-emp{{$item->id}}">
                    <i class="feather icon-trash"></i>
                  </button>
                </div>
              </div>

              <!-- Delete Modal (Bootstrap) -->
              <div class="modal fade text-left" id="delete-emp{{$item->id}}" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-dialog-scrollable" role="document">
                  <div class="modal-content">
                    <div class="modal-header">
                      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                      </button>
                    </div>
                    <div class="modal-body">
                      <h5>Datensatz löschen</h5>
                      <p>Möchten Sie diesen Datensatz wirklich löschen?</p>
                      <p>Datensatznummer: {{ $item->id }}</p>
                    </div>
                    <div class="modal-footer">
                      <a href="{{ url('/department_destroy').'/'.$item->id }}" class="btn btn-primary">Ja, löschen</a>
                    </div>
                  </div>
                </div>
              </div>
            </article>
          @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-6">
          {{ $data->links() }}
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@section('script')
<script>
  // 3D tilt + shine (no dependency)
  (function(){
    const maxTilt = 8;
    document.querySelectorAll('.will-tilt').forEach(card => {
      card.addEventListener('mousemove', (e) => {
        const r = card.getBoundingClientRect();
        const px = (e.clientX - r.left) / r.width;
        const py = (e.clientY - r.top) / r.height;
        const rx = (0.5 - py) * maxTilt;
        const ry = (px - 0.5) * maxTilt;
        card.classList.add('tilting');
        card.style.transform = `rotateX(${rx}deg) rotateY(${ry}deg)`;
        card.style.setProperty('--mx', `${px*100}%`);
        card.style.setProperty('--my', `${py*100}%`);
      });
      card.addEventListener('mouseleave', () => {
        card.classList.remove('tilting');
        card.style.transform = '';
      });
    });
  })();

  // Toastr hooks (keep your originals)
  $(document).ready(function(){
    @if(Session::has('update_msg'))
      toastr.success("{{ session('updated_msg') }}");
    @endif
    @if(Session::has('save_msg'))
      toastr.success("{{ session('save_msg') }}");
    @endif
    @if(Session::has('delete_msg'))
      toastr.error("{{ session('delete_msg') }}");
    @endif
  });
</script>
@endsection
