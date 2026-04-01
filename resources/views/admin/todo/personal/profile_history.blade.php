@if(count($history))
    <ul class="tp-history-list">
        @foreach($history as $entry)
            @php
                $rawAt    = $entry['at'] ?? null;
                $atCarbon = null;

                if ($rawAt instanceof \Carbon\Carbon) {
                    $atCarbon = $rawAt;
                } elseif (is_string($rawAt)) {
                    // Fix "2025-12-03 00:00:00 00:00:00" → take only first date+time part
                    $parts = preg_split('/\s+/', trim($rawAt));
                    if (count($parts) >= 2) {
                        $candidate = $parts[0] . ' ' . $parts[1]; // "Y-m-d H:i:s"
                    } else {
                        $candidate = $rawAt;
                    }

                    try {
                        $atCarbon = \Carbon\Carbon::parse($candidate);
                    } catch (\Exception $e) {
                        $atCarbon = null;
                    }
                }
            @endphp

            <li class="tp-history-item">
                <time>
                    @if($atCarbon)
                        {{ $atCarbon->format('d.m.Y H:i') }}
                    @else
                        –
                    @endif
                </time>
                <div>
                    <div class="tp-history-title">{{ $entry['title'] }}</div>
                    @if(!empty($entry['by']))
                        <div class="tp-history-meta">
                            von {{ $entry['by'] }}
                        </div>
                    @endif
                    @if(!empty($entry['details']))
                        <div style="font-size:.78rem;margin-top:.1rem;">
                            {{ \Illuminate\Support\Str::limit($entry['details'], 140) }}
                        </div>
                    @endif
                </div>
            </li>
        @endforeach
    </ul>
@else
    <div style="font-size:.8rem;color:#9ca3af;">Noch keine Aktivitäten erfasst.</div>
@endif
