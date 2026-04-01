{{-- resources/views/admin/tickets/partials/list.blade.php --}}
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

<div class="shell">
  <div class="shell-head">
    <div style="font-weight:950;color:var(--t-ink);">Liste</div>
    <div style="font-size:12px;font-weight:900;color:var(--t-muted);">Tipp: Strg/⌘ + K für Suche</div>
  </div>

  <div class="overflow-x-auto">
    <table class="w-full text-left">
      <thead class="bg-white/70 border-b border-[var(--t-border)]">
        <tr class="text-xs font-black text-slate-600">
          <th class="p-3">TICKET</th>
          <th class="p-3">KUNDE</th>
          <th class="p-3">PRODUKT</th>
          <th class="p-3">TICKETTYP</th>
          <th class="p-3">QUELLE</th>
          <th class="p-3">STATUS</th>
          <th class="p-3">ZUSTÄNDIGE</th>
          <th class="p-3">AUDIT</th>
          <th class="p-3">FEHLER</th>
          <th class="p-3">AKTUALISIERT</th>
          <th class="p-3 text-right">AKTION</th>
        </tr>
      </thead>

      <tbody>
        @forelse($tickets as $t)
          @php
            $st = strtolower($t->status ?? 'offen');

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

          <tr class="border-b border-[var(--t-border)] hover:bg-white/80">
            <td class="p-3">
              <a href="{{ $profileUrl }}" class="font-black text-[var(--t-ink)] hover:underline">#{{ $t->ticket_no }}</a>
              <div class="text-[11px] font-extrabold text-slate-500">{{ $t->priority ?: '—' }}</div>
            </td>

            <td class="p-3">
              <div class="text-sm font-black text-slate-800">
                {{ trim(($t->firma ?: '').' '.($t->name ?: '').' '.($t->lastname ?: '')) }}
              </div>
              <div class="text-[11px] font-semibold text-slate-500">{{ $t->street }} · {{ $t->postcode }} {{ $t->alt_city }}</div>
            </td>

            <td class="p-3 text-sm font-extrabold text-slate-700">{{ $t->product }}</td>

            <td class="p-3 text-sm font-black text-slate-700">{{ $errorTypeDe }}</td>
            <td class="p-3 text-sm font-black text-slate-700">{{ $sourceDe }}</td>

            <td class="p-3">
              <span class="status-pill {{ $pill }}" data-pill-id="{{ $t->id }}">{{ strtoupper($t->status ?? 'offen') }}</span>
              <div class="mt-2">
                <select class="input" style="border-radius:999px;padding:.45rem .65rem;"
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
              </div>
            </td>

            <td class="p-3">
              <div class="tk-avatars">
                @foreach(collect($t->employees ?? [])->take(5) as $e)
                  @php $img = $e['image'] ?? null; @endphp
                  @if($img)
                    <img class="avatar" src="{{ asset('images/employee/'.$img) }}" alt="">
                  @else
                    <div class="avatar"></div>
                  @endif
                @endforeach
                @if(count($t->employees ?? []) > 5)
                  <div class="avatar" style="display:flex;align-items:center;justify-content:center;font-size:10px;font-weight:950;color:#374151;background:#fff;">
                    +{{ count($t->employees ?? []) - 5 }}
                  </div>
                @endif
              </div>
            </td>

            <td class="p-3">
              <div class="audit-row" style="border:0;margin:0;padding:0;">
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
            </td>

            <td class="p-3">
              <button class="tk-btn" type="button" style="padding:.28rem .7rem;"
                data-open-errors="1"
                data-ticket-no="{{ $t->ticket_no }}"
                data-errors-json='@json($t->errors ?? [])'>
                FEHLER ({{ count($t->errors ?? []) }})
              </button>
            </td>

            <td class="p-3 text-[11px] font-black text-slate-500">
              {{ \Carbon\Carbon::parse($t->updated_at)->diffForHumans() }}
            </td>

            <td class="p-3 text-right">
              <a href="{{ $profileUrl }}" class="tk-btn tk-btn-primary" style="padding:.28rem .7rem;">ÖFFNEN</a>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="11" class="p-8 text-center text-sm font-black text-slate-500">Keine Tickets gefunden.</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
