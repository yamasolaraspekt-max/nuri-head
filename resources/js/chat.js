if (window.Echo?.connector?.pusher?.connection) {
    const conn = window.Echo.connector.pusher.connection;

    conn.bind("connected", () => {
        console.log("Echo connected to Reverb!");
    });

    conn.bind("disconnected", () => {
        console.log("Echo disconnected from Reverb!");
    });

    conn.bind("error", (err) => {
        console.error("Echo connection error:", err);
    });
} else {
    console.warn(
        "Echo not ready yet in chat.js – connection listeners skipped.",
    );
}

window.Echo.connector.pusher.connection.bind("disconnected", () => {
    console.log("Echo disconnected from Reverb!");
});

window.Echo.connector.pusher.connection.bind("error", (err) => {
    console.error("Echo connection error:", err);
});

console.log("✅ Echo (Reverb) initialized from Chatroom:", window.Echo);

/* -----------------------------------------------------------------------------
   Echo + Reverb + Vite diagnostics (drop-in)
   - Safe to include in prod. Logs only.
   - No external deps.
----------------------------------------------------------------------------- */
(function echoReverbViteDiagnostics() {
    const diag = {
        time: new Date().toISOString(),
        echoFound: !!window.Echo,
        driver: null,
        ws: {
            host: null,
            port: null,
            secure: null,
            transports: null,
            key: null,
            cluster: null,
        },
        state: null,
        lastError: null,
        directProbe: { url: null, ok: null, error: null },
        vite: { dev: false, url: null, ok: null },
    };
    window.__echoDiag = diag; // keep globally for quick inspection

    /* ---- Echo present? ---- */
    if (!window.Echo) {
        console.warn("Echo not found on window. Skipping Echo listeners.");
        tryPingVite();
        return;
    }

    // Try to detect Pusher-like connector used by Reverb
    const connector = window.Echo.connector;
    const pusherLike =
        connector &&
        (connector.pusher || connector.connection || connector.channels);
    const pusher = connector?.pusher;
    const connection = pusher?.connection || connector?.connection || null;

    // Extract options Laravel Echo used (pusher compatible)
    const opts = connector?.options || connector?.pusher?.config || {};
    diag.driver = pusher ? "pusher-compatible" : "unknown";
    diag.ws.host = opts.wsHost ?? location.hostname;
    diag.ws.port = Number(opts.wsPort ?? 6001);
    diag.ws.secure = !!(
        opts.forceTLS ??
        opts.encrypted ??
        location.protocol === "https:"
    );
    diag.ws.transports = opts.enabledTransports ?? ["ws", "wss"];
    diag.ws.key = opts.key ?? opts.client?.key ?? null;
    diag.ws.cluster = opts.cluster ?? null;

    // Verbose config print
    console.table({
        "Echo driver": diag.driver,
        wsHost: diag.ws.host,
        wsPort: diag.ws.port,
        secure: diag.ws.secure,
        enabledTransports: Array.isArray(diag.ws.transports)
            ? diag.ws.transports.join(",")
            : String(diag.ws.transports),
        key: diag.ws.key || "(none)",
        cluster: diag.ws.cluster || "(none)",
    });

    /* ---- Bind all connection events safely ---- */
    if (connection?.bind) {
        connection.bind("state_change", (s) => {
            diag.state = s.current ?? s?.current ?? "unknown";
            console.log("Echo state_change:", s);
        });
        connection.bind("connected", () => {
            diag.state = "connected";
            console.log("✅ Echo connected");
        });
        connection.bind("disconnected", () => {
            diag.state = "disconnected";
            console.warn("⚠️ Echo disconnected");
        });
        connection.bind("unavailable", () => {
            diag.state = "unavailable";
            console.warn("⚠️ Echo unavailable");
        });
        connection.bind("connecting", () => {
            diag.state = "connecting";
            console.log("Echo connecting…");
        });
        connection.bind("error", (err) => {
            diag.lastError = err;
            console.error("❌ Echo connection error:", err);
            classifySocketError(err);
        });
    } else {
        console.warn(
            "Echo connector has no connection.bind API. Check Echo init.",
        );
    }

    /* ---- Direct WebSocket probe (bypasses Echo) ---- */
    try {
        const host = diag.ws.host || location.hostname;
        const proto = diag.ws.secure ? "wss" : "ws";
        const port = diag.ws.port || (diag.ws.secure ? 443 : 80);
        // Reverb is Pusher protocol compatible. We only test TCP/WebSocket reachability.
        const url = `${proto}://${host}:${port}/app/${encodeURIComponent(
            diag.ws.key || "app",
        )}?protocol=7&client=js&version=8.x&flash=false`;
        diag.directProbe.url = url;

        const t0 = Date.now();
        const ws = new WebSocket(url);
        const to = setTimeout(() => {
            try {
                ws.close();
            } catch {}
            if (diag.directProbe.ok == null) {
                diag.directProbe.ok = false;
                diag.directProbe.error = "timeout";
                console.error("❌ Reverb WS probe timeout:", url);
            }
        }, 6000);

        ws.onopen = () => {
            clearTimeout(to);
            diag.directProbe.ok = true;
            console.log(`✅ Reverb WS reachable (${Date.now() - t0} ms):`, url);
            ws.close();
        };
        ws.onerror = (e) => {
            clearTimeout(to);
            diag.directProbe.ok = false;
            diag.directProbe.error = e?.message || "onerror";
            console.error("❌ Reverb WS probe error:", url, e);
        };
    } catch (e) {
        diag.directProbe.ok = false;
        diag.directProbe.error = e.message || String(e);
        console.error("❌ Reverb WS probe exception:", e);
    }

    /* ---- Vite dev server probe (optional) ---- */
    tryPingVite();

    function tryPingVite() {
        // Detect Vite HMR in browser builds
        const cand = [
            "http://localhost:5173/__vite_ping",
            "http://127.0.0.1:5173/__vite_ping",
            `${location.protocol}//${location.hostname}:5173/__vite_ping`,
        ];
        const url = cand.find(Boolean);
        diag.vite.url = url;

        // If app is bundled, this will just fail silently. That's fine.
        fetch(url, { mode: "no-cors" })
            .then(() => {
                diag.vite.dev = true;
                diag.vite.ok = true;
                console.log("🟢 Vite dev server reachable:", url);
            })
            .catch(() => {
                diag.vite.dev = false;
                diag.vite.ok = false;
                console.log(
                    "Vite dev server not detected. Running built assets or different port.",
                );
            });
    }

    function classifySocketError(err) {
        const msg = (err?.error?.message || err?.message || "").toLowerCase();
        if (msg.includes("failed") && msg.includes("tls")) {
            console.warn(
                "Hint: TLS failure. Check REVERB_SCHEME=wss and proxy SSL termination.",
            );
        } else if (
            msg.includes("401") ||
            msg.includes("403") ||
            msg.includes("unauthorized")
        ) {
            console.warn(
                "Hint: Auth error. Check /broadcasting/auth CSRF, Sanctum cookie, and auth middleware.",
            );
        } else if (msg.includes("closed before connection established")) {
            console.warn(
                "Hint: Port blocked or server not running. Verify reverb:start and firewall rules.",
            );
        } else if (msg.includes("net::err")) {
            console.warn(
                "Hint: Network error. Check wsHost/wsPort, container networking, and CORS for websockets.",
            );
        }
    }
})();

$(function () {
    const $ctx = $("#groupCustomerSearch");
    if (!$ctx.length) return;

    // Guard: ensure Select2 plugin exists
    if (!$.fn.select2) {
        console.error("Select2 not loaded on jQuery instance");
        return;
    }

    $ctx.select2({
        placeholder: "Kunde / Adresse / Produkt suchen…",
        width: "100%",
        ajax: {
            url: "/chat/contexts/search",
            dataType: "json",
            delay: 250,
            data: (params) => ({
                q: params.term || "",
            }),
            processResults: (data) => ({
                results: data,
            }),
        },
        minimumInputLength: 2,
        escapeMarkup: (m) => m,
        templateResult: formatContextOption,
        templateSelection: formatContextSelection,
    });
    function formatContextOption(item) {
        if (!item.id) return item.text;
        const line1 = item.text || "";
        const line2 = item.line2 || "";
        return `
            <div class="flex flex-col">
                <span class="font-semibold text-sm">${line1}</span>
                <span class="text-xs text-gray-500">${line2}</span>
            </div>
        `;
    }

    function formatContextSelection(item) {
        if (!item.id) return item.text;
        return item.text || "";
    }

    $ctx.on("select2:select", function (e) {
        const data = e.params.data;
        $("#groupCustomerId").val(data.customer_id || "");
        $("#groupAlternativeId").val(data.alternative_id || "");
        $("#groupProductId").val(data.product_id || "");
        $("#leadProductListId").val(data.id || "");
        $("#groupCustomerLabel").text(data.line2 || "");
    });
});

const userId = window.userId;
const csrf = window.csrfToken;
let selectedUserId = null;
let selectedGroupId = null;
let selectedChatType = null;
let selectedTutorialId = null;
const NEWS_FEED_ENDPOINT = "/chat/news"; // adjust to your route

let DOM = {};
// --- Small toast helper using #toast ----------------------------------------
function showToast(text) {
    const el = document.getElementById("toast");
    if (!el) return;
    el.textContent = text;
    el.classList.remove("opacity-0");
    el.classList.add("opacity-100");

    clearTimeout(el._timer);
    el._timer = setTimeout(() => {
        el.classList.remove("opacity-100");
        el.classList.add("opacity-0");
    }, 2000);
}

// --- Share modal state -------------------------------------------------------
let shareMessageId = null;
let shareMessageText = "";
let shareRecipientsLoaded = false;

function openShareModal(messageId, messageText) {
    shareMessageId = messageId;
    shareMessageText = messageText || "";

    const modal = document.getElementById("shareMessageModal");
    const preview = document.getElementById("shareMessagePreview");
    const selectEl = document.getElementById("shareTargetSelect");

    if (!modal || !preview || !selectEl) return;

    preview.value = shareMessageText;

    // Reset selection
    if (window.$ && $(selectEl).data("select2")) {
        $(selectEl).val(null).trigger("change");
    } else {
        Array.from(selectEl.options).forEach((o) => (o.selected = false));
    }

    loadShareRecipients();

    modal.classList.remove("hidden");
    modal.classList.add("flex");
}

function closeShareModal() {
    const modal = document.getElementById("shareMessageModal");
    if (!modal) return;
    modal.classList.add("hidden");
    modal.classList.remove("flex");
}

function loadShareRecipients() {
    if (shareRecipientsLoaded) return;

    const selectEl = document.getElementById("shareTargetSelect");
    if (!selectEl) return;

    fetch("/chat/employee")
        .then((res) => res.json())
        .then((users) => {
            users.forEach((u) => {
                if (!u || !u.id) return;
                const opt = document.createElement("option");
                opt.value = u.id;
                opt.textContent = `${u.name || ""} ${u.lastname || ""}`.trim();
                opt.dataset.avatar =
                    u.avatar || u.image || "/images/gender/users.png";
                selectEl.appendChild(opt);
            });

            if (window.$ && $.fn.select2) {
                $(selectEl).select2({
                    dropdownParent: $("#shareMessageModal"),
                    templateResult: function (user) {
                        if (!user.id) return user.text;
                        const avatar =
                            $(user.element).data("avatar") ||
                            "/images/gender/users.png";
                        return `
                            <div class="flex items-center gap-2">
                                <img src="${avatar}" class="w-6 h-6 rounded-full object-cover" />
                                <span>${user.text}</span>
                            </div>
                        `;
                    },
                    templateSelection: function (user) {
                        return user.text;
                    },
                    escapeMarkup: (m) => m,
                });
            }

            shareRecipientsLoaded = true;
        })
        .catch((err) => {
            console.error("Error loading share recipients:", err);
        });
}

function submitShare() {
    const selectEl = document.getElementById("shareTargetSelect");
    if (!selectEl) return;

    let userIds = [];
    if (window.$ && $(selectEl).data("select2")) {
        userIds = $(selectEl).val() || [];
    } else {
        userIds = Array.from(selectEl.selectedOptions).map((o) => o.value);
    }

    if (!shareMessageId || !userIds.length) {
        Swal?.fire?.("Hinweis", "Bitte Empfänger auswählen.", "info");
        return;
    }

    // optional: if you later add a separate note field:
    // const note = document.getElementById("shareNoteInput")?.value || "";
    const note = ""; // for now we send no extra note

    fetch("/chat/share", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": csrf,
            Accept: "application/json",
        },
        body: JSON.stringify({
            message_id: shareMessageId,
            recipients: userIds.map((id) => Number(id)), // <-- match backend
            note: note, // <-- match 'note' field
        }),
    })
        .then(async (res) => {
            if (!res.ok) {
                const txt = await res.text();
                throw new Error(`HTTP ${res.status}: ${txt.slice(0, 200)}`);
            }
            return res.json().catch(() => ({}));
        })
        .then(() => {
            closeShareModal();
            showToast("Nachricht wurde geteilt.");
        })
        .catch((err) => {
            console.error("share error:", err);
            Swal?.fire?.(
                "Fehler",
                "Nachricht konnte nicht geteilt werden.",
                "error",
            );
        });
}

// pin state for the current open chat/group
window.pinState = {
    type: null, // "user" | "group"
    id: null,
    isPinned: false,
};

function isBuilderQuery(text) {
    const t = text.trim().toLowerCase();

    const patterns = [
        "who is the developer",
        "who built this",
        "who created this",
        "who is the creator",
        "who made this",
        "who made zuhalify",
        "developer info",
        "about the developer",
    ];

    return patterns.some((p) => t === p || t.includes(p));
}

function showBuilderCard(questionText) {
    const profile = window.builderProfile || {};
    const name = profile.name || "Developer";
    const product = profile.product || "";
    const avatar = profile.avatar || window.defaultPic || "";
    const link = profile.link || "";
    const bio = profile.bio || "";

    // 1) Echo the user's question as a normal message from "me"
    const myMsg = {
        id: "local-" + Date.now(),
        message: questionText,
        type: "text",
        from_user: {
            id: window.userId,
            name: window.userName,
            image: window.userImage,
        },
        created_at: new Date().toISOString(),
    };
    addMessage(myMsg, true, false);

    // 2) Add the fancy developer card as a special system message
    const answerMsg = {
        id: "builder-" + Date.now(),
        type: "builder-info",
        builder: { name, product, avatar, link, bio },
        created_at: new Date().toISOString(),
    };
    addMessage(answerMsg, false, true);
}

// lookup maps
window.usersById = {};
let typingTimeout = null; // To manage the typing indicator display
let replyToId = null;
let replyPreviewText = "";
let editingMessageId = null;
const seenMessageIds = new Set();
let mediaRecorder = null;
let recordedChunks = [];
let isRecording = false;

let _lastRenderedDay = null; // resets when you load a chat

// === Auto-scroll helpers & "jump to latest" UI ===============================

// Show the arrow when the user is not near the bottom (e.g. scrolled up)
// Hide it when back near the bottom or after jumping.
function isNearBottom(threshold = 120) {
    const scroller = DOM.chatScroll || document.getElementById("chatScroll");
    if (!scroller) return true; // fail-safe: treat as near bottom
    const distance =
        scroller.scrollHeight - scroller.scrollTop - scroller.clientHeight;
    return distance <= threshold;
}

function ensureJumpToLatestUI() {
    if (DOM.jumpBtn) return;
    const container =
        document.querySelector(".conversation-col") || document.body;

    const btn = document.createElement("button");
    btn.id = "jumpToLatestBtn";
    btn.type = "button";
    btn.title = "Zum letzten Beitrag";
    btn.className = [
        "hidden",
        "absolute",
        "z-30",
        "bottom-24",
        "right-4", // sits above composer
        "rounded-full",
        "border",
        "shadow",
        "bg-white",
        "hover:bg-gray-50",
        "p-2",
    ].join(" ");

    btn.innerHTML = `<i data-feather="arrow-down"></i>`;
    btn.addEventListener("click", () => {
        scrollToBottom({ behavior: "smooth" });
    });

    container.appendChild(btn);
    DOM.jumpBtn = btn;
    if (window.feather) window.feather.replace();
}

function toggleJumpToLatest(show) {
    ensureJumpToLatestUI();
    DOM.jumpBtn.classList.toggle("hidden", !show);
}

function escapeHtml(s = "") {
    return s
        .replaceAll("&", "&amp;")
        .replaceAll("<", "&lt;")
        .replaceAll(">", "&gt;")
        .replaceAll('"', "&quot;")
        .replaceAll("'", "&#039;");
}

// very light auto-linker (http/https)
function linkify(text = "") {
    return text.replace(
        /\b(https?:\/\/[^\s<]+)\b/gi,
        (m) =>
            `<a href="${m}" target="_blank" rel="noopener noreferrer" class="underline break-words">${m}</a>`,
    );
}

// convert \n to <br>
function nl2br(text = "") {
    return text.replace(/\n/g, "<br>");
}

// 🗓️ Date helpers

const IMG = {
    EMP: window.employeeLocation || "/images/employee",
    DEFAULT_USER: (window.defaultPic || "/images/gender/users.png").replace(
        /\/+$/,
        "",
    ),
    GROUP_BASE: "/storage", // chat_group_avatars live on public disk
};

const STATUS_LABELS_DE = {
    open: "Offen",
    new: "Neu",
    lead: "Lead",
    offer: "Angebot",
    deal: "Deal",
    project: "Projekt",
    junk: "Junk",
    cancel: "Abgebrochen",
    pause: "Pausiert",
    completed: "Abgeschlossen",
    ticket: "Ticket",
};

