{{-- resources/views/admin/tickets/partials/cards.blade.php --}}
@php
  $ERROR_TYPE_DE = [
    'complaint'=>'Reklamation','emergency_service'=>'Notdienst','repair'=>'Reparatur','maintenance'=>'Wartung',
    'malfunction'=>'Störung','installation'=>'Installation','configuration_error'=>'Konfiguration','system_outage'=>'Systemausfall',
    'security_issue'=>'Sicherheitsproblem','user_error'=>'Bedienungsfehler','network_problem'=>'Netzwerkfehler','software_bug'=>'Softwarefehler',
    'hardware_defect'=>'Hardwarefehler','spare_part_request'=>'Ersatzteilanfrage','timeout'=>'Zeitüberschreitung',
    'communication_failure'=>'Kommunikationsproblem','power_outage'=>'Energieausfall','update_failure'=>'Updatefehler',
    'access_issue'=>'Zugriffsproblem','other'=>'Sonstiges',
  ];
  $SOURCE_DE = [
    'Kunde'=>'Kunde','Mitarbeiter'=>'Mitarbeiter','System'=>'System','Telefonisch'=>'Telefonisch','E-Mail'=>'E-Mail','Vor Ort'=>'Vor Ort',
    'Intern'=>'Intern','Extern'=>'Extern','Webformular'=>'Webformular','Support-Portal'=>'Support-Portal','Live-Chat'=>'Live-Chat','API'=>'API',
    'Monitoring'=>'Monitoring','Social Media'=>'Social Media','WhatsApp'=>'WhatsApp','Fax'=>'Fax','Slack'=>'Slack','Teams'=>'Teams','Besuch'=>'Besuch',
    'Manuell erstellt'=>'Manuell erstellt','Weitergeleitet'=>'Weitergeleitet',
  ];
  $norm = fn($v) => trim((string)$v);
@endphp

