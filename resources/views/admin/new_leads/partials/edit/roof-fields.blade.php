<div class="roof-group border rounded-2xl shadow-sm mb-4 bg-white">
    <div class="flex justify-between items-center px-4 py-2 border-b">
        <strong class="text-gray-700">{{ $index + 1 }}. Dachfläche</strong>
        <div class="flex gap-2">
            <button 
                type="button" 
                class="px-2 py-1 text-sm rounded-lg bg-gray-200 hover:bg-gray-300 toggle-collapse" 
                data-target="roof-collapse-{{ $index }}">
                Ein-/Ausklappen
            </button>
            <button 
                type="button" 
                class="px-2 py-1 text-sm rounded-lg bg-red-500 text-white hover:bg-red-600" 
                onclick="this.closest('.roof-group').remove()">
                Entfernen
            </button>
        </div>
    </div>

    <div id="roof-collapse-{{ $index }}" class="p-4 space-y-4">
        @php $r = $roof ?? null; @endphp

        <div class="grid md:grid-cols-3 gap-4">
            <div>
                <label class="text-sm text-gray-600">Name</label>
                <input type="text" class="input" 
                       name="roofs[{{ $index }}][designation]" 
                       value="{{ old("roofs.$index.designation", $r->designation ?? '') }}">
            </div>

            <!-- Dachform mit Icons -->
            <div>
                <label class="text-sm text-gray-600">Form</label>
                <div class="flex gap-2">
                    <label class="roof-option">
                        <input type="radio" class="hidden peer" 
                               name="roofs[{{ $index }}][roof]" value="Satteldach"
                               @checked(old("roofs.$index.roof", $r->roof ?? '') == 'Satteldach')>
                        <div class="roof-icon peer-checked:ring-2 peer-checked:ring-blue-500">
                            <!-- SVG Satteldach -->
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" stroke="currentColor">
                                <path d="M4 12L12 4l8 8" />
                            </svg>
                            <span class="text-xs">Sattel</span>
                        </div>
                    </label>
                    <label class="roof-option">
                        <input type="radio" class="hidden peer" 
                               name="roofs[{{ $index }}][roof]" value="Flachdach"
                               @checked(old("roofs.$index.roof", $r->roof ?? '') == 'Flachdach')>
                        <div class="roof-icon peer-checked:ring-2 peer-checked:ring-blue-500">
                            <!-- SVG Flachdach -->
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" stroke="currentColor">
                                <rect x="3" y="10" width="18" height="4" />
                            </svg>
                            <span class="text-xs">Flach</span>
                        </div>
                    </label>
                </div>
            </div>

            <div>
                <label class="text-sm text-gray-600">Eindeckung</label>
                <input type="text" class="input" 
                       name="roofs[{{ $index }}][roof_covering_name]" 
                       value="{{ old("roofs.$index.roof_covering_name", $r->roof_covering_name ?? '') }}">
            </div>

            <div>
                <label class="text-sm text-gray-600">Alter</label>
                <input type="number" step="any" class="input" 
                       name="roofs[{{ $index }}][roof_age]" 
                       value="{{ old("roofs.$index.roof_age", $r->roof_age ?? '') }}">
            </div>

            <div>
                <label class="text-sm text-gray-600">PV vorhanden</label>
                <div class="flex gap-2">
                    <label class="roof-option">
                        <input type="radio" class="hidden peer"
                               name="roofs[{{ $index }}][pv_existing]" value="Ja"
                               @checked(old("roofs.$index.pv_existing", $r->pv_existing ?? '') == 'Ja')>
                        <div class="roof-icon peer-checked:ring-green-500">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" stroke="currentColor">
                                <rect x="4" y="4" width="16" height="12" />
                            </svg>
                            <span class="text-xs">Ja</span>
                        </div>
                    </label>
                    <label class="roof-option">
                        <input type="radio" class="hidden peer"
                               name="roofs[{{ $index }}][pv_existing]" value="Nein"
                               @checked(old("roofs.$index.pv_existing", $r->pv_existing ?? '') == 'Nein')>
                        <div class="roof-icon peer-checked:ring-red-500">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" stroke="currentColor">
                                <path d="M4 4l16 16" />
                            </svg>
                            <span class="text-xs">Nein</span>
                        </div>
                    </label>
                </div>
            </div>
        </div>

        <div>
            <label class="text-sm text-gray-600">Bemerkung</label>
            <textarea class="input" rows="2" 
                      name="roofs[{{ $index }}][notes]">{{ old("roofs.$index.notes", $r->notes ?? '') }}</textarea>
        </div>
    </div>
</div>

<style>
.input {
    @apply w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none;
}
.roof-option {
    @apply cursor-pointer;
}
.roof-icon {
    @apply flex flex-col items-center justify-center border rounded-lg p-2 text-gray-600 hover:bg-gray-100;
}
</style>
