{{-- resources/views/admin/task/stage/partials/list_simple.blade.php --}}
@php
  $stageColors = ['#93c21c', '#c0d8ea', '#cfe09b', '#1abc9c', '#0275d8', '#8e44ad', '#5cb85c', '#f0ad4e'];
@endphp

@if(!$grouped || $grouped->count() < 1)
  <div class="group-card">
    <div class="group-top">
      <div class="group-left">
        <div class="group-title">Keine Schritte gefunden.</div>
        <div class="group-sub">Bitte Filter anpassen.</div>
      </div>
    </div>
  </div>
@else
  @foreach($grouped as $groupKey => $stageGroup)
    @php
      [$productId, $version] = array_pad(explode('||', (string)$groupKey), 2, '');

      $first         = $stageGroup->first();
      $productName   = optional($first->product)->article_group ?? 'Unbekannt';

      $productIdAttr = (int) $productId;
      $versionAttr   = (string) $version;

      $sectionKey    = optional($first->section)->phase_section ?? 'complete';
      $sectionId     = (int) ($first->phase_section_id ?? 0);

      $uniqueStatuses = $stageGroup->pluck('status')->filter()->unique()->values();
      $groupStatus    = $uniqueStatuses->count() === 1 ? $uniqueStatuses->first() : 'Gemischt';

      $badgeClass = $groupStatus === 'Published' ? 'published' : ($groupStatus === 'Draft' ? 'draft' : '');
      $statusLabel = $groupStatus === 'Published' ? 'Veröffentlicht' : ($groupStatus === 'Draft' ? 'Entwurf' : $groupStatus);
    @endphp

    <div class="group-card">
      <div class="group-top">
        <div class="group-left">
          <div class="group-title">
            <span>{{ $productName }}</span>
            <span style="opacity:.55;">—</span>
            <span>Version <span style="color:#0f172a;background:rgba(192,216,234,.55);padding:2px 8px;border-radius:999px;border:1px solid rgba(15,23,42,.08);">{{ $versionAttr }}</span></span>
            <span class="badge {{ $badgeClass }}">{{ $statusLabel }}</span>
          </div>
          <div class="group-sub">
            Sektion: <strong>{{ $sectionKey }}</strong>
            <span style="opacity:.65;">· ID: {{ $sectionId }}</span>
          </div>
        </div>

        <div class="group-actions">
          <button
            class="btn btn-ghost"
            type="button"
            onclick="document.getElementById('btnCopyWholeBucket')?.click();"
            title="Ganze Liste kopieren">
            <i class="feather icon-repeat"></i> Ganze Liste
          </button>

          <button
            class="btn btn-soft"
            type="button"
            onclick="StagePage.openMultiTargetModal();"
            title="Auswahl in mehrere Ziele">
            <i class="feather icon-copy"></i> Auswahl duplizieren
          </button>

          <button class="btn btn-outline" type="button"
                  onclick="openCreateStageModal('{{ $productIdAttr }}', '{{ $versionAttr }}')">
            <i class="feather icon-plus"></i> Schritt
          </button>
        </div>
      </div>

      <div class="step-wrapper"
           data-product="{{ $productIdAttr }}"
           data-version="{{ $versionAttr }}"
           data-section="{{ $sectionId }}">

        @foreach($stageGroup as $index => $stage)
          @php
            $color      = $stageColors[$index % count($stageColors)];
            $isDisabled = (($stage->status ?? 'Published') !== 'Published');
            $stageDate  = optional($stage->updated_at)->format('d.m.Y');
            $bucketSectionId = (int) ($stage->phase_section_id ?? $sectionId);
          @endphp

          <div class="stage-wrapper {{ $isDisabled ? 'is-disabled' : '' }}"
               data-id="{{ $stage->id }}"
               data-product="{{ (int)($stage->product_id ?? $productIdAttr) }}"
               data-version="{{ (string)($stage->version ?? $versionAttr) }}"
               data-section="{{ $bucketSectionId }}">

            <div class="stage-select-wrap">
              <label>
                <input type="checkbox" class="stage-select" value="{{ $stage->id }}">
                auswählen
              </label>
            </div>

            <div class="stage-meta">
              {{ $isDisabled ? 'Deaktiviert' : 'Aktiv' }} · {{ $stageDate }}
            </div>

            <div class="stage-arrow" style="background: {{ $isDisabled ? '#94a3b8' : $color }};">
              {{ $stage->stage }}
            </div>

            <div class="action-buttons">
              <button
                class="icon-btn"
                type="button"
                title="Phasen verwalten"
                onclick="window.location.href='{{ url('/phase_management') }}/{{ $productIdAttr }}/{{ $sectionId }}?version={{ urlencode($versionAttr) }}&stage_id={{ urlencode($stage->id) }}'">
                <i class="feather icon-layers"></i>
              </button>

              <button class="icon-btn" type="button" onclick="editStage({{ $stage->id }})" title="Bearbeiten">
                <i class="feather icon-edit"></i>
              </button>

              <button class="icon-btn danger" type="button" onclick="deleteStage({{ $stage->id }})" title="Löschen">
                <i class="feather icon-trash"></i>
              </button>
            </div>
          </div>
        @endforeach

        {{-- Add new stage card --}}
        <div class="stage-wrapper"
             data-product="{{ $productIdAttr }}"
             data-version="{{ $versionAttr }}"
             data-section="{{ $sectionId }}">
          <div class="stage-arrow"
               style="background: rgba(207,224,155,.85); color:#0f172a; font-size:18px; cursor:pointer; font-weight:900;"
               onclick="openCreateStageModal('{{ $productIdAttr }}', '{{ $versionAttr }}')"
               title="Neuen Schritt hinzufügen">
            +
          </div>
        </div>

      </div>
    </div>
  @endforeach
@endif

@if ($stages && $stages->hasPages())
  <div style="display:flex;justify-content:center;margin-top:14px;">
    {!! $stages->withQueryString()->links('pagination::bootstrap-4') !!}
  </div>
@endif
