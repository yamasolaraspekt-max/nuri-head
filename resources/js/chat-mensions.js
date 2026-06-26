/*
 * SA-DESK Chat Mentions Hotfix
 * Put in: resources/js/chat-mentions.js
 * Load/import after Echo/bootstrap is available.
 * Works with existing chat.js by:
 * - showing employee list when user types @ in #messageInput
 * - injecting selected mention IDs into every /chat/send request
 * - loading persistent mention toasts from /chat/mentions/unread
 */
(function () {
    'use strict';

    if (window.__SA_CHAT_MENTIONS_HOTFIX__) return;
    window.__SA_CHAT_MENTIONS_HOTFIX__ = true;

    const STATE = {
        employees: [],
        employeesLoaded: false,
        employeesLoading: false,
        selected: new Map(), // userId => display name
        picker: null,
        activeIndex: -1,
        input: null,
        fetchPatched: false,
        echoBooted: false,
        toastIds: new Set(),
    };

    function csrfToken() {
        return window.csrfToken || document.querySelector('meta[name="csrf-token"]')?.content || '';
    }

    function currentUserId() {
        return Number(
            window.userId ||
            document.querySelector('meta[name="chat-user-id"]')?.content ||
            document.querySelector('meta[name="user-id"]')?.content ||
            0
        );
    }

    function esc(value) {
        return String(value ?? '').replace(/[&<>"']/g, function (char) {
            return {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;',
            }[char] || char;
        });
    }

    function normalizeEmployeeResponse(data) {
        const rows = Array.isArray(data)
            ? data
            : (data?.employees || data?.users || data?.data || []);

        return rows
            .filter((row) => row && row.id)
            .map((row) => {
                const name = String(row.name || '').trim();
                const lastname = String(row.lastname || '').trim();
                const fullName = `${name} ${lastname}`.trim() || row.display_name || row.text || `User ${row.id}`;

                return {
                    id: Number(row.id),
                    name,
                    lastname,
                    fullName,
                    email: row.email || '',
                    employeeId: row.employee_id || row.emp_id || row.name_field || '',
                    avatar: row.avatar || row.image || '/images/gender/users.png',
                };
            })
            .filter((row) => row.id && row.id !== currentUserId());
    }

    async function loadEmployees(force = false) {
        if (STATE.employeesLoaded && !force) return STATE.employees;
        if (STATE.employeesLoading) return STATE.employees;

        STATE.employeesLoading = true;

        try {
            const response = await fetch('/chat/employee', {
                method: 'GET',
                headers: {
                    Accept: 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                credentials: 'same-origin',
            });

            if (!response.ok) {
                throw new Error(`HTTP ${response.status}`);
            }

            const data = await response.json();
            STATE.employees = normalizeEmployeeResponse(data);
            STATE.employeesLoaded = true;
            console.log('[chat-mentions] employees loaded:', STATE.employees.length);
        } catch (error) {
            console.error('[chat-mentions] /chat/employee failed:', error);
            STATE.employees = [];
        } finally {
            STATE.employeesLoading = false;
        }

        return STATE.employees;
    }

    function injectStyles() {
        if (document.getElementById('saChatMentionHotfixStyles')) return;

        const style = document.createElement('style');
        style.id = 'saChatMentionHotfixStyles';
        style.textContent = `
            .sa-chat-mention-picker {
                position: fixed;
                z-index: 2147483000;
                width: min(360px, calc(100vw - 24px));
                max-height: 320px;
                overflow-y: auto;
                padding: 8px;
                border-radius: 18px;
                background: #ffffff;
                border: 1px solid rgba(148, 163, 184, .45);
                box-shadow: 0 24px 70px rgba(15, 23, 42, .24);
            }
            .sa-chat-mention-picker.hidden { display: none !important; }
            .sa-chat-mention-head {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 8px;
                padding: 7px 9px 9px;
                color: #64748b;
                font-size: 11px;
                font-weight: 900;
                text-transform: uppercase;
                letter-spacing: .04em;
            }
            .sa-chat-mention-option {
                width: 100%;
                min-height: 48px;
                display: flex;
                align-items: center;
                gap: 10px;
                padding: 8px 10px;
                border: 0;
                border-radius: 14px;
                background: transparent;
                color: #0f172a;
                cursor: pointer;
                text-align: left;
            }
            .sa-chat-mention-option:hover,
            .sa-chat-mention-option.is-active { background: rgba(116, 178, 212, .13); }
            .sa-chat-mention-option img {
                width: 34px;
                height: 34px;
                border-radius: 999px;
                object-fit: cover;
                border: 1px solid rgba(148, 163, 184, .45);
                flex-shrink: 0;
                background: #f1f5f9;
            }
            .sa-chat-mention-name {
                display: block;
                color: #0f172a;
                font-size: 13px;
                font-weight: 900;
                line-height: 1.15;
            }
            .sa-chat-mention-meta {
                display: block;
                margin-top: 3px;
                color: #64748b;
                font-size: 11px;
                font-weight: 800;
            }
            .sa-chat-mention-empty {
                padding: 12px;
                color: #64748b;
                font-size: 12px;
                font-weight: 800;
            }
            .sa-mention-toast-wrap {
                position: fixed;
                top: 88px;
                right: 24px;
                z-index: 2147483000;
                display: flex;
                flex-direction: column;
                align-items: flex-end;
                gap: 12px;
                pointer-events: none;
            }
            .sa-mention-toast {
                width: min(390px, calc(100vw - 24px));
                pointer-events: auto;
                display: flex;
                gap: 12px;
                align-items: flex-start;
                padding: 14px;
                border-radius: 18px;
                background: linear-gradient(135deg, rgba(255,255,255,.98), rgba(248,250,252,.96));
                border: 1px solid rgba(116, 178, 212, .35);
                box-shadow: 0 22px 55px rgba(15, 23, 42, .18);
                cursor: pointer;
                position: relative;
                overflow: hidden;
                animation: saMentionToastIn .25s ease-out;
            }
            .sa-mention-toast::before {
                content: '';
                position: absolute;
                inset: 0 auto 0 0;
                width: 5px;
                background: linear-gradient(180deg, #74b2d4, #93c21c);
            }
            .sa-mention-avatar {
                width: 46px;
                height: 46px;
                border-radius: 999px;
                object-fit: cover;
                border: 2px solid rgba(147, 194, 28, .35);
                flex-shrink: 0;
            }
            .sa-mention-body { min-width: 0; flex: 1; }
            .sa-mention-kicker {
                font-size: 11px;
                font-weight: 950;
                color: #93c21c;
                text-transform: uppercase;
                letter-spacing: .04em;
                margin-bottom: 3px;
            }
            .sa-mention-title {
                margin: 0;
                color: #1f2937;
                font-size: 14px;
                font-weight: 950;
            }
            .sa-mention-msg {
                margin: 5px 0 0;
                color: #64748b;
                font-size: 12px;
                line-height: 1.45;
            }
            .sa-mention-action {
                margin-top: 9px;
                display: inline-flex;
                align-items: center;
                gap: 6px;
                font-size: 11px;
                font-weight: 900;
                color: #569ad8;
            }
            .sa-mention-close {
                width: 26px;
                height: 26px;
                border-radius: 999px;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                border: 0;
                background: transparent;
                color: #94a3b8;
                flex-shrink: 0;
                cursor: pointer;
            }
            .sa-mention-close:hover { background: #f1f5f9; color: #ef4444; }
            @keyframes saMentionToastIn {
                from { opacity: 0; transform: translateY(-12px) scale(.98); }
                to { opacity: 1; transform: translateY(0) scale(1); }
            }
            @media (max-width: 767px) {
                .sa-chat-mention-picker { left: 12px !important; right: 12px !important; width: auto; }
                .sa-mention-toast-wrap { top: 76px; left: 12px; right: 12px; align-items: stretch; }
            }
        `;

        document.head.appendChild(style);
    }

    function ensurePicker() {
        injectStyles();

        if (!STATE.picker) {
            STATE.picker = document.createElement('div');
            STATE.picker.id = 'saChatMentionPicker';
            STATE.picker.className = 'sa-chat-mention-picker hidden';
            document.body.appendChild(STATE.picker);
        }

        return STATE.picker;
    }

    function hidePicker() {
        if (STATE.picker) STATE.picker.classList.add('hidden');
        STATE.activeIndex = -1;
    }

    function getMentionTerm(input) {
        const caret = Number(input.selectionStart || 0);
        const before = String(input.value || '').slice(0, caret);
        const match = before.match(/@([^@\s]*)$/u);
        return match ? String(match[1] || '').toLowerCase() : null;
    }

    function positionPicker(input) {
        const box = ensurePicker();
        const rect = input.getBoundingClientRect();
        const top = Math.max(12, rect.top - Math.min(320, box.offsetHeight || 280) - 8);

        box.style.left = `${rect.left}px`;
        box.style.top = `${top}px`;

        if (window.innerWidth < 768) {
            box.style.left = '12px';
            box.style.right = '12px';
            box.style.top = `${Math.max(12, rect.top - 300)}px`;
        }
    }

    function updateActiveOption() {
        const options = Array.from(STATE.picker?.querySelectorAll('.sa-chat-mention-option') || []);
        options.forEach((option, index) => {
            option.classList.toggle('is-active', index === STATE.activeIndex);
            if (index === STATE.activeIndex) {
                option.scrollIntoView({ block: 'nearest' });
            }
        });
    }

    function renderPicker(input, employees, term) {
        const box = ensurePicker();

        const filtered = employees
            .filter((emp) => {
                if (term === '') return true;

                const haystack = [
                    emp.fullName,
                    emp.email,
                    emp.employeeId,
                    String(emp.id),
                ].join(' ').toLowerCase();

                return haystack.includes(term);
            })
            .slice(0, 30);

        if (!filtered.length) {
            box.innerHTML = `
                <div class="sa-chat-mention-head">@ Mitarbeiter markieren</div>
                <div class="sa-chat-mention-empty">Keine Mitarbeiter gefunden. Prüfe die Route <code>/chat/employee</code>.</div>
            `;
            positionPicker(input);
            box.classList.remove('hidden');
            return;
        }

        box.innerHTML = `
            <div class="sa-chat-mention-head">
                <span>@ Mitarbeiter markieren</span>
                <span>${filtered.length}</span>
            </div>
            ${filtered.map((emp) => `
                <button type="button"
                        class="sa-chat-mention-option"
                        data-user-id="${esc(emp.id)}"
                        data-user-name="${esc(emp.fullName)}">
                    <img src="${esc(emp.avatar)}" alt="">
                    <span>
                        <span class="sa-chat-mention-name">@${esc(emp.fullName)}</span>
                        <span class="sa-chat-mention-meta">Klicken zum Markieren</span>
                    </span>
                </button>
            `).join('')}
        `;

        STATE.activeIndex = 0;
        positionPicker(input);
        box.classList.remove('hidden');
        updateActiveOption();

        box.querySelectorAll('.sa-chat-mention-option').forEach((button) => {
            button.addEventListener('mousedown', (event) => {
                event.preventDefault();
            });

            button.addEventListener('click', () => {
                insertMention(input, button.dataset.userId, button.dataset.userName);
            });
        });
    }

    async function refreshPicker(input) {
        if (!input || input.disabled || input.readOnly) return;

        const term = getMentionTerm(input);
        if (term === null) {
            hidePicker();
            return;
        }

        const box = ensurePicker();
        box.innerHTML = `
            <div class="sa-chat-mention-head">@ Mitarbeiter markieren</div>
            <div class="sa-chat-mention-empty">Mitarbeiter werden geladen…</div>
        `;
        positionPicker(input);
        box.classList.remove('hidden');

        const employees = await loadEmployees();
        renderPicker(input, employees, term);
    }

    function insertMention(input, userId, userName) {
        if (!input || !userId || !userName) return;

        const caret = Number(input.selectionStart || 0);
        const before = String(input.value || '').slice(0, caret);
        const after = String(input.value || '').slice(caret);
        const mentionText = `@${userName} `;
        const newBefore = before.replace(/@([^@\s]*)$/u, mentionText);

        input.value = newBefore + after;
        input.focus();
        input.setSelectionRange(newBefore.length, newBefore.length);

        STATE.selected.set(Number(userId), String(userName));
        hidePicker();

        input.dispatchEvent(new Event('input', { bubbles: true }));
        console.log('[chat-mentions] selected mention:', userId, userName);
    }

    function activeMentionIds() {
        const text = String(STATE.input?.value || '');

        return Array.from(STATE.selected.entries())
            .filter(([, name]) => text.includes(`@${name}`))
            .map(([id]) => Number(id))
            .filter((id) => Number.isInteger(id) && id > 0);
    }

    function clearSelectedMentions() {
        STATE.selected.clear();
        hidePicker();
    }

    function patchFetchForMentions() {
        if (STATE.fetchPatched || !window.fetch) return;
        STATE.fetchPatched = true;

        const originalFetch = window.fetch.bind(window);

        window.fetch = function patchedFetch(input, init = {}) {
            let nextInit = init;
            let isChatSend = false;

            try {
                const url = typeof input === 'string' ? input : (input?.url || '');
                const method = String(init?.method || input?.method || 'GET').toUpperCase();
                isChatSend = method === 'POST' && String(url).includes('/chat/send');

                const ids = activeMentionIds();

                if (isChatSend && ids.length) {
                    nextInit = { ...init };

                    if (nextInit.body instanceof FormData) {
                        ids.forEach((id) => nextInit.body.append('mentions[]', String(id)));
                    } else if (nextInit.body instanceof URLSearchParams) {
                        ids.forEach((id) => nextInit.body.append('mentions[]', String(id)));
                    } else if (typeof nextInit.body === 'string') {
                        const payload = JSON.parse(nextInit.body || '{}');
                        payload.mentions = ids;
                        nextInit.body = JSON.stringify(payload);
                        nextInit.headers = {
                            ...(nextInit.headers || {}),
                            'Content-Type': 'application/json',
                        };
                    }

                    console.log('[chat-mentions] attached mentions to /chat/send:', ids);
                }
            } catch (error) {
                console.warn('[chat-mentions] could not attach mentions:', error);
            }

            const promise = originalFetch(input, nextInit);

            if (isChatSend) {
                promise.then((response) => {
                    if (response.ok) clearSelectedMentions();
                }).catch(() => {});
            }

            return promise;
        };
    }

    function bootPicker() {
        STATE.input = document.getElementById('messageInput');

        if (!STATE.input) {
            console.warn('[chat-mentions] #messageInput not found. Mention picker not started.');
            return;
        }

        ensurePicker();
        patchFetchForMentions();

        const triggerRefresh = () => refreshPicker(STATE.input);

        STATE.input.addEventListener('input', triggerRefresh);
        STATE.input.addEventListener('keyup', triggerRefresh);
        STATE.input.addEventListener('click', triggerRefresh);
        STATE.input.addEventListener('focus', triggerRefresh);

        STATE.input.addEventListener('keydown', (event) => {
            const isGermanAt = event.code === 'KeyQ' && (event.altKey || event.ctrlKey || event.metaKey);

            if (event.key === '@' || isGermanAt) {
                setTimeout(triggerRefresh, 20);
                return;
            }

            const box = ensurePicker();
            const isOpen = !box.classList.contains('hidden');
            if (!isOpen) return;

            const options = Array.from(box.querySelectorAll('.sa-chat-mention-option'));

            if (event.key === 'Escape') {
                event.preventDefault();
                hidePicker();
                return;
            }

            if (!options.length) return;

            if (event.key === 'ArrowDown') {
                event.preventDefault();
                STATE.activeIndex = (STATE.activeIndex + 1) % options.length;
                updateActiveOption();
            } else if (event.key === 'ArrowUp') {
                event.preventDefault();
                STATE.activeIndex = (STATE.activeIndex - 1 + options.length) % options.length;
                updateActiveOption();
            } else if (event.key === 'Enter' && STATE.activeIndex >= 0) {
                event.preventDefault();
                const option = options[STATE.activeIndex];
                insertMention(STATE.input, option.dataset.userId, option.dataset.userName);
            }
        });

        document.addEventListener('mousedown', (event) => {
            if (!STATE.picker || STATE.picker.classList.contains('hidden')) return;
            if (STATE.picker.contains(event.target) || STATE.input.contains(event.target)) return;
            hidePicker();
        });

        console.log('[chat-mentions] picker booted. Type @ in #messageInput.');
    }

    function toastWrap() {
        injectStyles();
        let wrap = document.getElementById('saMentionToastWrap');
        if (!wrap) {
            wrap = document.createElement('div');
            wrap.id = 'saMentionToastWrap';
            wrap.className = 'sa-mention-toast-wrap';
            document.body.appendChild(wrap);
        }
        return wrap;
    }

    async function markMentionRead(id) {
        if (!id) return;

        await fetch(`/chat/mentions/${id}/read`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken(),
                Accept: 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
            credentials: 'same-origin',
        });
    }

    function showMentionToast(payload) {
        const mention = payload?.mention || payload;
        if (!mention || !mention.id) return;

        const key = String(mention.id);
        if (STATE.toastIds.has(key)) return;
        STATE.toastIds.add(key);

        const toast = document.createElement('div');
        toast.className = 'sa-mention-toast';
        toast.dataset.mentionId = key;

        const sender = mention.sender_name || mention.from_name || 'Mitarbeiter';
        const avatar = mention.sender_avatar || mention.avatar || '/images/gender/users.png';
        const groupName = mention.group_name || mention.chat_name || 'Chat';
        const message = mention.message || 'Du wurdest in einer Nachricht markiert.';

        toast.innerHTML = `
            <img class="sa-mention-avatar" src="${esc(avatar)}" alt="">
            <div class="sa-mention-body">
                <div class="sa-mention-kicker">@Erwähnung</div>
                <p class="sa-mention-title">${esc(sender)} hat dich markiert</p>
                <p class="sa-mention-msg"><strong>${esc(groupName)}</strong><br>${esc(message)}</p>
                <div class="sa-mention-action">Öffnen und als gelesen markieren <span>→</span></div>
            </div>
            <button type="button" class="sa-mention-close" title="Schließen">×</button>
        `;

        toast.querySelector('.sa-mention-close')?.addEventListener('click', (event) => {
            event.preventDefault();
            event.stopPropagation();
            toast.remove();
            STATE.toastIds.delete(key);
        });

        toast.addEventListener('click', async () => {
            try {
                await markMentionRead(mention.id);
            } catch (error) {
                console.error('[chat-mentions] mark read failed:', error);
            }

            toast.remove();

            if (mention.open_url) {
                window.location.href = mention.open_url;
            }
        });

        toastWrap().prepend(toast);
    }

    async function loadUnreadMentionToasts() {
        try {
            const response = await fetch('/chat/mentions/unread', {
                method: 'GET',
                headers: {
                    Accept: 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                credentials: 'same-origin',
            });

            if (!response.ok) return;

            const data = await response.json();
            (data.mentions || []).forEach(showMentionToast);
        } catch (error) {
            console.warn('[chat-mentions] unread mentions failed:', error);
        }
    }

    function bootEchoMentionListener() {
        if (STATE.echoBooted) return;

        const uid = currentUserId();
        if (!uid) return;

        if (!window.Echo || typeof window.Echo.private !== 'function') {
            setTimeout(bootEchoMentionListener, 700);
            return;
        }

        STATE.echoBooted = true;

        window.Echo.private(`chat.user.${uid}`)
            .listen('.chat-mention-created', (event) => {
                showMentionToast(event.mention || event);
            });

        console.log('[chat-mentions] Echo listener booted:', `chat.user.${uid}`);
    }

    function boot() {
        injectStyles();
        bootPicker();
        loadUnreadMentionToasts();
        bootEchoMentionListener();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', boot);
    } else {
        boot();
    }
})();