function translateStatusDe(code) {
    if (!code) return null;
    const key = String(code).toLowerCase();
    if (STATUS_LABELS_DE[key]) return STATUS_LABELS_DE[key];
    return code.charAt(0).toUpperCase() + code.slice(1);
}

// helpers
const isAbs = (p) =>
    typeof p === "string" && (/^https?:\/\//i.test(p) || p.startsWith("//"));
const norm = (base, p, fallback = "") => {
    if (!p) return fallback;
    if (isAbs(p)) return p;
    p = String(p).replace(/^\/+/, "");
    base = String(base).replace(/\/+$/, "");
    return `${base}/${p}`;
};

function isSameDay(a, b) {
    return (
        a.getFullYear() === b.getFullYear() &&
        a.getMonth() === b.getMonth() &&
        a.getDate() === b.getDate()
    );
}

function setRightAvatarImage(url) {
    if (!DOM.activeChatAvatar) return;
    DOM.activeChatAvatar.innerHTML = `<img src="${url}" class="w-9 h-9 rounded-full object-cover" alt="Avatar">`;
}

function setRightAvatarInitials(initials = "?") {
    if (!DOM.activeChatAvatar) return;
    DOM.activeChatAvatar.innerHTML = escapeHtml(
        (initials || "?").toString().slice(0, 2).toUpperCase(),
    );
}

function updatePinButton() {
    if (!DOM.pinChatBtn) return;

    const state = window.pinState || {};
    if (!state.type || !state.id) {
        DOM.pinChatBtn.classList.add("hidden");
        return;
    }

    DOM.pinChatBtn.classList.remove("hidden");

    const labelSpan = DOM.pinChatBtn.querySelector("span");
    const icon = DOM.pinChatBtn.querySelector("i[data-feather]");
    const pinned = !!state.isPinned;

    if (labelSpan) {
        labelSpan.textContent = pinned ? "Anpinnung aufheben" : "Chat anpinnen";
    }

    if (icon) {
        icon.setAttribute("data-feather", pinned ? "bookmark" : "bookmark");
    }

    if (window.feather) window.feather.replace();
}

async function togglePin() {
    const state = window.pinState || {};
    if (!state.type || !state.id) return;

    try {
        const res = await fetch("/chat/pin/toggle", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": csrf,
                Accept: "application/json",
            },
            body: JSON.stringify({
                type: state.type,
                id: state.id,
            }),
        });

        if (!res.ok) {
            const txt = await res.text();
            throw new Error(`HTTP ${res.status}: ${txt.slice(0, 200)}`);
        }

        const data = await res.json();
        state.isPinned = !!data.is_pinned;
        window.pinState = state;

        updatePinButton();
        // Refresh list so pin order & badges are updated
        loadUsers();
    } catch (err) {
        console.error("togglePin error:", err);
        Swal?.fire?.("Fehler", "Chat konnte nicht angepinnt werden.", "error");
    }
}

(function safeWireSearches() {
    DOM.searchInput = document.getElementById("searchInput");
    if (DOM.searchInput) {
        const debounce = (fn, d = 200) => {
            let t;
            return (...a) => {
                clearTimeout(t);
                t = setTimeout(() => fn(...a), d);
            };
        };
        DOM.searchInput.addEventListener("input", debounce(filterUsers));
    }
    DOM.messageSearchInput = document.getElementById("messageSearchInput");
    if (DOM.messageSearchInput) {
        const debounce = (fn, d = 200) => {
            let t;
            return (...a) => {
                clearTimeout(t);
                t = setTimeout(() => fn(...a), d);
            };
        };
        DOM.messageSearchInput.addEventListener(
            "input",
            debounce(filterMessages),
        );
    }
})();

function setBadge(li, count) {
    let badge = li.querySelector(".badge");
    if (!badge) {
        badge = document.createElement("span");
        badge.className =
            "badge hidden text-xs bg-red-500 text-white rounded-full px-2 py-0.5 min-w-[20px] text-center";
        li.appendChild(badge);
    }
    const n = Math.max(0, Number(count || 0));
    badge.textContent = n;
    badge.classList.toggle("hidden", n <= 0);
    return badge;
}

function startOfDay(d) {
    return new Date(d.getFullYear(), d.getMonth(), d.getDate());
}

function formatDayLabel(date) {
    const today = startOfDay(new Date());
    const d = startOfDay(date);

    const oneDayMs = 24 * 60 * 60 * 1000;
    const diffDays = Math.round((today - d) / oneDayMs);

    if (diffDays === 0) return "Heute"; // Today
    if (diffDays === 1) return "Gestern"; // Yesterday

    // e.g. "Mo., 5. Aug. 2025"
    return date.toLocaleDateString("de-DE", {
        weekday: "short",
        day: "numeric",
        month: "short",
        year: "numeric",
    });
}

function insertDateDivider(dateObj) {
    if (!DOM.chatBox) return;
    const label = formatDayLabel(dateObj);

    const wrap = document.createElement("div");
    wrap.className = "my-4 flex items-center justify-center";

    const lineLeft = document.createElement("div");
    lineLeft.className = "flex-1 h-px bg-gray-300";

    const chip = document.createElement("span");
    chip.className =
        "mx-3 text-xs px-2 py-1 rounded-full bg-gray-200 text-gray-700";
    chip.textContent = label;

    const lineRight = document.createElement("div");
    lineRight.className = "flex-1 h-px bg-gray-300";

    wrap.appendChild(lineLeft);
    wrap.appendChild(chip);
    wrap.appendChild(lineRight);

    DOM.chatBox.appendChild(wrap);
}

// ─── User/Group filter ─────────────────────────────────────────────
function filterUsers() {
    const term = DOM.searchInput.value.trim().toLowerCase();
    DOM.userList.querySelectorAll("li").forEach((li) => {
        const nameEl = li.querySelector(".font-semibold");
        const name = nameEl?.textContent.trim().toLowerCase() || "";
        li.style.display = name.includes(term) ? "" : "none";
    });
}

// ─── Message filter ────────────────────────────────────────────────
function filterMessages() {
    const term = DOM.messageSearchInput.value.trim().toLowerCase();
    DOM.chatBox.querySelectorAll("div[data-msg-id]").forEach((msgDiv) => {
        const text = msgDiv
            .querySelector(".message-content")
            .innerText.toLowerCase();
        msgDiv.style.display = text.includes(term) ? "" : "none";
    });
}

// Document upload
const attachBtn = document.getElementById("attachBtn");
const fileInput = document.getElementById("fileInput");

if (attachBtn && fileInput) {
    attachBtn.addEventListener("click", () => fileInput.click());

    fileInput.addEventListener("change", () => {
        if (!fileInput.files || fileInput.files.length === 0) return;
        sendFiles(Array.from(fileInput.files));
        fileInput.value = "";
    });
} else {
    console.warn("attachBtn/fileInput missing – upload bindings skipped");
}

fileInput.addEventListener("change", () => {
    if (!fileInput.files || fileInput.files.length === 0) return;
    sendFiles(Array.from(fileInput.files));
    fileInput.value = ""; // reset selection
});

async function sendFiles(files) {
    if (composerReadOnly) {
        Swal?.fire?.(
            "Nur Lesen",
            "In dieser Gruppe kannst du keine Dateien senden.",
            "info",
        );
        return;
    }

    const fd = new FormData();
    files.forEach((f, i) => fd.append("files[]", f));
    if (selectedUserId) fd.append("to_user_id", selectedUserId);
    if (selectedGroupId) fd.append("group_id", selectedGroupId);
    fd.append("type", "file"); // let backend branch on type

    try {
        const res = await fetch("/chat/send", {
            method: "POST",
            headers: { "X-CSRF-TOKEN": csrf },
            body: fd,
        });
        const data = await res.json();

        // Render echoes of uploaded files
        (data.messages || [data.message]).forEach((m) => {
            if (!m) return;
            seenMessageIds.add(m.id);
            addMessage(
                {
                    ...m,
                    from_user_id: window.userId,
                    from_user: {
                        id: window.userId,
                        name: window.userName,
                        image: window.userImage,
                    },
                    created_at: m.created_at || new Date().toISOString(),
                },
                true,
            );
        });
        scrollToBottom();
    } catch (e) {
        console.error("Upload failed", e);
    }
}

// --- Drag & Drop upload like ChatGPT ----------------------------------------
function initDragAndDrop() {
    if (window.__chatDnDInit) return; // prevent double-binding
    window.__chatDnDInit = true;

    const overlay = document.getElementById("chatDropOverlay");
    if (!overlay) return;

    // Helper function to safely check if the user is dragging a file
    const hasFiles = (e) => {
        return (
            e.dataTransfer &&
            e.dataTransfer.types &&
            Array.from(e.dataTransfer.types).includes("Files")
        );
    };

    let dragTimer;

    // 1. When a file is dragged over the screen, show the overlay
    document.addEventListener("dragover", (e) => {
        if (!hasFiles(e)) return;
        e.preventDefault(); // MANDATORY: Tells the browser "allow dropping here"

        overlay.classList.remove("hidden");
        overlay.classList.add("flex");

        clearTimeout(dragTimer);
    });

    // 2. If the mouse leaves the browser window, start a quick timer to hide it
    document.addEventListener("dragleave", (e) => {
        if (!hasFiles(e)) return;
        e.preventDefault();

        clearTimeout(dragTimer);
        dragTimer = setTimeout(() => {
            overlay.classList.add("hidden");
            overlay.classList.remove("flex");
        }, 100);
    });

    // 3. When the user drops the file!
    document.addEventListener("drop", (e) => {
        if (!hasFiles(e)) return;

        e.preventDefault(); // Stop the browser from opening the file full-screen

        // Hide overlay immediately
        clearTimeout(dragTimer);
        overlay.classList.add("hidden");
        overlay.classList.remove("flex");

        // Ignore if dropped inside the group avatar dropzone
        const dz = document.getElementById("dropzone");
        if (dz && dz.contains(e.target)) return;

        // Grab the dropped files
        const files = Array.from(e.dataTransfer.files || []);
        if (files.length === 0) return;

        // Ensure a chat is open
        if (!selectedUserId && !selectedGroupId) {
            Swal?.fire?.(
                "Kein Chat ausgewählt",
                "Bitte wähle zuerst einen Chat aus.",
                "info",
            );
            return;
        }

        // Send to your backend!
        sendFiles(files);
    });
}

// Voice Control and send:

// Pick a supported mime
function pickAudioMime() {
    const candidates = [
        "audio/webm;codecs=opus",
        "audio/webm",
        "audio/ogg;codecs=opus",
        "audio/ogg",
        "audio/mp4", // Safari/iOS
        "audio/mpeg", // mp3 (rare from MediaRecorder)
    ];
    for (const t of candidates) {
        if (MediaRecorder.isTypeSupported && MediaRecorder.isTypeSupported(t))
            return t;
    }
    return ""; // let browser decide
}

const voiceBtn = document.getElementById("voiceBtn");
let audioCtx = null,
    analyser = null,
    sourceNode = null;
let rafId = null,
    recTimerInterval = null,
    recStartTs = 0;

// Hidden input fallback for browsers without MediaRecorder
const voiceFileInput = document.createElement("input");
voiceFileInput.type = "file";
voiceFileInput.accept = "audio/*";
voiceFileInput.capture = "microphone"; // hint for mobile
voiceFileInput.className = "hidden";
document.body.appendChild(voiceFileInput);

voiceFileInput.addEventListener("change", () => {
    const f = voiceFileInput.files?.[0];
    if (!f) return;
    const ext = (f.name.split(".").pop() || "webm").toLowerCase();
    sendVoice(f, ext).catch((err) =>
        console.error("voice upload (fallback) failed", err),
    );
    voiceFileInput.value = "";
});

function hasMicSupport() {
    const secure = window.isSecureContext || location.hostname === "localhost";
    const md = !!(
        navigator.mediaDevices && navigator.mediaDevices.getUserMedia
    );
    const legacy =
        navigator.getUserMedia ||
        navigator.webkitGetUserMedia ||
        navigator.mozGetUserMedia ||
        navigator.msGetUserMedia;
    return secure && (md || legacy);
}

function getMicStream(constraints = { audio: true }) {
    if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
        return navigator.mediaDevices.getUserMedia(constraints);
    }
    const legacy =
        navigator.getUserMedia ||
        navigator.webkitGetUserMedia ||
        navigator.mozGetUserMedia ||
        navigator.msGetUserMedia;
    return new Promise((resolve, reject) => {
        if (!legacy) return reject(new Error("getUserMedia not available"));
        legacy.call(navigator, constraints, resolve, reject);
    });
}

function hasMediaRecorder() {
    return typeof window.MediaRecorder !== "undefined";
}

// use the fallback-aware stream getter
async function startRecording() {
    if (!hasMicSupport()) {
        console.warn(
            "No mic support or insecure context – using file picker fallback",
        );
        voiceFileInput.click();
        return;
    }
    if (!hasMediaRecorder()) {
        console.warn(
            "MediaRecorder not supported – using file picker fallback",
        );
        voiceFileInput.click();
        return;
    }

    const stream = await getMicStream({ audio: true }); // <-- not navigator.mediaDevices directly
    startRecUI(stream);

    const mimeType = pickAudioMime();
    recordedChunks = [];

    mediaRecorder = new MediaRecorder(
        stream,
        mimeType ? { mimeType } : undefined,
    );

    isRecording = true;
    voiceBtn?.classList.add("bg-red-50");
    voiceBtn && (voiceBtn.innerHTML = '<i data-feather="square"></i>');
    window.feather && feather.replace();

    mediaRecorder.ondataavailable = (e) => {
        if (e.data && e.data.size > 0) recordedChunks.push(e.data);
    };
    mediaRecorder.onstop = () => {
        stopRecUI();

        try {
            const type = mediaRecorder.mimeType || mimeType || "audio/webm";
            const blob = new Blob(recordedChunks, { type });
            let ext = "webm";
            if (type.includes("ogg")) ext = "ogg";
            else if (type.includes("mpeg")) ext = "mp3";
            else if (type.includes("mp4") || type.includes("m4a")) ext = "m4a";
            sendVoice(blob, ext);
        } finally {
            stream.getTracks().forEach((t) => t.stop());
            isRecording = false;
            voiceBtn?.classList.remove("bg-red-50");
            voiceBtn && (voiceBtn.innerHTML = '<i data-feather="mic"></i>');
            window.feather && feather.replace();
        }
    };

    mediaRecorder.start();
}

function stopRecording() {
    try {
        if (mediaRecorder && mediaRecorder.state === "recording")
            mediaRecorder.stop();
    } catch (e) {
        console.warn("stopRecording error", e);
    }
}

// ✅ click handler that actually branches to fallback
voiceBtn.addEventListener("click", () => {
    if (!hasMicSupport() || !hasMediaRecorder()) {
        voiceFileInput.click();
        return;
    }
    if (isRecording) stopRecording();
    else startRecording();
});

// Optional: long-press hold-to-record (mobile-like)
let pressTimer = null;
voiceBtn.addEventListener("mousedown", (e) => {
    if (e.button !== 0) return;
    pressTimer = setTimeout(() => {
        if (!isRecording) startRecording();
    }, 150);
});
voiceBtn.addEventListener("mouseup", () => {
    clearTimeout(pressTimer);
    if (isRecording) stopRecording();
});
voiceBtn.addEventListener("mouseleave", () => {
    clearTimeout(pressTimer);
});

async function sendVoice(blob, ext = "webm") {
    const fd = new FormData();
    fd.append("voice", blob, `voice_${Date.now()}.${ext}`);
    if (selectedUserId) fd.append("to_user_id", selectedUserId);
    if (selectedGroupId) fd.append("group_id", selectedGroupId);
    fd.append("type", "audio");

    const res = await fetch("/chat/send", {
        method: "POST",
        headers: {
            "X-CSRF-TOKEN": csrf,
            Accept: "application/json",
        },
        body: fd,
        credentials: "same-origin",
    });

    const ct = res.headers.get("content-type") || "";
    const text = await res.text();
    if (!res.ok) throw new Error(`HTTP ${res.status}: ${text.slice(0, 300)}`);
    if (!ct.includes("application/json"))
        throw new Error(`Expected JSON, got ${ct}: ${text.slice(0, 300)}`);

    const data = JSON.parse(text);
    const msg = data.message;
    if (msg?.id) seenMessageIds.add(msg.id);
    addMessage(
        {
            ...msg,
            from_user_id: window.userId,
            from_user: {
                id: window.userId,
                name: window.userName,
                image: window.userImage,
            },
        },
        true,
    );
    scrollToBottom();
}

function renderReadPopover(el, readers) {
    if (!el) return;

    if (!readers || !readers.length) {
        el.innerHTML =
            '<div class="py-1 px-1 text-xs text-slate-500">Noch von niemandem gelesen</div>';
        return;
    }

    el.innerHTML = readers
        .map((r) => {
            const name =
                `${r.name || ""} ${r.lastname || ""}`.trim() || `User ${r.id}`;

            let label = "";
            if (r.read_at) {
                const d = new Date(r.read_at);

                const hh = d.getHours().toString().padStart(2, "0");
                const mm = d.getMinutes().toString().padStart(2, "0");
                const dd = d.getDate().toString().padStart(2, "0");
                const mo = (d.getMonth() + 1).toString().padStart(2, "0");
                const yyyy = d.getFullYear();

                // e.g. "12:00 - 12.02.2025"
                label = `${hh}:${mm} - ${dd}.${mo}.${yyyy}`;
            }

            return `
                <div class="py-0.5 text-xs flex items-center justify-between gap-2">
                    ${escapeHtml(
                        name,
                    )} - <span class="text-slate-400">${escapeHtml(
                        label,
                    )}</span> 
                </div>
            `;
        })
        .join("");
}

