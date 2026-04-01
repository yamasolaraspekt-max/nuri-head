<div 
    x-show="modal === '{{ $id }}'" 
    style="display:none;" 
    class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50"
>
    <div @click.away="modal=null" class="bg-white rounded-lg shadow-lg w-full max-w-lg p-6">
        <h2 class="text-lg font-bold mb-4">{{ $title }}</h2>
        {{ $slot }}
    </div>
</div>
