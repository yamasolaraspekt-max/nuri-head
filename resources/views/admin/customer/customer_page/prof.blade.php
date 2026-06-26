
<form class="prose flex flex--column">
    <h3>Shipping</h3>
    <mapbox-address-autofill>
        <input class="input mb12" name="address" autocomplete="shipping address-line1" placeholder="Address">
    </mapbox-address-autofill>

    <input class="input mb12" name="apartment" autocomplete="shipping address-line2" placeholder="Apartment">
    
    <div class="flex">
        <input class="input mb12" name="city" autocomplete="shipping address-level2" placeholder="City">
        <input class="input mb12 ml6" name="state" autocomplete="shipping address-level1" placeholder="State">
        <input class="input mb12 ml6" name="zip" autocomplete="shipping postal-code" placeholder="ZIP / Postcode">
    </div>
</form>

<script>
const ACCESS_TOKEN = '{{ config('mapbox.mapbox_token') }}';

const script = document.createElement('script');
script.src = 'path-to-your-mapbox-address-autofill-script.js';  // Adjust the path to your script
script.id = 'search-js';

script.onload = () => {
    const elements = document.querySelectorAll('mapbox-address-autofill');
    for (const autofill of elements) {
        autofill.accessToken = ACCESS_TOKEN;
    }
};

document.head.appendChild(script);
</script>

