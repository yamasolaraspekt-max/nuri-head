 

 <div class="card-content">
    <div class="table-responsive mt-1">
        <table class="table   mb-0">
            <thead>
                <tr>    
                    <th>Zweck</th>    
                    <th>Anfrage durch</th>      
                    <th>Erstellt von</th>   
                    <th>Anfrage an</th>  
                    <th>Status</th>
                    <th>Bearbeitung</th>
                </tr>
            </thead>
            <tbody>
              @if($leave->count() > 0)
                <div class="oc-list">
                    @foreach($leave as $item)
                        @php
                            $empImage = $item->emp_image ? asset('images/employee/' . $item->emp_image) : asset('images/gender/male.png');
                            $requestImage = $item->rimage ? asset('images/employee/' . $item->rimage) : asset('images/gender/male.png');

                            $approvedLabel = $item->approved === 'Yes' ? 'Genehmigt' : 'Ausstehend';
                            $approvedClass = $item->approved === 'Yes' ? 'green' : 'orange';

                            $statusLabel = $item->status === 'accept' ? 'Akzeptiert' : ($item->status ?: 'Offen');
                            $statusClass = $item->status === 'accept' ? 'green' : 'gray';
                        @endphp

                        <div class="oc-item">
                            <div class="oc-item-row">
                                <div class="oc-cell">
                                    <div class="oc-cell-title">ID / Zeitraum</div>

                                    <span class="oc-id-badge">#{{ $item->leave_id }}</span>

                                    <div class="oc-subt mt-1">
                                        {{ $item->start_date }} – {{ $item->end_date }}
                                    </div>

                                    @if($item->old_start)
                                        <div class="oc-subt mt-1" style="color:#d97706;">
                                            Alt: {{ $item->old_start }} – {{ $item->old_end }}
                                        </div>
                                    @endif
                                </div>

                                <div class="oc-cell">
                                    <div class="oc-cell-title">Antrag</div>

                                    <div class="oc-main">
                                        <div class="oc-ttl">Urlaubsanfrage</div>
                                        <div class="oc-subt">
                                            {{ $item->duration ?? 0 }} Tag(e) · {{ $item->reason ?? 'Urlaub' }}
                                        </div>
                                    </div>
                                </div>

                                <div class="oc-cell">
                                    <div class="oc-cell-title">Status</div>

                                    <span class="oc-status-pill {{ $approvedClass }}">
                                        {{ $approvedLabel }}
                                    </span>

                                    <span class="oc-status-pill {{ $statusClass }} mt-1">
                                        {{ $statusLabel }}
                                    </span>
                                </div>

                                <div class="oc-cell">
                                    <div class="oc-cell-title">Grund</div>

                                    <div class="oc-main">
                                        <div class="oc-ttl" style="font-size:14px;">
                                            {{ $item->reason ?? '—' }}
                                        </div>

                                        <div class="oc-subt">
                                            {{ $item->description ?? 'Keine Beschreibung' }}
                                        </div>
                                    </div>
                                </div>

                                <div class="oc-cell">
                                    <div class="oc-cell-title">Anfrage an</div>

                                    <div class="d-flex align-items-center">
                                        <img src="{{ $requestImage }}" class="rounded-circle mr-2" width="34" height="34"
                                            style="object-fit:cover;" alt="">

                                        <div>
                                            <div class="oc-ttl" style="font-size:13px;">
                                                {{ $item->rlastname }} {{ $item->rname }}
                                            </div>
                                            <div class="oc-subt">Freigabe</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="oc-cell">
                                    <div class="oc-cell-title">Notizen</div>

                                    <button type="button" class="oc-btn-ic primary leave-notes" data-id="{{ $item->leave_id }}"
                                        title="Notizen">
                                        <i class="feather icon-file-text"></i>
                                    </button>
                                </div>

                                <div class="oc-cell">
                                    <div class="oc-cell-title">Aktionen</div>

                                    <div class="oc-actions">
                                        <button type="button" class="oc-btn-ic warning check-leave" data-id="{{ $item->leave_id }}"
                                            data-start-date="{{ $item->start_date }}" data-end-date="{{ $item->end_date }}"
                                            data-employee-id="{{ $item->emp_id }}" title="Konflikt prüfen">
                                            <i class="feather icon-calendar"></i>
                                        </button>

                                        @if($item->request_answer !== 'accept')
                                            <button type="button" class="oc-btn-ic success accept-btn" data-leave-id="{{ $item->leave_id }}"
                                                data-employee-id="{{ $item->emp_id }}" title="Akzeptieren">
                                                <i class="feather icon-check"></i>
                                            </button>

                                            <button type="button" class="oc-btn-ic danger reject-btn" data-leave-id="{{ $item->leave_id }}"
                                                data-start="{{ $item->start_date }}" data-end="{{ $item->end_date }}"
                                                data-employee-id="{{ $item->emp_id }}" title="Ablehnen">
                                                <i class="feather icon-x"></i>
                                            </button>
                                        @endif

                                        <button type="button" class="oc-btn-ic danger delete-leave" data-id="{{ $item->leave_id }}"
                                            title="Löschen">
                                            <i class="feather icon-trash-2"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                @if($leave->hasPages())
                    <div class="oc-pagination">
                        {{ $leave->links('pagination::bootstrap-4') }}
                    </div>
                @endif
            @else
                <div class="oc-empty">Keine Anträge gefunden.</div>
            @endif
            
            <script>
                if (window.feather) window.feather.replace();
            </script> 

               
            </tbody>
        </table>
    </div>

  
</div>


<div class="mt-2">
    {{ $leave->links('pagination::bootstrap-4') }}
</div>
