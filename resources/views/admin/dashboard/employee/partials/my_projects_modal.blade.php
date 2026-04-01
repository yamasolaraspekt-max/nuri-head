@if($projects->isEmpty())
    <div class="text-center py-8 text-gray-500">
        <p>Sie sind aktuell keinem Projekt zugewiesen.</p>
    </div>
@else
    <div class="grid grid-cols-1 gap-4">
    @foreach($projects as $proj)
        <div class="bg-white rounded-lg p-4 shadow-sm border border-gray-200 hover:border-blue-300 transition">
            <div class="flex justify-between items-center mb-2">
                <span class="bg-blue-100 text-blue-800 text-xs font-semibold px-2.5 py-0.5 rounded border border-blue-200">
                    {{ $proj->product_name }}
                </span>
                <span class="text-xs font-bold {{ $proj->status == 'completed' ? 'text-green-600' : 'text-orange-500' }}">
                    {{ strtoupper($proj->status) }}
                </span>
            </div>
            
            <h4 class="font-bold text-gray-800">{{ $proj->customer_name }} {{ $proj->customer_lastname }}</h4>
            <p class="text-sm text-gray-500 mb-2">{{ $proj->customer_company }}</p>
            <p class="text-xs text-gray-400"><i class="feather icon-map-pin"></i> {{ $proj->street }}, {{ $proj->city }}</p>

            <div class="mt-3 pt-3 border-t border-gray-100">
                <div class="flex justify-between items-center">
                    <span class="text-xs text-gray-500">Aktuelle Phase:</span>
                    <span class="text-sm font-medium text-gray-700">{{ $proj->stage ?? 'Initial' }}</span>
                </div>
                @if($proj->last_history)
                    <div class="mt-1 text-xs text-gray-400">
                        Letzte Änderung: {{ $proj->last_history['changed_at'] }}
                        @if(isset($proj->last_history['new_price']))
                             | Preis: {{ $proj->last_history['new_price'] }}€
                        @endif
                    </div>
                @endif
            </div>
        </div>
    @endforeach
    </div>
@endif