function injectSolarNewsIntoChat(news) {
    const msg = {
        id: news.id || `news-${Date.now()}`,
        type: (news.type || "system").toLowerCase(),
        message: news.message || news.text || news.title || "",
        from_user: {
            id: 0,
            name: "System",
            image: null,
        },
        group_id: null,
        created_at: news.created_at || new Date().toISOString(),
        is_read: false,
        read_by: [],
    };

    if (msg.message && !msg.message.trim().startsWith("[Solar News]")) {
        msg.message = `[Solar News] ${msg.message}`;
    }

    // If the News view is open, render directly there
    if (selectedChatType === "news" && DOM.chatBox) {
        const created = new Date(msg.created_at);
        if (!_lastRenderedDay || !isSameDay(_lastRenderedDay, created)) {
            insertDateDivider(created);
            _lastRenderedDay = created;
        }
        addMessage(msg, false, true);
    }

    // Update unread badge on News entry
    const li = DOM.userList?.querySelector('li[data-news="1"]');
    if (li) {
        const badge = setBadge(li, 0);
        const current = parseInt(badge.textContent || "0", 10) || 0;
        setBadge(li, current + 1);
    }
}

// Subscribe to the news channel once Echo is ready
function subscribeSolarNewsChannel() {
    if (!window.Echo) {
        console.warn("Echo not ready – cannot subscribe to solar.news yet.");
        return;
    }

    window.Echo.channel("solar.news").listen(".SolarNewsPushed", (e) => {
        // adapt to your payload structure
        const payload = e.payload || e.news || e;
        injectSolarNewsIntoChat(payload);
    });
}

// Initialize the chat UI once the DOM is fully loaded
// ─── Initialize UI & wire filters ───────────────────────────────────
function initChatUI() {
    const ids = [
        "userList",
        "chatBox",
        "chatTitle",
        "messageInput",
        "sendButton",
        "notificationSound",
        "typingIndicator",
        "emojiList",
        "chatScroll",
        "pinChatBtn",
    ];
    ids.forEach((id) => (DOM[id] = document.getElementById(id)));

    if (DOM.pinChatBtn) {
        DOM.pinChatBtn.addEventListener("click", togglePin);
    }
    // Voice button enable/disable based on support
    const micOK = hasMicSupport();
    const recOK = hasMediaRecorder();

    if (!micOK) {
        // keep it enabled; our click handler already opens the file picker fallback
        voiceBtn.title =
            "Keine sichere Umgebung – es wird Datei-Aufnahme genutzt";
    } else if (!recOK) {
        voiceBtn.title =
            "Direktaufnahme nicht unterstützt – Datei-Aufnahme wird genutzt";
    }

    // Search inputs
    DOM.searchInput = document.getElementById("searchInput");
    DOM.messageSearchInput = document.getElementById("messageSearchInput");

    DOM.recHUD = document.getElementById("recHUD");
    DOM.recTimer = document.getElementById("recTimer");
    DOM.waveCanvas = document.getElementById("waveCanvas");

    // Local autogrow for the composer
    function autogrow(el) {
        el.style.height = "auto";
        el.style.height = Math.min(el.scrollHeight, 160) + "px"; // cap at ~8 lines
    }

    if (DOM.messageInput) {
        DOM.messageInput.addEventListener("input", () => {
            autogrow(DOM.messageInput);
            sendTyping();
        });

        DOM.messageInput.addEventListener("paste", (e) => {
            // Get clipboard data
            const clipboardItems = e.clipboardData || window.clipboardData;
            if (!clipboardItems) return;

            const items = clipboardItems.items;
            const filesToUpload = [];

            // Look for files (images, documents) in the clipboard
            for (let i = 0; i < items.length; i++) {
                if (items[i].kind === "file") {
                    const file = items[i].getAsFile();
                    if (file) filesToUpload.push(file);
                }
            }

            // If files were found, upload them!
            if (filesToUpload.length > 0) {
                e.preventDefault(); // Prevent the default behavior (pasting text/paths)

                if (!selectedUserId && !selectedGroupId) {
                    Swal?.fire?.(
                        "No Chat Selected",
                        "Please select a chat first.",
                        "info",
                    );
                    return;
                }

                // Send the files using your existing upload function
                sendFiles(filesToUpload);
            }
        });

        // Enter to send, Shift+Enter for newline
        DOM.messageInput.addEventListener("keydown", (e) => {
            if (e.key === "Enter" && !e.shiftKey) {
                e.preventDefault();
                sendMessage();
            }
        });
    }

    // Debounce helper
    function debounce(fn, delay = 200) {
        let timer;
        return (...args) => {
            clearTimeout(timer);
            timer = setTimeout(() => fn.apply(this, args), delay);
        };
    }

    // ✅ Make sure the floating jump button exists
    ensureJumpToLatestUI();

    // Show/hide the arrow based on scroll position
    if (DOM.chatScroll) {
        DOM.chatScroll.addEventListener(
            "scroll",
            () => {
                toggleJumpToLatest(!isNearBottom(120));
            },
            { passive: true },
        );
    }

    // Wire user list search
    if (DOM.searchInput) {
        DOM.searchInput.addEventListener("input", debounce(filterUsers));
    }

    // Wire message search
    if (DOM.messageSearchInput) {
        DOM.messageSearchInput.addEventListener(
            "input",
            debounce(filterMessages),
        );
    }

    // Emoji popover
    const emojiBtn = document.getElementById("emojiBtn");
    if (DOM.emojiList && DOM.messageInput && emojiBtn) {
        emojiBtn.addEventListener("click", (e) => {
            e.stopPropagation();
            DOM.emojiList.classList.toggle("hidden");
        });
        DOM.emojiList.querySelectorAll("span").forEach((emoji) => {
            emoji.addEventListener("click", () => {
                DOM.messageInput.value += emoji.textContent;
                DOM.messageInput.focus();
                DOM.emojiList.classList.add("hidden");
            });
        });
        document.addEventListener("click", () => {
            DOM.emojiList.classList.add("hidden");
        });
    }

    Notification.requestPermission();
    if (DOM.sendButton) DOM.sendButton.addEventListener("click", sendMessage);
    if (DOM.messageInput)
        DOM.messageInput.addEventListener("input", sendTyping);

    // Load initial data & subscribe
    // Fix: Wait for users to load before applying unread counts
    loadUsers().then(() => {
        loadUnreadCounts();
    });

    window.Echo.private(`chat.user.${userId}`)
        .listen(".message-sent", handleIncoming)
        .listen(".message-read", (e) => {
            (e.message_ids || []).forEach((id) => {
                const msgEl = DOM.chatBox.querySelector(
                    `div[data-msg-id="${id}"]`,
                );
                if (!msgEl) return;

                const timeEl = msgEl.querySelector(
                    ".message-content div.text-xs",
                );
                if (timeEl) {
                    const raw = timeEl.textContent.trim().split(" ")[0];
                    timeEl.innerHTML = `${raw} <span class="text-blue-500 ml-1">✓✓</span>`;
                }

                const pop = msgEl.querySelector(".read-info-popover");
                if (pop && e.reader) {
                    renderReadPopover(pop, [e.reader]);
                }
            });
        })
        .listen(".group-membership-updated", (e) => {
            console.log("🔄 group-membership-updated", e);
            // simplest: reload employees + groups list
            loadUsers();
        });

    // Default: always land at the last message
    requestAnimationFrame(() => scrollToBottom({ behavior: "auto" }));
    DOM.activeChatAvatar = document.getElementById("activeChatAvatar");
    DOM.activeChatName = document.getElementById("activeChatName");
    DOM.activeChatMeta = document.getElementById("activeChatMeta");
    // Share modal buttons
    const shareCancelBtn = document.getElementById("shareCancelBtn");
    const shareCancelBtnFooter = document.getElementById(
        "shareCancelBtnFooter",
    );
    const shareSendBtn = document.getElementById("shareSendBtn");

    if (shareCancelBtn)
        shareCancelBtn.addEventListener("click", closeShareModal);
    if (shareCancelBtnFooter)
        shareCancelBtnFooter.addEventListener("click", closeShareModal);
    if (shareSendBtn) shareSendBtn.addEventListener("click", submitShare);

    setupPresence();
    initDragAndDrop();
    subscribeSolarNewsChannel();
}

let composerReadOnly = false;

function setComposerReadOnly(isReadOnly) {
    composerReadOnly = !!isReadOnly;

    const composerWrapper = document.getElementById("composerWrapper"); // if you have a wrapper
    const input = DOM.messageInput;
    const send = DOM.sendButton;
    const attach = document.getElementById("attachBtn");
    const voice = document.getElementById("voiceBtn");
    const emoji = document.getElementById("emojiBtn");

    if (composerWrapper) {
        composerWrapper.classList.toggle("opacity-50", composerReadOnly);
        composerWrapper.classList.toggle(
            "pointer-events-none",
            composerReadOnly,
        );
    }

    if (input) {
        input.disabled = composerReadOnly;
        input.placeholder = composerReadOnly
            ? "Sie haben in dieser Gruppe nur Lesezugriff."
            : "Write a message…";
    }
    if (send) send.disabled = composerReadOnly;
    if (attach) attach.disabled = composerReadOnly;
    if (voice) voice.disabled = composerReadOnly;
    if (emoji) emoji.disabled = composerReadOnly;
}

function formatMMSS(ms) {
    const total = Math.floor(ms / 1000);
    const m = Math.floor(total / 60);
    const s = total % 60;
    return `${m.toString().padStart(2, "0")}:${s.toString().padStart(2, "0")}`;
}

function startRecUI(stream) {
    if (!DOM.recHUD) return;

    // Show HUD
    DOM.recHUD.classList.remove("hidden");
    voiceBtn?.classList.add("recording-pulse");

    // Timer
    recStartTs = performance.now();
    DOM.recTimer.textContent = "00:00";
    recTimerInterval = setInterval(() => {
        DOM.recTimer.textContent = formatMMSS(performance.now() - recStartTs);
    }, 200);

    // Waveform
    try {
        audioCtx = new (window.AudioContext || window.webkitAudioContext)();
        sourceNode = audioCtx.createMediaStreamSource(stream);
        analyser = audioCtx.createAnalyser();
        analyser.fftSize = 1024;

        const dataArray = new Uint8Array(analyser.fftSize);
        sourceNode.connect(analyser);

        const canvas = DOM.waveCanvas;
        const ctx = canvas?.getContext("2d");
        if (!ctx) return;

        // Retina scale
        const dpr = window.devicePixelRatio || 1;
        const cssW = canvas.clientWidth || 200;
        const cssH = canvas.clientHeight || 28;
        canvas.width = Math.floor(cssW * dpr);
        canvas.height = Math.floor(cssH * dpr);

        function draw() {
            rafId = requestAnimationFrame(draw);
            analyser.getByteTimeDomainData(dataArray);

            ctx.clearRect(0, 0, canvas.width, canvas.height);
            ctx.lineWidth = Math.max(1, dpr);
            ctx.strokeStyle = "#0ea5e9"; // sky-500
            ctx.beginPath();

            const slice = canvas.width / dataArray.length;
            let x = 0;
            for (let i = 0; i < dataArray.length; i++) {
                const v = dataArray[i] / 128.0; // ~[0,2]
                const y = (v * canvas.height) / 2; // center around mid
                i === 0 ? ctx.moveTo(x, y) : ctx.lineTo(x, y);
                x += slice;
            }
            ctx.stroke();
        }
        draw();
    } catch (err) {
        console.warn("Visualizer failed:", err);
    }
}

async function acceptGroupInvite(groupId, li) {
    try {
        const res = await fetch(`/chat/group/invite/${groupId}/accept`, {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": csrf,
                Accept: "application/json",
            },
        });
        if (!res.ok) throw new Error(`HTTP ${res.status}`);

        await loadUsers();
        Swal?.fire?.("Joined", "You joined the group.", "success");
    } catch (err) {
        console.error("acceptGroupInvite error:", err);
        Swal?.fire?.("Error", "Invitation could not be accepted.", "error");
    }
}

async function rejectGroupInvite(groupId, li) {
    try {
        const res = await fetch(`/chat/group/invite/${groupId}/decline`, {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": csrf,
                Accept: "application/json",
            },
        });
        if (!res.ok) throw new Error(`HTTP ${res.status}`);

        li?.remove();
        await loadUsers(); // <--- add this so left sidebar is consistent
        Swal?.fire?.("Declined", "Invitation was declined.", "success");
    } catch (err) {
        console.error("rejectGroupInvite error:", err);
        Swal?.fire?.("Error", "Invitation could not be declined.", "error");
    }
}

function stopRecUI() {
    // Hide HUD
    DOM.recHUD?.classList.add("hidden");
    voiceBtn?.classList.remove("recording-pulse");

    // Stop draw loop + timer
    if (rafId) cancelAnimationFrame(rafId);
    rafId = null;

    if (recTimerInterval) clearInterval(recTimerInterval);
    recTimerInterval = null;

    if (DOM.recTimer) DOM.recTimer.textContent = "00:00";

    // Clean audio graph
    try {
        sourceNode?.disconnect();
    } catch {}
    sourceNode = null;
    analyser = null;

    if (audioCtx) {
        audioCtx.close().catch(() => {});
        audioCtx = null;
    }
}

function updateUserPreview(message) {
    const preview = DOM.userList.querySelector(
        `small.preview[data-id="${message.from_user_id}"]`,
    );
    if (preview) {
        preview.textContent = message.message?.slice(0, 40) || "Neue Nachricht";
    }
}

/**
 * Loads the list of users/employees from the backend and populates the sidebar.
 */