<div class="tk-cards-grid">
  @php $hasAny = false; @endphp

  @foreach($tickets as $t)
    @php
      $st = strtolower($t->status ?? 'offen');
      $hasAny = true;

      $pill = $st==='process' ? 's-proc' : ($st==='junk' ? 's-junk' : ($st==='end' ? 's-end' : 's-open'));
      $profileUrl = url('problem/profile/'.$t->id);

      $errorTypeKey = $norm($t->error_type);
      $errorTypeDe  = $ERROR_TYPE_DE[$errorTypeKey] ?? ($errorTypeKey ?: '—');

      $sourceKey = $norm($t->source ?? '');
      $sourceDe  = $SOURCE_DE[$sourceKey] ?? ($sourceKey ?: '—');

      $ticketJson = $t->ticket_json ?? [
        'id'=>$t->id,
        'ticket_no'=>$t->ticket_no,
        'status'=>$t->status ?? 'offen',
        'profile_url'=>$profileUrl,
        'customer'=>trim(($t->firma ?: '').' '.($t->name ?: '').' '.($t->lastname ?: '')),
        'product'=>$t->product ?? '',
        'error_type'=>$errorTypeKey,
        'source'=>$sourceKey,
        'created_at'=>$t->created_at,
        'updated_at'=>$t->updated_at,
        'edit_date'=>$t->edit_date ?? null,
        'end_date'=>$t->end_date ?? null,
        'employees'=>$t->employees ?? [],
        'errors'=>$t->errors ?? [],
        'created_by_user'=>$t->created_by_user ?? null,
        'updated_by_user'=>$t->updated_by_user ?? null,
        'ended_by_user'=>$t->ended_by_user ?? null,
        'current_user'=>$t->current_user ?? null,
      ];
    @endphp

    <div class="shell" style="padding:.9rem;">
      <div class="flex items-start justify-between gap-2">
        <a href="{{ $profileUrl }}" class="text-lg font-black text-[var(--t-ink)] hover:underline">#{{ $t->ticket_no }}</a>
        <span class="status-pill {{ $pill }}" data-pill-id="{{ $t->id }}">{{ strtoupper($t->status ?? 'offen') }}</span>
      </div>

      <div class="mt-2 text-sm font-black text-slate-800">
        {{ trim(($t->firma ?: '').' '.($t->name ?: '').' '.($t->lastname ?: '')) }}
      </div>
      <div class="text-xs font-semibold text-slate-500">{{ $t->street }} · {{ $t->postcode }} {{ $t->alt_city }}</div>

      <div class="mt-3 flex flex-wrap gap-2">
        <span class="rounded-full px-3 py-1 text-[11px] font-black border border-[var(--t-border)] bg-[rgba(147,194,28,.10)]">
          {{ $errorTypeDe }}
        </span>
        <span class="rounded-full px-3 py-1 text-[11px] font-black border border-[var(--t-border)] bg-[rgba(116,178,212,.12)]">
          Quelle: {{ $sourceDe }}
        </span>
        <span class="rounded-full px-3 py-1 text-[11px] font-black border border-[var(--t-border)] bg-[rgba(116,178,212,.12)]">
          {{ $t->product }}
        </span>
        @if($t->priority)
          <span class="rounded-full px-3 py-1 text-[11px] font-black border border-[var(--t-border)] bg-[rgba(226,88,62,.10)]">
            {{ strtoupper($t->priority) }}
          </span>
        @endif
      </div>

      <div class="mt-4 flex items-center justify-between gap-2">
        <div class="tk-avatars">
          @foreach(collect($t->employees ?? [])->take(4) as $e)
            @php $img = $e['image'] ?? null; @endphp
            @if($img)
              <img class="avatar" src="{{ asset('images/employee/'.$img) }}" alt="">
            @else
              <div class="avatar"></div>
            @endif
          @endforeach
          @if(count($t->employees ?? []) > 4)
            <div class="avatar" style="display:flex;align-items:center;justify-content:center;font-size:10px;font-weight:950;color:#374151;background:#fff;">
              +{{ count($t->employees ?? []) - 4 }}
            </div>
          @endif
        </div>

        <button class="tk-btn" type="button" style="padding:.28rem .7rem;"
          data-open-errors="1"
          data-ticket-no="{{ $t->ticket_no }}"
          data-errors-json='@json($t->errors ?? [])'>
          FEHLER ({{ count($t->errors ?? []) }})
        </button>
      </div>

      <div class="audit-row">
        <div class="audit-item">
          <span class="k">ERSTELLT</span>
          <span class="v">{{ data_get($ticketJson,'created_by_user.name','—') }}</span>
          <span class="d">{{ $t->created_at ? \Carbon\Carbon::parse($t->created_at)->format('Y-m-d H:i') : '—' }}</span>
        </div>
        <div class="audit-item">
          <span class="k">BEARBEITET</span>
          <span class="v">{{ data_get($ticketJson,'updated_by_user.name','—') }}</span>
          <span class="d">
            @php $ed = $t->edit_date ?: $t->updated_at; @endphp
            {{ $ed ? \Carbon\Carbon::parse($ed)->format('Y-m-d H:i') : '—' }}
          </span>
        </div>
      </div>

      <div class="mt-3 flex items-center justify-between gap-2">
        <select class="input"
                style="border-radius:999px;padding:.45rem .65rem;font-size:11px;"
                data-ticket-status="1"
                data-ticket-id="{{ $t->id }}"
                data-ticket-no="{{ $t->ticket_no }}"
                data-ticket-json='@json($ticketJson)'
                data-prev-status="{{ $st }}">
          <option value="offen" @selected(($t->status ?? 'offen')==='offen')>OFFEN</option>
          <option value="process" @selected(($t->status ?? '')==='process')>IN BEARBEITUNG</option>
          <option value="end" @selected(($t->status ?? '')==='end')>BEENDET</option>
          <option value="junk" @selected(($t->status ?? '')==='junk')>JUNK</option>
        </select>

        <a href="{{ $profileUrl }}" class="tk-btn tk-btn-primary" style="padding:.28rem .7rem;">ÖFFNEN</a>
      </div>

      <div class="mt-3 text-[11px] font-black text-slate-500">
        Aktualisiert: {{ \Carbon\Carbon::parse($t->updated_at)->diffForHumans() }}
      </div>
    </div>
  @endforeach

  @if(!$hasAny)
    <div class="col-span-full p-10 text-center text-sm font-black text-slate-500">Keine Tickets gefunden.</div>
  @endif
</div>
