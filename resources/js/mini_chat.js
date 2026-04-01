(function () {
    // -------------------------------------------------------------------------
    // Bootstrap current user + CSRF
    // -------------------------------------------------------------------------
    const me = {
        id: Number(document.body.dataset.userId || window.userId || 0),
        name: (document.body.dataset.userName || window.userName || "").trim(),
        image: document.body.dataset.userImage || window.userImage || "",
    };

    if (!me.id) {
        console.warn("[mini-chat] No authenticated user id – widget disabled.");
        return;
    }

    const csrf =
        document.querySelector('meta[name="csrf-token"]')?.content ||
        window.csrfToken ||
        "";

    // -------------------------------------------------------------------------
    // DOM references
    // -------------------------------------------------------------------------
    const launcher = document.getElementById("chatLauncher");
    const launcherBadge = document.getElementById("launcherBadge");
    const win = document.getElementById("chatWindow");

    const btnMinimize = document.getElementById("btnMinimize");
    const btnUsers = document.getElementById("btnUsers");
    const backdrop = document.getElementById("miniChatBackdrop");

    const sidebar = document.getElementById("miniChatSidebar");
    const userList = document.getElementById("userList");
    const userSearch = document.getElementById("userSearch");
    const onlineCount = document.getElementById("onlineCount");

    const convAvatar = document.getElementById("convAvatar");
    const convName = document.getElementById("convName");
    const convStatus = document.getElementById("convStatus");
    const convBadges = document.getElementById("convBadges");
    const typingIndicator = document.getElementById("typingIndicator");

    const messagesEl = document.getElementById("messages");
    const replyBanner = document.getElementById("replyBanner");
    const replyText = document.getElementById("replyText");
    const replyClose = document.getElementById("replyClose");

    const msgInput = document.getElementById("msgInput");
    const btnSend = document.getElementById("btnSend");
    const btnAttach = document.getElementById("btnAttach");
    const btnVoice = document.getElementById("btnVoice");
    const fileInput = document.getElementById("fileInput");

    if (!launcher || !win) {
        console.warn("[mini-chat] DOM nodes not found – abort.");
        return;
    }

    // -------------------------------------------------------------------------
    // State
    // -------------------------------------------------------------------------
    let chats = []; // { key, type:'user'|'group', id, name, lastname, isGroup, avatar, initials, online, unread, lastMsg, lastMsgAt }
    let activeChatKey = null;
    let replyToId = null;

    const seenIds = new Set(); // for dedup of appended messages

    // voice recording
    let mediaRecorder = null;
    let voiceChunks = [];
    let isRecording = false;

    // typing (minimal)
    let typingTimeout = null;

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------
    function chatKey(type, id) {
        return (type === "group" ? "g-" : "u-") + String(id);
    }

    function isGroupKey(key) {
        return key && key.startsWith("g-");
    }

    function getChatByKey(key) {
        return chats.find((c) => c.key === key) || null;
    }

    function escapeHtml(str = "") {
        return String(str)
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }

    function linkify(text = "") {
        return text.replace(
            /\b(https?:\/\/[^\s<]+)\b/gi,
            (m) =>
                `<a href="${m}" target="_blank" rel="noopener noreferrer">${m}</a>`
        );
    }

    function nl2br(text = "") {
        return text.replace(/\n/g, "<br>");
    }

    function formatTime(dateStr) {
        if (!dateStr) return "";
        const d = new Date(dateStr);
        if (Number.isNaN(d.getTime())) return "";
        return d.toLocaleTimeString([], { hour: "2-digit", minute: "2-digit" });
    }

    function formatDayLabel(date) {
        const today = new Date();
        const t = new Date(
            today.getFullYear(),
            today.getMonth(),
            today.getDate()
        );
        const d = new Date(date.getFullYear(), date.getMonth(), date.getDate());

        const diff = (t - d) / (24 * 60 * 60 * 1000);
        if (diff === 0) return "Heute";
        if (diff === 1) return "Gestern";

        return date.toLocaleDateString("de-DE", {
            weekday: "short",
            day: "numeric",
            month: "short",
            year: "numeric",
        });
    }

    function sameDay(a, b) {
        return (
            a.getFullYear() === b.getFullYear() &&
            a.getMonth() === b.getMonth() &&
            a.getDate() === b.getDate()
        );
    }

    function scrollToBottom() {
        if (!messagesEl) return;
        messagesEl.scrollTop = messagesEl.scrollHeight;
    }

    function openWindow() {
        win.classList.remove("d-none");
        launcher.setAttribute("aria-expanded", "true");
        win.animate(
            [
                { transform: "translateY(12px)", opacity: 0 },
                { transform: "translateY(0)", opacity: 1 },
            ],
            { duration: 160, easing: "ease-out" }
        );
    }

    function closeWindow() {
        win.classList.add("d-none");
        launcher.setAttribute("aria-expanded", "false");
        win.classList.remove("mini-chat-show-users");
    }

    function toggleUsersPanel(show) {
        if (show === true) {
            win.classList.add("mini-chat-show-users");
        } else if (show === false) {
            win.classList.remove("mini-chat-show-users");
        } else {
            win.classList.toggle("mini-chat-show-users");
        }
    }

    function updateLauncherBadge() {
        const total = chats.reduce(
            (sum, c) => sum + (Number(c.unread) || 0),
            0
        );
        if (total > 0) {
            launcherBadge.textContent = total > 99 ? "99+" : String(total);
            launcherBadge.classList.remove("d-none");
        } else {
            launcherBadge.classList.add("d-none");
        }
    }

    function avatarUrlOrInitials(chat) {
        const avatar =
            chat.avatar ||
            chat.image ||
            (chat.isGroup ? null : chat.image_url) ||
            null;
        const initials =
            chat.initials ||
            (
                (chat.name || "")
                    .split(/\s+/)
                    .filter(Boolean)
                    .map((p) => p[0])
                    .join("") || "?"
            ).substring(0, 2);

        return { avatar, initials: initials.toUpperCase() };
    }

    function updateOnlineDot(id, on) {
        const el = userList.querySelector(
            `.mini-chat-item[data-type="user"][data-id="${id}"] .mini-chat-item-online-dot`
        );
        if (el) {
            el.style.display = on ? "block" : "none";
        }
    }

    function shortTextForType(evt) {
        const t = (evt.type || "").toLowerCase();
        if (t === "audio") return "🎤 Sprachnachricht";
        if (t === "image") return "🖼 Bild";
        if (t === "file") return "📎 Anhang";
        return evt.message || "Neue Nachricht";
    }

    // -------------------------------------------------------------------------
    // Rendering: chat list
    // -------------------------------------------------------------------------
    function renderChatList(filterText = "") {
        const term = filterText.trim().toLowerCase();
        userList.innerHTML = "";

        const filtered = chats
            .slice()
            .filter((c) => {
                if (!term) return true;
                const full = `${c.name || ""} ${
                    c.lastname || ""
                }`.toLowerCase();
                return full.includes(term);
            })
            .sort((a, b) => {
                const A = Number(a.lastMsgAt || 0);
                const B = Number(b.lastMsgAt || 0);
                return B - A;
            });

        filtered.forEach((chat) => {
            const li = document.createElement("li");
            li.className = "mini-chat-item";
            li.dataset.key = chat.key;
            li.dataset.type = chat.type;
            li.dataset.id = chat.id;

            if (chat.key === activeChatKey) {
                li.classList.add("mini-chat-item--active");
            }

            const { avatar, initials } = avatarUrlOrInitials(chat);

            const avatarWrap = document.createElement("div");
            avatarWrap.className = "mini-chat-item-avatar-wrap";

            if (avatar) {
                const img = document.createElement("img");
                img.src = avatar;
                img.alt = chat.name || "";
                img.className = "mini-chat-item-avatar";
                avatarWrap.appendChild(img);
            } else {
                const span = document.createElement("div");
                span.className = "mini-chat-item-avatar-initials";
                span.textContent = initials;
                avatarWrap.appendChild(span);
            }

            if (chat.type === "user") {
                const dot = document.createElement("span");
                dot.className = "mini-chat-item-online-dot";
                dot.style.display = chat.online ? "block" : "none";
                avatarWrap.appendChild(dot);
            }

            const meta = document.createElement("div");
            meta.className = "mini-chat-item-meta";

            const nameRow = document.createElement("div");
            nameRow.className = "mini-chat-item-name-row";

            const nameSpan = document.createElement("span");
            nameSpan.className = "mini-chat-item-name";
            const fullName =
                `${chat.name || ""} ${chat.lastname || ""}`.trim() ||
                chat.displayName ||
                chat.groupName ||
                "";
            nameSpan.textContent =
                fullName || (chat.isGroup ? "Gruppe" : "User");

            nameRow.appendChild(nameSpan);

            if (chat.isGroup) {
                const pill = document.createElement("span");
                pill.className = "mini-chat-item-pill";
                pill.textContent = "Gruppe";
                nameRow.appendChild(pill);
            }

            meta.appendChild(nameRow);

            const preview = document.createElement("div");
            preview.className = "mini-chat-item-preview";
            preview.textContent = chat.lastMsg || "";
            preview.dataset.key = chat.key;
            meta.appendChild(preview);

            const right = document.createElement("div");
            right.className = "mini-chat-item-right";

            const timeSpan = document.createElement("span");
            timeSpan.className = "mini-chat-item-time";
            timeSpan.textContent = chat.lastMsgAt
                ? formatTime(chat.lastMsgAt)
                : "";
            right.appendChild(timeSpan);

            const unread = Number(chat.unread) || 0;
            if (unread > 0) {
                const badge = document.createElement("span");
                badge.className = "mini-chat-item-unread";
                badge.textContent = unread > 99 ? "99+" : String(unread);
                badge.dataset.key = chat.key;
                right.appendChild(badge);
            }

            li.appendChild(avatarWrap);
            li.appendChild(meta);
            li.appendChild(right);

            li.addEventListener("click", () => {
                const type = li.dataset.type;
                const id = Number(li.dataset.id);
                openChat(type, id);
            });

            userList.appendChild(li);
        });

        const online = chats.filter(
            (c) => c.type === "user" && c.online
        ).length;
        onlineCount.textContent = online;
        updateLauncherBadge();
    }

    function updateChatPreviewAndUnread(key, previewText, incrementUnread) {
        const chat = getChatByKey(key);
        if (!chat) return;

        if (previewText) {
            chat.lastMsg = previewText.slice(0, 80);
            chat.lastMsgAt = new Date().toISOString();
        }

        if (incrementUnread) {
            chat.unread = (Number(chat.unread) || 0) + 1;
        }

        renderChatList(userSearch.value || "");
    }

    function resetUnreadForChat(key) {
        const chat = getChatByKey(key);
        if (!chat) return;
        chat.unread = 0;
        renderChatList(userSearch.value || "");
    }

    // -------------------------------------------------------------------------
    // Messages rendering
    // -------------------------------------------------------------------------
    // -------------------------------------------------------------------------
    // Messages rendering
    // -------------------------------------------------------------------------
    let lastDayRendered = null;

    function appendDayDivider(dateObj) {
        if (!messagesEl) return;

        const wrap = document.createElement("div");
        wrap.className = "mini-chat-day-divider";

        const left = document.createElement("div");
        left.className = "mini-chat-day-divider-line";

        const mid = document.createElement("div");
        mid.className = "mini-chat-day-divider-label";
        mid.textContent = formatDayLabel(dateObj);

        const right = document.createElement("div");
        right.className = "mini-chat-day-divider-line";

        wrap.appendChild(left);
        wrap.appendChild(mid);
        wrap.appendChild(right);
        messagesEl.appendChild(wrap);
    }

    function safelyAppend(msg, mine) {
        if (!messagesEl) return;

        // dedupe
        if (msg.id && seenIds.has(msg.id)) return;
        if (msg.id) seenIds.add(msg.id);

        const created = new Date(msg.created_at || new Date());
        if (!lastDayRendered || !sameDay(lastDayRendered, created)) {
            appendDayDivider(created);
            lastDayRendered = created;
        }

        const row = document.createElement("div");
        row.className =
            "mini-chat-msg-row " +
            (mine ? "mini-chat-msg-row--me" : "mini-chat-msg-row--them");
        if (msg.id) row.dataset.msgId = msg.id;

        const bubble = document.createElement("div");
        bubble.className =
            "mini-chat-msg-bubble " +
            (mine ? "mini-chat-msg-bubble--me" : "mini-chat-msg-bubble--them");

        const parts = [];

        // Sender name above incoming bubble
        const from = msg.from_user || msg.user || msg.fromUser || {};
        const backendLabel = (msg.sender_label || "").trim();
        const fallbackName = [from.name || "", from.lastname || ""]
            .join(" ")
            .trim();
        const senderLabel = backendLabel || fallbackName;

        if (!mine && senderLabel) {
            parts.push(
                `<div class="mini-chat-msg-sender"><span>${escapeHtml(
                    senderLabel
                )}</span></div>`
            );
        }

        // Reply preview
        if (msg.reply_to_preview) {
            parts.push(
                `<div class="mini-chat-msg-reply">${escapeHtml(
                    msg.reply_to_preview
                )}</div>`
            );
        }

        // Text body
        if (msg.message && !msg.deleted_at) {
            const safe = linkify(nl2br(escapeHtml(msg.message)));
            parts.push(`<div class="mini-chat-msg-text">${safe}</div>`);
        } else if (msg.deleted_at) {
            parts.push(
                `<div class="mini-chat-msg-text"><em>Diese Nachricht wurde gelöscht.</em></div>`
            );
        }

        // Audio
        const t = (msg.type || "").toLowerCase();
        const audioUrl = msg.audio_url || msg.file_url;
        if (t === "audio" && audioUrl) {
            parts.push(
                `<div class="mini-chat-msg-audio"><audio controls src="${audioUrl}"></audio></div>`
            );
        }

        // Attachments
        const atts = Array.isArray(msg.attachments) ? msg.attachments : [];
        if (atts.length) {
            const attHtml = atts
                .map((att) => {
                    const isImg =
                        att.is_image ||
                        (att.mime && String(att.mime).startsWith("image/"));
                    if (isImg) {
                        return `<img src="${att.url}" alt="${escapeHtml(
                            att.name || ""
                        )}" onclick="window.open('${
                            att.url
                        }','_blank','noopener')">`;
                    }
                    return `<a href="${att.url}" target="_blank" rel="noopener">
                            <i class="feather icon-file"></i>${escapeHtml(
                                att.name || "Datei"
                            )}
                        </a>`;
                })
                .join("");
            parts.push(
                `<div class="mini-chat-msg-attachments">${attHtml}</div>`
            );
        }

        bubble.innerHTML = parts.join("");

        const meta = document.createElement("div");
        meta.className = "mini-chat-msg-meta";

        const timeSpan = document.createElement("span");
        timeSpan.textContent = formatTime(msg.created_at);
        meta.appendChild(timeSpan);

        if (mine) {
            const ticks = document.createElement("span");
            ticks.className = "mini-chat-ticks";
            ticks.textContent = msg.is_read ? "✓✓" : "✓";
            meta.appendChild(ticks);
        }

        bubble.appendChild(meta);
        row.appendChild(bubble);
        messagesEl.appendChild(row);
        scrollToBottom();
    }

    async function loadMessagesForActive() {
        if (!activeChatKey) return;
        const active = getChatByKey(activeChatKey);
        if (!active) return;

        messagesEl.innerHTML =
            '<div class="mini-chat-empty">Nachrichten werden geladen…</div>';
        seenIds.clear();
        lastDayRendered = null;

        const url =
            active.type === "group"
                ? `/chat/group/fetch/${active.id}`
                : `/chat/fetch/${active.id}`;

        try {
            const res = await fetch(url, { credentials: "same-origin" });
            const list = await res.json();
            messagesEl.innerHTML = "";
            const arr = Array.isArray(list) ? list : [];
            arr.slice()
                .sort(
                    (a, b) =>
                        new Date(a.created_at || 0) -
                        new Date(b.created_at || 0)
                )
                .forEach((m) => {
                    const mine = Number(m.from_user_id) === Number(me.id);
                    safelyAppend(m, mine);
                });
        } catch (e) {
            console.error("[mini-chat] loadMessages error", e);
            messagesEl.innerHTML =
                '<div class="mini-chat-empty text-danger">Fehler beim Laden.</div>';
        }
    }

    // -------------------------------------------------------------------------
    // Backend calls: load chats
    // -------------------------------------------------------------------------
    async function loadChats() {
        try {
            const res = await fetch("/chat/employees", {
                credentials: "same-origin",
            });
            const data = await res.json();

            const employees = Array.isArray(data.employees)
                ? data.employees
                : [];
            const groups = Array.isArray(data.groups) ? data.groups : [];

            const userChats = employees.map((u) => ({
                key: chatKey("user", u.id),
                type: "user",
                id: u.id,
                name: u.name || "",
                lastname: u.lastname || "",
                isGroup: false,
                avatar: u.image || null,
                online: !!u.online,
                unread: Number(u.unread || 0),
                lastMsg: (u.last_msg || "").slice(0, 80),
                lastMsgAt: u.last_msg_at || null,
            }));

            const groupChats = groups.map((g) => {
                const label = g.name || g.context_label || "Gruppe";
                const lastSender = (
                    g.last_from_name ||
                    g.last_sender_name ||
                    ""
                ).trim();
                const lastMsg = g.last_msg || "";
                const preview = lastSender
                    ? `${lastSender}: ${lastMsg}`
                    : lastMsg;
                return {
                    key: chatKey("group", g.id),
                    type: "group",
                    id: g.id,
                    name: label,
                    lastname: "",
                    isGroup: true,
                    avatar: g.avatar
                        ? `/storage/${String(g.avatar).replace(/^\/+/, "")}`
                        : null,
                    online: false,
                    unread: Number(g.unread || 0),
                    lastMsg: (preview || "").slice(0, 80),
                    lastMsgAt: g.last_msg_at || null,
                };
            });

            chats = [...userChats, ...groupChats];
            renderChatList(userSearch.value || "");
        } catch (e) {
            console.error("[mini-chat] loadChats failed", e);
        }
    }

    // -------------------------------------------------------------------------
    // Mark read
    // -------------------------------------------------------------------------
    function markAsReadDirect(otherId) {
        fetch(`/chat/mark-read/${otherId}`, {
            method: "POST",
            headers: { "X-CSRF-TOKEN": csrf },
            credentials: "same-origin",
        }).catch(() => {});
    }

    function markAsReadGroup(groupId) {
        fetch(`/chat/group/mark-read/${groupId}`, {
            method: "POST",
            headers: { "X-CSRF-TOKEN": csrf },
            credentials: "same-origin",
        }).catch(() => {});
    }

    // -------------------------------------------------------------------------
    // Open chat
    // -------------------------------------------------------------------------
    function setHeaderForChat(chat) {
        const { avatar, initials } = avatarUrlOrInitials(chat);
        if (avatar) {
            convAvatar.innerHTML =
                '<img src="' +
                avatar +
                '" alt="' +
                escapeHtml(chat.name || "") +
                '">';
        } else {
            convAvatar.textContent = initials;
        }

        const fullName =
            `${chat.name || ""} ${chat.lastname || ""}`.trim() ||
            chat.name ||
            "";
        convName.textContent = fullName || (chat.isGroup ? "Gruppe" : "Chat");

        if (chat.type === "user") {
            convStatus.textContent = chat.online ? "Online" : "Offline";
        } else {
            convStatus.textContent = "Gruppe";
        }

        convBadges.innerHTML = "";
        if (chat.isGroup) {
            const pill = document.createElement("span");
            pill.className = "mini-chat-main-badge";
            pill.textContent = "Gruppe";
            convBadges.appendChild(pill);
        }
    }

    function openChat(type, id) {
        const key = chatKey(type, id);
        activeChatKey = key;
        const chat = getChatByKey(key);
        if (!chat) return;

        setHeaderForChat(chat);

        msgInput.disabled = false;
        btnSend.disabled = false;
        btnAttach.disabled = false;
        btnVoice.disabled = false;

        resetUnreadForChat(key);

        // mark read on backend
        if (type === "group") {
            markAsReadGroup(id);
        } else {
            markAsReadDirect(id);
        }

        loadMessagesForActive();

        // close sidebar on mobile
        toggleUsersPanel(false);
    }

    // -------------------------------------------------------------------------
    // Send text, files, voice
    // -------------------------------------------------------------------------
    async function sendText() {
        if (!activeChatKey) return;
        const text = (msgInput.value || "").replace(/\r/g, "");
        if (!text.trim()) return;

        const active = getChatByKey(activeChatKey);
        if (!active) return;

        const payload = {
            message: text,
            type: "text",
            to_user_id: active.type === "user" ? active.id : null,
            group_id: active.type === "group" ? active.id : null,
            reply_to_id: replyToId || null,
        };

        msgInput.value = "";
        hideReply();

        try {
            const res = await fetch("/chat/send", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": csrf,
                },
                credentials: "same-origin",
                body: JSON.stringify(payload),
            });
            const data = await res.json();
            const msg = data.message;
            if (msg) {
                const mine = Number(msg.from_user_id) === Number(me.id);
                safelyAppend(msg, mine);
            }
        } catch (e) {
            console.error("[mini-chat] sendText failed", e);
        }
    }

    async function sendFiles(files) {
        if (!activeChatKey || !files || files.length === 0) return;
        const active = getChatByKey(activeChatKey);
        if (!active) return;

        const fd = new FormData();
        Array.from(files).forEach((f) => fd.append("files[]", f));

        if (active.type === "user") {
            fd.append("to_user_id", active.id);
        } else {
            fd.append("group_id", active.id);
        }
        fd.append("type", "file");

        try {
            const res = await fetch("/chat/send", {
                method: "POST",
                body: fd,
                headers: { "X-CSRF-TOKEN": csrf },
                credentials: "same-origin",
            });
            const data = await res.json();
            const list = data.messages || (data.message ? [data.message] : []);
            list.forEach((m) => {
                const mine = Number(m.from_user_id) === Number(me.id);
                safelyAppend(m, mine);
            });
        } catch (e) {
            console.error("[mini-chat] sendFiles failed", e);
        }
    }

    async function sendVoiceBlob(blob, ext) {
        if (!activeChatKey || !blob) return;
        const active = getChatByKey(activeChatKey);
        if (!active) return;

        const fd = new FormData();
        fd.append("voice", blob, `voice_${Date.now()}.${ext || "webm"}`);
        if (active.type === "user") fd.append("to_user_id", active.id);
        if (active.type === "group") fd.append("group_id", active.id);
        fd.append("type", "audio");

        try {
            const res = await fetch("/chat/send", {
                method: "POST",
                body: fd,
                headers: { "X-CSRF-TOKEN": csrf },
                credentials: "same-origin",
            });
            const data = await res.json();
            const msg = data.message;
            if (msg) {
                const mine = Number(msg.from_user_id) === Number(me.id);
                safelyAppend(msg, mine);
            }
        } catch (e) {
            console.error("[mini-chat] sendVoiceBlob failed", e);
        }
    }

    // -------------------------------------------------------------------------
    // Voice recording (simple)
    // -------------------------------------------------------------------------
    function hasMicSupport() {
        const secure =
            window.isSecureContext || location.hostname === "localhost";
        return (
            secure &&
            !!(navigator.mediaDevices && navigator.mediaDevices.getUserMedia)
        );
    }

    function pickAudioMime() {
        const candidates = [
            "audio/webm;codecs=opus",
            "audio/webm",
            "audio/ogg;codecs=opus",
            "audio/ogg",
        ];
        if (!window.MediaRecorder || !MediaRecorder.isTypeSupported) return "";
        for (const t of candidates) {
            if (MediaRecorder.isTypeSupported(t)) return t;
        }
        return "";
    }

    async function startRecording() {
        if (!hasMicSupport() || !window.MediaRecorder) {
            console.warn("[mini-chat] MediaRecorder not supported.");
            return;
        }

        try {
            const stream = await navigator.mediaDevices.getUserMedia({
                audio: true,
            });
            const mimeType = pickAudioMime();
            voiceChunks = [];
            mediaRecorder = new MediaRecorder(
                stream,
                mimeType ? { mimeType } : undefined
            );
            isRecording = true;
            btnVoice.classList.add("mini-chat-icon-btn--recording");

            mediaRecorder.ondataavailable = (e) => {
                if (e.data && e.data.size > 0) voiceChunks.push(e.data);
            };
            mediaRecorder.onstop = () => {
                stream.getTracks().forEach((t) => t.stop());
                isRecording = false;
                btnVoice.classList.remove("mini-chat-icon-btn--recording");

                try {
                    const type =
                        mediaRecorder.mimeType || mimeType || "audio/webm";
                    const blob = new Blob(voiceChunks, { type });
                    let ext = "webm";
                    if (type.includes("ogg")) ext = "ogg";
                    else if (type.includes("mp3")) ext = "mp3";
                    sendVoiceBlob(blob, ext);
                } catch (e) {
                    console.error("[mini-chat] build blob failed", e);
                }
            };

            mediaRecorder.start();
        } catch (e) {
            console.error("[mini-chat] getUserMedia failed", e);
        }
    }

    function stopRecording() {
        try {
            if (mediaRecorder && mediaRecorder.state === "recording") {
                mediaRecorder.stop();
            }
        } catch (e) {
            console.warn("[mini-chat] stopRecording error", e);
        }
    }

    // -------------------------------------------------------------------------
    // Reply banner
    // -------------------------------------------------------------------------
    function showReply(msgId, preview) {
        replyToId = msgId;
        replyText.textContent = preview.slice(0, 80);
        replyBanner.style.display = "flex";
    }

    function hideReply() {
        replyToId = null;
        replyText.textContent = "";
        replyBanner.style.display = "none";
    }

    // -------------------------------------------------------------------------
    // Realtime: Echo
    // -------------------------------------------------------------------------
    function initPresence() {
        if (!window.Echo || !window.Echo.join) return;
        window.Echo.join("online")
            .here((list) => {
                list.forEach((u) => {
                    const chat = chats.find(
                        (c) => c.type === "user" && c.id === u.id
                    );
                    if (chat) chat.online = true;
                    updateOnlineDot(u.id, true);
                });
                const online = chats.filter(
                    (c) => c.type === "user" && c.online
                ).length;
                onlineCount.textContent = online;
            })
            .joining((u) => {
                const chat = chats.find(
                    (c) => c.type === "user" && c.id === u.id
                );
                if (chat) chat.online = true;
                updateOnlineDot(u.id, true);
                const online = chats.filter(
                    (c) => c.type === "user" && c.online
                ).length;
                onlineCount.textContent = online;
            })
            .leaving((u) => {
                const chat = chats.find(
                    (c) => c.type === "user" && c.id === u.id
                );
                if (chat) chat.online = false;
                updateOnlineDot(u.id, false);
                const online = chats.filter(
                    (c) => c.type === "user" && c.online
                ).length;
                onlineCount.textContent = online;
            })
            .error((e) => console.error("[mini-chat] presence error", e));
    }

    function handleMessageReadEvent(e) {
        const ids = e.message_ids || [];
        ids.forEach((id) => {
            const row = messagesEl.querySelector(
                `.mini-chat-msg-row[data-msg-id="${id}"]`
            );
            if (!row) return;
            const ticks = row.querySelector(".mini-chat-ticks");
            if (ticks) {
                ticks.textContent = "✓✓";
            }
        });
    }

    function handleGroupReadEvent(e) {
        const id = e.chat_id;
        if (!id) return;
        const row = messagesEl.querySelector(
            `.mini-chat-msg-row[data-msg-id="${id}"]`
        );
        if (!row) return;
        const ticks = row.querySelector(".mini-chat-ticks");
        if (ticks) {
            ticks.textContent = "✓✓";
        }
    }

    function handleIncomingMessage(e) {
        if (!e) return;

        const fromId = e.from_user_id;
        const toId = e.to_user_id;
        const groupId = e.group_id;

        const isMine = Number(fromId) === Number(me.id);

        let key = null;
        if (groupId) {
            key = chatKey("group", groupId);
        } else {
            const otherId = fromId === me.id ? Number(toId) : Number(fromId);
            key = chatKey("user", otherId);
        }

        // Update preview + unread
        const preview = shortTextForType(e);
        const isActive = key === activeChatKey;

        if (!isActive && !isMine) {
            updateChatPreviewAndUnread(key, preview, true);
        } else {
            updateChatPreviewAndUnread(key, preview, false);
        }

        // If this is the active chat, append
        if (isActive) {
            safelyAppend(e, isMine);

            // mark as read if from other
            const active = getChatByKey(activeChatKey);
            if (active && !isMine) {
                if (active.type === "group") {
                    markAsReadGroup(active.id);
                } else {
                    markAsReadDirect(active.id);
                }
            }
        }
    }

    function initEcho() {
        if (!window.Echo || !window.Echo.private) {
            console.warn("[mini-chat] Echo not ready.");
            return;
        }

        window.Echo.private(`chat.user.${me.id}`)
            .listen(".message-sent", handleIncomingMessage)
            .listen(".message-read", handleMessageReadEvent)
            .listen(".group-membership-updated", () => {
                loadChats();
            });

        // Group read events on group channels might be used by big chat,
        // but mini chat will also subscribe only when needed via user channel.
        // GroupMessageRead broadcasts on chat.group.{id}, but we don't join here
        // to keep it light. We'll still get simple read info via direct events.
    }

    // -------------------------------------------------------------------------
    // Events
    // -------------------------------------------------------------------------
    // Launcher & window
    launcher.addEventListener("click", () => {
        if (win.classList.contains("d-none")) openWindow();
        else closeWindow();
    });

    btnMinimize?.addEventListener("click", () => {
        closeWindow();
    });

    btnUsers?.addEventListener("click", () => {
        toggleUsersPanel();
    });

    backdrop?.addEventListener("click", () => {
        toggleUsersPanel(false);
    });

    document.addEventListener("keydown", (e) => {
        if (e.key === "Escape" && !win.classList.contains("d-none")) {
            if (win.classList.contains("mini-chat-show-users")) {
                toggleUsersPanel(false);
            } else {
                closeWindow();
            }
        }
    });

    // Search
    if (userSearch) {
        userSearch.addEventListener("input", () => {
            renderChatList(userSearch.value || "");
        });
    }

    // Composer
    msgInput.addEventListener("keydown", (e) => {
        if (e.key === "Enter" && !e.shiftKey) {
            e.preventDefault();
            sendText();
        }
    });

    btnSend.addEventListener("click", () => {
        sendText();
    });

    btnAttach.addEventListener("click", () => {
        fileInput?.click();
    });

    fileInput?.addEventListener("change", () => {
        if (fileInput.files && fileInput.files.length) {
            sendFiles(fileInput.files);
            fileInput.value = "";
        }
    });

    btnVoice.addEventListener("click", () => {
        if (!isRecording) startRecording();
        else stopRecording();
    });

    replyClose?.addEventListener("click", () => {
        hideReply();
    });

    // -------------------------------------------------------------------------
    // Init
    // -------------------------------------------------------------------------
    if (window.feather && window.feather.replace) {
        window.feather.replace();
    }

    loadChats();
    initPresence();
    initEcho();
})();