function loadUsers() {
    fetch("/chat/employees")
        .then((res) => res.json())
        .then((data) => {
            const employees = Array.isArray(data.employees)
                ? data.employees
                : [];
            const groups = Array.isArray(data.groups) ? data.groups : [];

            // in-memory list used by typing etc.
            window.users = employees.map((u) => ({
                id: u.id,
                name: u.name,
                lastname: u.lastname,
            }));

            window.usersById = {};
            employees.forEach((u) => {
                window.usersById[u.id] = u;
            });

            function makeInitials(name = "") {
                return (
                    name
                        .split(/\s+/)
                        .filter(Boolean)
                        .map((p) => p[0].toUpperCase())
                        .join("")
                        .slice(0, 2) || "?"
                );
            }

            const normalizedGroups = groups.map((g) => {
                const label = g.name || g.context_label || "Gruppe";
                const hasAvatar = !!g.avatar;

                const lastSender = (
                    g.last_from_name ||
                    g.last_sender_name ||
                    ""
                ).trim();
                const baseMsg = g.last_msg || "Keine Nachrichten";
                const previewText = lastSender
                    ? `${lastSender}: ${baseMsg}`
                    : baseMsg;

                const status =
                    g.membership_status || g.pivot?.status || "accepted";
                const isPending = status === "pending";

                const isPinned = !!g.is_pinned;

                return {
                    ...g,
                    isGroup: true,
                    hasAvatar,
                    initials: makeInitials(label),
                    avatar_url: hasAvatar
                        ? norm(
                              IMG.GROUP_BASE,
                              g.avatar,
                              `public/images/gender/male.png`,
                          )
                        : null,
                    preview_text: previewText,
                    membership_status: status,
                    isPending,
                    is_pinned: isPinned,
                };
            });

            // for right sidebar
            window.groupsById = {};
            normalizedGroups.forEach((g) => {
                window.groupsById[g.id] = g;
            });

            const normalizedUsers = employees.map((u) => ({
                ...u,
                isGroup: false,
                image_url: isAbs(u.image)
                    ? u.image
                    : norm(
                          IMG.EMP,
                          u.image || IMG.DEFAULT_USER,
                          `${IMG.EMP}/${IMG.DEFAULT_USER}`,
                      ),
                is_pinned: !!u.is_pinned,
            }));

            // pin first, then by last_msg_at desc
            const allChats = [...normalizedUsers, ...normalizedGroups].sort(
                (a, b) => {
                    const aPinned = a.is_pinned ? 1 : 0;
                    const bPinned = b.is_pinned ? 1 : 0;
                    if (aPinned !== bPinned) {
                        return bPinned - aPinned; // pinned first
                    }

                    const A = Number(a.last_msg_at || 0);
                    const B = Number(b.last_msg_at || 0);
                    return B - A;
                },
            );

            DOM.userList.innerHTML = "";

            allChats.forEach((chat) => {
                const li = document.createElement("li");

                if (chat.isGroup) {
                    li.dataset.groupId = chat.id;
                    li.dataset.pinned = chat.is_pinned ? "1" : "0";

                    const avatarHtml = chat.hasAvatar
                        ? `<img src="${
                              chat.avatar_url
                          }" class="w-8 h-8 rounded-full border object-cover" alt="${escapeHtml(
                              chat.name,
                          )}" />`
                        : `<div class="w-8 h-8 rounded-full bg-slate-200 flex items-center justify-center text-xs font-semibold text-slate-700">
                                ${escapeHtml(chat.initials || "?")}
                           </div>`;

                    const preview = (
                        chat.preview_text || "Keine Nachrichten"
                    ).slice(0, 40);

                    const pinIcon = chat.is_pinned
                        ? `<i data-feather="bookmark" class="w-3 h-3 text-amber-500 ml-1 flex-shrink-0"></i>`
                        : "";

                    if (chat.isPending) {
                        li.className =
                            "group-entry flex items-center gap-2 p-2 bg-amber-50 border border-amber-200 rounded-md";

                        li.innerHTML = `
                            ${avatarHtml}
                            <div class="flex-1 min-w-0">
                                <div class="font-semibold text-gray-800 truncate flex items-center gap-1">
                                    ${escapeHtml(chat.name)} (Einladung)
                                    ${pinIcon}
                                </div>
                                <small class="preview text-xs text-gray-500">
                                    Du wurdest in diese Gruppe eingeladen.
                                </small>
                            </div>
                            <div class="flex flex-col gap-1 ml-2">
                                <button
                                    type="button"
                                    class="text-xs px-2 py-1 rounded-full bg-emerald-500 text-white hover:bg-emerald-600"
                                    data-action="accept"
                                >
                                    Annehmen
                                </button>
                                <button
                                    type="button"
                                    class="text-xs px-2 py-1 rounded-full bg-red-500 text-white hover:bg-red-600"
                                    data-action="reject"
                                >
                                    Ablehnen
                                </button>
                            </div>
                        `;

                        li.querySelector(
                            '[data-action="accept"]',
                        ).addEventListener("click", (e) => {
                            e.stopPropagation();
                            acceptGroupInvite(chat.id, li);
                        });

                        li.querySelector(
                            '[data-action="reject"]',
                        ).addEventListener("click", (e) => {
                            e.stopPropagation();
                            rejectGroupInvite(chat.id, li);
                        });
                    } else {
                        li.className =
                            "group-entry flex items-center gap-2 p-2 cursor-pointer hover:bg-gray-50 rounded-md transition";

                        li.innerHTML = `
                            ${avatarHtml}
                            <div class="flex-1 min-w-0">
                                <div class="font-semibold text-gray-800 truncate flex items-center gap-1">
                                    ${escapeHtml(chat.name)}
                                    ${pinIcon}
                                </div>
                                <small class="preview text-xs text-gray-500">${escapeHtml(
                                    preview,
                                )}</small>
                            </div>
                        `;

                        setBadge(li, chat.unread || 0);

                        li.addEventListener("click", () =>
                            openGroupChat(chat.id, chat.name),
                        );

                        subscribeToGroup(chat.id);
                    }

                    DOM.userList.appendChild(li);
                } else {
                    li.dataset.id = chat.id;
                    li.dataset.pinned = chat.is_pinned ? "1" : "0";
                    li.className =
                        "user-entry flex items-center gap-2 p-2 cursor-pointer hover:bg-gray-50 rounded-md transition";

                    const fullName =
                        `${chat.name || ""} ${chat.lastname || ""}`.trim() ||
                        "Unbekannt";

                    const pinIcon = chat.is_pinned
                        ? `<i data-feather="bookmark" class="w-3 h-3 text-amber-500 ml-1 flex-shrink-0"></i>`
                        : "";

                    li.innerHTML = `
                        <div class="relative">
                            <img src="${
                                chat.image_url
                            }" class="w-8 h-8 rounded-full object-cover border avatar" alt="${escapeHtml(
                                fullName,
                            )}" />
                            <span class="status-dot absolute -bottom-0.5 -right-0.5 w-2 h-2 rounded-full bg-gray-400 border border-white"></span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="font-semibold text-gray-800 truncate flex items-center gap-1">
                                ${escapeHtml(fullName)}
                                ${pinIcon}
                            </div>
                            <small class="preview text-xs text-gray-500" data-id="${
                                chat.id
                            }">
                                ${(chat.last_msg || "Keine Nachrichten").slice(
                                    0,
                                    40,
                                )}
                            </small>
                        </div>
                        <span class="badge hidden text-xs bg-red-500 text-white rounded-full px-2 py-0.5 min-w-[20px] text-center"></span>
                    `;

                    li.addEventListener("click", () => {
                        openChat(chat.id, fullName);
                        subscribeToChat(chat.id);
                    });

                    DOM.userList.appendChild(li);
                }
            });

            function injectNewsEntry() {
                if (!DOM.userList) return;

                // Avoid duplicates
                const existing =
                    DOM.userList.querySelector('li[data-news="1"]');
                if (existing) return;

                const li = document.createElement("li");
                li.dataset.news = "1";
                li.className = [
                    "news-entry",
                    "flex items-center gap-2 p-2 mb-1 cursor-pointer",
                    "rounded-md border border-dashed border-emerald-400",
                    "bg-emerald-50/80 hover:bg-emerald-100 transition",
                ].join(" ");

                li.innerHTML = `
        <div class="flex-shrink-0 w-8 h-8 rounded-full bg-emerald-500 text-white flex items-center justify-center">
            <i data-feather="zap" class="w-4 h-4"></i>
        </div>
        <div class="flex-1 min-w-0">
            <div class="font-semibold text-emerald-900 truncate">Solar News</div>
            <small class="text-[11px] text-emerald-700">
                Systemmeldungen & Produkt-Updates
            </small>
        </div>
        <span class="badge hidden text-xs bg-red-500 text-white rounded-full px-2 py-0.5 min-w-[20px] text-center"></span>
    `;

                li.addEventListener("click", openNewsChat);
                // Always on top
                DOM.userList.prepend(li);

                if (window.feather) window.feather.replace();
            }

            function injectTutorialsEntry() {
                if (!DOM.userList) return;

                // avoid duplicates
                const existing = DOM.userList.querySelector(
                    'li[data-tutorials="1"]',
                );
                if (existing) return;

                const li = document.createElement("li");
                li.dataset.tutorials = "1";
                li.className =
                    "tutorial-entry flex items-center gap-2 p-2 mb-1 cursor-pointer " +
                    "rounded-md border border-dashed border-emerald-400 bg-emerald-50/70 " +
                    "hover:bg-emerald-100 transition";

                li.innerHTML = `
                    <div class="flex-shrink-0 w-8 h-8 rounded-full bg-emerald-500 text-white flex items-center justify-center">
                        <i data-feather="book-open" class="w-4 h-4"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="font-semibold text-emerald-800 truncate">Tutorials &amp; Hilfe</div>
                        <small class="text-xs text-emerald-700">
                            Lernmodule, Video-Guides, How-Tos
                        </small>
                    </div>
                `;

                li.addEventListener("click", openTutorialChat);
                // put Tutorials on top
                DOM.userList.prepend(li);
            }

            if (window.feather) feather.replace();
            // call this at the end of loadUsers()
            injectTutorialsEntry();
            injectNewsEntry();
            loadUnreadCounts();
        })
        .catch((err) => {
            console.error("❌ Fehler beim Laden der Nutzer/Gruppen:", err);
        });
}

function moveUserToTop(userId) {
    const li = DOM.userList.querySelector(`li[data-id='${userId}']`);
    if (!li) return;

    const isPinned = li.dataset.pinned === "1";
    if (isPinned) {
        // pinned conversations always stay at very top
        DOM.userList.prepend(li);
        return;
    }

    // insert after last pinned item, if any
    const pinnedLis = DOM.userList.querySelectorAll("li[data-pinned='1']");
    if (pinnedLis.length) {
        pinnedLis[pinnedLis.length - 1].after(li);
    } else {
        DOM.userList.prepend(li);
    }
}

function moveGroupToTop(groupId) {
    const li = DOM.userList.querySelector(
        `li.group-entry[data-group-id='${groupId}']`,
    );
    if (!li) return;

    const isPinned = li.dataset.pinned === "1";
    if (isPinned) {
        DOM.userList.prepend(li);
        return;
    }

    const pinnedLis = DOM.userList.querySelectorAll("li[data-pinned='1']");
    if (pinnedLis.length) {
        pinnedLis[pinnedLis.length - 1].after(li);
    } else {
        DOM.userList.prepend(li);
    }
}

function showUnread(groupId, increment = true, setTo = null) {
    const li = DOM.userList.querySelector(
        `li.group-entry[data-group-id="${groupId}"]`,
    );
    if (!li) return;
    const badge = setBadge(li, 0);
    let val = Number(badge.textContent || 0);
    if (typeof setTo === "number") {
        val = setTo;
    } else {
        val = increment ? val + 1 : Math.max(0, val - 1);
    }
    setBadge(li, val);
}

// Initialize the chat UI once the DOM is fully loaded
document.addEventListener("DOMContentLoaded", initChatUI);
/**
 * Sets up the Laravel Echo presence channel to track online/offline status of users.
 */
function setupPresence() {
    window.Echo.join("online")
        .here((users) => {
            // Mark all currently online users
            users.forEach((u) => markOnline(u.id, true));
            console.log(
                "Online users:",
                users.map((u) => u.id),
            );
        })
        .joining((u) => {
            // Mark user as online when they join
            markOnline(u.id, true);
            console.log("User joined:", u.id);
        })
        .leaving((u) => {
            // Mark user as offline when they leave
            markOnline(u.id, false);
            console.log("User left:", u.id);
        })
        .error((error) => {
            console.error("Presence channel error:", error);
        });
}

/**
 * Updates the UI to reflect a user's online or offline status.
 * @param {number} id - The ID of the user.
 * @param {boolean} isOnline - True if the user is online, false otherwise.
 */
function markOnline(id, isOnline) {
    const dot = DOM.userList.querySelector(`li[data-id="${id}"] .status-dot`);
    const wrapper = dot?.closest("div.relative");
    if (!dot || !wrapper) return; // Exit if elements not found

    dot.classList.toggle("bg-green-500", isOnline);
    dot.classList.toggle("bg-gray-400", !isOnline);
    wrapper.classList.toggle("border-green-500", isOnline);
    wrapper.classList.toggle("border-transparent", !isOnline);
}

/**
 * Subscribes to the private chat channel between the current user and another user.
 * @param {number} otherId - The ID of the other user.
 */
window.subscribedPrivates = window.subscribedPrivates || new Set();

function subscribeToChat(otherId) {
    const [a, b] = [userId, otherId].sort((x, y) => x - y);
    const channelName = `chat.${a}.${b}`;
    if (window.subscribedPrivates.has(channelName)) return;
    window.subscribedPrivates.add(channelName);

    window.Echo.private(channelName)
        .stopListening(".message-sent") // ✅ avoid stacked handlers
        .listen(".message-sent", handleIncoming)
        .listenForWhisper("typing", (e) =>
            showTyping(otherId, e.typing_user_id),
        );
}

function showTyping(fromId, typingUserId) {
    if (selectedUserId === fromId) {
        const typingUserObj = window.users.find((u) => u.id == typingUserId);
        const typingUser = typingUserObj
            ? `${typingUserObj.name} ${typingUserObj.lastname}`
            : `Mitarbeiter ${typingUserId}`;

        DOM.typingIndicator.textContent = `${typingUser} schreibt...`;
        DOM.typingIndicator.classList.remove("hidden");

        if (typingTimeout) clearTimeout(typingTimeout);

        typingTimeout = setTimeout(() => {
            DOM.typingIndicator.classList.add("hidden");
        }, 2000);
    }
}

function getUserNameById(id) {
    const user = window.users?.find((u) => u.id == id);
    return user ? `${user.name} ${user.lastname ?? ""}`.trim() : null;
}

/**
 * Handles incoming messages received via WebSocket.
 * @param {object} e - The event payload containing message data.
 * Expected structure based on your broadcastWith():
 * e = {
 * id: ...,
 * message: "The actual message text", // This is the message text directly
 * type: ...,
 * from_user: {
 * id: 123,
 * name: "Sender Name",
 * image: "sender_avatar_url"
 * },
 * to_user_id: ...,
 * reply_to_preview: ...,
 * created_at: "2023-07-25T10:30:00.000000Z",
 * is_read: ...
 * }
 */
function handleIncoming(e) {
    console.log("📥 handleIncoming triggered", e);

    if (!e) return;

    // Normalize sender for DM + group events
    const from =
        e.from_user ||
        e.user ||
        e.sender ||
        (e.message && e.message.user) ||
        null;

    if (!from || !from.id) {
        console.warn("Incoming event without recognizable sender:", e);
        return;
    }

    // Ensure we always have e.from_user for the rest of the code
    e.from_user = from;

    // Normalize group id if present
    const normalizedGroupId =
        e.group_id ||
        e.groupId ||
        (e.group && (e.group.id || e.group.group_id)) ||
        null;

    if (normalizedGroupId && !e.group_id) {
        e.group_id = normalizedGroupId;
    }

    // Do not require e.message — voice/image may have no text
    if (seenMessageIds.has(e.id)) return;
    seenMessageIds.add(e.id);

    const isMine = from.id === userId;
    const name = (from.name || "") + (from.lastname ? " " + from.lastname : "");
    const fromId = from.id;
    const toId = e.to_user_id;

    if (!isMine) {
        playNotification();
        if (Notification.permission === "granted") {
            new Notification(`${name || "Neuer Chat"}`, {
                body:
                    e.message ||
                    (e.type === "voice" || e.type === "audio"
                        ? "🎤 Sprachnachricht"
                        : "Neue Nachricht"),
                icon: from.image || "/favicon.ico",
            });
        }
    }

    const created = new Date(e.created_at || Date.now());

    // FIX: Force Number() conversion for strict comparisons
    const isCurrentPrivateChat =
        !!selectedUserId &&
        ((Number(fromId) === Number(selectedUserId) &&
            Number(toId) === Number(userId)) ||
            (Number(fromId) === Number(userId) &&
                Number(toId) === Number(selectedUserId)));

    const isCurrentGroupChat =
        !!selectedGroupId &&
        e.group_id &&
        Number(selectedGroupId) === Number(e.group_id);

    if (isCurrentPrivateChat || isCurrentGroupChat) {
        // date divider
        if (!_lastRenderedDay || !isSameDay(_lastRenderedDay, created)) {
            insertDateDivider(created);
            _lastRenderedDay = created;
        }

        addMessage(
            {
                ...e,
                from_user_id: fromId,
                created_at: e.created_at || new Date().toISOString(),
            },
            isMine,
            !isMine,
        );

        if (!isMine && selectedUserId) {
            // DM read receipts
            markAsRead(selectedUserId);
        }

        if (DOM.typingIndicator) {
            DOM.typingIndicator.classList.add("hidden");
        }
    } else {
        // Not the currently open conversation
        if (e.group_id) {
            // group: bump unread + preview via list updater
            showUnread(e.group_id, true);
            updateUserListPreview(e);
        } else {
            // private: unread badge + preview
            showBadge(fromId);
            updatePreview(
                fromId,
                e.message ||
                    (e.type === "voice" || e.type === "audio"
                        ? "🎤 Sprachnachricht"
                        : "Neue Nachricht"),
                true,
            );
            updateUserListPreview(e);
        }
    }
}

function updateUserListPreview(payload) {
    const fromObj =
        payload.from_user ||
        payload.user ||
        payload.sender ||
        (payload.message && payload.message.user) ||
        null;

    const fromId = fromObj?.id || null;

    const groupId =
        payload.group_id ||
        payload.groupId ||
        (payload.group && (payload.group.id || payload.group.group_id)) ||
        null;

    const selector = groupId
        ? `li.group-entry[data-group-id="${groupId}"]`
        : fromId
          ? `li.user-entry[data-id="${fromId}"]`
          : null;

    if (!selector) return;

    const entry = DOM.userList.querySelector(selector);
    if (!entry) return;

    const previewEl = entry.querySelector(".preview");
    let text =
        payload.message ||
        (payload.type === "voice" || payload.type === "audio"
            ? "🎤 Sprachnachricht"
            : "Neue Nachricht") ||
        "Neue Nachricht";

    if (groupId) {
        // build "User: text"
        const from = payload.from_user || {};
        const rawName =
            (from.employee?.name || from.name || "") +
            (from.employee?.lastname || from.lastname
                ? ` ${from.employee?.lastname || from.lastname}`
                : "");
        const senderName = rawName.trim() || "Unbekannt";
        text = `${senderName}: ${text}`;
    }

    if (previewEl) previewEl.textContent = text.slice(0, 40);

    if (groupId) {
        showUnread(groupId, true);
        moveGroupToTop(groupId);
    } else if (fromId) {
        const badge = setBadge(
            entry,
            Number(entry.querySelector(".badge")?.textContent || 0) + 1,
        );
        moveUserToTop(fromId);
    }
}

// ── hook into your handleIncoming to move/top + update badges
const _origHandleIncoming =
    typeof handleIncoming === "function" ? handleIncoming : null;
window.handleIncoming = function (e) {
    if (_origHandleIncoming) _origHandleIncoming(e);

    const fromId = e?.from_user?.id;
    const isCurrentPrivate =
        !!selectedUserId &&
        ((fromId === selectedUserId && e.to_user_id === userId) ||
            (fromId === userId && e.to_user_id === selectedUserId));

    const isCurrentGroup =
        !!selectedGroupId &&
        e.group_id &&
        Number(selectedGroupId) === Number(e.group_id);

    if (!isCurrentPrivate && !isCurrentGroup) {
        updateUserListPreview(e);
    }
};

document.addEventListener(
    "click",
    () => {
        DOM.notificationSound
            .play()
            .then(() => {
                DOM.notificationSound.pause();
                DOM.notificationSound.currentTime = 0;
            })
            .catch(() => {});
    },
    { once: true },
);

/**
 * Plays the notification sound.
 */
function playNotification() {
    const snd = DOM.notificationSound;
    snd.currentTime = 0; // Rewind to start in case it's still playing
    snd.play().catch((error) => {
        // Catch and ignore play() errors (e.g., user hasn't interacted with document)
        console.warn(
            "Notification sound play failed (user interaction needed):",
            error,
        );
    });
}

/**
 * Displays or increments the unread message badge for a specific user.
 * @param {number} uid - The ID of the user.
 */
function showBadge(uid) {
    const badge = DOM.userList.querySelector(`li[data-id="${uid}"] .badge`);
    if (badge) {
        badge.textContent = +badge.textContent + 1; // Increment count
        badge.classList.remove("hidden"); // Make badge visible
    }
}

