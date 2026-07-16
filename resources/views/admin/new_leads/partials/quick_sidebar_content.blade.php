<!-- Custom Styles für die Sidebar -->
<style>
  :root {
    --text-main: #1f2937;
    --text-muted: #6b7280;
    --border: #e5e7eb;
    --primary: var(--sa-accent);
    --primary-light: var(--sa-accent-light);
    --blue: #74b2d4;
    --blue-light: #eff6ff;
    --card-bg: #ffffff;
    --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / .05);
    --shadow: 0 4px 6px -1px rgb(0 0 0 / .1), 0 2px 4px -1px rgb(0 0 0 / .06);
    --radius: 14px;
    --transition: all .2s ease-in-out;
  }
  
  .qs-header-info { margin-bottom: 15px; }
  .qs-title { font-size: 20px; font-weight: 800; letter-spacing: -.02em; color: #111827; margin-bottom: 4px; }
  .qs-subtitle { font-size: 13px; color: var(--text-muted); }

  /* Suchfeld Styling */
  .qs-search-wrapper { position: relative; margin-bottom: 20px; }
  .qs-search-icon { position: absolute; left: 12px; top: 10px; color: var(--text-muted); font-size: 16px; }
  .qs-search-input { width: 100%; padding: 10px 12px 10px 36px; border-radius: 10px; border: 1px solid var(--border); background: #f9fafb; font-size: 13px; outline: none; transition: var(--transition); }
  .qs-search-input:focus { background: #fff; border-color: var(--primary); box-shadow: 0 0 0 3px var(--primary-light); }

  /* Custom Tabs */
  .oc-nav-tabs { display: flex; gap: 8px; border-bottom: 1px solid var(--border); margin-bottom: 16px; padding-bottom: 10px; overflow-x: auto; }
  .oc-nav-tabs .nav-link { font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: .06em; color: var(--text-muted); padding: 8px 14px; border-radius: 10px; background: transparent; border: 1px solid transparent; cursor: pointer; transition: var(--transition); }
  .oc-nav-tabs .nav-link:hover { background: #f9fafb; color: var(--text-main); border-color: var(--border); }
  .oc-nav-tabs .nav-link.active { background: var(--primary); color: #fff; border-color: var(--primary); box-shadow: var(--shadow-sm); }

  /* Item Cards */
  .oc-sb-item { background: var(--card-bg); border: 1px solid var(--border); border-radius: var(--radius); padding: 14px; margin-bottom: 12px; transition: var(--transition); box-shadow: var(--shadow-sm); }
  .oc-sb-item:hover { border-color: var(--primary); box-shadow: var(--shadow); }
  .oc-sb-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 8px; }
  .oc-sb-author { font-size: 14px; font-weight: 800; color: #111827; }
  .oc-sb-date { font-size: 11px; color: var(--text-muted); font-weight: 600; }
  .oc-sb-content { font-size: 13px; color: var(--text-main); line-height: 1.5; margin-bottom: 10px; }
  
  /* Pills */
  .oc-pill-group { display: flex; flex-wrap: wrap; gap: 6px; }
  .oc-sb-pill { display: inline-flex; align-items: center; padding: 4px 8px; border-radius: 8px; font-size: 10px; font-weight: 800; text-transform: uppercase; letter-spacing: .04em; white-space: nowrap; }
  .oc-sb-pill.obj { background: var(--blue-light); color: var(--blue); border: 1px solid rgba(116,178,212,.2); }
  .oc-sb-pill.prd { background: var(--primary-light); color: var(--primary); border: 1px solid rgba(147,194,28,.2); }
  .oc-sb-pill.stage { background: #fffbeb; color: #d97706; border: 1px solid #fde7b0; }
  
  .qs-appointment-title { font-size: 12px; font-weight: 800; color: var(--text-muted); text-transform: uppercase; letter-spacing: .05em; border-bottom: 1px dashed var(--border); padding-bottom: 6px; margin-top: 16px; margin-bottom: 10px; }
</style>

<div class="qs-header-info">
    <div class="qs-title">{{ $customer->title }} {{ $customer->name }} {{ $customer->lastname }}</div>
    <div class="qs-subtitle">
        <i class="feather icon-map-pin"></i> {{ $customer->street }}, {{ $customer->postcode }} {{ $customer->city }}
    </div>
</div>

<!-- Suchfeld -->
<div class="qs-search-wrapper">
    <i class="feather icon-search qs-search-icon"></i>
    <input type="text" id="quickSidebarSearch" class="qs-search-input" placeholder="Suchen in Inhalten, Autoren, Produkten...">
</div>

<!-- Custom Nav Tabs -->
<ul class="nav oc-nav-tabs" id="quickSidebarTabs" role="tablist">
    <li class="nav-item">
        <a class="nav-link active" id="qs-notes-tab" data-toggle="tab" href="#qs-notes" role="tab">Notizen ({{ $customer->customerNotes->count() }})</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" id="qs-reports-tab" data-toggle="tab" href="#qs-reports" role="tab">Berichte ({{ $customer->reports->count() }})</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" id="qs-comments-tab" data-toggle="tab" href="#qs-comments" role="tab">Termin-Reports</a>
    </li>
</ul>

<div class="tab-content" id="quickSidebarTabContent">
    <!-- 1. NOTIZEN TAB -->
    <div class="tab-pane fade show active" id="qs-notes" role="tabpanel">
        @forelse($customer->customerNotes->sortByDesc('created_at') as $note)
            <div class="oc-sb-item search-item">
                <div class="oc-sb-header">
                    <div class="oc-sb-author">
                        <i class="feather icon-user"></i> {{ $note->creator->name ?? 'System' }} {{ $note->creator->lastname ?? '' }}
                    </div>
                    <div class="oc-sb-date">{{ $note->created_at->format('d.m.Y H:i') }}</div>
                </div>
                
                <div class="oc-sb-content">
                    {!! nl2br(e($note->description)) !!}
                </div>

                <div class="oc-pill-group">
                    @if($note->alternative)
                        <span class="oc-sb-pill obj"><i class="feather icon-home mr-25"></i> {{ $note->alternative->object_name ?? 'Objekt #'.$note->alternative->id }}</span>
                    @endif
                    @if($note->product)
                        <span class="oc-sb-pill prd"><i class="feather icon-box mr-25"></i> {{ $note->product->article_group }}</span>
                    @endif
                    @if($note->stage)
                        <span class="oc-sb-pill stage">{{ $note->stage }}</span>
                    @endif
                </div>
            </div>
        @empty
            <div class="text-center p-4 text-muted border rounded" style="border-style: dashed !important; border-radius: var(--radius) !important;">
                <i class="feather icon-inbox" style="font-size: 24px;"></i><br>
                <small>Keine Notizen vorhanden.</small>
            </div>
        @endforelse
    </div>

    <!-- 2. REPORTS TAB -->
    <div class="tab-pane fade" id="qs-reports" role="tabpanel">
        @forelse($customer->reports->sortByDesc('created_at') as $report)
            <div class="oc-sb-item search-item" style="border-left: 3px solid var(--blue);">
                <div class="oc-sb-header">
                    <div class="oc-sb-author">
                        <i class="feather icon-file-text"></i> {{ $report->reporter->name ?? $report->employee->name ?? 'Mitarbeiter' }} 
                    </div>
                    <div class="oc-sb-date">{{ $report->created_at->format('d.m.Y H:i') }}</div>
                </div>
                
                <div class="oc-sb-content">
                    {!! nl2br(e($report->report ?? 'Kein Textinhalt')) !!}
                </div>

                <div class="oc-pill-group">
                    @if($report->alternative)
                        <span class="oc-sb-pill obj"><i class="feather icon-home mr-25"></i> {{ $report->alternative->object_name ?? 'Objekt #'.$report->alternative->id }}</span>
                    @endif
                    @if($report->product)
                        <span class="oc-sb-pill prd"><i class="feather icon-box mr-25"></i> {{ $report->product->article_group }}</span>
                    @endif
                    @if($report->stage)
                        <span class="oc-sb-pill stage">{{ $report->stage }}</span>
                    @endif
                </div>
            </div>
        @empty
            <div class="text-center p-4 text-muted border rounded" style="border-style: dashed !important; border-radius: var(--radius) !important;">
                <i class="feather icon-inbox" style="font-size: 24px;"></i><br>
                <small>Keine Berichte vorhanden.</small>
            </div>
        @endforelse
    </div>

    <!-- 3. APPOINTMENT REPORTS TAB -->
    <div class="tab-pane fade" id="qs-comments" role="tabpanel">
        @php $hasAppointmentReports = false; @endphp
        
        @foreach($appointments as $appointment)
            @if($appointment->reports && $appointment->reports->count() > 0)
                @php $hasAppointmentReports = true; @endphp
                <div class="qs-appointment-title">
                    <i class="feather icon-calendar"></i> {{ \Carbon\Carbon::parse($appointment->start_date)->format('d.m.Y') }} | {{ $appointment->name }}
                </div>

                @foreach($appointment->reports->sortByDesc('created_at') as $appReport)
                    <div class="oc-sb-item search-item" style="border-left: 3px solid var(--primary);">
                        <div class="oc-sb-header">
                            <div class="oc-sb-author">
                                <i class="feather icon-message-circle"></i> {{ $appReport->reporter->name ?? $appReport->employee->name ?? 'Autor' }}
                            </div>
                            <div class="oc-sb-date">{{ $appReport->created_at->format('d.m.Y H:i') }}</div>
                        </div>
                        
                        <div class="oc-sb-content">
                            {!! nl2br(e($appReport->report)) !!}
                        </div>
                        
                        @if($appReport->next_step)
                            <div class="mt-2 pt-2 border-top">
                                <span class="oc-sb-pill obj"><i class="feather icon-arrow-right"></i> Nächster Schritt</span>
                                <span style="font-size: 12px; color: var(--text-main);">{{ $appReport->next_step }}</span>
                            </div>
                        @endif
                    </div>
                @endforeach
            @endif
        @endforeach

        @if(!$hasAppointmentReports)
            <div class="text-center p-4 text-muted border rounded" style="border-style: dashed !important; border-radius: var(--radius) !important;">
                <i class="feather icon-inbox" style="font-size: 24px;"></i><br>
                <small>Keine Termin-Berichte vorhanden.</small>
            </div>
        @endif
    </div>
</div>