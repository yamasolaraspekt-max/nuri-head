/* =========================================================
   FINAL OBJECT CONTEXT MENU + SCREENSHOT LEFT-CLICK DISABLE
   Put this after all old profile scripts.
========================================================= */
(function (window, document) {
    'use strict';

    if (window.__MA_OBJECT_CONTEXT_MENU_FINAL_BLADE_FIX__) {
        return;
    }
    window.__MA_OBJECT_CONTEXT_MENU_FINAL_BLADE_FIX__ = true;

    function csrf() {
        var meta = document.querySelector('meta[name="csrf-token"]');
        return meta ? meta.getAttribute('content') : '';
    }

    function safeCss(value) {
        if (window.CSS && typeof window.CSS.escape === 'function') {
            return window.CSS.escape(String(value));
        }
        return String(value).replace(/([ #;?%&,.+*~\':"!^$[\]()=>|\/@])/g, '\\$1');
    }

    function toast(type, title, text) {
        if (window.Swal && typeof window.Swal.fire === 'function') {
            return window.Swal.fire(title || '', text || '', type || 'info');
        }
        if (text || title) {
            alert((title ? title + '\n' : '') + (text || ''));
        }
    }

    function confirmDelete() {
        if (window.Swal && typeof window.Swal.fire === 'function') {
            return window.Swal.fire({
                title: 'Objekt löschen?',
                text: 'Dieses Objekt wird gelöscht. Diese Aktion kann nicht einfach rückgängig gemacht werden.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ja, löschen',
                cancelButtonText: 'Abbrechen',
                confirmButtonColor: '#e50656'
            }).then(function (result) {
                return !!result.isConfirmed;
            });
        }
        return Promise.resolve(window.confirm('Objekt wirklich löschen?'));
    }

    function closestObjectSection(target) {
        if (!target || !target.closest) return null;
        return target.closest('[data-object-section], .object-section');
    }

    function getThumb(section) {
        if (!section) return null;
        return section.querySelector('.object-thumb-link[data-alternative-id]');
    }

    function getObjectData(section) {
        var thumb = getThumb(section);
        var customerId = section?.dataset?.customerId || thumb?.dataset?.customerId || document.querySelector('.dashboard-btn[data-customer-id]')?.dataset?.customerId || '';
        var alternativeId = section?.dataset?.alternativeId || thumb?.dataset?.alternativeId || '';
        return {
            section: section,
            thumb: thumb,
            customerId: String(customerId || ''),
            alternativeId: String(alternativeId || ''),
            editUrl: section?.dataset?.editUrl || (customerId && alternativeId ? ('/new_lead_edit/' + encodeURIComponent(customerId) + '/' + encodeURIComponent(alternativeId)) : ''),
            deleteUrl: section?.dataset?.deleteUrl || (alternativeId ? ('/lead/objects/' + encodeURIComponent(alternativeId)) : '')
        };
    }

    function removeScreenshotInlineHandlers(root) {
        (root || document).querySelectorAll('.object-thumb-link').forEach(function (link) {
            link.removeAttribute('onclick');
            link.dataset.screenshotClickDisabled = '1';
        });
    }

    function ensureMenu() {
        var menu = document.getElementById('maObjectContextMenu');
        if (menu) return menu;

        menu = document.createElement('div');
        menu.id = 'maObjectContextMenu';
        menu.className = 'ma-object-context-menu';
        menu.setAttribute('role', 'menu');
        menu.setAttribute('aria-hidden', 'true');
        menu.innerHTML = '' +
            '<button type="button" class="ma-object-context-item" data-object-context-action="screenshot">' +
                '<i data-feather="image"></i><span>Screenshot öffnen</span>' +
            '</button>' +
            '<button type="button" class="ma-object-context-item" data-object-context-action="edit">' +
                '<i data-feather="edit-2"></i><span>Objekt bearbeiten</span>' +
            '</button>' +
            '<button type="button" class="ma-object-context-item" data-object-context-action="add-product">' +
                '<i data-feather="plus-circle"></i><span>Neues Produkt</span>' +
            '</button>' +
            '<div class="ma-object-context-separator"></div>' +
            '<button type="button" class="ma-object-context-item is-danger" data-object-context-action="delete">' +
                '<i data-feather="trash-2"></i><span>Objekt löschen</span>' +
            '</button>';

        document.body.appendChild(menu);
        if (window.feather && typeof window.feather.replace === 'function') {
            window.feather.replace();
        }
        return menu;
    }

    function hideMenu() {
        var menu = document.getElementById('maObjectContextMenu');
        if (!menu) return;
        menu.classList.remove('is-open');
        menu.setAttribute('aria-hidden', 'true');
        menu.style.left = '-9999px';
        menu.style.top = '-9999px';
        delete menu.dataset.customerId;
        delete menu.dataset.alternativeId;
    }

    function showMenu(event, data) {
        var menu = ensureMenu();
        menu.dataset.customerId = data.customerId || '';
        menu.dataset.alternativeId = data.alternativeId || '';

        var screenshotBtn = menu.querySelector('[data-object-context-action="screenshot"]');
        if (screenshotBtn) {
            screenshotBtn.disabled = !data.thumb;
        }

        var addBtn = menu.querySelector('[data-object-context-action="add-product"]');
        if (addBtn) {
            addBtn.disabled = !(data.customerId && data.alternativeId);
        }

        var editBtn = menu.querySelector('[data-object-context-action="edit"]');
        if (editBtn) {
            editBtn.disabled = !(data.customerId && data.alternativeId);
        }

        var deleteBtn = menu.querySelector('[data-object-context-action="delete"]');
        if (deleteBtn) {
            deleteBtn.disabled = !data.alternativeId;
        }

        menu.classList.add('is-open');
        menu.setAttribute('aria-hidden', 'false');

        var x = event.clientX;
        var y = event.clientY;
        var rect = menu.getBoundingClientRect();
        var margin = 12;

        if (x + rect.width + margin > window.innerWidth) {
            x = window.innerWidth - rect.width - margin;
        }
        if (y + rect.height + margin > window.innerHeight) {
            y = window.innerHeight - rect.height - margin;
        }

        menu.style.left = Math.max(margin, x) + 'px';
        menu.style.top = Math.max(margin, y) + 'px';
    }

    function openScreenshot(customerId, alternativeId) {
        var thumb = null;
        try {
            thumb = document.querySelector('.object-thumb-link[data-alternative-id="' + safeCss(alternativeId) + '"]');
        } catch (error) {
            thumb = document.querySelector('.object-thumb-link[data-alternative-id="' + String(alternativeId).replace(/"/g, '\\"') + '"]');
        }

        if (!thumb) {
            return toast('warning', 'Nicht gefunden', 'Für dieses Objekt wurde kein Screenshot-Link gefunden.');
        }

        if (typeof window.openSidebarGallery === 'function') {
            window.openSidebarGallery(thumb);
        } else if (typeof window.openObjectMapSidebar === 'function') {
            window.openObjectMapSidebar(thumb);
        } else {
            toast('error', 'Fehler', 'Screenshot-Funktion wurde nicht gefunden.');
        }
    }

    function openEdit(customerId, alternativeId) {
        if (!customerId || !alternativeId) {
            return toast('warning', 'Fehlt', 'Kunde oder Objekt-ID fehlt.');
        }
        window.location.href = '/new_lead_edit/' + encodeURIComponent(customerId) + '/' + encodeURIComponent(alternativeId);
    }

    function openAddProduct(customerId, alternativeId) {
        if (!customerId || !alternativeId) {
            return toast('warning', 'Fehlt', 'Kunde oder Objekt-ID fehlt.');
        }

        var existing = document.querySelector('.addNewProduct[data-id="' + safeCss(customerId) + '"][data-alternative-id="' + safeCss(alternativeId) + '"]');
        if (existing) {
            existing.dispatchEvent(new MouseEvent('click', { bubbles: true, cancelable: true, view: window }));
            return;
        }

        var btn = document.createElement('button');
        btn.className = 'kebab-item addNewProduct';
        btn.type = 'button';
        btn.dataset.id = customerId;
        btn.dataset.alternativeId = alternativeId;
        btn.style.position = 'fixed';
        btn.style.left = '-9999px';
        btn.style.top = '-9999px';
        btn.innerHTML = '<i class="feather icon-plus-circle text-success"></i> Neues Produkt';
        document.body.appendChild(btn);
        btn.dispatchEvent(new MouseEvent('click', { bubbles: true, cancelable: true, view: window }));
        setTimeout(function () {
            btn.remove();
        }, 500);
    }

    async function deleteObject(customerId, alternativeId) {
        if (!alternativeId) {
            return toast('warning', 'Fehlt', 'Objekt-ID fehlt.');
        }

        var ok = await confirmDelete();
        if (!ok) return;

        var section = null;
        try {
            section = document.querySelector('[data-object-section][data-alternative-id="' + safeCss(alternativeId) + '"]');
        } catch (error) {
            section = null;
        }
        if (!section) {
            var thumb = document.querySelector('.object-thumb-link[data-alternative-id="' + safeCss(alternativeId) + '"]');
            section = closestObjectSection(thumb);
        }

        try {
            var res = await fetch('/lead/objects/' + encodeURIComponent(alternativeId), {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': csrf(),
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin'
            });

            var data = await res.json().catch(function () { return {}; });
            if (!res.ok || data.success === false) {
                throw new Error(data.message || 'Objekt konnte nicht gelöscht werden.');
            }

            var sidebar = document.getElementById('sidebarGallery' + alternativeId);
            if (sidebar) sidebar.remove();

            if (section) {
                section.style.transition = 'opacity .22s ease, transform .22s ease, max-height .28s ease';
                section.style.opacity = '0';
                section.style.transform = 'translateX(-14px)';
                section.style.maxHeight = section.scrollHeight + 'px';
                setTimeout(function () {
                    section.style.maxHeight = '0px';
                }, 20);
                setTimeout(function () {
                    section.remove();
                }, 320);
            }

            if (window.Swal && typeof window.Swal.fire === 'function') {
                window.Swal.fire({ icon: 'success', title: 'Gelöscht', timer: 1200, showConfirmButton: false });
            }
        } catch (error) {
            console.error(error);
            toast('error', 'Fehler', error.message || 'Objekt konnte nicht gelöscht werden.');
        }
    }

    /* Disable only left-click on screenshot thumbnail. Context menu can still open it. */
    window.addEventListener('click', function (event) {
        var thumb = event.target && event.target.closest ? event.target.closest('.object-thumb-link[data-alternative-id]') : null;
        if (!thumb) return;
        if (event.__maAllowScreenshotSidebar === true) return;

        event.preventDefault();
        event.stopPropagation();
        if (typeof event.stopImmediatePropagation === 'function') {
            event.stopImmediatePropagation();
        }
        return false;
    }, true);

    document.addEventListener('contextmenu', function (event) {
        var section = closestObjectSection(event.target);
        if (!section) return;
        if (!section.closest('.customerSidebar')) return;

        event.preventDefault();
        event.stopPropagation();
        var data = getObjectData(section);
        if (!data.alternativeId) return;
        showMenu(event, data);
    }, true);

    document.addEventListener('click', function (event) {
        var menu = document.getElementById('maObjectContextMenu');
        var item = event.target.closest ? event.target.closest('#maObjectContextMenu [data-object-context-action]') : null;

        if (!item) {
            if (menu && !event.target.closest('#maObjectContextMenu')) {
                hideMenu();
            }
            return;
        }

        event.preventDefault();
        event.stopPropagation();
        if (typeof event.stopImmediatePropagation === 'function') {
            event.stopImmediatePropagation();
        }

        var customerId = menu?.dataset?.customerId || '';
        var alternativeId = menu?.dataset?.alternativeId || '';
        var action = item.dataset.objectContextAction;
        hideMenu();

        if (action === 'screenshot') return openScreenshot(customerId, alternativeId);
        if (action === 'edit') return openEdit(customerId, alternativeId);
        if (action === 'add-product') return openAddProduct(customerId, alternativeId);
        if (action === 'delete') return deleteObject(customerId, alternativeId);
    }, true);

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') hideMenu();
    });

    document.addEventListener('DOMContentLoaded', function () {
        removeScreenshotInlineHandlers(document);
        ensureMenu();
    });

    removeScreenshotInlineHandlers(document);
})(window, document);