/**
 * Updates the message preview text in the user list.
 * @param {number} uid - The ID of the user.
 * @param {string} text - The new preview text.
 * @param {boolean} highlight - Whether to highlight the preview (e.g., for unread).
 */
function updatePreview(uid, text, highlight = false) {
    const el = DOM.userList.querySelector(`.preview[data-id="${uid}"]`);
    if (!el) return;
    el.textContent = text.slice(0, 40); // Truncate long messages
    el.classList.toggle("font-bold", highlight); // Apply bold for highlighting
}

/**
 * Opens a chat window with a specific user, fetching and displaying messages.
 * @param {number} otherId - The ID of the user to chat with.
 * @param {string} name - The name of the user to display in the chat header.
 */

async function openNewsChat() {
    selectedChatType = "news";
    selectedUserId = null;
    selectedGroupId = null;
    selectedTutorialId = null;

    // News is read-only
    setComposerReadOnly(true);

    // Left side highlighting
    document
        .querySelectorAll(
            ".user-entry, .group-entry, .tutorial-entry, .news-entry",
        )
        .forEach((el) => el.classList.remove("bg-gray-100", "font-bold"));

    const li = DOM.userList.querySelector('li[data-news="1"]');
    if (li) {
        li.classList.add("bg-gray-100", "font-bold");
        const badge = li.querySelector(".badge");
        if (badge) {
            badge.textContent = "0";
            badge.classList.add("hidden");
        }
    }

    // Header in the middle
    const titleEl = document.getElementById("chatTitle");
    if (titleEl) titleEl.textContent = "Solar News";

    // Right sidebar
    const sidebar = document.getElementById("rightSidebar");
    const nameEl = document.getElementById("activeChatName");
    const metaEl = document.getElementById("activeChatMeta");
    const contextBox = document.getElementById("activeChatContext");
    const addMemberBtn = document.getElementById("addMemberBtn");
    const groupActions = document.getElementById("groupActions");
    const membersList = document.getElementById("groupMembers");

    if (sidebar) sidebar.classList.remove("hidden");
    if (nameEl) nameEl.textContent = "Solar News";
    if (metaEl)
        metaEl.textContent = "Systemmeldungen, Wartung & Produkt-Updates";

    if (contextBox) {
        contextBox.classList.add("hidden");
        contextBox.innerHTML = "";
    }

    if (addMemberBtn) addMemberBtn.classList.add("hidden");
    if (groupActions) groupActions.classList.add("hidden");
    if (membersList) membersList.innerHTML = "";

    setRightAvatarInitials("N"); // simple badge avatar for News

    window.pinState = { type: null, id: null, isPinned: false };
    updatePinButton();

    _lastRenderedDay = null;
    await loadNewsMessages();
}

async function loadNewsMessages() {
    if (!DOM.chatBox) return;

    DOM.chatBox.innerHTML =
        '<div class="py-6 text-center text-slate-400 text-sm">News werden geladen…</div>';

    try {
        const res = await fetch(NEWS_FEED_ENDPOINT, {
            headers: { Accept: "application/json" },
        });
        if (!res.ok) throw new Error(`HTTP ${res.status}`);

        const data = await res.json();
        const items = Array.isArray(data)
            ? data
            : data.messages || data.news || [];

        DOM.chatBox.innerHTML = "";
        _lastRenderedDay = null;

        items
            .slice()
            .sort((a, b) => {
                const ta = new Date(a.created_at || 0).getTime();
                const tb = new Date(b.created_at || 0).getTime();
                if (ta && tb && ta !== tb) return ta - tb;
                return (a.id ?? 0) - (b.id ?? 0);
            })
            .forEach((n) => {
                const created = new Date(n.created_at || Date.now());

                if (
                    !_lastRenderedDay ||
                    !isSameDay(_lastRenderedDay, created)
                ) {
                    insertDateDivider(created);
                    _lastRenderedDay = created;
                }

                const msg = {
                    id: n.id || `news-${created.getTime()}`,
                    message: n.message || n.text || n.title || "",
                    type: (n.type || "system").toLowerCase(),
                    from_user: {
                        id: 0,
                        name: "System",
                        image: null,
                    },
                    group_id: null,
                    created_at: n.created_at || created.toISOString(),
                    is_read: !!n.is_read,
                    read_by: n.read_by || [],
                };

                // Ensure Solar News prefix for styling
                if (
                    msg.message &&
                    !msg.message.trim().startsWith("[Solar News]")
                ) {
                    msg.message = `[Solar News] ${msg.message}`;
                }

                addMessage(msg, false, false);
            });

        scrollToBottom({ behavior: "auto" });
    } catch (err) {
        console.error("loadNewsMessages error:", err);
        DOM.chatBox.innerHTML =
            '<div class="py-6 text-center text-red-500 text-sm">News konnten nicht geladen werden.</div>';
    }
}

async function openTutorialChat() {
    selectedChatType = "tutorials";
    selectedUserId = null;
    selectedGroupId = null;
    selectedTutorialId = null;

    // make composer read-only (Tutorials is just learning, no sending)
    setComposerReadOnly(true);

    // mark entry active in left sidebar
    document
        .querySelectorAll(
            ".user-entry, .group-entry, .tutorial-entry, .news-entry",
        )
        .forEach((el) => el.classList.remove("bg-gray-100", "font-bold"));

    const li = DOM.userList.querySelector('li[data-tutorials="1"]');
    if (li) li.classList.add("bg-gray-100", "font-bold");

    // header in the middle
    const titleEl = document.getElementById("chatTitle");
    if (titleEl) {
        titleEl.textContent = "Tutorials & Hilfe";
    }

    // right sidebar: plain info
    const sidebar = document.getElementById("rightSidebar");
    const nameEl = document.getElementById("activeChatName");
    const metaEl = document.getElementById("activeChatMeta");
    const contextBox = document.getElementById("activeChatContext");
    const addMemberBtn = document.getElementById("addMemberBtn");
    const groupActions = document.getElementById("groupActions");

    if (sidebar) sidebar.classList.remove("hidden");
    if (nameEl) nameEl.textContent = "Tutorials";
    if (metaEl) metaEl.textContent = "Guides, Schulungen & How-Tos";
    if (contextBox) {
        contextBox.classList.add("hidden");
        contextBox.textContent = "";
    }
    if (addMemberBtn) addMemberBtn.classList.add("hidden");
    if (groupActions) groupActions.classList.add("hidden");

    // no pinning for Tutorials
    window.pinState = { type: null, id: null, isPinned: false };
    updatePinButton();

    // avatar “T”
    setRightAvatarInitials("T");

    // load topic list
    loadTutorialTopics();
}

function openChat(otherId, userName) {
    // 1. Update State
    selectedUserId = Number(otherId); // Force number to match incoming event IDs
    selectedGroupId = null;
    selectedChatType = "user";
    selectedTutorialId = null;

    // 2. Reset UI State
    setComposerReadOnly(false); // Private chats are always writable
    _lastRenderedDay = null; // Reset date divider tracking

    // 3. Update Header Title
    const titleEl = document.getElementById("chatTitle");
    if (titleEl) titleEl.textContent = userName;

    // 4. Highlight Active User in Sidebar & Clear Badge
    // Remove active class from all
    document
        .querySelectorAll(
            ".user-entry, .group-entry, .tutorial-entry, .news-entry",
        )
        .forEach((el) => {
            el.classList.remove("bg-gray-100", "font-bold");
        });

    // Find the clicked user entry
    const current = document.querySelector(`.user-entry[data-id="${otherId}"]`);
    if (current) {
        current.classList.add("bg-gray-100", "font-bold");

        // FIX: Visually clear the unread badge immediately (Optimistic UI)
        const badge = current.querySelector(".badge");
        if (badge) {
            badge.textContent = "0";
            badge.classList.add("hidden");
        }
    }

    // 5. Clear Chat Box immediately to show loading state
    if (DOM.chatBox) {
        DOM.chatBox.innerHTML = "";
    }

    // 6. Handle Pinning Button
    const userMeta = (window.usersById && window.usersById[otherId]) || {
        is_pinned: false,
    };
    window.pinState = {
        type: "user",
        id: otherId,
        isPinned: !!userMeta.is_pinned,
    };
    updatePinButton();

    // 7. Update Right Sidebar (Details)
    const sidebar = document.getElementById("rightSidebar");
    const nameEl = document.getElementById("activeChatName");
    const metaEl = document.getElementById("activeChatMeta");
    const addMemberBtn = document.getElementById("addMemberBtn");
    const groupActions = document.getElementById("groupActions");
    const contextBox = document.getElementById("activeChatContext");

    if (sidebar && nameEl && metaEl) {
        sidebar.classList.remove("hidden");
        nameEl.textContent = userName;
        metaEl.textContent = "Direktnachricht";

        // Hide group-specific elements
        if (contextBox) {
            contextBox.classList.add("hidden");
            contextBox.innerHTML = "";
        }
        if (addMemberBtn) addMemberBtn.classList.add("hidden");
        if (groupActions) groupActions.classList.add("hidden");
        const membersList = document.getElementById("groupMembers");
        if (membersList) membersList.innerHTML = "";

        // Avatar Logic: Try to get from DOM first, then Memory, then Default
        let avatarUrl = null;

        // Try getting image from the list item we just clicked
        if (current) {
            const img = current.querySelector("img.avatar");
            if (img) avatarUrl = img.src;
        }

        // Fallback to data object
        if (!avatarUrl && window.usersById && window.usersById[otherId]) {
            const u = window.usersById[otherId];
            if (u.image) {
                avatarUrl = isAbs(u.image)
                    ? u.image
                    : norm(IMG.EMP, u.image, IMG.DEFAULT_USER);
            }
        }

        // Set Avatar in Right Sidebar
        if (avatarUrl) {
            setRightAvatarImage(avatarUrl);
        } else {
            // Generate initials
            const initials = userName
                .split(/\s+/)
                .filter(Boolean)
                .map((p) => p[0])
                .join("")
                .slice(0, 2)
                .toUpperCase();
            setRightAvatarInitials(initials);
        }
    }

    // 8. Load Data & Mark Read
    loadMessages();
    markAsRead(otherId);
}

function openGroupChat(groupId, groupName) {
    // 1. Update State
    selectedUserId = null;
    selectedGroupId = Number(groupId); // Force number
    selectedChatType = "group";
    selectedTutorialId = null;

    // 2. Fetch Group Data form Memory
    const group = window.groupsById ? window.groupsById[groupId] : null;

    // Guard: Pending Invitation
    if (group && group.isPending) {
        Swal?.fire?.("Hinweis", "Bitte zuerst die Einladung annehmen.", "info");
        return;
    }

    // 3. Reset UI State & Permissions
    _lastRenderedDay = null; // Reset date divider tracking

    // Check write permissions
    const isReadOnly = group && group.can_write === false;
    setComposerReadOnly(isReadOnly);

    // 4. Update Header Title
    const title = group?.name || groupName || `Gruppe #${groupId}`;
    const titleEl = document.getElementById("chatTitle");
    if (titleEl) titleEl.textContent = title;

    // 5. Highlight Active Group in Sidebar & Clear Badge
    // Remove active class from all
    document
        .querySelectorAll(
            ".user-entry, .group-entry, .tutorial-entry, .news-entry",
        )
        .forEach((el) => el.classList.remove("bg-gray-100", "font-bold"));

    // Find the clicked group entry
    const current = document.querySelector(
        `.group-entry[data-group-id="${groupId}"]`,
    );
    if (current) {
        current.classList.add("bg-gray-100", "font-bold");

        // FIX: Visually clear unread badge immediately (Optimistic UI)
        // Using your helper function: id, increment=false, setTo=0
        showUnread(groupId, false, 0);
    }

    // 6. Clear Chat Box
    if (DOM.chatBox) {
        DOM.chatBox.innerHTML = "";
    }

    // 7. Handle Pinning
    const groupMeta = (window.groupsById &&
        (window.groupsById[groupId] || window.groupsById[String(groupId)])) || {
        is_pinned: false,
    };
    window.pinState = {
        type: "group",
        id: groupId,
        isPinned: !!groupMeta.is_pinned,
    };
    updatePinButton();

    // 8. Update Right Sidebar (Details)
    const sidebar = document.getElementById("rightSidebar");
    const nameEl = document.getElementById("activeChatName");
    const metaEl = document.getElementById("activeChatMeta");
    const addMemberBtn = document.getElementById("addMemberBtn");
    const groupActions = document.getElementById("groupActions");
    const contextBox = document.getElementById("activeChatContext");

    if (sidebar && nameEl && metaEl) {
        sidebar.classList.remove("hidden");
        nameEl.textContent = title;
        metaEl.textContent = group?.context_label || "Gruppe";

        // Show group actions
        if (groupActions) groupActions.classList.remove("hidden");
        if (addMemberBtn) addMemberBtn.classList.remove("hidden");

        // Context Box (Customer/Product info)
        if (contextBox) {
            const parts = [];
            if (group?.customer_name) parts.push(group.customer_name);
            if (group?.customer_address) parts.push(group.customer_address);
            if (group?.product_label) parts.push(group.product_label);

            const label =
                group?.context_label || parts.filter(Boolean).join(" · ");

            if (label) {
                contextBox.classList.remove("hidden");
                contextBox.textContent = label;
            } else {
                contextBox.classList.add("hidden");
                contextBox.textContent = "";
            }
        }

        // Avatar Logic
        if (group?.avatar) {
            setRightAvatarImage(norm(IMG.GROUP_BASE, group.avatar));
        } else {
            const initials = (title || "")
                .split(/\s+/)
                .filter(Boolean)
                .map((p) => p[0])
                .join("")
                .slice(0, 2)
                .toUpperCase();
            setRightAvatarInitials(initials);
        }
    }

    // 9. Load Group Members
    const membersList = document.getElementById("groupMembers");
    if (membersList) {
        membersList.innerHTML = "";
        if (group?.members) {
            renderMembers(group.members);
        } else {
            // Fetch if not in memory
            fetch(`/chat/group/users/${groupId}`)
                .then((r) => r.json())
                .then((members) => renderMembers(members))
                .catch((err) =>
                    console.error("Fehler beim Laden der Mitglieder:", err),
                );
        }
    }

    // 10. Load Messages & Mark Read
    loadMessages(); // Uses selectedGroupId internally
    markGroupRead(groupId);
}
function renderMembers(members) {
    const list = document.getElementById("groupMembers");
    list.innerHTML = "";

    members.forEach((member) => {
        const status = member.pivot?.status || member.status || "accepted";
        if (status === "declined") return; // safety, should already be filtered backend

        const li = document.createElement("li");

        const avatar = member.avatar || "/default-avatar.png";
        const role =
            (member.pivot && member.pivot.role) || member.role || "member";

        const roleLabel =
            role === "admin"
                ? "Admin"
                : role === "moderator"
                  ? "Moderator"
                  : "Mitglied";

        const statusChip =
            status === "pending"
                ? `<span class="text-[10px] px-1.5 py-0.5 rounded-full bg-amber-100 text-amber-700 ml-1">
                        Eingeladen
                   </span>`
                : "";

        const base = window.employeeLocation || "/images/employee";

        li.innerHTML = `
            <div class="flex items-center gap-2">
                <img src="${base}/${avatar}"
                     class="w-6 h-6 rounded-full object-cover" />
                <span class="text-[10px] px-1.5 py-0.5 rounded-full bg-slate-100 text-slate-700">
                    ${roleLabel}
                </span>
                ${statusChip}
                <span class="text-sm">
                    ${member.name ?? ""} ${member.lastname ?? ""}
                </span>
            </div>
            <button
                type="button"
                class="text-xs text-red-500 hover:text-red-700"
                data-user-id="${member.id}"
            >
                Entfernen
            </button>
        `;

        const removeBtn = li.querySelector("button[data-user-id]");
        removeBtn.addEventListener("click", () => {
            if (!selectedGroupId) return;
            removeMemberFromGroup(selectedGroupId, member.id);
        });

        list.appendChild(li);
    });
}

function removeMemberFromGroup(groupId, userId) {
    if (!groupId || !userId) return;
    if (!confirm("Mitglied wirklich aus der Gruppe entfernen?")) return;

    fetch(`/chat/group/remove-member/${groupId}/${userId}`, {
        method: "DELETE",
        headers: {
            "X-CSRF-TOKEN": csrf,
            Accept: "application/json",
        },
    })
        .then((res) => {
            if (!res.ok) {
                throw new Error(`HTTP ${res.status}`);
            }
            return res.json();
        })
        .then((data) => {
            renderMembers(data.members || []);
        })
        .catch((err) => {
            console.error("remove-member error:", err);
            Swal?.fire?.(
                "Fehler",
                "Mitglied konnte nicht entfernt werden.",
                "error",
            );
        });
}

document.getElementById("addMemberBtn").addEventListener("click", () => {
    if (!selectedGroupId && selectedUserId) {
        // Convert 1-on-1 to group
        fetch("/chat/group/create-from-private", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": csrf,
            },
            body: JSON.stringify({
                user_ids: [selectedUserId], // the person in private chat
            }),
        })
            .then((res) => res.json())
            .then((data) => {
                openGroupChat(data.group.id, data.group.name, data.members);
            });
    } else if (selectedGroupId) {
        openEditGroupModal(selectedGroupId); // edit group modal
    }
});

