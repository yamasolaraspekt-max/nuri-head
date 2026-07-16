@if($customers->isEmpty())
    <div class="flex flex-col items-center justify-center py-12 text-slate-400">
        <div class="bg-slate-50 p-4 rounded-full mb-3">
            <i class="ri-user-unfollow-line text-3xl opacity-50"></i>
        </div>
        <p class="font-medium">Sie haben aktuell keine zugewiesenen Kunden.</p>
    </div>
@else
    <div class="space-y-4">
        @foreach($customers as $cust)
            <div class="bg-white rounded-xl p-5 border border-slate-200 shadow-sm hover:shadow-md transition-shadow duration-200 relative group/card">
                
                {{-- Customer Header --}}
                <div class="flex justify-between items-start mb-4">
                    <div class="flex-1">
                        {{-- Profile Link --}}
                        <a href="{{ url('new_lead_profile/' . $cust->id) }}" 
                           class="inline-flex items-center gap-2 group/link text-decoration-none">
                            <h4 class="font-bold text-lg text-slate-800 group-hover/link:text-blue-600 transition-colors">
                                {{ $cust->firma ? $cust->firma . ' – ' : '' }}{{ $cust->name }} {{ $cust->lastname }}
                            </h4>
                            <i class="ri-external-link-line text-slate-400 text-sm group-hover/link:text-blue-600 transition-colors opacity-0 group-hover/link:opacity-100"></i>
                        </a>

                        {{-- Meta Info --}}
                        <div class="flex flex-wrap gap-x-4 gap-y-1 mt-1 text-sm text-slate-500">
                            <span class="flex items-center gap-1.5">
                                <i class="ri-hashtag text-slate-400"></i> {{ $cust->customer_no ?? '-' }}
                            </span>
                            @if($cust->phone)
                                <span class="hidden sm:inline text-slate-300">|</span>
                                <span class="flex items-center gap-1.5">
                                    <i class="ri-phone-line text-slate-400"></i> {{ $cust->phone }}
                                </span>
                            @endif
                            @if($cust->city)
                                <span class="hidden sm:inline text-slate-300">|</span>
                                <span class="flex items-center gap-1.5">
                                    <i class="ri-map-pin-line text-slate-400"></i> {{ $cust->city }}
                                </span>
                            @endif
                        </div>
                    </div>
                    
                    {{-- Quick Action Button (Visible on Hover) --}}
                    <a href="{{ url('new_lead_profile/' . $cust->id) }}" 
                       class="opacity-0 group-hover/card:opacity-100 transition-opacity p-2 rounded-full bg-slate-50 text-slate-600 hover:bg-blue-50 hover:text-blue-600">
                        <i class="ri-arrow-right-line"></i>
                    </a>
                </div>

                {{-- Objects / Houses --}}
                @if($cust->objects->isNotEmpty())
                    <div class="pl-4 border-l-2 border-slate-100 space-y-4">
                        
                        @foreach($cust->objects as $obj)
                            <div class="house-item">
                                {{-- Object Header --}}
                                <div class="flex flex-wrap justify-between items-baseline mb-2">
                                    <span class="font-semibold text-slate-700 text-sm flex items-center gap-2">
                                        <div class="w-1.5 h-1.5 rounded-full bg-slate-300"></div>
                                        {{ $obj->object_name ?? 'Objekt' }}
                                    </span>
                                    <span class="text-xs text-slate-400 bg-slate-50 px-2 py-0.5 rounded">
                                        {{ $obj->street }}
                                    </span>
                                </div>

                                {{-- Products Grid --}}
                                @if($obj->products->isNotEmpty())
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                        @foreach($obj->products as $prod)
                                            <div class="bg-slate-50 rounded-lg p-3 border border-slate-200 relative overflow-hidden group/product hover:border-blue-200 hover:bg-white transition-colors">
                                                
                                                {{-- Status Color Strip --}}
                                                <div class="absolute left-0 top-0 bottom-0 w-1 
                                                    {{ $prod->status == 'completed' ? 'bg-emerald-400' : ($prod->status == 'open' ? 'bg-blue-400' : 'bg-slate-300') }}">
                                                </div>

                                                <div class="pl-2.5">
                                                    <div class="flex justify-between items-start mb-1">
                                                        <span class="font-semibold text-slate-700 text-sm">
                                                            {{ $prod->product_name }}
                                                        </span>
                                                        
                                                        {{-- Status Badge --}}
                                                        <span class="text-[10px] uppercase font-bold tracking-wide px-1.5 py-0.5 rounded
                                                            {{ $prod->status == 'completed' ? 'bg-emerald-100 text-emerald-700' : 'bg-blue-100 text-blue-700' }}">
                                                            {{ ucfirst($prod->status) }}
                                                        </span>
                                                    </div>

                                                    <div class="flex items-center justify-between text-xs mt-2">
                                                        <span class="text-slate-500 truncate max-w-[120px]" title="Aktuelle Phase">
                                                            {{ $prod->stage ?? 'Initial' }}
                                                        </span>
                                                        
                                                        @if($prod->last_history)
                                                            <span class="text-slate-400 text-[10px]">
                                                                Updated: {{ \Carbon\Carbon::parse($prod->last_history['changed_at'] ?? now())->format('d.m.y') }}
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else 
                                    <div class="text-xs text-slate-400 italic pl-4 py-1">
                                        Keine Produkte hinterlegt
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        @endforeach
    </div>
@endif