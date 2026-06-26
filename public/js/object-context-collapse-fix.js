/* =========================================================
   Object + Context Feed Collapse Scroll Fix
   Put this AFTER all old profile scripts and customer-context-feed.js
========================================================= */
(function (window, document) {
    'use strict';

    if (window.__MA_OBJECT_CONTEXT_COLLAPSE_FIX__) {
        return;
    }
    window.__MA_OBJECT_CONTEXT_COLLAPSE_FIX__ = true;

    function cssEscape(value) {
        if (window.CSS && typeof window.CSS.escape === 'function') {
            return window.CSS.escape(String(value));
        }
        return String(value).replace(/([ #;?%&,.+*~\':"!^$[\]()=>|/@])/g, '\\$1');
    }

    function refreshIcons() {
        if (window.feather && typeof window.feather.replace === 'function') {
            window.feather.replace();
        }
    }

    function isVisible(el) {
        if (!el) return false;
        return el.classList.contains('show') || el.classList.contains('is-open') || window.getComputedStyle(el).display !== 'none';
    }

    function findObjectList(target) {
        if (!target) return null;

        if (typeof target === 'string') {
            return document.getElementById(target) || document.querySelector('#' + cssEscape(target));
        }

        if (target.classList && (target.classList.contains('product-list') || target.classList.contains('ma-product-list'))) {
            return target;
        }

        var header = target.closest ? target.closest('.object-header') : null;
        if (header) {
            var next = header.nextElementSibling;
            while (next) {
                if (next.classList && (next.classList.contains('product-list') || next.classList.contains('ma-product-list'))) {
                    return next;
                }
                next = next.nextElementSibling;
            }
        }

        return null;
    }

    function findObjectHeader(list) {
        if (!list) return null;
        var prev = list.previousElementSibling;
        while (prev) {
            if (prev.classList && prev.classList.contains('object-header')) {
                return prev;
            }
            prev = prev.previousElementSibling;
        }
        return list.closest('.object-section')?.querySelector('.object-header') || null;
    }

    function setObjectState(list, open) {
        if (!list) return;

        var header = findObjectHeader(list);
        var wrapper = list.closest('.object-section');

        list.classList.toggle('show', !!open);
        list.classList.toggle('is-open', !!open);
        list.style.display = open ? 'block' : 'none';
        list.setAttribute('aria-hidden', open ? 'false' : 'true');

        if (header) {
            header.classList.toggle('is-open', !!open);
            header.setAttribute('aria-expanded', open ? 'true' : 'false');
        }

        if (wrapper) {
            wrapper.classList.toggle('is-open', !!open);
        }
    }

    function closeObjectList(list) {
        setObjectState(list, false);
    }

    function closeSubNavs(scope) {
        (scope || document).querySelectorAll('.sub-nav, .ma-sub-nav').forEach(function (sub) {
            sub.classList.remove('show', 'is-open');
            sub.style.display = 'none';
            sub.setAttribute('aria-hidden', 'true');
        });

        (scope || document).querySelectorAll('.project-link.active, .project-card.active, .ma-product-card.active').forEach(function (card) {
            card.classList.remove('active', 'is-open');
        });
    }

    window.toggleObject = function (target) {
        var list = findObjectList(target);
        if (!list) return false;

        var sidebar = list.closest('.customerSidebar') || document;
        var shouldOpen = !isVisible(list);

        sidebar.querySelectorAll('.product-list, .ma-product-list').forEach(function (other) {
            if (other !== list) {
                closeObjectList(other);
            }
        });

        closeSubNavs(sidebar);
        setObjectState(list, shouldOpen);

        if (shouldOpen) {
            setTimeout(function () {
                list.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            }, 80);
        }

        refreshIcons();
        return shouldOpen;
    };

    window.toggleProduct = function (target) {
        var subNav = null;
        var card = null;

        if (typeof target === 'string') {
            subNav = document.getElementById(target) || document.querySelector('#' + cssEscape(target));
            card = document.querySelector('[data-product-key="' + cssEscape(target) + '"]');
        } else if (target && target.closest) {
            card = target.closest('.project-link, .project-card, .ma-product-card');
            var key = card ? card.getAttribute('data-product-key') : null;
            subNav = key ? document.getElementById(key) : null;
        }

        if (!subNav) return false;

        var parentList = subNav.closest('.product-list, .ma-product-list');
        if (parentList && !isVisible(parentList)) {
            window.toggleObject(parentList);
        }

        var shouldOpen = !isVisible(subNav);
        var scope = parentList || document;

        scope.querySelectorAll('.sub-nav, .ma-sub-nav').forEach(function (other) {
            if (other !== subNav) {
                other.classList.remove('show', 'is-open');
                other.style.display = 'none';
                other.setAttribute('aria-hidden', 'true');
            }
        });

        scope.querySelectorAll('.project-link.active, .project-card.active, .ma-product-card.active').forEach(function (otherCard) {
            if (otherCard !== card) {
                otherCard.classList.remove('active', 'is-open');
            }
        });

        subNav.classList.toggle('show', shouldOpen);
        subNav.classList.toggle('is-open', shouldOpen);
        subNav.style.display = shouldOpen ? 'block' : 'none';
        subNav.setAttribute('aria-hidden', shouldOpen ? 'false' : 'true');

        if (card) {
            card.classList.toggle('active', shouldOpen);
            card.classList.toggle('is-open', shouldOpen);
        }

        if (shouldOpen) {
            setTimeout(function () {
                subNav.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            }, 80);
        }

        refreshIcons();
        return shouldOpen;
    };

    /* Prevent old inline onclick="toggleObject('object0')" from double-firing. */
    document.addEventListener('click', function (event) {
        var header = event.target.closest('.customerSidebar .object-header');
        if (!header) return;

        if (event.target.closest('a, button, input, select, textarea, label, .sidebar-gallery, .object-thumb-link, [data-no-object-toggle]')) {
            return;
        }

        event.preventDefault();
        event.stopPropagation();
        if (typeof event.stopImmediatePropagation === 'function') {
            event.stopImmediatePropagation();
        }

        window.toggleObject(header);
    }, true);

    document.addEventListener('keydown', function (event) {
        if (event.key !== 'Enter' && event.key !== ' ') return;

        var header = event.target.closest('.customerSidebar .object-header');
        if (!header) return;

        event.preventDefault();
        window.toggleObject(header);
    }, true);

    /* Context feed collapse: scroll the opened body into readable position. */
    document.addEventListener('click', function (event) {
        var head = event.target.closest('#note-list .ma-feed-head, #note-list [data-feed-collapse]');
        if (!head) return;

        var card = head.closest('.ma-feed-card');
        if (!card) return;

        setTimeout(function () {
            if (!card.classList.contains('is-open')) return;
            var body = card.querySelector('.ma-feed-body');
            (body || card).scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }, 120);
    }, false);

    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.customerSidebar .product-list, .customerSidebar .ma-product-list').forEach(function (list) {
            var open = isVisible(list) && list.style.display !== 'none';
            setObjectState(list, open);
        });
        refreshIcons();
    });
})(window, document);
