// customer-context-feed.js

(function (window, document) {
    'use strict';

    if (window.__MA_CONTEXT_FEED_SWITCHER_FIXED__) {
        return;
    }

    window.__MA_CONTEXT_FEED_SWITCHER_FIXED__ = true;

    function qs(selector, root) {
        return (root || document).querySelector(selector);
    }

    function qsa(selector, root) {
        return Array.prototype.slice.call((root || document).querySelectorAll(selector));
    }

    function csrf() {
        return qs('meta[name="csrf-token"]')?.getAttribute('content') || window.csrf || '';
    }

    function getSwitcher() {
        return qs('#maNoteTypeSwitcher');
    }

    function getNoteList() {
        return qs('#note-list');
    }

    function getCtx() {
        if (typeof window.maEnsureNoteContext === 'function') {
            var ensured = window.maEnsureNoteContext();
            if (ensured) {
                return ensured;
            }
        }

        var noteList = getNoteList();

        return {
            customerId: noteList?.dataset?.customerId || '',
            alternativeId: noteList?.dataset?.alternativeId || '',
            productId: noteList?.dataset?.productId || noteList?.dataset?.genericId || '',
            genericId: noteList?.dataset?.genericId || noteList?.dataset?.productId || '',
            uniqueId: noteList?.dataset?.uniqueId || '',
            leadProductListId: noteList?.dataset?.uniqueId || '',
            noteType: noteList?.dataset?.noteType || 'general'
        };
    }

    function refreshIconsAndNotes() {
        if (window.feather && typeof window.feather.replace === 'function') {
            window.feather.replace();
        }

        if (typeof window.initNoteListeners === 'function') {
            window.initNoteListeners();
        }
    }

    function setLoading(text) {
        var noteList = getNoteList();

        if (!noteList) {
            return;
        }

        noteList.innerHTML =
            '<div class="ma-feed-empty">' +
                '<div class="d-flex align-items-center">' +
                    '<span class="ma-note-type-icon bg-blue mr-2">' +
                        '<span class="spinner-border spinner-border-sm"></span>' +
                    '</span>' +
                    '<div>' +
                        '<div class="ma-feed-title">Wird geladen</div>' +
                        '<div class="ma-feed-meta">' + (text || 'Bitte warten...') + '</div>' +
                    '</div>' +
                '</div>' +
            '</div>';
    }

    function setError(message) {
        var noteList = getNoteList();

        if (!noteList) {
            return;
        }

        noteList.innerHTML =
            '<div class="ma-feed-empty">' +
                '<div class="d-flex align-items-center">' +
                    '<span class="ma-note-type-icon bg-pink mr-2">' +
                        '<i data-feather="alert-triangle"></i>' +
                    '</span>' +
                    '<div>' +
                        '<div class="ma-feed-title">Fehler</div>' +
                        '<div class="ma-feed-meta">' + (message || 'Bereich konnte nicht geladen werden.') + '</div>' +
                    '</div>' +
                '</div>' +
            '</div>';

        refreshIconsAndNotes();
    }

    function cleanLoadedHtml(html) {
        var wrapper = document.createElement('div');
        wrapper.innerHTML = html || '';

        qsa('#maNoteTypeSwitcher, .ma-note-type-switcher, [data-note-feed-menu], [data-note-feed-current]', wrapper)
            .forEach(function (el) {
                var parentSwitcher = el.closest('.ma-note-type-switcher');
                if (parentSwitcher) {
                    parentSwitcher.remove();
                } else {
                    el.remove();
                }
            });

        qsa('#note_title', wrapper).forEach(function (el) {
            el.remove();
        });

        return wrapper.innerHTML;
    }

    function updateSwitcherTitle(item) {
        var switcher = getSwitcher();

        if (!switcher || !item) {
            return;
        }

        var current = qs('[data-note-feed-current]', switcher);

        if (!current) {
            return;
        }

        var label = item.dataset.label || 'Aktuelle Notizen';
        var subtitle = item.dataset.subtitle || 'Gefilterter Kundenbereich';
        var icon = item.dataset.icon || 'message-square';
        var color = item.dataset.color || 'blue';

        current.innerHTML =
            '<span class="ma-note-type-icon bg-' + color + '">' +
                '<i data-feather="' + icon + '"></i>' +
            '</span>' +
            '<span class="ma-note-type-text">' +
                '<strong>' + label + '</strong>' +
                '<small>' + subtitle + '</small>' +
            '</span>' +
            '<i data-feather="chevron-down" class="ma-note-type-chevron"></i>';

        refreshIconsAndNotes();
    }

    window.maLoadContextFeed = async function (type) {
        var noteList = getNoteList();

        if (!noteList) {
            return;
        }

        var ctx = getCtx();

        if (!ctx || !ctx.customerId) {
            setError('Kunde fehlt. Bitte zuerst Kunde oder Objekt öffnen.');
            return;
        }

        noteList.dataset.feedType = type;
        document.body.classList.toggle('ma-context-feed-active', type !== 'notes');

        if (type === 'notes') {
            var notesUrl =
                '/customer-notes/context/' +
                encodeURIComponent(ctx.customerId || 0) + '/' +
                encodeURIComponent(ctx.alternativeId || 0) + '/' +
                encodeURIComponent(ctx.genericId || ctx.productId || 0) + '/' +
                encodeURIComponent(ctx.uniqueId || ctx.leadProductListId || '');

            setLoading('Lade Notizen...');

            try {
                var notesRes = await fetch(notesUrl, {
                    method: 'GET',
                    headers: {
                        'Accept': 'text/html',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    credentials: 'same-origin'
                });

                if (!notesRes.ok) {
                    throw new Error('Notizen konnten nicht geladen werden.');
                }

                var notesHtml = await notesRes.text();

                noteList.innerHTML = cleanLoadedHtml(notesHtml);
                noteList.dataset.feedType = 'notes';

                refreshIconsAndNotes();
            } catch (error) {
                console.error(error);
                setError(error.message || 'Notizen konnten nicht geladen werden.');
            }

            return;
        }

        var params = new URLSearchParams({
            customer_id: ctx.customerId || '',
            alternative_id: ctx.alternativeId || '',
            product_id: ctx.genericId || ctx.productId || '',
            lead_product_list_id: ctx.uniqueId || ctx.leadProductListId || ''
        });

        setLoading('Lade Bereich...');

        try {
            var res = await fetch('/customer-context-feed/' + encodeURIComponent(type) + '?' + params.toString(), {
                method: 'GET',
                headers: {
                    'Accept': 'text/html',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin'
            });

            if (!res.ok) {
                throw new Error('Bereich konnte nicht geladen werden.');
            }

            var html = await res.text();

            noteList.innerHTML = cleanLoadedHtml(html);
            noteList.dataset.feedType = type;

            refreshIconsAndNotes();
        } catch (error) {
            console.error(error);
            setError(error.message || 'Dieser Bereich konnte nicht geladen werden.');
        }
    };

    document.addEventListener('click', function (event) {
        var switcher = getSwitcher();

        var switcherButton = switcher && event.target.closest('#maNoteTypeSwitcher [data-note-feed-current]');

        if (switcherButton) {
            event.preventDefault();
            event.stopPropagation();

            switcher.classList.toggle('is-open');
            return;
        }

        var feedItem = switcher && event.target.closest('#maNoteTypeSwitcher [data-feed-type]');

        if (feedItem) {
            event.preventDefault();
            event.stopPropagation();

            qsa('#maNoteTypeSwitcher .ma-note-type-item').forEach(function (btn) {
                btn.classList.remove('active');
            });

            feedItem.classList.add('active');
            switcher.classList.remove('is-open');

            updateSwitcherTitle(feedItem);

            window.maLoadContextFeed(feedItem.dataset.feedType || 'notes');
            return;
        }

        var collapseBtn = event.target.closest('#note-list [data-feed-collapse]');

        if (collapseBtn) {
            event.preventDefault();
            event.stopPropagation();

            var card = collapseBtn.closest('.ma-feed-card');

            if (card) {
                card.classList.toggle('is-open');
            }

            return;
        }

        if (switcher && !event.target.closest('#maNoteTypeSwitcher')) {
            switcher.classList.remove('is-open');
        }
    }, false);

    document.addEventListener('submit', async function (event) {
        var form = event.target.closest('#note-list .ma-context-form');

        if (!form) {
            return;
        }

        event.preventDefault();

        var url = form.dataset.contextPost;

        if (!url) {
            return;
        }

        var btn = form.querySelector('button[type="submit"]');
        var oldHtml = btn ? btn.innerHTML : '';

        if (btn) {
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';
        }

        try {
            var res = await fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrf(),
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: new FormData(form),
                credentials: 'same-origin'
            });

            var data = await res.json().catch(function () {
                return {};
            });

            if (!res.ok || data.success === false) {
                throw new Error(data.message || 'Speichern fehlgeschlagen.');
            }

            var activeType = getNoteList()?.dataset?.feedType || 'notes';
            await window.maLoadContextFeed(activeType);
        } catch (error) {
            console.error(error);

            if (window.Swal && typeof window.Swal.fire === 'function') {
                window.Swal.fire('Fehler', error.message || 'Konnte nicht gespeichert werden.', 'error');
            } else {
                alert(error.message || 'Konnte nicht gespeichert werden.');
            }
        } finally {
            if (btn) {
                btn.disabled = false;
                btn.innerHTML = oldHtml;
            }
        }
    });

})(window, document);