function openEditGroupModal(groupId) {
    const modal = document.getElementById("addMemberModal");
    const select = document.getElementById("addUserSelect");

    if (!modal || !select) {
        console.error("AddMemberModal oder addUserSelect nicht gefunden");
        return;
    }

    modal.classList.remove("hidden");
    select.innerHTML = ""; // Clear previous options

    // ID der aktuellen Gruppe merken
    select.dataset.groupId = groupId;

    // globale Einstellungen zurücksetzen
    const roleEl = document.getElementById("addMemberRole");
    const histEl = document.getElementById("addMemberHistoryFromJoin");
    const writeEl = document.getElementById("addMemberCanWrite");

    if (roleEl) roleEl.value = "member";
    if (histEl) histEl.checked = true; // nur neue Nachrichten
    if (writeEl) writeEl.checked = true; // darf schreiben

    // Benutzer für Select2 laden
    fetch("/chat/employee")
        .then((res) => res.json())
        .then((users) => {
            users.forEach((user) => {
                if (!user || !user.id || user.id === userId) return;

                const option = document.createElement("option");
                option.value = user.id;
                option.textContent = `${user.name} ${user.lastname ?? ""}`;
                option.dataset.avatar = user.avatar || "/default-avatar.png";
                select.appendChild(option);
            });

            // Select2 initialisieren
            $("#addUserSelect").select2({
                dropdownParent: $("#addMemberModal"),
                templateResult: formatUserOption,
                templateSelection: formatUserOption,
                escapeMarkup: (m) => m,
            });
        })
        .catch((err) => {
            console.error(
                "Fehler beim Laden der Mitarbeiter für AddMember:",
                err,
            );
        });

    function formatUserOption(user) {
        if (!user.id) return user.text;

        const avatarUrl =
            $(user.element).data("avatar") || "/default-avatar.png";
        return `
            <div class="flex items-center gap-2">
                <img src="${avatarUrl}" class="w-6 h-6 rounded-full object-cover" />
                <span>${user.text}</span>
            </div>
        `;
    }
}

function submitAddMembers() {
    const select = document.getElementById("addUserSelect");
    const groupId = select.dataset.groupId || selectedGroupId;

    const selectedIds = Array.from(select.selectedOptions).map(
        (opt) => opt.value,
    );

    if (!groupId) {
        return Swal.fire("Fehler", "Keine Gruppe ausgewählt.", "error");
    }

    if (selectedIds.length === 0) {
        return Swal.fire(
            "Bitte mindestens einen Benutzer auswählen.",
            "",
            "warning",
        );
    }

    // Globale Einstellungen aus dem Modal
    const roleEl = document.getElementById("addMemberRole");
    const histEl = document.getElementById("addMemberHistoryFromJoin");
    const writeEl = document.getElementById("addMemberCanWrite");

    const role = roleEl?.value || "member";
    const historyFromJoin = histEl ? histEl.checked : true;
    const canWrite = writeEl ? writeEl.checked : true;

    // Backend erwartet: members: [ { id, role, history_visibility, can_write }, ... ]
    const members = selectedIds.map((id) => ({
        id: Number(id),
        role: role,
        history_visibility: historyFromJoin ? "from_join" : "all",
        can_write: !!canWrite,
    }));

    fetch(`/chat/group/add-members/${groupId}`, {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": csrf,
            Accept: "application/json",
        },
        body: JSON.stringify({ members }),
    })
        .then(async (res) => {
            if (!res.ok) {
                const txt = await res.text();
                throw new Error(`HTTP ${res.status}: ${txt.slice(0, 300)}`);
            }
            return res.json();
        })
        .then((data) => {
            closeAddMemberModal();
            if (data.members) {
                renderMembers(data.members);
            }
            Swal.fire(
                "Erfolgreich!",
                "Mitglieder wurden hinzugefügt.",
                "success",
            );
        })
        .catch((err) => {
            console.error("add-members error:", err);
            Swal.fire("Fehler", "Konnte Mitglieder nicht hinzufügen.", "error");
        });
}

function closeAddMemberModal() {
    const modal = document.getElementById("addMemberModal");
    const select = document.getElementById("addUserSelect");

    if (modal) {
        modal.classList.add("hidden");
    }

    // reset selection (Select2 + native)
    if (select) {
        // native
        [...select.options].forEach((o) => (o.selected = false));
        // Select2
        if (window.$ && $(select).data("select2")) {
            $(select).val(null).trigger("change");
        }
        delete select.dataset.groupId;
    }
}

// make sure it's also on window if needed by inline handlers
window.closeAddMemberModal = closeAddMemberModal;

window.submitAddMembers = submitAddMembers;

function difficultyLabel(level) {
    const l = (level || "").toLowerCase();
    if (l === "beginner") return "Einsteiger";
    if (l === "intermediate") return "Fortgeschritten";
    if (l === "advanced") return "Profi";
    return level || "";
}

function difficultyColor(level) {
    const l = (level || "").toLowerCase();
    if (l === "beginner") return "bg-emerald-100 text-emerald-800";
    if (l === "intermediate") return "bg-sky-100 text-sky-800";
    if (l === "advanced") return "bg-fuchsia-100 text-fuchsia-800";
    return "bg-slate-100 text-slate-700";
}

async function loadTutorialTopics(query = "") {
    if (!DOM.chatBox) return;

    DOM.chatBox.innerHTML =
        '<div class="py-6 text-center text-slate-400 text-sm">Tutorials werden geladen…</div>';

    const url =
        "/chat/tutorials" + (query ? `?q=${encodeURIComponent(query)}` : "");

    try {
        const res = await fetch(url, {
            headers: { Accept: "application/json" },
        });
        if (!res.ok) throw new Error(`HTTP ${res.status}`);
        const data = await res.json();
        renderTutorialList(data.topics || []);
    } catch (e) {
        console.error("loadTutorialTopics error:", e);
        DOM.chatBox.innerHTML =
            '<div class="py-6 text-center text-red-500 text-sm">Tutorials konnten nicht geladen werden.</div>';
    }
}

function renderTutorialList(topics) {
    if (!DOM.chatBox) return;

    if (!Array.isArray(topics) || !topics.length) {
        DOM.chatBox.innerHTML = `
            <div class="py-8 text-center text-sm text-slate-500">
                Noch keine Tutorials angelegt.
            </div>
        `;
        return;
    }

    const container = document.createElement("div");
    container.className = "space-y-4";

    const header = document.createElement("div");
    header.className = "flex items-center justify-between mb-1";

    header.innerHTML = `
        <div>
            <div class="text-xs uppercase tracking-wide text-emerald-600 font-semibold">
                Lernbereich
            </div>
            <div class="text-sm text-slate-600">
                Wähle ein Thema, um die Anleitung im Chat zu sehen.
            </div>
        </div>
        <div class="hidden sm:flex items-center gap-2 text-xs text-slate-500">
            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                Einsteiger
            </span>
            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-sky-50 text-sky-700">
                <span class="w-1.5 h-1.5 rounded-full bg-sky-400"></span>
                Fortgeschritten
            </span>
            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-fuchsia-50 text-fuchsia-700">
                <span class="w-1.5 h-1.5 rounded-full bg-fuchsia-400"></span>
                Profi
            </span>
        </div>
    `;

    container.appendChild(header);

    const list = document.createElement("div");
    list.className = "grid gap-3 md:grid-cols-2";

    topics.forEach((t) => {
        const card = document.createElement("button");
        card.type = "button";
        card.className =
            "tutorial-card text-left w-full rounded-xl border border-slate-200 bg-white " +
            "hover:border-emerald-400 hover:shadow-sm transition p-3 flex flex-col gap-2";

        const mins = t.estimated_minutes
            ? `${t.estimated_minutes} Min.`
            : "Kurzanleitung";

        card.innerHTML = `
            <div class="flex items-center justify-between gap-2">
                <div class="flex items-center gap-2 min-w-0">
                    <div class="w-7 h-7 rounded-full bg-emerald-50 text-emerald-700 flex items-center justify-center">
                        <i data-feather="play-circle" class="w-4 h-4"></i>
                    </div>
                    <div class="min-w-0">
                        <div class="text-sm font-semibold text-slate-800 truncate" title="${escapeHtml(
                            t.title || "",
                        )}">
                            ${escapeHtml(t.title || "")}
                        </div>
                        ${
                            t.prompt_label
                                ? `<div class="text-[11px] text-emerald-700 mt-0.5">
                                     ${escapeHtml(t.prompt_label)}
                                   </div>`
                                : ""
                        }
                    </div>
                </div>
                <div class="flex flex-col items-end gap-1">
                    <span class="text-[11px] text-slate-500">${mins}</span>
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium ${difficultyColor(
                        t.difficulty,
                    )}">
                        ${escapeHtml(difficultyLabel(t.difficulty))}
                    </span>
                </div>
            </div>
            ${
                t.short_intro
                    ? `<p class="text-xs text-slate-600 mt-1 line-clamp-2">${escapeHtml(
                          t.short_intro,
                      )}</p>`
                    : ""
            }
        `;

        card.addEventListener("click", () => {
            selectedTutorialId = t.id;
            loadTutorialDetail(t.id);
        });

        list.appendChild(card);
    });

    container.appendChild(list);

    DOM.chatBox.innerHTML = "";
    DOM.chatBox.appendChild(container);

    if (window.feather) feather.replace();
}

async function loadTutorialDetail(id) {
    if (!DOM.chatBox) return;

    DOM.chatBox.innerHTML =
        '<div class="py-6 text-center text-slate-400 text-sm">Tutorial wird geladen…</div>';

    try {
        const res = await fetch(`/chat/tutorials/${id}`, {
            headers: { Accept: "application/json" },
        });
        if (!res.ok) throw new Error(`HTTP ${res.status}`);
        const data = await res.json();
        renderTutorialDetail(data.topic, data.media || []);
    } catch (e) {
        console.error("loadTutorialDetail error:", e);
        DOM.chatBox.innerHTML =
            '<div class="py-6 text-center text-red-500 text-sm">Tutorial konnte nicht geladen werden.</div>';
    }
}

function renderTutorialDetail(topic, media) {
    if (!DOM.chatBox) return;

    const wrapper = document.createElement("div");
    wrapper.className = "space-y-4";

    const topBar = document.createElement("div");
    topBar.className = "flex items-center justify-between gap-2";

    const mins = topic.estimated_minutes
        ? `${topic.estimated_minutes} Minuten`
        : "";

    topBar.innerHTML = `
        <button type="button"
            class="inline-flex items-center gap-1 rounded-full border border-slate-200 bg-white px-2.5 py-1 text-xs text-slate-700 hover:bg-slate-50">
            <i data-feather="arrow-left" class="w-3 h-3"></i>
            Zurück zur Übersicht
        </button>
        <div class="flex items-center gap-2 text-[11px] text-slate-500">
            ${
                mins
                    ? `<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-slate-100">
                           <i data-feather="clock" class="w-3 h-3"></i>${mins}
                       </span>`
                    : ""
            }
            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full ${difficultyColor(
                topic.difficulty,
            )}">
                <i data-feather="target" class="w-3 h-3"></i>
                ${escapeHtml(difficultyLabel(topic.difficulty))}
            </span>
        </div>
    `;

    topBar.querySelector("button").addEventListener("click", () => {
        selectedTutorialId = null;
        loadTutorialTopics();
    });

    const titleBlock = document.createElement("div");
    titleBlock.className = "space-y-1";

    titleBlock.innerHTML = `
        <h3 class="text-lg font-semibold text-slate-900">${escapeHtml(
            topic.title || "",
        )}</h3>
        ${
            topic.short_intro
                ? `<p class="text-sm text-slate-600">${escapeHtml(
                      topic.short_intro,
                  )}</p>`
                : ""
        }
    `;

    const bodyBlock = document.createElement("div");
    bodyBlock.className =
        "mt-2 text-sm leading-relaxed text-slate-800 tutorial-body";
    // topic.body is trusted Quill HTML from your backend
    bodyBlock.innerHTML = topic.body || "";

    wrapper.appendChild(topBar);
    wrapper.appendChild(titleBlock);
    wrapper.appendChild(bodyBlock);

    // Media section
    if (Array.isArray(media) && media.length) {
        const mediaWrap = document.createElement("div");
        mediaWrap.className = "mt-4 space-y-2";

        const heading = document.createElement("div");
        heading.className = "text-xs font-semibold text-slate-700 uppercase";
        heading.textContent = "Materialien";
        mediaWrap.appendChild(heading);

        media.forEach((m) => {
            const row = document.createElement("div");
            row.className =
                "flex items-center gap-3 p-2 rounded-md border border-slate-200 bg-slate-50 text-xs";

            let icon = "file";
            if (m.media_type === "image") icon = "image";
            if (m.media_type === "video") icon = "video";
            if (m.media_type === "audio") icon = "music";

            row.innerHTML = `
                <div class="w-7 h-7 rounded-full bg-white flex items-center justify-center border border-slate-200">
                    <i data-feather="${icon}" class="w-3.5 h-3.5"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="font-medium text-slate-800 truncate">
                        ${escapeHtml(m.title || m.media_type || "Datei")}
                    </div>
                    ${
                        m.description
                            ? `<div class="text-[11px] text-slate-600 truncate">${escapeHtml(
                                  m.description,
                              )}</div>`
                            : ""
                    }
                </div>
                <a href="${m.url}" target="_blank" rel="noopener"
                    class="text-[11px] text-emerald-700 hover:text-emerald-900 whitespace-nowrap">
                    Öffnen
                </a>
            `;
            mediaWrap.appendChild(row);
        });

        wrapper.appendChild(mediaWrap);
    }

    DOM.chatBox.innerHTML = "";
    DOM.chatBox.appendChild(wrapper);

    scrollToBottom({ behavior: "auto" });
    if (window.feather) feather.replace();
}

function loadMessages() {
    const chatBox = DOM.chatBox;
    chatBox.innerHTML =
        '<div class="text-center text-gray-400">Nachrichten werden geladen…</div>';

    const url = selectedGroupId
        ? `/chat/group/fetch/${selectedGroupId}`
        : `/chat/fetch/${selectedUserId}`;

    fetch(url)
        .then((res) => res.json())
        .then((messages) => {
            chatBox.innerHTML = "";
            _lastRenderedDay = null; // reset date divider tracking on reload

            messages
                .slice()
                .sort((a, b) => {
                    const ta = new Date(a.created_at || 0).getTime();
                    const tb = new Date(b.created_at || 0).getTime();
                    if (ta && tb && ta !== tb) return ta - tb;
                    return (a.id ?? 0) - (b.id ?? 0);
                })
                .forEach((msg) => {
                    const created = new Date(msg.created_at);

                    // Day separator
                    if (
                        !_lastRenderedDay ||
                        !isSameDay(_lastRenderedDay, created)
                    ) {
                        insertDateDivider(created);
                        _lastRenderedDay = created;
                    }

                    const isMine =
                        msg.from_user_id === window.userId ||
                        msg.from_user?.id === window.userId;

                    addMessage(
                        {
                            ...msg,
                            from_user_id: msg.from_user?.id ?? msg.from_user_id,
                            created_at: msg.created_at,
                        },
                        isMine,
                        false,
                    );

                    if (msg.id) seenMessageIds.add(msg.id);
                });

            scrollToBottom();
            filterMessages();

            // 🔵 After messages are loaded, mark as read
            if (selectedGroupId) {
                // group chat
                markGroupRead(selectedGroupId);
            } else if (selectedUserId) {
                // direct chat
                markAsRead(selectedUserId);
            }
        })
        .catch((err) => {
            console.error("❌ Fehler beim Laden der Nachrichten:", err);
            chatBox.innerHTML =
                '<div class="text-center text-red-400">Fehler beim Laden.</div>';
        });
}

function scrollToBottom(opts = {}) {
    const scroller = DOM.chatScroll || document.getElementById("chatScroll");
    if (!scroller) return;

    const behavior = opts.behavior || "smooth";
    scroller.scrollTo({ top: scroller.scrollHeight, behavior });

    // When we jump to the bottom, hide the arrow
    toggleJumpToLatest(false);
}

/**
 * Adds a message to the chat box display.
 * @param {object} msg - The message object (e.g., { message: "...", created_at: "..." }).
 * @param {boolean} isMine - True if the message was sent by the current user, false otherwise.
 */

