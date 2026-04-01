<!DOCTYPE html>
<html lang="de">
<head>
  <meta charset="UTF-8" />
  <title>flaschenpost.clone – Single Page Demo</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />

  <!-- Tailwind CDN -->
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            fpGreen: '#1db954',
            fpBlue: '#0050ff',
          },
        },
      },
    };
  </script>
</head>
<body class="bg-gray-50 text-gray-900">
  <!-- HEADER -->
  <header class="border-b border-gray-200 bg-white sticky top-0 z-30">
    <div class="max-w-6xl mx-auto px-4 py-3 flex items-center gap-4">
      <!-- Logo -->
      <button data-nav="home" class="flex items-center gap-2">
        <div class="w-8 h-8 rounded-full bg-fpBlue flex items-center justify-center text-white text-xs font-bold">
          fp
        </div>
        <span class="font-semibold text-lg tracking-tight">
          flaschenpost<span class="text-fpBlue">.clone</span>
        </span>
      </button>

      <!-- PLZ / Liefergebiet -->
      <button
        class="hidden sm:inline-flex items-center gap-2 ml-4 px-3 py-1.5 border border-gray-200 rounded-full text-sm text-gray-700 hover:bg-gray-50"
        id="zipButton">
        <svg class="w-4 h-4" viewBox="0 0 20 20" fill="none">
          <path d="M10 2.5C6.96 2.5 4.5 4.96 4.5 8c0 3.04 2.89 6.59 4.43 8.33.59.68 1.56.68 2.15 0C12.61 14.59 15.5 11.04 15.5 8 15.5 4.96 13.04 2.5 10 2.5z" stroke="currentColor" stroke-width="1.6"/>
          <circle cx="10" cy="8" r="2" stroke="currentColor" stroke-width="1.6"/>
        </svg>
        <span id="zipLabel">PLZ eingeben</span>
      </button>

      <!-- Suche (global, nur optisch) -->
      <div class="flex-1 flex">
        <div class="w-full max-w-xl mx-auto">
          <label class="relative block">
            <span class="sr-only">Suche</span>
            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
              🔍
            </span>
            <input
              id="globalSearchInput"
              type="search"
              placeholder="Getränke, Marken, Kategorien… (Demo)"
              class="w-full pl-9 pr-3 py-1.5 rounded-full border border-gray-200 focus:outline-none focus:ring-2 focus:ring-fpBlue/60 focus:border-fpBlue text-sm"
            />
          </label>
        </div>
      </div>

      <!-- Account / Cart -->
      <div class="flex items-center gap-2">
        <button
          class="hidden sm:inline-flex items-center gap-1 px-3 py-1.5 rounded-full text-sm border border-gray-200 text-gray-700 hover:bg-gray-50">
          <span class="text-xs">👤</span>
          <span>Login</span>
        </button>

        <button data-nav="cart"
          class="relative inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-sm bg-fpBlue text-white hover:bg-blue-700">
          <span>🛒</span>
          <span>Warenkorb</span>
          <span id="cartCountBadge"
                class="inline-flex items-center justify-center text-xs font-semibold bg-white text-fpBlue rounded-full min-w-[1.4rem] h-5 px-1">
            0
          </span>
        </button>
      </div>
    </div>
  </header>

  <!-- MAIN -->
  <main class="max-w-6xl mx-auto px-4 pt-6 pb-10">
    <!-- VIEW: HOME -->
    <section data-view="home" class="space-y-10">
      <!-- Hero -->
      <section class="grid gap-6 md:grid-cols-[2fr,1.2fr] items-center">
        <div>
          <h1 class="text-3xl md:text-4xl font-bold tracking-tight mb-3">
            Getränke & Lebensmittel in <span class="text-fpBlue">120 Minuten</span> geliefert.
          </h1>
          <p class="text-gray-600 mb-4 text-sm md:text-base">
            Bestell deine Lieblingsgetränke, Snacks und mehr. Wir liefern bequem bis an deine Wohnungstür (Demo).
          </p>

          <form id="zipForm" class="flex flex-col sm:flex-row gap-2 max-w-md">
            <input
              id="zipInput"
              type="text"
              maxlength="5"
              placeholder="PLZ eingeben"
              class="flex-1 px-3 py-2 rounded-full border border-gray-300 focus:outline-none focus:ring-2 focus:ring-fpBlue/60 text-sm"
              required
            />
            <button
              type="submit"
              class="px-4 py-2 rounded-full bg-fpBlue text-white text-sm font-semibold hover:bg-blue-700">
              Liefergebiet prüfen
            </button>
          </form>

          <p id="zipMessage" class="text-xs text-gray-500 mt-2 hidden"></p>

          <div class="flex flex-wrap gap-2 mt-4 text-xs text-gray-600">
            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-white border border-gray-200">
              ⏱️ Lieferung in 120 Min.
            </span>
            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-white border border-gray-200">
              🔄 Einfache Pfandrücknahme
            </span>
            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-white border border-gray-200">
              💳 Online bezahlen
            </span>
          </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 flex flex-col gap-3">
          <div class="flex items-center justify-between text-sm">
            <div>
              <div class="font-semibold">Deine nächste Lieferung</div>
              <div class="text-gray-500 text-xs">Beispiel-Lieferfenster</div>
            </div>
            <div class="px-2 py-1 rounded-full bg-emerald-50 text-emerald-700 text-xs font-semibold">
              Heute, 18:00–20:00
            </div>
          </div>

          <div class="grid grid-cols-3 gap-3 mt-2">
            <div class="flex flex-col items-center text-xs">
              <div class="w-12 h-20 bg-gradient-to-b from-blue-100 to-blue-300 rounded-lg flex items-center justify-center text-[10px]">
                Wasser
              </div>
              <span class="mt-1 text-gray-600">Wasser</span>
            </div>
            <div class="flex flex-col items-center text-xs">
              <div class="w-12 h-20 bg-gradient-to-b from-amber-100 to-amber-300 rounded-lg flex items-center justify-center text-[10px]">
                Bier
              </div>
              <span class="mt-1 text-gray-600">Bier</span>
            </div>
            <div class="flex flex-col items-center text-xs">
              <div class="w-12 h-20 bg-gradient-to-b from-pink-100 to-pink-300 rounded-lg flex items-center justify-center text-[10px]">
                Soft
              </div>
              <span class="mt-1 text-gray-600">Softdrinks</span>
            </div>
          </div>
        </div>
      </section>

      <!-- Kategorien -->
      <section>
        <div class="flex items-center justify-between mb-4">
          <h2 class="text-lg font-semibold">Kategorien</h2>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
          <button data-category="bier"
                  class="group bg-white border border-gray-100 rounded-xl p-4 flex flex-col gap-3 hover:shadow-sm">
            <div class="w-10 h-10 rounded-full bg-amber-100 flex items-center justify-center text-lg">
              🍺
            </div>
            <div>
              <div class="text-sm font-semibold">Bier</div>
              <div class="text-xs text-gray-500">Helles, Pils, Radler…</div>
            </div>
          </button>

          <button data-category="wasser"
                  class="group bg-white border border-gray-100 rounded-xl p-4 flex flex-col gap-3 hover:shadow-sm">
            <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center text-lg">
              💧
            </div>
            <div>
              <div class="text-sm font-semibold">Wasser</div>
              <div class="text-xs text-gray-500">Still & Sprudel</div>
            </div>
          </button>

          <button data-category="softdrinks"
                  class="group bg-white border border-gray-100 rounded-xl p-4 flex flex-col gap-3 hover:shadow-sm">
            <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center text-lg">
              🥤
            </div>
            <div>
              <div class="text-sm font-semibold">Softdrinks</div>
              <div class="text-xs text-gray-500">Cola, Limo & mehr</div>
            </div>
          </button>

          <button data-category="snacks"
                  class="group bg-white border border-gray-100 rounded-xl p-4 flex flex-col gap-3 hover:shadow-sm">
            <div class="w-10 h-10 rounded-full bg-yellow-100 flex items-center justify-center text-lg">
              🍿
            </div>
            <div>
              <div class="text-sm font-semibold">Snacks</div>
              <div class="text-xs text-gray-500">Chips, Nüsse…</div>
            </div>
          </button>
        </div>
      </section>

      <!-- Bestseller -->
      <section>
        <div class="flex items-center justify-between mb-4">
          <h2 class="text-lg font-semibold">Beliebt in deiner Nähe</h2>
        </div>
        <div id="homeProductGrid" class="grid grid-cols-2 md:grid-cols-4 gap-3"></div>
      </section>
    </section>

    <!-- VIEW: CATEGORY -->
    <section data-view="category" class="hidden">
      <header class="flex items-center justify-between mb-4">
        <div>
          <h1 id="categoryTitle" class="text-xl font-semibold">Produkte</h1>
          <p class="text-xs text-gray-500 mt-1">
            Demo-Produktliste nach Kategorie.
          </p>
        </div>
        <button data-nav="home" class="text-xs text-fpBlue hover:underline">
          ← Zur Startseite
        </button>
      </header>

      <div class="flex flex-wrap gap-2 mb-4 text-xs">
        <button class="px-3 py-1.5 rounded-full border border-gray-200 bg-white text-gray-700">
          Sortierung: Beliebteste
        </button>
        <button class="px-3 py-1.5 rounded-full border border-gray-200 bg-white text-gray-700">
          Pfandkiste
        </button>
        <button class="px-3 py-1.5 rounded-full border border-gray-200 bg-white text-gray-700">
          Angebote
        </button>
      </div>

      <div id="categoryGrid" class="grid grid-cols-2 md:grid-cols-4 gap-3"></div>
    </section>

    <!-- VIEW: CART -->
    <section data-view="cart" class="hidden">
      <header class="flex items-center justify-between mb-4">
        <h1 class="text-xl font-semibold">Warenkorb</h1>
        <button data-nav="home" class="text-xs text-fpBlue hover:underline">
          ← Weiter einkaufen
        </button>
      </header>

      <div class="grid gap-6 md:grid-cols-[2fr,1fr]">
        <div id="cartItems" class="bg-white border border-gray-100 rounded-xl p-4"></div>

        <aside id="cartSummary" class="bg-white border border-gray-100 rounded-xl p-4 h-fit hidden">
          <h2 class="text-sm font-semibold mb-3">Zusammenfassung</h2>
          <dl class="text-sm text-gray-700 space-y-1">
            <div class="flex justify-between">
              <dt>Zwischensumme</dt>
              <dd id="cartSubtotal">0,00 €</dd>
            </div>
            <div class="flex justify-between">
              <dt>Pfand</dt>
              <dd id="cartDeposit">0,00 €</dd>
            </div>
            <div class="flex justify-between font-semibold border-t border-gray-100 pt-2 mt-2">
              <dt>Gesamt</dt>
              <dd id="cartTotal">0,00 €</dd>
            </div>
          </dl>

          <button
            type="button"
            data-nav="checkout"
            class="mt-4 w-full inline-flex items-center justify-center px-4 py-2 rounded-full bg-fpBlue text-white text-sm font-semibold hover:bg-blue-700">
            Zur Kasse
          </button>

          <button
            type="button"
            id="clearCartBtn"
            class="mt-2 w-full inline-flex items-center justify-center px-4 py-1.5 rounded-full border border-gray-200 text-xs text-gray-600 hover:bg-gray-50">
            Warenkorb leeren
          </button>
        </aside>
      </div>
    </section>

    <!-- VIEW: CHECKOUT -->
    <section data-view="checkout" class="hidden">
      <header class="flex items-center justify-between mb-4">
        <h1 class="text-xl font-semibold">Checkout</h1>
        <button data-nav="cart" class="text-xs text-fpBlue hover:underline">
          ← Zurück zum Warenkorb
        </button>
      </header>

      <div class="grid gap-6 md:grid-cols-[2fr,1fr]">
        <form id="checkoutForm" class="bg-white border border-gray-100 rounded-xl p-4 space-y-4">
          <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1">Name</label>
            <input type="text" name="name" required
                   class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-fpBlue" />
          </div>
          <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1">Straße & Hausnummer</label>
            <input type="text" name="street" required
                   class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-fpBlue" />
          </div>
          <div class="grid grid-cols-3 gap-3">
            <div class="col-span-1">
              <label class="block text-xs font-semibold text-gray-700 mb-1">PLZ</label>
              <input type="text" name="zip" required
                     class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-fpBlue" />
            </div>
            <div class="col-span-2">
              <label class="block text-xs font-semibold text-gray-700 mb-1">Stadt</label>
              <input type="text" name="city" required
                     class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-fpBlue" />
            </div>
          </div>
          <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1">Telefon</label>
            <input type="tel" name="phone" required
                   class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-fpBlue" />
          </div>
          <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1">Lieferzeitfenster</label>
            <select name="slot" required
                    class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-fpBlue">
              <option value="">Bitte wählen</option>
              <option value="today_18_20">Heute, 18–20 Uhr</option>
              <option value="today_20_22">Heute, 20–22 Uhr</option>
              <option value="tomorrow_10_12">Morgen, 10–12 Uhr</option>
            </select>
          </div>

          <button type="submit"
                  class="w-full inline-flex items-center justify-center px-4 py-2 rounded-full bg-fpBlue text-white text-sm font-semibold hover:bg-blue-700">
            Bestellung abschicken (Demo)
          </button>
        </form>

        <aside class="bg-white border border-gray-100 rounded-xl p-4 h-fit">
          <h2 class="text-sm font-semibold mb-3">Bestellübersicht</h2>
          <dl class="text-sm text-gray-700 space-y-1">
            <div class="flex justify-between">
              <dt>Zwischensumme</dt>
              <dd id="checkoutSubtotal">0,00 €</dd>
            </div>
            <div class="flex justify-between">
              <dt>Pfand</dt>
              <dd id="checkoutDeposit">0,00 €</dd>
            </div>
            <div class="flex justify-between font-semibold border-t border-gray-100 pt-2 mt-2">
              <dt>Gesamt</dt>
              <dd id="checkoutTotal">0,00 €</dd>
            </div>
          </dl>
          <p class="mt-3 text-xs text-gray-500">
            Dies ist nur eine Demo. Es findet keine echte Bestellung statt.
          </p>
        </aside>
      </div>
    </section>

    <!-- VIEW: ORDER TRACKING -->
    <section data-view="order" class="hidden">
      <header class="flex items-center justify-between mb-4">
        <h1 class="text-xl font-semibold">Bestellung</h1>
        <button data-nav="home" class="text-xs text-fpBlue hover:underline">
          ← Zur Startseite
        </button>
      </header>

      <div id="orderContainer" class="bg-white border border-gray-100 rounded-xl p-4 text-sm text-gray-700">
        <!-- wird per JS gefüllt -->
      </div>
    </section>
  </main>

  <!-- FOOTER -->
  <footer class="border-t border-gray-200 mt-10 bg-white">
    <div class="max-w-6xl mx-auto px-4 py-6 text-xs text-gray-500 flex flex-wrap gap-4 justify-between">
      <div>&copy; 2025 flaschenpost.clone — Nicht mit flaschenpost SE verbunden. Demo-Frontend.</div>
      <div class="flex gap-4">
        <a href="#" class="hover:text-gray-700">AGB</a>
        <a href="#" class="hover:text-gray-700">Datenschutz</a>
        <a href="#" class="hover:text-gray-700">Impressum</a>
      </div>
    </div>
  </footer>

  <!-- SCRIPT: DATA + LOGIC -->
  <script>
    // ---------------------- DATA ----------------------
    const PRODUCTS = [
      {
        id: 'p-bier-1',
        name: 'Beispiel Pils 20x0,5l',
        brand: 'Beispielbrauerei',
        category: 'bier',
        price: 15.99,
        deposit: 3.10,
        volume: '20x0,5l',
      },
      {
        id: 'p-bier-2',
        name: 'Radler 20x0,5l',
        brand: 'Sommerbräu',
        category: 'bier',
        price: 16.49,
        deposit: 3.10,
        volume: '20x0,5l',
      },
      {
        id: 'p-wasser-1',
        name: 'Stillwasser 12x1,0l',
        brand: 'Quellfrisch',
        category: 'wasser',
        price: 5.99,
        deposit: 3.30,
        volume: '12x1,0l',
      },
      {
        id: 'p-wasser-2',
        name: 'Medium Wasser 12x0,7l',
        brand: 'Bergquelle',
        category: 'wasser',
        price: 4.49,
        deposit: 3.30,
        volume: '12x0,7l',
      },
      {
        id: 'p-soft-1',
        name: 'Orangenlimonade 12x1,0l',
        brand: 'OrangeMax',
        category: 'softdrinks',
        price: 11.49,
        deposit: 3.30,
        volume: '12x1,0l',
      },
      {
        id: 'p-soft-2',
        name: 'Cola 12x1,0l',
        brand: 'ColaPlus',
        category: 'softdrinks',
        price: 12.49,
        deposit: 3.30,
        volume: '12x1,0l',
      },
      {
        id: 'p-snack-1',
        name: 'Paprika Chips 175g',
        brand: 'Snacky',
        category: 'snacks',
        price: 1.99,
        deposit: 0,
        volume: '175g',
      },
      {
        id: 'p-snack-2',
        name: 'Erdnüsse gesalzen 200g',
        brand: 'KnusperMix',
        category: 'snacks',
        price: 1.79,
        deposit: 0,
        volume: '200g',
      },
    ];

    function getProductById(id) {
      return PRODUCTS.find(p => p.id === id) || null;
    }

    // ---------------------- CART ----------------------
    const CART_KEY = 'fp_clone_cart';
    const ORDER_KEY = 'fp_clone_last_order';

    function loadCart() {
      try {
        const raw = localStorage.getItem(CART_KEY);
        return raw ? JSON.parse(raw) : [];
      } catch {
        return [];
      }
    }

    function saveCart(cart) {
      localStorage.setItem(CART_KEY, JSON.stringify(cart));
    }

    function addToCart(productId, qty = 1) {
      const cart = loadCart();
      const existing = cart.find(i => i.productId === productId);
      if (existing) {
        existing.qty += qty;
      } else {
        cart.push({ productId, qty });
      }
      saveCart(cart);
      updateCartBadge();
    }

    function updateCartItem(productId, qty) {
      let cart = loadCart();
      cart = cart
        .map(i => i.productId === productId ? { ...i, qty: Number(qty) || 0 } : i)
        .filter(i => i.qty > 0);
      saveCart(cart);
      updateCartBadge();
    }

    function removeFromCart(productId) {
      const cart = loadCart().filter(i => i.productId !== productId);
      saveCart(cart);
      updateCartBadge();
    }

    function clearCart() {
      saveCart([]);
      updateCartBadge();
    }

    function cartTotals() {
      const cart = loadCart();
      let subtotal = 0;
      let deposit = 0;

      const items = cart.map(item => {
        const p = getProductById(item.productId);
        if (!p) return null;
        const lineSubtotal = p.price * item.qty;
        const lineDeposit = p.deposit * item.qty;
        subtotal += lineSubtotal;
        deposit += lineDeposit;
        return { product: p, qty: item.qty, lineSubtotal, lineDeposit };
      }).filter(Boolean);

      return { items, subtotal, deposit, total: subtotal + deposit };
    }

    function updateCartBadge() {
      const el = document.getElementById('cartCountBadge');
      if (!el) return;
      const cart = loadCart();
      const count = cart.reduce((sum, i) => sum + i.qty, 0);
      el.textContent = count;
    }

    // ---------------------- VIEWS ----------------------
    function showView(name) {
      document.querySelectorAll('[data-view]').forEach(section => {
        section.classList.toggle('hidden', section.getAttribute('data-view') !== name);
      });
      if (name === 'home') renderHomeProducts();
      if (name === 'category') renderCategoryPage(window.currentCategory || 'all');
      if (name === 'cart') renderCartPage();
      if (name === 'checkout') {
        renderCheckoutSummary();
      }
      if (name === 'order') renderOrderView();
    }

    // ---------------------- ZIP ----------------------
    function initZip() {
      const form = document.getElementById('zipForm');
      const input = document.getElementById('zipInput');
      const msg = document.getElementById('zipMessage');
      const label = document.getElementById('zipLabel');

      const stored = localStorage.getItem('fp_clone_zip');
      if (stored && label) label.textContent = stored;

      if (!form || !input) return;

      form.addEventListener('submit', e => {
        e.preventDefault();
        const zip = input.value.trim();
        if (!/^\d{5}$/.test(zip)) {
          msg.classList.remove('hidden');
          msg.textContent = 'Bitte eine gültige deutsche PLZ (5 Ziffern) eingeben.';
          msg.classList.add('text-red-600');
          return;
        }
        localStorage.setItem('fp_clone_zip', zip);
        if (label) label.textContent = zip;
        msg.classList.remove('hidden');
        msg.classList.remove('text-red-600');
        msg.classList.add('text-emerald-600');
        msg.textContent = 'Liefergebiet gespeichert (Demo).';
      });
    }

    // ---------------------- PRODUCT CARDS ----------------------
    function productCardHTML(product) {
      const price = product.price.toFixed(2).replace('.', ',');
      const deposit = product.deposit
        ? `+ ${product.deposit.toFixed(2).replace('.', ',')} € Pfand`
        : 'ohne Pfand';

      return `
        <div class="bg-white border border-gray-100 rounded-xl p-3 flex flex-col justify-between">
          <div>
            <div class="text-[11px] text-gray-500 mb-1">${product.brand}</div>
            <div class="text-sm font-semibold leading-tight line-clamp-2">
              ${product.name}
            </div>
            <div class="text-[11px] text-gray-500 mt-1">${product.volume}</div>
          </div>
          <div class="mt-3 flex items-end justify-between gap-2">
            <div>
              <div class="text-base font-semibold">${price} €</div>
              <div class="text-[11px] text-gray-500">${deposit}</div>
            </div>
            <button
              data-product-id="${product.id}"
              class="add-to-cart-btn inline-flex items-center justify-center px-3 py-1.5 rounded-full text-xs font-semibold bg-fpBlue text-white hover:bg-blue-700">
              In den Warenkorb
            </button>
          </div>
        </div>
      `;
    }

    function wireAddToCartButtons(root = document) {
      root.querySelectorAll('.add-to-cart-btn').forEach(btn => {
        btn.addEventListener('click', () => {
          const id = btn.getAttribute('data-product-id');
          if (!id) return;
          addToCart(id, 1);
          const oldText = btn.textContent;
          btn.textContent = 'Hinzugefügt';
          setTimeout(() => (btn.textContent = oldText), 700);
        });
      });
    }

    // ---------------------- HOME ----------------------
    function renderHomeProducts(limit = 4) {
      const grid = document.getElementById('homeProductGrid');
      if (!grid) return;
      const shuffled = [...PRODUCTS].sort(() => Math.random() - 0.5);
      const list = shuffled.slice(0, limit);
      grid.innerHTML = list.map(productCardHTML).join('');
      wireAddToCartButtons(grid);
    }

    // ---------------------- CATEGORY ----------------------
    const CATEGORY_LABEL = {
      bier: 'Bier',
      wasser: 'Wasser',
      softdrinks: 'Softdrinks',
      snacks: 'Snacks',
      all: 'Alle Produkte',
    };

    function renderCategoryPage(category = 'all') {
      const container = document.getElementById('categoryGrid');
      const titleEl = document.getElementById('categoryTitle');
      if (!container) return;

      let list = PRODUCTS;
      if (category !== 'all') {
        list = PRODUCTS.filter(p => p.category === category);
      }

      if (titleEl) titleEl.textContent = CATEGORY_LABEL[category] || 'Produkte';

      if (!list.length) {
        container.innerHTML = '<p class="text-sm text-gray-500">Keine Produkte in dieser Kategorie.</p>';
        return;
      }

      container.innerHTML = list.map(productCardHTML).join('');
      wireAddToCartButtons(container);
    }

    // ---------------------- CART PAGE ----------------------
    function renderCartPage() {
      const container = document.getElementById('cartItems');
      const summary = document.getElementById('cartSummary');
      if (!container || !summary) return;

      const totals = cartTotals();

      if (!totals.items.length) {
        container.innerHTML = '<p class="text-sm text-gray-500">Dein Warenkorb ist leer.</p>';
        summary.classList.add('hidden');
        return;
      }

      summary.classList.remove('hidden');

      container.innerHTML = totals.items.map(item => {
        const p = item.product;
        return `
          <div class="flex items-start justify-between gap-3 py-3 border-b border-gray-100">
            <div class="flex-1">
              <div class="text-sm font-semibold">${p.name}</div>
              <div class="text-[11px] text-gray-500">${p.brand} • ${p.volume}</div>
              <div class="text-[11px] text-gray-500 mt-1">
                Einzelpreis: ${p.price.toFixed(2).replace('.', ',')} €,
                Pfand: ${p.deposit.toFixed(2).replace('.', ',')} €
              </div>
            </div>
            <div class="flex flex-col items-end gap-2">
              <div class="flex items-center gap-1">
                <input
                  type="number"
                  min="1"
                  value="${item.qty}"
                  data-cart-qty="${p.id}"
                  class="w-16 px-2 py-1 border border-gray-300 rounded text-xs"
                />
                <button data-cart-remove="${p.id}" class="text-[11px] text-red-500 hover:underline">
                  Entfernen
                </button>
              </div>
              <div class="text-xs text-gray-700">
                <div>Zwischensumme: ${item.lineSubtotal.toFixed(2).replace('.', ',')} €</div>
                <div>Pfand: ${item.lineDeposit.toFixed(2).replace('.', ',')} €</div>
              </div>
            </div>
          </div>
        `;
      }).join('');

      document.getElementById('cartSubtotal').textContent =
        totals.subtotal.toFixed(2).replace('.', ',') + ' €';
      document.getElementById('cartDeposit').textContent =
        totals.deposit.toFixed(2).replace('.', ',') + ' €';
      document.getElementById('cartTotal').textContent =
        totals.total.toFixed(2).replace('.', ',') + ' €';

      container.querySelectorAll('[data-cart-qty]').forEach(input => {
        input.addEventListener('change', () => {
          const id = input.getAttribute('data-cart-qty');
          const qty = Number(input.value) || 1;
          updateCartItem(id, qty);
          renderCartPage();
        });
      });

      container.querySelectorAll('[data-cart-remove]').forEach(btn => {
        btn.addEventListener('click', () => {
          const id = btn.getAttribute('data-cart-remove');
          removeFromCart(id);
          renderCartPage();
        });
      });
    }

    // ---------------------- CHECKOUT SUMMARY ----------------------
    function renderCheckoutSummary() {
      const totals = cartTotals();
      document.getElementById('checkoutSubtotal').textContent =
        totals.subtotal.toFixed(2).replace('.', ',') + ' €';
      document.getElementById('checkoutDeposit').textContent =
        totals.deposit.toFixed(2).replace('.', ',') + ' €';
      document.getElementById('checkoutTotal').textContent =
        totals.total.toFixed(2).replace('.', ',') + ' €';
    }

    // ---------------------- ORDER VIEW ----------------------
    function renderOrderView() {
      const container = document.getElementById('orderContainer');
      if (!container) return;

      const raw = localStorage.getItem(ORDER_KEY);
      if (!raw) {
        container.innerHTML = '<p class="text-sm text-gray-500">Keine Bestellung gefunden.</p>';
        return;
      }
      const order = JSON.parse(raw);

      const itemsHtml = order.items.map(it => `
        <li class="flex justify-between text-xs py-1 border-b border-gray-100">
          <span>${it.qty}× ${it.product.name}</span>
          <span>${it.lineTotal.toFixed(2).replace('.', ',')} €</span>
        </li>
      `).join('');

      container.innerHTML = `
        <div class="flex justify-between mb-2 text-xs text-gray-500">
          <span>Bestellnummer</span>
          <span>${order.id}</span>
        </div>
        <div class="flex justify-between mb-2 text-xs text-gray-500">
          <span>Status</span>
          <span class="inline-flex items-center gap-1 text-emerald-600">
            ● Wird vorbereitet (Demo)
          </span>
        </div>
        <div class="flex justify-between mb-2 text-xs text-gray-500">
          <span>Lieferzeitfenster</span>
          <span>${order.slotLabel}</span>
        </div>
        <div class="mt-4">
          <h2 class="text-sm font-semibold mb-2">Artikel</h2>
          <ul class="mb-3">
            ${itemsHtml}
          </ul>
          <dl class="text-xs text-gray-700 space-y-1">
            <div class="flex justify-between">
              <dt>Zwischensumme</dt>
              <dd>${order.subtotal.toFixed(2).replace('.', ',')} €</dd>
            </div>
            <div class="flex justify-between">
              <dt>Pfand</dt>
              <dd>${order.deposit.toFixed(2).replace('.', ',')} €</dd>
            </div>
            <div class="flex justify-between font-semibold border-t border-gray-100 pt-2 mt-2">
              <dt>Gesamt</dt>
              <dd>${order.total.toFixed(2).replace('.', ',')} €</dd>
            </div>
          </dl>
        </div>
      `;
    }

    // ---------------------- INIT ----------------------
    document.addEventListener('DOMContentLoaded', () => {
      window.currentCategory = 'all';

      // Navigation
      document.querySelectorAll('[data-nav]').forEach(btn => {
        btn.addEventListener('click', () => {
          const view = btn.getAttribute('data-nav');
          showView(view);
        });
      });

      // Kategorie-Auswahl auf Home
      document.querySelectorAll('[data-category]').forEach(btn => {
        btn.addEventListener('click', () => {
          window.currentCategory = btn.getAttribute('data-category') || 'all';
          showView('category');
        });
      });

      // Clear cart button
      const clearBtn = document.getElementById('clearCartBtn');
      if (clearBtn) {
        clearBtn.addEventListener('click', () => {
          clearCart();
          renderCartPage();
        });
      }

      // Checkout form
      const checkoutForm = document.getElementById('checkoutForm');
      if (checkoutForm) {
        checkoutForm.addEventListener('submit', e => {
          e.preventDefault();
          const totals = cartTotals();
          if (!totals.items.length) {
            alert('Warenkorb ist leer (Demo).');
            return;
          }

          const data = new FormData(checkoutForm);
          const slot = data.get('slot');
          const slotLabelMap = {
            today_18_20: 'Heute, 18–20 Uhr',
            today_20_22: 'Heute, 20–22 Uhr',
            tomorrow_10_12: 'Morgen, 10–12 Uhr',
          };

          const order = {
            id: 'FP-' + Math.floor(Math.random() * 1e6).toString().padStart(6, '0'),
            createdAt: new Date().toISOString(),
            customer: {
              name: data.get('name'),
              street: data.get('street'),
              zip: data.get('zip'),
              city: data.get('city'),
              phone: data.get('phone'),
            },
            slot: slot,
            slotLabel: slotLabelMap[slot] || 'Unbekannt',
            items: totals.items.map(it => ({
              product: { id: it.product.id, name: it.product.name },
              qty: it.qty,
              lineTotal: it.lineSubtotal + it.lineDeposit,
            })),
            subtotal: totals.subtotal,
            deposit: totals.deposit,
            total: totals.total,
          };

          localStorage.setItem(ORDER_KEY, JSON.stringify(order));
          clearCart();
          showView('order');
        });
      }

      initZip();
      updateCartBadge();
      renderHomeProducts();
      showView('home');
    });
  </script>
</body>
</html>
