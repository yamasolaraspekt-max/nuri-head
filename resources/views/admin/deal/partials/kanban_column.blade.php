@foreach($data as $item)
    @php
        $currentStatus = $statusKey ?? $item->status ?? $status ?? 'open';

        $statusLabel = $dealWorkflowLabelMap[$item->status] ?? $dealWorkflowLabelMap[$currentStatus] ?? match ($item->status ?? $currentStatus) {
            'complete' => 'Abgeschlossen',
            'Junk' => 'Junk',
            default => ucfirst(str_replace('_', ' ', (string) ($item->status ?? $currentStatus))),
        };

        $statusColor = $dealWorkflowColorMap[$item->status] ?? $dealWorkflowColorMap[$currentStatus] ?? '#93c21c';

        $auftragNo = $item->order_number
            ?? $item->deal_no
            ?? $item->offer_number
            ?? ('#' . $item->id);

        $customerName = trim(($item->name ?? '') . ' ' . ($item->lastname ?? '')) ?: ($item->firma ?? 'Unbekannter Kunde');

        $latestChangeAt = $item->latest_change_at ?? $item->updated_at ?? $item->created_at ?? null;
        $latestChangeSource = $item->latest_change_source ?? 'Auftrag';
        $latestChangeText = $item->latest_change_text ?? 'Auftrag wurde geändert';
        $isFreshChange = $latestChangeAt
            ? \Carbon\Carbon::parse($latestChangeAt)->greaterThanOrEqualTo(now()->subDays(3))
            : false;

        $empGender = $item->emp_gender ?? $item->gender ?? null;
        $defaultImage = $empGender === 'Male'
            ? asset('images/gender/male.png')
            : asset('images/gender/female.png');

        $employeeImage = (!empty($item->emp_image) && file_exists(public_path('images/employee/' . $item->emp_image)))
            ? asset('images/employee/' . $item->emp_image)
            : $defaultImage;
    @endphp

    <div id="deal-kanban-{{ $item->id }}" class="kanban-card kanban-item" data-id="{{ $item->id }}"
        data-emp-id="{{ $item->employee_id }}" data-stage="{{ $item->status }}" data-product-id="{{ $item->product_id }}"
        data-search="{{ strtolower($customerName . ' ' . ($item->article_group ?? '') . ' ' . ($item->city ?? '') . ' ' . $auftragNo) }}">
        <div class="d-flex justify-content-between align-items-start gap-2">
            <div class="min-w-0">
                <a href="{{ route('deal.profile', $item->id) }}" class="deal-link">
                    <strong class="d-block text-truncate">{{ $customerName }}</strong>
                </a>

                <div class="deal-subt">
                    <i class="feather icon-hash"></i> {{ $auftragNo }}
                    · <i class="feather icon-map-pin"></i> {{ $item->city ?? 'Ort unbekannt' }}
                </div>
            </div>

            @if($latestChangeAt)
                <span class="deal-update-badge {{ $isFreshChange ? 'fresh' : '' }}" title="{{ $latestChangeText }}">
                    {{ $isFreshChange ? 'Neu' : 'Update' }} · {{ $latestChangeSource }}
                </span>
            @endif
        </div>

        @if($latestChangeAt)
            <div class="deal-latest-change-line mt-2">
                <i class="feather icon-zap"></i>
                {{ $latestChangeText }}
                <div class="deal-subt">{{ \Carbon\Carbon::parse($latestChangeAt)->format('d.m.Y H:i') }} ·
                    {{ \Carbon\Carbon::parse($latestChangeAt)->diffForHumans() }}</div>
            </div>
        @endif

        <div class="mt-2">
            <div class="deal-service-box">
                <div class="deal-service-badge">{{ $item->initial ?? '?' }}</div>
                <div class="deal-service-line"></div>
                <img src="{{ $employeeImage }}" alt="" class="deal-profile"
                    title="{{ trim(($item->emp_name ?? '') . ' ' . ($item->emp_lastname ?? '')) ?: 'Nicht zugewiesen' }}">
                <div class="deal-main">
                    <div class="deal-ttl">{{ $item->article_group ?? 'Produkt' }}</div>
                    <div class="deal-subt">{{ $item->service ?? 'Nicht gesetzt' }}</div>
                </div>
            </div>
        </div>

        <div class="d-flex align-items-center justify-content-between mt-3 gap-2">
            <span class="deal-status-pill"
                style="background: {{ $statusColor }}20; color: {{ $statusColor }}; border:1px solid {{ $statusColor }}55;">
                {{ $statusLabel }}
            </span>

            <div class="deal-actions">
                <a href="{{ route('deal.profile', $item->id) }}" class="deal-btn-ic primary" title="Auftrag Profil">
                    <i class="feather icon-file-text"></i>
                </a>

                <button type="button" class="deal-btn-ic open-deal-history-sidebar" data-deal-id="{{ $item->id }}"
                    data-order-number="{{ $auftragNo }}" title="Historie">
                    <i class="feather icon-clock"></i>
                </button>

                <button type="button" class="deal-btn-ic warning open-notes-sidebar position-relative"
                    data-deal-id="{{ $item->id }}" data-customer-id="{{ $item->customer_id }}"
                    data-alternative-id="{{ $item->alternative_id }}" data-product-id="{{ $item->product_id }}"
                    title="Notizen">
                    <i class="fa fa-sticky-note-o"></i>
                    @if(($item->notes_count ?? 0) > 0)
                        <span class="badge">{{ $item->notes_count }}</span>
                    @endif
                </button>

                <button type="button" class="deal-btn-ic open-upload-sidebar position-relative"
                    data-deal-id="{{ $item->id }}" data-customer-id="{{ $item->customer_id }}"
                    data-alternative-id="{{ $item->alternative_id }}" data-product-id="{{ $item->product_id }}"
                    data-offer-folder-id="{{ $item->offer_folder_id ?? '' }}" title="Dokumente">
                    <i class="fa fa-picture-o"></i>
                    @if(($item->files_count ?? 0) > 0)
                        <span class="badge">{{ $item->files_count }}</span>
                    @endif
                </button>
            </div>
        </div>
    </div>
@endforeach