function addMessage(msg, isMine, isNew = false) {
    if (msg.type === "builder-info") {
        const profile = msg.builder || window.builderProfile || {};
        const name = profile.name || "Developer";
        const product = profile.product || "";
        const avatar = profile.avatar || window.defaultPic || "";
        const link = profile.link || "";
        const bio = profile.bio || "";

        const wrapper = document.createElement("div");
        wrapper.className = "w-full sm:max-w-lg mx-auto my-4";

        const card = document.createElement("div");
        card.className = [
            "relative overflow-hidden rounded-2xl border border-slate-200",
            "bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900",
            "text-slate-50 shadow-lg",
        ].join(" ");

        card.innerHTML = `
            <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-emerald-400 via-sky-400 to-fuchsia-500"></div>
            <div class="px-4 sm:px-6 pt-4 pb-5 sm:pb-6 flex flex-col sm:flex-row sm:items-start gap-4">
                <div class="flex-shrink-0">
                    <div class="relative">
                        <img src="${avatar}" alt="${name}"
                             class="w-16 h-16 sm:w-20 sm:h-20 rounded-2xl object-cover ring-2 ring-emerald-400/80 shadow-md">
                        <span class="absolute -bottom-1 -right-1 inline-flex items-center justify-center w-6 h-6 rounded-full bg-emerald-500 text-xs font-semibold shadow">
                            ⚡
                        </span>
                    </div>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 flex-wrap">
                        <h3 class="text-lg sm:text-xl font-semibold tracking-tight">
                            ${name}
                        </h3>
                        ${
                            product
                                ? `<span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] bg-emerald-500/15 text-emerald-200 border border-emerald-400/30">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                                        Creator of ${product}
                                   </span>`
                                : ""
                        }
                    </div>
                    <p class="mt-1 text-xs text-slate-400 uppercase tracking-[0.18em]">
                        Developer • Writer • Youth Activist
                    </p>
                    <p class="mt-3 text-sm leading-relaxed text-slate-200 whitespace-pre-line">
                        ${bio}
                    </p>
                    <div class="mt-4 flex flex-wrap gap-2">
                        ${
                            link
                                ? `<a href="${link}" target="_blank" rel="noopener noreferrer"
                                       class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-slate-100 text-slate-900 text-xs font-medium hover:bg-white transition">
                                        <i data-feather="linkedin" class="w-4 h-4"></i>
                                        LinkedIn
                                   </a>`
                                : ""
                        }
                        ${
                            product
                                ? `<span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full border border-slate-600/80 text-[11px] text-slate-200">
                                        <i data-feather="code" class="w-3 h-3"></i>
                                        Built for ${product}
                                   </span>`
                                : ""
                        }
                    </div>
                </div>
            </div>
        `;

        wrapper.appendChild(card);

        if (!DOM.chatBox) {
            console.warn("chatBox is not ready. Skipping builder-info.");
            return;
        }

        DOM.chatBox.appendChild(wrapper);
        scrollToBottom({ behavior: "smooth" });
        if (window.feather) window.feather.replace();
        return;
    }

    const from = msg.from_user || {};
    const isGroup = !!msg.group_id;

    const root = document.createElement("div");
    root.className = `group relative mb-3 w-full sm:max-w-lg ${
        isMine ? "ml-auto text-right" : "mr-auto text-left"
    }`;
    root.dataset.msgId = msg.id;

    const displayNameRaw =
        (from.employee?.name || from.name || "") +
        (from.employee?.lastname || from.lastname
            ? ` ${from.employee?.lastname || from.lastname}`
            : "");
    const displayName = displayNameRaw.trim() || "Unbekannt";

    if (!isGroup) {
        const sender = document.createElement("div");
        sender.className = "text-xs font-semibold text-gray-600 mb-0 px-1";
        sender.textContent = isMine ? "Du" : displayName;
        root.appendChild(sender);
    }

    const row = document.createElement("div");
    row.className =
        "mt-0.5 flex items-start gap-2 " +
        (isMine ? "flex-row-reverse text-right" : "flex-row text-left");

    const avatarWrap = document.createElement("div");
    avatarWrap.className = "flex-shrink-0";

    let avatarUrl = from.image || from.avatar || from.employee?.image || null;
    if (avatarUrl) {
        avatarUrl = isAbs(avatarUrl)
            ? avatarUrl
            : norm(IMG.EMP, avatarUrl, IMG.DEFAULT_USER);
    } else {
        avatarUrl = IMG.DEFAULT_USER;
        if (!isAbs(avatarUrl)) {
            avatarUrl = norm(IMG.EMP, avatarUrl, IMG.DEFAULT_USER);
        }
    }

    const avatarImg = document.createElement("img");
    avatarImg.src = avatarUrl;
    avatarImg.alt = displayName;
    avatarImg.className = "w-7 h-7 rounded-full object-cover border";
    avatarWrap.appendChild(avatarImg);

    const bubble = document.createElement("div");
    bubble.className =
        "message-bubble rounded-2xl text-sm shadow-sm px-3 py-2 " +
        (isMine ? "bg-mine" : "bg-theirs");

    if (isNew && !isMine) {
        bubble.classList.add("message-new");
    }

    const content = document.createElement("div");
    content.className = "message-content";

    if (msg.reply_to_preview) {
        const quote = document.createElement("div");
        quote.className =
            "border-l-4 pl-3 pr-1 py-1 mb-2 italic text-sm bg-white/10 text-gray-600";
        quote.innerHTML = `<i data-feather="corner-up-left" class="inline-block w-4 h-4 mr-1"></i>${escapeHtml(
            msg.reply_to_preview,
        )}`;
        quote.addEventListener("click", () =>
            replyToMessage(msg.reply_to_id, msg.reply_to_preview),
        );
        content.appendChild(quote);
    }

    const t = (msg.type || "").toLowerCase();
    let rawMsg = msg.message || "";
    let displayText = rawMsg;
    const isSolarNews =
        t === "solar-news" || rawMsg.trim().startsWith("[Solar News]");

    if (isSolarNews) {
        displayText = rawMsg.replace(/^\s*\[Solar News\]\s*/i, "");
        bubble.classList.add("news-solar");
    }

    // TEXT
    let msgText = null;
    if (rawMsg && !msg.deleted_at) {
        msgText = document.createElement("div");
        msgText.className = "message-text";

        let inner = linkify(nl2br(escapeHtml(displayText)));

        if (isSolarNews) {
            const badgeHtml = `
                <div class="news-badge">
                    <span class="news-badge-dot"></span>
                    Solar News
                </div>
            `;
            inner = badgeHtml + "<br>" + inner;
        } else if (isGroup) {
            if (t === "system") {
                inner = `<span class="font-semibold">System:</span> ` + inner;
            } else {
                const label = isMine ? "Du" : displayName;
                inner =
                    `<span class="font-semibold">${escapeHtml(
                        label,
                    )}:</span> ` + inner;
            }
        }

        msgText.innerHTML = inner;

        // Clamp long messages with "Mehr lesen"
        const plainLength = displayText.length;
        const lineCount = displayText.split(/\n/).length;
        const needsClamp = plainLength > 400 || lineCount > 6;

        if (needsClamp) {
            msgText.classList.add("sa-clamped");

            const toggleBtn = document.createElement("button");
            toggleBtn.type = "button";
            toggleBtn.className =
                "mt-1 text-xs text-emerald-700 hover:text-emerald-900 underline";
            toggleBtn.textContent = "Mehr lesen";

            toggleBtn.addEventListener("click", () => {
                const clamped = msgText.classList.toggle("sa-clamped");
                toggleBtn.textContent = clamped ? "Mehr lesen" : "Weniger";
            });

            content.appendChild(msgText);
            content.appendChild(toggleBtn);
        } else {
            content.appendChild(msgText);
        }
    }

    // VOICE / AUDIO
    const isVoice = t === "voice" || t === "audio";
    let audioUrl = msg.audio_url || msg.file_url;
    if (!audioUrl && isVoice && msg.file_path) {
        const clean = String(msg.file_path).replace(/^\/+/, "");
        audioUrl = `/storage/${clean}`;
    }
    if (isVoice && audioUrl) {
        const audio = document.createElement("audio");
        audio.controls = true;
        audio.src = audioUrl;
        audio.className = "mt-1 max-w-full";
        content.appendChild(audio);
    }

    // ATTACHMENTS
    if (Array.isArray(msg.attachments) && msg.attachments.length) {
        const list = document.createElement("div");
        list.className = "mt-1 space-y-1";

        msg.attachments.forEach((att) => {
            const isImg = att.is_image || att.mime?.startsWith?.("image/");
            if (isImg) {
                const img = document.createElement("img");
                img.src = att.url;
                img.alt = att.name || "Bild";
                img.className = "mt-1 max-w-xs rounded cursor-pointer";
                img.addEventListener("click", () =>
                    window.open(att.url, "blank", "noopener"),
                );
                list.appendChild(img);
            } else {
                const a = document.createElement("a");
                a.href = att.url;
                a.target = "blank";
                a.rel = "noopener";
                a.className =
                    "inline-flex items-center gap-1 underline break-all";
                a.innerHTML = `<i data-feather="file"></i> ${escapeHtml(
                    att.name || "Datei",
                )}`;
                list.appendChild(a);
            }
        });

        content.appendChild(list);
    }

    const time = document.createElement("div");
    time.className = `text-xs mt-1 ${
        isMine ? "text-blue-600" : "text-gray-500"
    } ${isMine ? "text-right" : "text-left"}`;

    const timeString = new Date(
        msg.created_at || Date.now(),
    ).toLocaleTimeString([], { hour: "2-digit", minute: "2-digit" });

    let ticks = "";
    if (isMine) {
        ticks = msg.is_read
            ? `<span class="ml-1">✓✓</span>`
            : `<span class="ml-1 text-gray-400">✓</span>`;
    }
    time.innerHTML = `${timeString} ${ticks}`;
    content.appendChild(time);

    const hover = document.createElement("div");
    hover.className = "hover-buttons text-xs";

    // Read info (if exists)
    if (msg.read_by && msg.read_by.length) {
        const infoBtn = document.createElement("button");
        infoBtn.type = "button";
        infoBtn.className =
            "read-info-btn text-[10px] opacity-80 hover:opacity-100 mr-1";
        infoBtn.innerHTML = `<i data-feather="info" class="w-3 h-3"></i>`;

        const pop = document.createElement("div");
        pop.className =
            "read-info-popover hidden absolute z-40 top-7 right-2 bg-white border border-slate-200 rounded-md shadow-lg px-2 py-1 text-xs text-slate-700 max-w-xs";

        renderReadPopover(pop, msg.read_by || []);

        infoBtn.addEventListener("click", (ev) => {
            ev.stopPropagation();
            pop.classList.toggle("hidden");
        });

        document.addEventListener(
            "click",
            (ev) => {
                if (!pop.contains(ev.target) && ev.target !== infoBtn) {
                    pop.classList.add("hidden");
                }
            },
            { capture: true, once: true },
        );

        hover.appendChild(infoBtn);
        bubble.appendChild(pop);
    }

    const replyBtn = document.createElement("button");
    replyBtn.className =
        "text-xs opacity-70 hover:opacity-100 hover:text-blue-500 transition-opacity";
    replyBtn.innerHTML = `<i data-feather="corner-up-left" class="w-4 h-4"></i>`;
    replyBtn.addEventListener("click", () =>
        enableReply(msg.id, msg.message || ""),
    );
    hover.appendChild(replyBtn);

    if (isMine) {
        const base = "text-xs opacity-70 hover:opacity-100 transition-opacity";

        const editBtn = document.createElement("button");
        editBtn.className = `${base} hover:text-yellow-500`;
        editBtn.innerHTML = `<i data-feather="edit-2" class="w-4 h-4"></i>`;
        editBtn.addEventListener("click", () => editMessage(msg.id));
        hover.appendChild(editBtn);

        const delBtn = document.createElement("button");
        delBtn.className = `${base} hover:text-red-500`;
        delBtn.innerHTML = `<i data-feather="trash-2" class="w-4 h-4"></i>`;
        delBtn.addEventListener("click", () => deleteMessage(msg.id));
        hover.appendChild(delBtn);
    }

    // --- Custom menu (Copy + Share) -----------------------------------------
    const menuBtn = document.createElement("button");
    menuBtn.type = "button";
    menuBtn.className =
        "msg-menu-toggle text-xs opacity-70 hover:opacity-100 hover:text-slate-700 transition-opacity";
    menuBtn.innerHTML = `<i data-feather="more-vertical" class="w-4 h-4"></i>`;

    const menu = document.createElement("div");
    menu.className =
        "msg-menu hidden absolute z-40 top-7 right-2 bg-white border border-slate-200 rounded-md shadow-lg py-1 text-xs text-slate-700 min-w-[140px]";

    menu.innerHTML = `
        <button type="button"
                class="msg-menu-item flex items-center gap-2 px-3 py-1 w-full text-left hover:bg-slate-100"
                data-action="copy">
            <i data-feather="copy" class="w-3 h-3"></i>
            <span>Kopieren</span>
        </button>
        <button type="button"
                class="msg-menu-item flex items-center gap-2 px-3 py-1 w-full text-left hover:bg-slate-100"
                data-action="share">
            <i data-feather="share-2" class="w-3 h-3"></i>
            <span>Teilen</span>
        </button>
    `;

    menuBtn.addEventListener("click", (ev) => {
        ev.stopPropagation();
        menu.classList.toggle("hidden");
    });

    const copyBtn = menu.querySelector('[data-action="copy"]');
    const shareBtn = menu.querySelector('[data-action="share"]');

    if (copyBtn) {
        copyBtn.addEventListener("click", async () => {
            try {
                if (navigator.clipboard && navigator.clipboard.writeText) {
                    await navigator.clipboard.writeText(rawMsg || "");
                    showToast("Nachricht kopiert.");
                } else {
                    // Fallback
                    const ta = document.createElement("textarea");
                    ta.value = rawMsg || "";
                    document.body.appendChild(ta);
                    ta.select();
                    document.execCommand("copy");
                    document.body.removeChild(ta);
                    showToast("Nachricht kopiert.");
                }
            } catch (err) {
                console.error("copy failed:", err);
                Swal?.fire?.("Fehler", "Kopieren nicht möglich.", "error");
            } finally {
                menu.classList.add("hidden");
            }
        });
    }

    if (shareBtn) {
        shareBtn.addEventListener("click", () => {
            openShareModal(msg.id, rawMsg || "");
            menu.classList.add("hidden");
        });
    }

    hover.appendChild(menuBtn);
    bubble.appendChild(menu);

    bubble.appendChild(content);
    bubble.appendChild(hover);

    row.appendChild(avatarWrap);
    row.appendChild(bubble);
    root.appendChild(row);

    if (!DOM.chatBox) {
        console.warn("chatBox is not ready. Skipping addMessage.");
        return;
    }

    DOM.chatBox.appendChild(root);

    const shouldStick = isMine || isNearBottom(140);
    if (shouldStick) {
        scrollToBottom({ behavior: "smooth" });
    } else {
        toggleJumpToLatest(true);
    }

    if (window.feather) window.feather.replace();
}

/**
 * Sends a message to the selected user via the backend API.
 */
let _isSending = false;

function sendMessage() {
    if (composerReadOnly) {
        Swal?.fire?.(
            "Read-only",
            "You cannot send messages in this group.",
            "info",
        );
        return;
    }

    if (_isSending) return;

    // Keep newlines, just trim edges
    const raw = (DOM.messageInput.value || "").replace(/\r/g, "");
    if (!raw.trim()) return;

    const trimmed = raw.trim();
    if (!trimmed) return;

    // If user asks about the developer, answer locally with a fancy card
    if (isBuilderQuery(trimmed)) {
        showBuilderCard(raw);
        DOM.messageInput.value = "";
        // reset height if you use auto-grow
        DOM.messageInput.style.height = "";
        replyToId = null;
        editingMessageId = null;
        document.getElementById("replyBox")?.classList.add("hidden");
        scrollToBottom();
        return;
    }

    // Must have a target
    if (!selectedUserId && !selectedGroupId) return;

    const payload = {
        message: raw, // keep \n; render will handle it
        type: "text",
        reply_to_id: replyToId || null,
        to_user_id: selectedUserId || null,
        group_id: selectedGroupId || null,
        edit_id: editingMessageId || null,
    };

    // UI lock
    _isSending = true;
    DOM.sendButton.disabled = true;

    // ⚡ optimistic bubble (only when creating, not editing)
    let tempId = null;
    if (!editingMessageId) {
        tempId = `temp-${Date.now()}`;
        const optimistic = {
            id: tempId,
            message: payload.message,
            type: "text",
            from_user_id: window.userId,
            from_user: {
                id: window.userId,
                name: window.userName,
                image: window.userImage,
            },
            created_at: new Date().toISOString(),
            is_read: false,
        };
        seenMessageIds.add(tempId);
        addMessage(optimistic, true);
    }

    fetch("/chat/send", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": csrf,
        },
        body: JSON.stringify(payload),
    })
        .then(async (res) => {
            if (!res.ok) {
                const text = await res.text();
                throw new Error(`HTTP ${res.status}: ${text.slice(0, 300)}`);
            }
            return res.json();
        })
        .then((data) => {
            const msg = data?.message;
            if (!msg?.id)
                throw new Error("No valid message returned from server.");

            // mark real id as seen (avoid duplicate when echo arrives)
            seenMessageIds.add(msg.id);

            if (editingMessageId) {
                // Update existing bubble text
                const el = DOM.chatBox.querySelector(
                    `div[data-msg-id="${editingMessageId}"] .message-content .message-text`,
                );
                if (el)
                    el.innerHTML = linkify(
                        nl2br(escapeHtml(msg.message || "")),
                    );
            } else {
                // Swap temp -> real id + time
                if (tempId) {
                    const node = DOM.chatBox.querySelector(
                        `div[data-msg-id="${tempId}"]`,
                    );
                    if (node) node.dataset.msgId = msg.id;
                }
            }

            updatePreview(
                selectedUserId || payload.group_id,
                msg.message || "",
            );
        })
        .catch((err) => {
            console.error("Error sending message:", err);

            // Rollback optimistic message on error
            if (!editingMessageId && tempId) {
                const node = DOM.chatBox.querySelector(
                    `div[data-msg-id="${tempId}"]`,
                );
                if (node) node.remove();
                seenMessageIds.delete(tempId);
            }

            Swal?.fire?.(
                "Senden fehlgeschlagen",
                err.message || "Unbekannter Fehler",
                "error",
            );
        })
        .finally(() => {
            // reset input + UI
            DOM.messageInput.value = "";
            if (typeof autogrow === "function") autogrow(DOM.messageInput);
            editingMessageId = null;
            cancelReply();

            _isSending = false;
            DOM.sendButton.disabled = false;
            scrollToBottom();
        });
}

function enableReply(msgId, previewText) {
    replyToId = msgId;
    replyPreviewText = previewText;

    const replyBox = document.getElementById("replyBox");
    replyBox.innerHTML = `
        <div class="flex justify-between items-center bg-gray-100 p-2 rounded">
            <div class="text-sm text-gray-600 truncate max-w-xs">↩️ ${previewText}</div>
            <button onclick="cancelReply()" class="text-red-500">✖</button>
        </div>
    `;
    replyBox.classList.remove("hidden");
}

