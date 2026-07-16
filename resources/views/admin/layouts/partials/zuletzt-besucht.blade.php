{{-- Zuletzt besucht — aufklappbarer Bereich (wie ein Verlauf), clientseitig (localStorage).
     Eingeklappt = kein Platzverbrauch; ausklappen -> zuletzt besuchte Seiten. Additiv, kein Backend.
     Nutzt die vorhandene Bereichs-Optik (.sa-sidebar-section), aber mit eigenem, robustem Umschalter.
     Stand 2026-07-15. --}}
<div id="sa-recent-section" class="sa-sidebar-section is-collapsed" data-nav-recent-section style="display:none;">
    <button type="button" class="sa-section-toggle" id="sa-recent-toggle" aria-expanded="false">
        <span class="sa-section-title-wrap">
            <i data-lucide="history" class="sa-section-custom-icon"></i>
            <span class="sa-section-title">Zuletzt besucht</span>
        </span>
        <span class="sa-section-right">
            <span class="sa-section-count" id="sa-recent-count">0</span>
            <i data-lucide="chevron-down" class="icon-sm sa-section-chevron"></i>
        </span>
    </button>
    <div class="sa-section-body">
        <div class="sa-section-body-inner">
            <div id="sa-recent-list"></div>
            <button type="button" id="sa-recent-clear" class="nav-item" title="Verlauf leeren">
                <div class="nav-item-content">
                    <i data-lucide="trash-2" class="icon-md"></i>
                    <span>Verlauf leeren</span>
                </div>
            </button>
        </div>
    </div>
</div>

<script>
(function () {
    'use strict';
    var KEY = 'sa_nav_recent';
    var MAX = 12, SHOW = 8;

    function read() {
        try { var a = JSON.parse(localStorage.getItem(KEY) || '[]'); return Array.isArray(a) ? a : []; }
        catch (e) { return []; }
    }
    function write(a) { try { localStorage.setItem(KEY, JSON.stringify(a.slice(0, MAX))); } catch (e) {} }

    function record(label, url) {
        if (!label || !url) return;
        label = String(label).trim().replace(/\s+/g, ' ');
        if (!label) return;
        var a = read().filter(function (x) { return x.url !== url; });
        a.unshift({ label: label, url: url });
        write(a);
    }
    window.navRecent = { read: read, record: record, clear: function () { try { localStorage.removeItem(KEY); } catch (e) {} } };

    function currentPath() { return location.pathname + location.search; }
    function esc(s) {
        return String(s).replace(/[&<>"']/g, function (c) {
            return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
        });
    }
    function toPath(href) {
        try { var u = new URL(href, location.origin); return u.pathname + u.search; } catch (e) { return href; }
    }

    function render() {
        var section = document.getElementById('sa-recent-section');
        var list = document.getElementById('sa-recent-list');
        var count = document.getElementById('sa-recent-count');
        if (!section || !list) return;
        var here = currentPath();
        var items = read().filter(function (x) { return x.url !== here; }).slice(0, SHOW);
        if (count) count.textContent = String(items.length);
        if (!items.length) { section.style.display = 'none'; list.innerHTML = ''; return; }
        list.innerHTML = items.map(function (x) {
            return '<a class="nav-item" href="' + esc(x.url) + '">' +
                '<div class="nav-item-content">' +
                '<i data-lucide="clock" class="icon-md"></i>' +
                '<span>' + esc(x.label) + '</span>' +
                '</div></a>';
        }).join('');
        section.style.display = '';
        if (window.lucide && typeof window.lucide.createIcons === 'function') { try { window.lucide.createIcons(); } catch (e) {} }
    }

    // Eigener, robuster Aufklapp-Umschalter (nutzt die vorhandene is-open/is-collapsed-Optik)
    function bindToggle() {
        var btn = document.getElementById('sa-recent-toggle');
        var section = document.getElementById('sa-recent-section');
        if (!btn || !section) return;
        btn.addEventListener('click', function () {
            var open = section.classList.toggle('is-open');
            section.classList.toggle('is-collapsed', !open);
            btn.setAttribute('aria-expanded', open ? 'true' : 'false');
        });
    }

    function bindClear() {
        var clr = document.getElementById('sa-recent-clear');
        if (!clr) return;
        clr.addEventListener('click', function (e) {
            e.preventDefault(); e.stopPropagation();
            window.navRecent.clear();
            render();
        });
    }

    function recordCurrent() {
        var active = document.querySelector('.sidebar-nav .nav-item.active, .sidebar-nav a.nav-item[aria-current="page"]');
        var label = null;
        if (active) { var sp = active.querySelector('.nav-item-content span, span'); label = sp ? sp.textContent : active.textContent; }
        if (!label && document.title) label = document.title.split(/[|–\-]/)[0];
        record(label, currentPath());
    }

    function bindClicks() {
        var nav = document.querySelector('.sidebar-nav');
        if (!nav || !nav.addEventListener) return;
        nav.addEventListener('click', function (ev) {
            var a = ev.target && ev.target.closest ? ev.target.closest('a.nav-item[href]') : null;
            if (!a) return;
            if (a.closest('#sa-recent-section')) return; // Klicks in der Verlaufsliste nicht doppelt zählen
            var href = a.getAttribute('href');
            if (!href || href === '#' || href.charAt(0) === '#') return;
            var sp = a.querySelector('.nav-item-content span, span');
            record(sp ? sp.textContent : a.textContent, toPath(href));
        }, true);
    }

    function init() { recordCurrent(); bindToggle(); bindClear(); bindClicks(); render(); }
    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', init);
    else init();
})();
</script>
