 @extends('admin.layouts.app')

@section('title', 'GC Online – übernommene Artikel')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-8 space-y-4">
    <h1 class="text-xl font-semibold text-slate-900">Übernommene Artikel aus GC Online</h1>

    @if(empty($items))
        <p class="text-sm text-slate-600">Keine Artikel übernommen.</p>
    @else
        <table class="w-full text-sm border border-slate-200 rounded-lg overflow-hidden">
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-3 py-2 text-left">Art.-Nr.</th>
                    <th class="px-3 py-2 text-left">Bezeichnung</th>
                    <th class="px-3 py-2 text-right">Menge</th>
                    <th class="px-3 py-2">EH</th>
                </tr>
            </thead>
            <tbody>
                @foreach($items as $item)
                    <tr class="border-t border-slate-100">
                        <td class="px-3 py-2 font-mono text-xs">{{ $item['art_no'] }}</td>
                        <td class="px-3 py-2">{{ $item['short'] ?: $item['long'] }}</td>
                        <td class="px-3 py-2 text-right">{{ $item['qty'] }}</td>
                        <td class="px-3 py-2 text-center">{{ $item['unit'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
@endsection