function replyToMessage(id, text) {
    const original = DOM.chatBox.querySelector(`div[data-msg-id="${id}"]`);
    if (original) {
        original.classList.add("ring-2", "ring-yellow-400");
        original.scrollIntoView({ behavior: "smooth", block: "center" });

        setTimeout(() => {
            original.classList.remove("ring-2", "ring-yellow-400");
        }, 2000); // Remove highlight after 2s
    }
}

function editMessage(id) {
    const msgEl = DOM.chatBox.querySelector(`div[data-msg-id="${id}"]`);
    if (!msgEl) return;
    DOM.messageInput.classList.add("border-yellow-400", "ring-2");

    const textEl = msgEl.querySelector(".message-content .message-text");
    if (!textEl) return; // no editable text (voice/attachment-only)
    const msgText = textEl.textContent;

    DOM.messageInput.value = msgText;
    editingMessageId = id;

    console.log("Editing message:", id);
}

function deleteMessage(id) {
    if (!confirm("Möchtest du diese Nachricht löschen?")) return;

    fetch(`/chat/delete/${id}`, {
        method: "DELETE",
        headers: {
            "X-CSRF-TOKEN": csrf,
        },
    })
        .then((res) => res.json())
        .then(() => {
            const msgEl = DOM.chatBox.querySelector(`div[data-msg-id="${id}"]`);
            if (msgEl) {
                const msgText = msgEl.querySelector(
                    ".message-content > div:nth-child(2)",
                );
                if (msgText) {
                    msgText.innerHTML = `<i class="italic text-sm text-gray-500">Diese Nachricht wurde gelöscht</i>`;
                }
            }
        })
        .catch((err) => {
            console.error("Löschen fehlgeschlagen:", err);
        });
}

function cancelReply() {
    replyToId = null;
    replyPreviewText = "";
    document.getElementById("replyBox").classList.add("hidden");
}

/**
 * Sends a "typing" whisper event to the other user in the current chat.
 */
function sendTyping() {
    if (selectedUserId) {
        const [a, b] = [userId, selectedUserId].sort((x, y) => x - y);
        window.Echo.private(`chat.${a}.${b}`).whisper("typing", {
            typing_user_id: userId,
        });
    } else if (selectedGroupId) {
        window.Echo.private(`chat.group.${selectedGroupId}`).whisper("typing", {
            typing_user_id: userId,
        });
    }
}

function playNotificationSound() {
    const sound = document.getElementById("notificationSound");
    if (sound) {
        sound.currentTime = 0;
        sound.play().catch((err) => {
            console.warn("🔇 Browser blocked notification sound:", err);
        });
    }
}

// 🔄 Prevent duplicate group subscriptions
window.subscribedGroups = window.subscribedGroups || [];

/**
 * Subscribe to a private group chat channel and handle real-time messages
 */
function handleGroupMessageRead(e) {
    const msgEl = DOM.chatBox.querySelector(`div[data-msg-id="${e.chat_id}"]`);
    if (!msgEl) return;

    const pop = msgEl.querySelector(".read-info-popover");
    if (!pop) return;

    renderReadPopover(pop, e.read_by || []);
}

function subscribeToGroup(groupId) {
    if (window.subscribedGroups.includes(groupId)) return;
    window.subscribedGroups.push(groupId);

    window.Echo.private(`chat.group.${groupId}`)
        .stopListening(".message-sent")
        .listen(".message-sent", (data) => {
            if (seenMessageIds.has(data.id)) return;
            seenMessageIds.add(data.id);

            console.log("📩 New group message:", data);
            handleIncoming(data);
        })
        .listen(".message-read", handleGroupMessageRead)
        .listenForWhisper("typing", (e) =>
            showTyping(groupId, e.typing_user_id),
        );
}

/**
 * Marks all messages from a specific user as read on the backend.
 * @param {number} otherId - The ID of the user whose messages should be marked as read.
 */

function markAsRead(otherId) {
    fetch(`/chat/mark-read/${otherId}`, {
        method: "POST",
        headers: {
            "X-CSRF-TOKEN": csrf,
        },
    })
        .then((res) => {
            if (!res.ok) {
                throw new Error(`HTTP error! status: ${res.status}`);
            }
            return res.json(); // Or just return res if no JSON is expected
        })
        .then(loadUnreadCounts) // Reload unread counts after marking as read
        .catch((error) =>
            console.error("Error marking messages as read:", error),
        );
}

/**
 * Marks all messages in a group as read for the current user.
 * Triggers GroupMessageRead events on the backend.
 */
function markGroupRead(groupId) {
    if (!groupId) return;

    fetch(`/chat/group/mark-read/${groupId}`, {
        method: "POST",
        headers: {
            "X-CSRF-TOKEN": csrf,
            Accept: "application/json",
        },
    })
        .then((res) => {
            if (!res.ok) {
                throw new Error(`HTTP error! status: ${res.status}`);
            }
            return res.json().catch(() => ({}));
        })
        .then(() => {
            // Optional: reset unread badge for this group
            showUnread(groupId, false, 0);
        })
        .catch((error) => {
            console.error("Error marking group messages as read:", error);
        });
}

/**
 * Loads and updates the unread message counts for all users in the sidebar.
 */
function loadUnreadCounts() {
    fetch("/chat/unread-counts")
        .then((res) => res.json())
        .then((counts) => {
            Object.entries(counts).forEach(([id, count]) => {
                const badge = DOM.userList.querySelector(
                    `li[data-id="${id}"] .badge`,
                );
                if (badge) {
                    badge.textContent = count;
                    badge.classList.toggle("hidden", count == 0); // Hide badge if count is zero
                }
                // Also update the preview in case it was highlighted as unread
                const previewEl = DOM.userList.querySelector(
                    `.preview[data-id="${id}"]`,
                );
                if (previewEl) {
                    previewEl.classList.toggle("font-bold", count > 0);
                }
            });
        })
        .catch((error) => console.error("Error loading unread counts:", error));
}

feather.replace(); // after any new icons are injected
const createBtn = document.getElementById("createGroupBtn");
const cancelBtn = document.getElementById("cancelGroupBtn");
const submitBtn = document.getElementById("submitGroupBtn");
const modal = document.getElementById("groupModal");
const groupNameInput = document.getElementById("groupName");
const userList = document.getElementById("userCheckboxList");
const renameGroupBtn = document.getElementById("renameGroupBtn");
const leaveGroupBtn = document.getElementById("leaveGroupBtn");
const deleteGroupBtn = document.getElementById("deleteGroupBtn");

// 🚀 Open modal
createBtn.addEventListener("click", () => {
    modal.classList.remove("hidden");
    populateUserCheckboxList(); // no groupId for new group
});

// ❌ Close modal
cancelBtn.addEventListener("click", () => {
    modal.classList.add("hidden");
    groupNameInput.value = "";
    submitBtn.removeAttribute("data-id");
    userList.innerHTML = "";
});

// ✅ Save group
submitBtn.addEventListener("click", async () => {
    const groupName = groupNameInput.value.trim();
    const groupId = submitBtn.dataset.id;
    const isEdit = !!groupId;

    const selectedCheckboxes = Array.from(
        document.querySelectorAll(".member-checkbox:checked"),
    );

    if (!groupName || selectedCheckboxes.length === 0) {
        return Swal.fire({
            icon: "warning",
            title: "Fehlende Angaben",
            text: "Bitte Gruppenname und mindestens ein Mitglied auswählen.",
        });
    }

    const members = selectedCheckboxes.map((checkbox) => {
        const userId = checkbox.value;
        const roleSelect = document.getElementById(`role-${userId}`);
        const role = roleSelect?.value || "member";
        return { id: userId, role };
    });

    const customerId =
        document.getElementById("groupCustomerId")?.value || null;
    const alternativeId =
        document.getElementById("groupAlternativeId")?.value || null;
    const productId = document.getElementById("groupProductId")?.value || null;
    const leadProductListId =
        document.getElementById("leadProductListId")?.value || null;
    const avatarPath =
        document.getElementById("groupAvatarPath")?.value || null;

    const endpoint = isEdit
        ? `/chat/group/update/${groupId}`
        : "/chat/group/create";

    const method = isEdit ? "PUT" : "POST";

    try {
        const response = await fetch(endpoint, {
            method,
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": csrf,
                Accept: "application/json",
            },
            body: JSON.stringify({
                name: groupName,
                members,
                customer_id: customerId,
                alternative_id: alternativeId,
                product_id: productId,
                lead_product_list_id: leadProductListId,
                avatar: avatarPath,
            }),
        });

        const contentType = response.headers.get("content-type") || "";

        if (!response.ok) {
            const errorText = await response.text();
            throw new Error(
                `Fehler ${response.status}: ${errorText.slice(0, 300)}`,
            );
        }

        if (!contentType.includes("application/json")) {
            const html = await response.text();
            throw new Error(
                "Server hat HTML statt JSON zurückgegeben:\n" +
                    html.slice(0, 300),
            );
        }

        await response.json();

        Swal.fire({
            icon: "success",
            title: isEdit ? "Gruppe aktualisiert!" : "Gruppe erstellt!",
            timer: 1500,
            showConfirmButton: false,
        });

        modal.classList.add("hidden");
        groupNameInput.value = "";
        submitBtn.removeAttribute("data-id");
        userList.innerHTML = ""; // correct variable

        document.getElementById("groupCustomerId").value = "";
        document.getElementById("groupCustomerSearch").value = "";
        const customerLabelEl = document.getElementById("groupCustomerLabel");
        if (customerLabelEl) {
            customerLabelEl.textContent = "";
        }

        loadUsers();
    } catch (err) {
        console.error("Fehler beim Speichern:", err.message);
        Swal.fire({
            icon: "error",
            title: "Fehler beim Speichern",
            html: `<pre class="text-left text-xs">${err.message}</pre>`,
        });
    }
});

// 🧩 Load users with checkboxes and role selectors
function populateUserCheckboxList(groupId = null) {
    fetch("/chat/employee")
        .then((res) => res.json())
        .then((users) => {
            userList.innerHTML = "";

            users.forEach((user) => {
                if (!user || !user.id || user.id === userId) return; // skip current user

                const div = document.createElement("div");
                div.className = "flex items-center justify-between gap-2 mb-1";

                const name = user.name ?? "Unbekannt";
                const lastname = user.lastname ?? "";

                div.innerHTML = `
                    <div class="flex items-center gap-2">
                        <input type="checkbox" value="${user.id}" id="user-${user.id}" class="form-checkbox member-checkbox" />
                        <label for="user-${user.id}" class="text-sm">${name} ${lastname}</label>
                    </div>
                    <select name="roles[${user.id}]" id="role-${user.id}" class="text-xs px-1 py-0.5 border rounded role-select hidden">
                        <option value="member">👤 Member</option>
                        <option value="moderator">🛡 Moderator</option>
                        <option value="admin">⭐ Admin</option>
                    </select>
                `;

                div.querySelector(".member-checkbox").addEventListener(
                    "change",
                    (e) => {
                        const select = div.querySelector(".role-select");
                        select.classList.toggle("hidden", !e.target.checked);
                    },
                );

                userList.appendChild(div);
            });

            // 🛠 Pre-fill existing group data (edit mode)
            if (groupId) {
                fetch(`/chat/group/users/${groupId}`)
                    .then((res) => res.json())
                    .then((members) => {
                        members.forEach((u) => {
                            const cb = document.getElementById(`user-${u.id}`);
                            const roleSelect = document.getElementById(
                                `role-${u.id}`,
                            );
                            const role = u.pivot?.role ?? u.role ?? "member";

                            if (cb) cb.checked = true;
                            if (roleSelect) {
                                roleSelect.classList.remove("hidden");
                                roleSelect.value = role;
                            }
                        });
                    });
            }
        });
}

function openGroupEditModal(groupId) {
    if (!groupId) return;

    const g =
        (window.groupsById && window.groupsById[groupId]) ||
        (window.groupsById && window.groupsById[String(groupId)]) ||
        null;

    modal.classList.remove("hidden");
    submitBtn.dataset.id = groupId; // tells submit handler we are updating

    // Basic fields
    groupNameInput.value = g?.name || "";

    const customerIdEl = document.getElementById("groupCustomerId");
    const alternativeIdEl = document.getElementById("groupAlternativeId");
    const productIdEl = document.getElementById("groupProductId");
    const leadProductListEl = document.getElementById("leadProductListId");
    const avatarPathEl = document.getElementById("groupAvatarPath");
    const customerLabelEl = document.getElementById("groupCustomerLabel");

    if (customerIdEl) customerIdEl.value = g?.customer_id || "";
    if (alternativeIdEl) alternativeIdEl.value = g?.alternative_id || "";
    if (productIdEl) productIdEl.value = g?.product_id || "";
    if (leadProductListEl)
        leadProductListEl.value = g?.lead_product_list_id || "";
    if (avatarPathEl) avatarPathEl.value = g?.avatar || "";

    if (customerLabelEl) {
        const parts = [];
        if (g?.customer_name) parts.push(g.customer_name);
        if (g?.customer_address) parts.push(g.customer_address);
        if (g?.product_label) parts.push(g.product_label);
        customerLabelEl.textContent =
            g?.context_label || parts.filter(Boolean).join(" · ") || "";
    }

    // We do NOT try to preselect Select2 here; user can search again if needed

    // Preload member checkboxes + roles for this group
    populateUserCheckboxList(groupId);
}

renameGroupBtn?.addEventListener("click", () => {
    if (!selectedGroupId) return;
    openGroupEditModal(selectedGroupId);
});

leaveGroupBtn?.addEventListener("click", () => {
    if (!selectedGroupId) return;

    Swal.fire({
        title: "Gruppe verlassen?",
        text: "Du kannst später wieder hinzugefügt werden.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Ja, verlassen",
        cancelButtonText: "Abbrechen",
    }).then((result) => {
        if (!result.isConfirmed) return;

        fetch(`/chat/group/leave/${selectedGroupId}`, {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": csrf,
                Accept: "application/json",
            },
        })
            .then((res) => {
                if (!res.ok) throw new Error(`HTTP ${res.status}`);
                return res.json().catch(() => ({}));
            })
            .then(() => {
                // Remove group from sidebar
                const li = DOM.userList.querySelector(
                    `li.group-entry[data-group-id="${selectedGroupId}"]`,
                );
                if (li) li.remove();

                if (window.groupsById) {
                    delete window.groupsById[selectedGroupId];
                    delete window.groupsById[String(selectedGroupId)];
                }

                // Reset middle + right panels if this group was open
                if (selectedGroupId) {
                    selectedGroupId = null;
                    document.getElementById("chatTitle").textContent =
                        "Wähle einen Kontakt";
                    if (DOM.chatBox) DOM.chatBox.innerHTML = "";
                    const membersList = document.getElementById("groupMembers");
                    if (membersList) membersList.innerHTML = "";
                    const ga = document.getElementById("groupActions");
                    if (ga) ga.classList.add("hidden");
                    window.pinState = { type: null, id: null, isPinned: false };
                    updatePinButton();
                }

                Swal.fire(
                    "Verlassen",
                    "Du hast die Gruppe verlassen.",
                    "success",
                );
            })
            .catch((err) => {
                console.error("leave-group error:", err);
                Swal.fire(
                    "Fehler",
                    "Gruppe konnte nicht verlassen werden.",
                    "error",
                );
            });
    });
});

deleteGroupBtn?.addEventListener("click", () => {
    if (!selectedGroupId) return;

    Swal.fire({
        title: "Gruppe wirklich löschen?",
        text: "Diese Aktion kann nicht rückgängig gemacht werden.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Ja, löschen",
        cancelButtonText: "Abbrechen",
    }).then((result) => {
        if (!result.isConfirmed) return;

        fetch(`/chat/group/delete/${selectedGroupId}`, {
            method: "DELETE",
            headers: {
                "X-CSRF-TOKEN": csrf,
                Accept: "application/json",
            },
        })
            .then((res) => {
                if (!res.ok) throw new Error(`HTTP ${res.status}`);
                return res.json().catch(() => ({}));
            })
            .then(() => {
                // Remove from sidebar
                const li = DOM.userList.querySelector(
                    `li.group-entry[data-group-id="${selectedGroupId}"]`,
                );
                if (li) li.remove();

                if (window.groupsById) {
                    delete window.groupsById[selectedGroupId];
                    delete window.groupsById[String(selectedGroupId)];
                }

                // Reset UI if this group was open
                selectedGroupId = null;
                document.getElementById("chatTitle").textContent =
                    "Wähle einen Kontakt";
                if (DOM.chatBox) DOM.chatBox.innerHTML = "";
                const membersList = document.getElementById("groupMembers");
                if (membersList) membersList.innerHTML = "";
                const ga = document.getElementById("groupActions");
                if (ga) ga.classList.add("hidden");

                Swal.fire("Gelöscht", "Die Gruppe wurde gelöscht.", "success");
            })
            .catch((err) => {
                console.error("delete-group error:", err);
                Swal.fire(
                    "Fehler",
                    "Gruppe konnte nicht gelöscht werden.",
                    "error",
                );
            });
    });
});

// 🖼 Dropzone for group avatar
Dropzone.autoDiscover = false;

const avatarDropzone = new Dropzone("#dropzone", {
    url: "/chat/group/upload-avatar",
    maxFiles: 1,
    acceptedFiles: "image/*",
    paramName: "avatar",
    headers: {
        "X-CSRF-TOKEN": csrf,
    },
    init() {
        this.on("success", function (file, response) {
            document.getElementById("groupAvatarPath").value = response.path;
        });

        this.on("maxfilesexceeded", function (file) {
            this.removeAllFiles();
            this.addFile(file);
        });
    },
});
