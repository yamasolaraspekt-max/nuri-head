@php
  /** Kanonischer Code -> Deutsches Label */
  $TYPE_MAP = [
    'Task'        => 'Aufgabe',
    'Appointment' => 'Termin',
    'Project'     => 'Projekt',
    'Offer'       => 'Angebot',
    'Problem'     => 'Ticket',
    'Pause'       => 'Pause',
    'Manual'      => 'Manuell',
    'Missing'     => 'Fehlend',
    'Other'       => 'Sonstiges',
  ];

  /* Updated for Lucide icon classes */
  $TYPE_ICON = [
    'Task'        => 'check-square',
    'Appointment' => 'calendar',
    'Project'     => 'briefcase',
    'Offer'       => 'file-text',
    'Problem'     => 'triangle-alert',
    'Pause'       => 'coffee',
    'Manual'      => 'pen-tool',
    'Missing'     => 'alert-circle',
    'Other'       => 'circle',
  ];

  $BILLING_MAP = [
    'billable'     => 'Abrechenbar',
    'non_billable' => 'Nicht abrechenbar',
    'internal'     => 'Intern',
  ];

  $totalWorked = 0;
@endphp

@forelse ($entries as $entry)
  @php
    $code       = $entry['type'] ?? 'Other';
    $label      = $TYPE_MAP[$code] ?? $code;
    $icon       = $TYPE_ICON[$code] ?? 'circle';
    $isMissing  = !empty($entry['is_missing']);

    $entryHours = (float) ($entry['hours'] ?? 0);
    try {
      $startMin = null;
      $endMin = null;
      if (!empty($entry['time_start']) && preg_match('/^\d{2}:\d{2}/', (string) $entry['time_start'])) {
        [$h, $m] = array_map('intval', explode(':', substr((string) $entry['time_start'], 0, 5)));
        $startMin = ($h * 60) + $m;
      }
      if (!empty($entry['time_end']) && preg_match('/^\d{2}:\d{2}/', (string) $entry['time_end'])) {
        [$h, $m] = array_map('intval', explode(':', substr((string) $entry['time_end'], 0, 5)));
        $endMin = ($h * 60) + $m;
      }
      if ($startMin !== null && $endMin !== null && $endMin > $startMin) {
        $entryHours = round(($endMin - $startMin) / 60, 2);
      }
    } catch (\Throwable $e) {
      $entryHours = max(0, abs((float) ($entry['hours'] ?? 0)));
    }
    $entryHours = max(0, $entryHours);

    if (!$isMissing) { $totalWorked += $entryHours; }

    $id        = $entry['id'] ?? null;
    $fromSaved = !empty($entry['from_saved']);

    $billing  = $entry['billing_type'] ?? null;
    $category = $entry['activity_category'] ?? null;
    $isTravel = !empty($entry['is_travel']);

    $reportableType = $entry['reportable_type'] ?? '';
    $reportableId   = $entry['reportable_id'] ?? '';

    $selectedIds = collect($entry['customers'] ?? [])
      ->pluck('id')
      ->map(fn($v) => (string) $v)
      ->all();

    $rowDate = $start_date ?? ($entry['date'] ?? '');
    $travelId = 'travel_' . $loop->index . '_' . ($id ?: 'new');

    // Termin/Aufgabe/Ticket descriptions from planning are context only.
    // They are shown in the card header/context box, not inside the report textarea.
    $plannedTypes = ['Task', 'Appointment', 'Problem'];
    $hasPlannedContext = !$fromSaved && in_array($code, $plannedTypes, true) && !empty($reportableId);

    $entryTitle = trim((string) (
      $entry['title']
      ?? $entry['name']
      ?? $entry['subject']
      ?? $entry['reportable_title']
      ?? $entry['reference_title']
      ?? ''
    ));

    $sourceDescription = trim(strip_tags((string) (
      $entry['source_description']
      ?? $entry['planned_description']
      ?? $entry['original_description']
      ?? ''
    )));

    $rawReportDescription = (string) ($entry['description'] ?? '');

    if ($hasPlannedContext && $sourceDescription === '') {
      $sourceDescription = trim(strip_tags($rawReportDescription));
    }

    $reportDescription = $hasPlannedContext ? '' : $rawReportDescription;
    $headerDescription = $hasPlannedContext ? $sourceDescription : '';
    $sourceTitle = $entryTitle !== '' ? $entryTitle : $label;

    $relatedReport = $entry['related_report'] ?? [];
    $relatedModule = $relatedReport['module'] ?? null;
    $hasMyRelatedReport = !empty($relatedReport['has_mine']);
    $hasOtherRelatedReport = !empty($relatedReport['other_count']);
    $myRelatedReportText = (string) ($relatedReport['my_report_text'] ?? '');
    $myRelatedReportId = $relatedReport['my_report_id'] ?? null;
    $otherEmployeeNames = $relatedReport['other_employee_names'] ?? [];
    $otherReports = $relatedReport['other_reports'] ?? [];
    $allRelatedReports = $relatedReport['all_reports'] ?? [];
    if (empty($allRelatedReports) && !empty($otherReports)) {
      $allRelatedReports = $otherReports;
    }
    $otherNamesText = implode(', ', array_filter($otherEmployeeNames));
    $isRelatedReportType = in_array($code, ['Task', 'Appointment', 'Problem'], true) && !empty($reportableId);

    if ($hasMyRelatedReport && trim($myRelatedReportText) !== '') {
      $reportDescription = $myRelatedReportText;
    }

    $reportLocked = $isRelatedReportType && $hasMyRelatedReport;
    $reportBlockedByOthers = $isRelatedReportType && !$hasMyRelatedReport && $hasOtherRelatedReport;
    $hasAnyRelatedReport = $isRelatedReportType && ($hasMyRelatedReport || $hasOtherRelatedReport);
    $reportInputDescription = $hasAnyRelatedReport ? '' : $reportDescription;
  @endphp

  <tr
    class="daily_report_tr dr-entry-row {{ $isMissing ? 'missing-row' : '' }}"
    data-type="{{ $code }}"
    data-missing="{{ $isMissing ? '1' : '0' }}"
    data-id="{{ $id ?? '' }}"
    data-reportable-type="{{ $reportableType }}"
    data-reportable-id="{{ $reportableId }}"
    data-source-title="{{ e($sourceTitle) }}"
    data-source-description="{{ e($sourceDescription) }}"
    data-related-module="{{ $relatedModule ?? '' }}"
    data-related-my-report="{{ $hasMyRelatedReport ? '1' : '0' }}"
    data-related-other-report="{{ $hasOtherRelatedReport ? '1' : '0' }}"
  >
    <td colspan="8" class="dr-entry-cell">
      <input type="hidden" name="reportable_type" value="{{ $reportableType }}">
      <input type="hidden" name="reportable_id" value="{{ $reportableId }}">
      <input type="hidden" class="source_title_input" value="{{ e($sourceTitle) }}">
      <input type="hidden" class="source_description_input" value="{{ e($sourceDescription) }}">
      <input type="hidden" name="related_report_action" class="related_report_action_input" value="auto">
      <input type="hidden" name="related_report_id" class="related_report_id_input" value="{{ $myRelatedReportId ?? '' }}">

      <article class="dr-entry-card {{ $isMissing ? 'is-missing' : '' }} {{ $fromSaved ? 'is-saved' : 'is-source' }}">
        <div class="dr-entry-main">
          <div class="dr-entry-top">
            <button type="button" class="dr-entry-typeblock dr-entry-toggle" aria-expanded="false">
              <span class="dr-entry-type-icon">
                <i data-lucide="{{ $icon }}"></i>
              </span>
              <div>
                <div class="dr-entry-titleline">
                  <span class="dr-entry-type-label">{{ $label }}</span>
                  @if($sourceTitle !== '' && $sourceTitle !== $label)
                    <span class="dr-entry-source-title">{{ $sourceTitle }}</span>
                  @endif
                  @if($fromSaved)
                    <span class="dr-mini-badge saved">Gespeichert</span>
                  @else
                    <span class="dr-mini-badge source">Aus Planung</span>
                  @endif
                  @if($isMissing)
                    <span class="dr-mini-badge missing">Fehlzeit</span>
                  @endif
                  @if($reportLocked)
                    <span class="dr-mini-badge locked"><i data-lucide="lock"></i> Bericht gesperrt</span>
                  @elseif($reportBlockedByOthers)
                    <span class="dr-mini-badge other-report"><i data-lucide="users"></i> Bereits berichtet</span>
                  @endif
                </div>
                <div class="dr-entry-subline">
                  <span class="dr-summary-time">{{ $entry['time_start'] ?? '--:--' }} – {{ $entry['time_end'] ?? '--:--' }} · {{ number_format($entryHours, 2, ',', '.') }} Std.</span>
                  <span>•</span>
                  <span class="dr-summary-address">{{ $entry['address'] ?? 'Kein Arbeitsort gesetzt' }}</span>
                  @if($headerDescription !== '')
                    <span>•</span>
                    <span class="dr-source-summary">{{ \Illuminate\Support\Str::limit($headerDescription, 120) }}</span>
                  @endif
                </div>
              </div>
              <i data-lucide="chevron-down" class="dr-collapse-icon"></i>
            </button>

            <div class="dr-entry-actions">
              @if (!empty($id))
                <button type="button"
                        class="dr-row-action ghost editRow"
                        data-id="{{ $id }}"
                        title="Eintrag bearbeiten">
                  <i data-lucide="pencil"></i>
                  <span>Bearbeiten</span>
                </button>
              @endif

              <button type="button"
                      class="dr-row-action primary saveRow"
                      data-id="{{ $id ?? '' }}"
                      title="{{ $fromSaved ? 'Änderungen speichern' : 'Eintrag übernehmen' }}">
                <i data-lucide="{{ $fromSaved ? 'save' : 'check' }}"></i>
                <span>{{ $fromSaved ? 'Speichern' : 'Übernehmen' }}</span>
              </button>

              @if (!empty($id))
                <button type="button"
                        class="dr-row-action danger deleteRow"
                        data-id="{{ $id }}"
                        title="Löschen">
                  <i data-lucide="trash-2"></i>
                  <span>Löschen</span>
                </button>
              @endif

              <span class="dr-action-counter">
                <button type="button"
                        class="dr-row-action ghost btn-notes"
                        title="Notizen"
                        data-date="{{ $rowDate }}"
                        data-entry="{{ $id ?? '__null' }}">
                  <i data-lucide="message-square"></i>
                </button>
                <span class="count-badge note-count hidden" data-entry="{{ $id ?? '' }}">0</span>
              </span>

              <span class="dr-action-counter">
                <button type="button"
                        class="dr-row-action ghost btn-attach"
                        title="Anhänge"
                        data-date="{{ $rowDate }}"
                        data-entry="{{ $id ?? '' }}">
                  <i data-lucide="paperclip"></i>
                </button>
                <span class="count-badge attach-count hidden" data-entry="{{ $id ?? '' }}">0</span>
              </span>
            </div>
          </div>

          <div class="dr-entry-body" hidden>
          <div class="dr-entry-grid">
            <div class="dr-field-group time-group">
              <label>Zeit</label>
              <div class="dr-time-pair">
                <input type="time"
                       class="dr-control start_time_input"
                       name="start_time"
                       value="{{ $entry['time_start'] }}">
                <span>–</span>
                <input type="time"
                       class="dr-control end_time_input"
                       name="end_time"
                       value="{{ $entry['time_end'] }}">
              </div>
            </div>

            <div class="dr-field-group hours-group">
              <label>Std.</label>
              <input type="number"
                     class="dr-control hours_spent_input is-auto-calculated"
                     name="hours_spent"
                     step="0.25"
                     min="0"
                     value="{{ number_format($entryHours, 2, '.', '') }}">
            </div>

            <div class="dr-field-group type-group">
              <label>Typ</label>
              <select name="type" class="dr-control select2" data-placeholder="Typ wählen">
                @foreach ($TYPE_MAP as $val => $text)
                  <option value="{{ $val }}" {{ $code === $val ? 'selected' : '' }}>
                    {{ $text }}
                  </option>
                @endforeach
              </select>
            </div>

            <div class="dr-field-group place-group">
              <label>Arbeitsort</label>
              <input type="text"
                     class="dr-control autocomplete-address"
                     name="address"
                     value="{{ $entry['address'] ?? '' }}"
                     placeholder="Arbeitsort / Adresse">
            </div>
          </div>

          <div class="dr-row-time-warning" hidden></div>

          <div class="dr-entry-grid secondary">
            <div class="dr-field-group billing-group">
              <label>Abrechnung</label>
              <select name="billing_type" class="dr-control">
                <option value="">Abrechnung wählen</option>
                <option value="billable" {{ $billing === 'billable' ? 'selected' : '' }}>Abrechenbar</option>
                <option value="non_billable" {{ $billing === 'non_billable' ? 'selected' : '' }}>Nicht abrechenbar</option>
                <option value="internal" {{ $billing === 'internal' ? 'selected' : '' }}>Intern</option>
              </select>
            </div>

            <div class="dr-field-group category-group">
              <label>Kategorie / Tag</label>
              <input type="text"
                     name="activity_category"
                     class="dr-control"
                     placeholder="z.B. Montage, Planung, Telefonat"
                     value="{{ $category ?? '' }}">
            </div>

            <div class="dr-field-group travel-group">
              <label>Option</label>
              <div class="dr-travel-toggle">
                <input type="checkbox"
                       class="is_travel_input"
                       id="{{ $travelId }}"
                       name="is_travel"
                       value="1"
                       {{ $isTravel ? 'checked' : '' }}>
                <label for="{{ $travelId }}">
                  <i data-lucide="navigation"></i>
                  Reisezeit
                </label>
              </div>
            </div>
          </div>

          <div class="dr-entry-report-area">
            <div class="dr-field-group customer-group">
              <label>Kunden / Start-End-Zeit</label>
              <select name="customer_ids[]"
                      class="dr-control select2 customer-multi"
                      data-placeholder="Kunden wählen"
                      multiple>
                @foreach ($customers as $customer)
                  <option value="{{ $customer->id }}"
                    {{ in_array((string) $customer->id, $selectedIds, true) ? 'selected' : '' }}>
                    {{ trim(($customer->name ?? '') . ' ' . ($customer->lastname ?? '')) ?: ('Kunde #' . $customer->id) }}
                  </option>
                @endforeach
              </select>

              <div class="customer-shares dr-customer-shares">
                @if (!empty($entry['customers']))
                  @foreach ($entry['customers'] as $c)
                    @php
                      $customerIdForWork = (int) ($c['id'] ?? 0);
                      $workOptions = collect(($customerWorkOptions ?? [])[$customerIdForWork] ?? []);
                      $objectOptions = $workOptions
                        ->filter(fn($opt) => !empty($opt['alternative_id']))
                        ->unique('alternative_id')
                        ->values();
                      $selectedAlternativeId = (string) ($c['alternative_id'] ?? '');
                      $selectedLeadProductListId = (string) ($c['lead_product_list_id'] ?? '');
                      $selectedProductId = (string) ($c['product_id'] ?? '');
                    @endphp
                    <div class="customer-share dr-customer-share" data-id="{{ $c['id'] }}">
                      <div class="dr-customer-name">
                        {{ $c['display_name'] ?? (trim(($c['name'] ?? '') . ' ' . ($c['lastname'] ?? '')) ?: ('Kunde #' . $c['id'])) }}
                      </div>

                      <div class="dr-customer-object-product">
                        <select name="alternative_id[{{ $c['id'] }}]"
                                class="dr-control customer_object_select"
                                data-customer-id="{{ $c['id'] }}">
                          <option value="">Objekt wählen</option>
                          @foreach($objectOptions as $objectOption)
                            <option value="{{ $objectOption['alternative_id'] }}" {{ $selectedAlternativeId === (string) $objectOption['alternative_id'] ? 'selected' : '' }}>
                              {{ $objectOption['object_label'] ?? ('Objekt #' . $objectOption['alternative_id']) }}
                            </option>
                          @endforeach
                        </select>

                        <select name="lead_product_list_id[{{ $c['id'] }}]"
                                class="dr-control customer_product_select"
                                data-customer-id="{{ $c['id'] }}">
                          <option value="">Produkt wählen</option>
                          @foreach($workOptions as $workOption)
                            <option value="{{ $workOption['lead_product_list_id'] }}"
                                    data-alternative-id="{{ $workOption['alternative_id'] ?? '' }}"
                                    data-product-id="{{ $workOption['product_id'] ?? '' }}"
                                    {{ $selectedLeadProductListId === (string) $workOption['lead_product_list_id'] ? 'selected' : '' }}>
                              {{ $workOption['product_label'] ?? ('Produkt #' . ($workOption['product_id'] ?? '')) }}
                            </option>
                          @endforeach
                        </select>

                        <input type="hidden"
                               name="product_id[{{ $c['id'] }}]"
                               class="customer_product_id_input"
                               value="{{ $selectedProductId }}">
                      </div>

                      <div class="dr-customer-time-pair">
                        <input type="time"
                               name="share_start_time[{{ $c['id'] }}]"
                               class="dr-control customer_share_start_input"
                               value="{{ $c['share_start_time'] ?? '' }}"
                               title="Startzeit Kunde">
                        <span>–</span>
                        <input type="time"
                               name="share_end_time[{{ $c['id'] }}]"
                               class="dr-control customer_share_end_input"
                               value="{{ $c['share_end_time'] ?? '' }}"
                               title="Endzeit Kunde">
                      </div>

                      <div>
                        <input type="number"
                               step="0.01"
                               min="0"
                               name="share_hours[{{ $c['id'] }}]"
                               class="dr-control customer_share_hours_input dr-customer-share-hours"
                               placeholder="Std."
                               value="{{ $c['share_hours'] !== null ? number_format($c['share_hours'], 2, '.', '') : '' }}"
                               readonly>
                      </div>

                      <div class="dr-customer-note-actions">
                        <input type="text"
                               name="customer_note[{{ $c['id'] }}]"
                               class="dr-control"
                               placeholder="Kundennotiz"
                               value="{{ $c['note'] ?? '' }}">

                        <button type="button"
                                class="dr-row-action ghost btn-notes"
                                title="Notizen"
                                data-date="{{ $rowDate }}"
                                data-entry="{{ $id ?? '__null' }}">
                          <i data-lucide="message-square"></i>
                        </button>

                        <button type="button"
                                class="dr-row-action ghost btn-attach"
                                title="Anhänge"
                                data-date="{{ $rowDate }}"
                                data-entry="{{ $id ?? '' }}">
                          <i data-lucide="paperclip"></i>
                        </button>
                      </div>
                      <div class="dr-customer-share-warning"></div>
                    </div>
                  @endforeach
                @endif
              </div>
              <div class="dr-customer-share-total" hidden>
                <span>Kundenzeiten gesamt</span>
                <strong>0,00 Std.</strong>
              </div>
            </div>

            <div class="dr-field-group description-group">
              @if($headerDescription !== '')
                <div class="dr-source-context">
                  <div class="dr-source-context-title">
                    <i data-lucide="info"></i>
                    Geplante Beschreibung aus {{ $label }}
                  </div>
                  <div class="dr-source-context-text">{{ $headerDescription }}</div>
                </div>
              @endif

              @if($hasAnyRelatedReport)
                <div class="dr-report-lock-box {{ $reportLocked ? 'is-mine' : 'is-other' }}">
                  <div class="dr-report-lock-main">
                    <span class="dr-report-lock-icon"><i data-lucide="{{ $reportLocked ? 'lock' : 'users' }}"></i></span>
                    <div>
                      @if($reportLocked)
                        <strong>Zu diesem {{ $label }} gibt es bereits deinen gespeicherten Bericht.</strong>
                      @else
                        <strong>Bereits berichtet{{ $otherNamesText ? ' von ' . $otherNamesText : '' }}.</strong>
                      @endif
                      <span>Der bestehende Modulbericht wird nicht überschrieben. Du kannst einen neuen eigenen Bericht erstellen oder den vorhandenen Bericht bestätigen.</span>
                    </div>
                  </div>
                  <div class="dr-report-choice-actions">
                    <button type="button" class="dr-btn dr-add-own-report">
                      <i data-lucide="plus-circle"></i> Neuen eigenen Bericht schreiben
                    </button>
                    <button type="button" class="dr-btn-soft dr-agree-report">
                      <i data-lucide="check-circle"></i> Ich stimme dem Bericht zu
                    </button>
                  </div>
                </div>
              @endif

              @if(!empty($allRelatedReports))
                <div class="dr-related-report-list">
                  <div class="dr-related-report-list-head">
                    <strong><i data-lucide="clipboard-list"></i> Vorhandene Berichte zu diesem {{ $label }}</strong>
                    <span>{{ count($allRelatedReports) }} Bericht{{ count($allRelatedReports) === 1 ? '' : 'e' }}</span>
                  </div>

                  @foreach($allRelatedReports as $relatedItem)
                    @php
                      $relatedHtml = (string)($relatedItem['report'] ?? '');
                      $relatedPlain = trim((string)($relatedItem['report_plain'] ?? strip_tags($relatedHtml)));
                      $relatedIsHtml = !empty($relatedItem['report_is_html']);
                      $relatedIsMine = (int)($relatedItem['employee_id'] ?? 0) === (int)($employee_id ?? 0);
                    @endphp

                    <div class="dr-related-report-item {{ $relatedIsMine ? 'is-mine' : 'is-other' }}">
                      <div class="dr-related-report-meta">
                        <span class="dr-related-avatar"><i data-lucide="{{ $relatedIsMine ? 'user-check' : 'user' }}"></i></span>
                        <div>
                          <strong>{{ $relatedItem['employee_name'] ?? 'Mitarbeiter' }}</strong>
                          <span>
                            {{ $relatedItem['source_label'] ?? 'Modulbericht' }}
                            @if(!empty($relatedItem['created_label'])) · {{ $relatedItem['created_label'] }} @endif
                            @if($relatedIsMine) · Dein Bericht @endif
                          </span>
                        </div>
                      </div>

                      <div class="dr-related-report-content">
                        @if($relatedIsHtml)
                          <div class="dr-related-report-html">{!! $relatedHtml !!}</div>
                          @if($relatedPlain !== '')
                            <details class="dr-related-report-plain">
                              <summary>Textversion anzeigen</summary>
                              <div>{{ $relatedPlain }}</div>
                            </details>
                          @endif
                        @else
                          <div class="dr-related-report-text">{{ $relatedPlain !== '' ? $relatedPlain : $relatedHtml }}</div>
                        @endif
                      </div>
                      <div class="dr-customer-share-warning"></div>
                    </div>
                  @endforeach
                </div>
              @endif

              <label>Mein Bericht / Arbeitsbeschreibung</label>
              <textarea name="description"
                        class="dr-control description_input {{ $hasAnyRelatedReport ? 'is-related-blocked' : '' }}"
                        rows="3"
                        placeholder="{{ $hasAnyRelatedReport ? 'Bitte wähle zuerst: neuen Bericht schreiben oder vorhandenen Bericht bestätigen.' : 'Hier deinen eigenen Tagesbericht schreiben – die geplante Beschreibung bleibt nur als Kontext sichtbar.' }}"
                        {{ $hasAnyRelatedReport ? 'readonly' : '' }}>{{ $reportInputDescription }}</textarea>
            </div>
          </div>
        </div>
      </div>
      </article>
    </td>
  </tr>
@empty
  <tr class="dr-empty-row">
    <td colspan="8">
      <div class="dr-empty-state">
        <i data-lucide="inbox"></i>
        <strong>Keine Tagespositionen gefunden.</strong>
        <span>Für diesen Tag wurden keine Einträge geladen.</span>
      </div>
    </td>
  </tr>
@endforelse

<tr class="total_footer dr-total-footer">
  <td colspan="8">
    <div class="dr-total-box mt-3">
      <span>Gesamtzeit</span>
      <strong class="dr-total-worked-inline">{{ number_format($totalWorked, 2, ',', '.') }} Std.</strong>
      <small>Die Kopf-Kacheln werden nach dem Laden/Speichern automatisch aktualisiert.</small>
    </div>
  </td>
</tr>