{{-- kommunikation.blade.php (example) --}}
<div class="section-content">
    <h3>Auftaage</h3>
    <p>Kunde: {{ $customer->name ?? '' }}</p>
    <p>Objekt: {{ $alternative->object_name ?? '' }}</p>
    <p>Produkt-ID: {{ $productData->product_id }}</p>
</div>
