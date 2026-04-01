(function () {
    // ——— Bootstrap current user + CSRF ————————————————————————————————
      window.__miniChatLoaded = (window.__miniChatLoaded || 0) + 1;
      console.log("[mini-chat] script loaded", {
          meId: document.body.dataset.userId || window.userId,
          count: window.__miniChatLoaded,
      });
    const me = {
        id: Number(document.body.dataset.userId || window.userId || 0),
        name: (document.body.dataset.userName || window.userName || "").trim(),
        image: document.body.dataset.userImage || window.userImage || "",
    };
    const csrf =
        document.querySelector('meta[name="csrf-token"]')?.content ||
        window.csrfToken ||
        "";

    // ——— DOM ————————————————————————————————————————————————
    const chatWindow = document.getElementById("chatWindow");
    const chatLauncher = document.getElementById("chatLauncher");
    const launcherBadge = document.getElementById("launcherBadge");
    const btnMinimize = document.getElementById("btnMinimize");
    const btnPop = document.getElementById("btnPop");

    const userList = document.getElementById("userList");
    const userSearch = document.getElementById("userSearch");

    const convAvatar = document.getElementById("convAvatar");
    const convName = document.getElementById("convName");
    const convStatus = document.getElementById("convStatus");
    const convOnlineDot = document.getElementById("convOnlineDot");

    const messages = document.getElementById("messages");
    const msgInput = document.getElementById("msgInput");
    const btnSend = document.getElementById("btnSend");
    const btnAttach = document.getElementById("btnAttach");
    const fileInput = document.getElementById("fileInput");
    const onlineCount = document.getElementById("onlineCount");

    // ——— State ————————————————————————————————————————————————
    let activeUserId = null; // current 1:1 chat target
    let activeGroupId = null; // (optional) group chat
    const seen = new Set(); // dedupe incoming
    const subscribedPrivates = new Set();
    const subscribedGroups = new Set();
    let users = []; // sidebar records (employees)

    // ——— Helpers ————————————————————————————————————————————————
    function escapeHtml(str = "") {
        return String(str)
            .replaceAll("&", "&amp;")
            .replaceAll("<", "&lt;")
            .replaceAll(">", "&gt;");
    }
    function linkify(t = "") {
        return t.replace(
            /\b(https?:\/\/[^\s<]+)\b/gi,
            (m) =>
                `<a href="${m}" target="_blank" rel="noopener" class="underline">${m}</a>`
        );
    }
    function nl2br(t = "") {
        return t.replace(/\n/g, "<br>");
    }
    function scrollToEnd() {
        messages.scrollTo({ top: messages.scrollHeight, behavior: "smooth" });
    }

    function updateLauncherBadge() {
        // Sum from the in-memory model first
        let total = Array.isArray(users)
            ? users.reduce((sum, u) => sum + (Number(u.unread) || 0), 0)
            : 0;

        // Fallback: sum visible DOM badges (in case you only bumped the DOM)
        if (!total) {
            document.querySelectorAll("#userList .badge").forEach((b) => {
                const n = parseInt(b.textContent || "0", 10);
                if (!Number.isNaN(n)) total += n;
            });
        }

        if (total > 0) {
            launcherBadge.textContent = total > 99 ? "99+" : String(total);
            launcherBadge.classList.remove("hidden");
        } else {
            launcherBadge.classList.add("hidden");
        }
    }

    function renderUsers(filter = "") {
        const f = filter.trim().toLowerCase();
        userList.innerHTML = "";
        const frag = document.createDocumentFragment();

        users
            .filter((u) =>
                `${u.name || ""} ${u.lastname || ""}`.toLowerCase().includes(f)
            )
            .forEach((u) => {
                const li = document.createElement("li");
                li.dataset.id = u.id;
                li.className =
                    "group cursor-pointer rounded-xl border border-transparent hover:border-gray-200 hover:bg-gray-50 p-2 transition";
                li.innerHTML = `
            <div class="flex items-center gap-2">
              <div class="relative">
                <img src="${
                    u.image || "/images/gender/users.png"
                }" class="h-9 w-9 rounded-full object-cover border"/>
                <span class="absolute -bottom-0 -right-0 ${
                    u.online ? "" : "hidden"
                } h-2.5 w-2.5 rounded-full bg-green-500 ring-2 ring-white"></span>
              </div>
              <div class="min-w-0 flex-1">
                <div class="flex items-center gap-2">
                  <span class="truncate text-sm font-medium">${escapeHtml(
                      `${u.name || ""} ${u.lastname || ""}`.trim()
                  )}</span>
                  <span class="ml-auto badge ${
                      u.unread ? "" : "hidden"
                  } inline-flex items-center justify-center rounded-full bg-blue-200 text-black text-[10px] h-5 min-w-[20px] px-1.5">${
                    u.unread || 0
                }</span>
                </div>
                <div class="truncate text-[11px] text-gray-500 preview" data-id="${
                    u.id
                }">${escapeHtml(u.last_msg || "")}</div>
              </div>
            </div>`;
                frag.appendChild(li);
            });
        userList.appendChild(frag);
        onlineCount.textContent = users.filter((u) => u.online).length;
        updateLauncherBadge();
    }

    function setActiveUser(id) {
        activeUserId = id;
        activeGroupId = null;
        const u = users.find((x) => Number(x.id) === Number(id));
        if (!u) return;
        u.unread = 0;
        updateLauncherBadge();
        renderUsers(userSearch.value);

        // header
        const initials =
            ((u.name || "").charAt(0) + (u.lastname || "").charAt(0)).trim() ||
            "—";
        convAvatar.textContent = initials;
        convName.textContent = `${u.name || ""} ${u.lastname || ""}`.trim();
        convStatus.textContent = u.online ? "Online" : "Offline";
        convOnlineDot.classList.toggle("hidden", !u.online);

        msgInput.disabled = false;
        btnSend.disabled = false;

        subscribeToPrivate(id);
        loadMessages();
        markAsRead(id);
    }

    function showBadge(uid) {
        // Update DOM chip
        const badge = userList.querySelector(`li[data-id="${uid}"] .badge`);
        if (badge) {
            const next = Number(badge.textContent || "0") + 1;
            badge.textContent = String(next);
            badge.classList.remove("hidden");
        }
        // Update the in-memory model as source of truth
        const rec = users.find((u) => Number(u.id) === Number(uid));
        if (rec) rec.unread = (Number(rec.unread) || 0) + 1;

        updateLauncherBadge();
    }

    function updatePreview(uid, text) {
        const el = userList.querySelector(`.preview[data-id="${uid}"]`);
        if (el) el.textContent = (text || "").slice(0, 40);
    }

    console.log("appendMessage mini-chat", msg.sender_label, msg.from_user);

    // ——— Rendering messages ————————————————————————————————————
    function appendMessage(msg, mine) {
        // debug: see raw message
        console.log("[mini-chat] appendMessage", {
            id: msg.id,
            sender_label: msg.sender_label,
            from_user: msg.from_user,
        });

        const wrap = document.createElement("div");
        wrap.className = `mini-chat-msg-row ${
            mine ? "mini-chat-msg-row--me" : "mini-chat-msg-row--them"
        }`;
        if (msg.id) wrap.dataset.msgId = msg.id;

        // Sender label: backend field first, then fallback to from_user
        const from = msg.from_user || msg.user || msg.fromUser || {};
        const backendLabel = (msg.sender_label || "").trim();
        const fallbackName = [from.name || "", from.lastname || ""]
            .join(" ")
            .trim();
        const senderLabel = backendLabel || fallbackName;

        // attachments
        let attHtml = "";
        if (Array.isArray(msg.attachments) && msg.attachments.length) {
            attHtml += '<div class="mini-chat-msg-attachments">';
            msg.attachments.forEach((att) => {
                const isImg = att.is_image || att.mime?.startsWith?.("image/");
                if (isImg) {
                    attHtml += `<img src="${att.url}" alt="${escapeHtml(
                        att.name || ""
                    )}" onclick="window.open('${
                        att.url
                    }','_blank','noopener')">`;
                } else {
                    attHtml += `<a href="${
                        att.url
                    }" target="_blank" rel="noopener">
                                <i class="feather icon-file"></i>${escapeHtml(
                                    att.name || "Datei"
                                )}
                            </a>`;
                }
            });
            attHtml += "</div>";
        }

        // audio
        let audioHtml = "";
        const t = (msg.type || "").toLowerCase();
        const aurl = msg.audio_url || msg.file_url;
        if ((t === "audio" || t === "voice") && aurl) {
            audioHtml = `<div class="mini-chat-msg-audio">
                        <audio controls src="${aurl}"></audio>
                     </div>`;
        }

        const safe = linkify(nl2br(escapeHtml(msg.message || "")));

        const time = new Date(msg.created_at || Date.now()).toLocaleTimeString(
            [],
            { hour: "2-digit", minute: "2-digit" }
        );

        const status = msg.status || (msg.is_read ? "read" : "sent");
        let ticks = "";
        if (mine) {
            if (status === "read" || msg.is_read || msg.read_at) {
                ticks = "✓✓";
            } else {
                ticks = "✓";
            }
        }

        const readers = msg.readers || msg.read_by || [];
        let readersLabel = "";
        if (readers.length && mine) {
            const names = readers
                .map((r) => [r.name, r.lastname].filter(Boolean).join(" "))
                .filter(Boolean);
            if (names.length) {
                const short = names.slice(0, 2).join(", ");
                const extra = names.length > 2 ? ` +${names.length - 2}` : "";
                readersLabel = `Gelesen von ${short}${extra}`;
            }
        }

        wrap.innerHTML = `
        <div class="mini-chat-msg-bubble ${
            mine ? "mini-chat-msg-bubble--me" : "mini-chat-msg-bubble--them"
        }">
            ${
                // ⬅ sender name above bubble
                !mine && senderLabel
                    ? `<div class="mini-chat-msg-sender">
                           <span>${escapeHtml(senderLabel)}</span>
                       </div>`
                    : ""
            }
            ${
                msg.reply_to_preview
                    ? `<div class="mini-chat-msg-reply">
                           ${escapeHtml(msg.reply_to_preview)}
                       </div>`
                    : ""
            }
            ${
                msg.message
                    ? `<div class="mini-chat-msg-text">${safe}</div>`
                    : ""
            }
            ${audioHtml}
            ${attHtml}
            <div class="mini-chat-msg-meta">
                ${
                    readersLabel && mine
                        ? `<span class="mini-chat-readers" title="${escapeHtml(
                              readersLabel
                          )}">${escapeHtml(readersLabel)}</span>`
                        : ""
                }
                <span>${time}</span>
                ${
                    mine && ticks
                        ? `<span class="mini-chat-ticks">${ticks}</span>`
                        : ""
                }
            </div>
        </div>
    `;

        messages.appendChild(wrap);
    }

    // ——— Backend calls —————————————————————————————————————————
    function loadUsers() {
        fetch("/chat/employees", { credentials: "same-origin" })
            .then((r) => r.json())
            .then((data) => {
                users = (data.employees || []).filter(
                    (u) => Number(u.id) !== me.id
                );
                users.forEach((u) => subscribeToPrivate(u.id));
                renderUsers();
                updateLauncherBadge();
                feather.replace?.();
            })
            .catch((e) => console.error("loadUsers failed", e));
    }

    function loadMessages() {
        messages.innerHTML = '<div class="mini-chat-empty">Loading…</div>';
        const url = activeGroupId
            ? `/chat/group/fetch/${activeGroupId}`
            : `/chat/fetch/${activeUserId}`;

        fetch(url, { credentials: "same-origin" })
            .then((r) => r.json())
            .then((list) => {
                messages.innerHTML = "";
                (list || [])
                    .sort(
                        (a, b) =>
                            new Date(a.created_at || 0) -
                            new Date(b.created_at || 0)
                    )
                    .forEach((m) => {
                        if (m.id) seen.add(m.id);
                        const mine =
                            (m.from_user?.id ?? m.from_user_id) == me.id;
                        appendMessage(m, mine);
                    });
                scrollToEnd();

                if (activeGroupId) {
                    markGroupAsRead(activeGroupId);
                } else if (activeUserId) {
                    markAsRead(activeUserId);
                }
            })
            .catch((e) => {
                messages.innerHTML =
                    '<div class="mini-chat-empty text-red-500">Failed to load.</div>';
                console.error(e);
            });
    }

    function sendText() {
        const text = msgInput.value.replace(/\r/g, "");
        if (!text.trim() || (!activeUserId && !activeGroupId)) return;

        const tempId = `temp-${Date.now()}`;
        const optimistic = {
            id: tempId,
            message: text,
            type: "text",
            from_user_id: me.id,
            created_at: new Date().toISOString(),
            is_read: false,
        };
        seen.add(tempId);
        appendMessage(optimistic, true);
        scrollToEnd();

        fetch("/chat/send", {
            method: "POST",
            credentials: "same-origin",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": csrf,
            },
            body: JSON.stringify({
                message: text,
                type: "text",
                to_user_id: activeUserId,
                group_id: activeGroupId,
                reply_to_id: null,
            }),
        })
            .then(async (r) => {
                if (!r.ok) throw new Error(await r.text());
                return r.json();
            })
            .then(({ message }) => {
                if (message?.id) seen.add(message.id);
            })
            .catch((err) => {
                console.error("send failed", err);
            })
            .finally(() => {
                msgInput.value = "";
            });
    }

    function sendFiles(files) {
        if ((!activeUserId && !activeGroupId) || !files?.length) return;
        const fd = new FormData();
        Array.from(files).forEach((f) => fd.append("files[]", f));
        if (activeUserId) fd.append("to_user_id", activeUserId);
        if (activeGroupId) fd.append("group_id", activeGroupId);
        fd.append("type", "file");

        fetch("/chat/send", {
            method: "POST",
            body: fd,
            headers: { "X-CSRF-TOKEN": csrf },
            credentials: "same-origin",
        })
            .then((r) => r.json())
            .then((data) => {
                const list = data.messages || [data.message].filter(Boolean);
                list.forEach((m) => {
                    seen.add(m.id);
                    appendMessage({ ...m, from_user_id: me.id }, true);
                });
                scrollToEnd();
            })
            .catch((e) => console.error("upload failed", e));
    }

    function markAsRead(otherId) {
        fetch(`/chat/mark-read/${otherId}`, {
            method: "POST",
            headers: { "X-CSRF-TOKEN": csrf },
            credentials: "same-origin",
        }).catch(() => {});
    }

    function markAsRead(otherId) {
        if (!otherId) return;
        fetch(`/chat/mark-read/${otherId}`, {
            method: "POST",
            headers: { "X-CSRF-TOKEN": csrf },
            credentials: "same-origin",
        }).catch(() => {});
    }

    function markGroupAsRead(groupId) {
        if (!groupId) return;
        fetch(`/chat/group/mark-read/${groupId}`, {
            method: "POST",
            headers: { "X-CSRF-TOKEN": csrf },
            credentials: "same-origin",
        }).catch(() => {});
    }

    // ——— Echo wiring ———————————————————————————————————————————
    function subscribeToPrivate(otherId) {
        const a = Math.min(me.id, Number(otherId));
        const b = Math.max(me.id, Number(otherId));
        const ch = `chat.${a}.${b}`;
        if (subscribedPrivates.has(ch)) return;
        subscribedPrivates.add(ch);

        window.Echo?.private(ch)
            .stopListening(".message-sent")
            .listen(".message-sent", handleIncoming)
            .listenForWhisper("typing", (e) => {
                if (Number(otherId) === Number(activeUserId)) {
                    /* show typing ui if needed */
                }
            });
    }

    function setupPresence() {
        window.Echo?.join("online")
            .here((list) => list.forEach((u) => flipOnline(u.id, true)))
            .joining((u) => flipOnline(u.id, true))
            .leaving((u) => flipOnline(u.id, false));
    }

    function flipOnline(id, on) {
        const entry = userList.querySelector(`li[data-id="${id}"]`);
        if (!entry) return;
        const dot = entry.querySelector("span.h-2.w-2, span.h-2.5.w-2.5");
        if (dot) dot.classList.toggle("hidden", !on);
        const record = users.find((u) => Number(u.id) === Number(id));
        if (record) {
            record.online = !!on;
            if (activeUserId == id) {
                convStatus.textContent = on ? "Online" : "Offline";
                convOnlineDot.classList.toggle("hidden", !on);
            }
        }
        onlineCount.textContent = users.filter((u) => u.online).length;
    }

    function playDing() {
        document
            .getElementById("notificationSound")
            ?.play()
            .catch(() => {});
    }

    function handleIncoming(e) {
        if (!e || !e.from_user || !e.from_user.id) return;
        if (e.id && seen.has(e.id)) return;
        if (e.id) seen.add(e.id);

        const fromId = e.from_user.id;
        const mine = Number(fromId) === me.id;

        const isCurrentPrivate =
            !!activeUserId &&
            (fromId == activeUserId ||
                (fromId == me.id && e.to_user_id == activeUserId));

        const isCurrentGroup =
            !!activeGroupId &&
            e.group_id &&
            Number(e.group_id) === Number(activeGroupId);

        if (isCurrentPrivate || isCurrentGroup) {
            appendMessage(e, mine);
            scrollToEnd();

            if (!mine) {
                if (isCurrentPrivate && activeUserId) {
                    markAsRead(activeUserId);
                }
                if (isCurrentGroup && activeGroupId) {
                    markGroupAsRead(activeGroupId);
                }
            }
        } else {
            if (!mine && e.group_id == null) {
                showBadge(fromId);
                updatePreview(
                    fromId,
                    e.message ||
                        (e.type === "audio"
                            ? "🎤 Sprachnachricht"
                            : "Neue Nachricht")
                );
                updateLauncherBadge();
            }
            playDing();
        }
    }

    // ——— UI Events —————————————————————————————————————————————
    chatLauncher.addEventListener("click", () => {
        chatWindow.classList.remove("d-none");
        chatLauncher.setAttribute("aria-expanded", "true");
        chatWindow.animate(
            [
                { transform: "translateY(12px)", opacity: 0 },
                { transform: "translateY(0)", opacity: 1 },
            ],
            { duration: 160, easing: "ease-out" }
        );
    });
    btnMinimize.addEventListener("click", () => {
        chatWindow.classList.add("d-none");
        chatLauncher.setAttribute("aria-expanded", "false");
    });
    btnPop.addEventListener("click", () => {
        chatWindow.querySelector("aside")?.classList.toggle("hidden");
    });

    userList.addEventListener("click", (e) => {
        const li = e.target.closest("li[data-id]");
        if (!li) return;
        setActiveUser(Number(li.dataset.id));
    });
    userSearch.addEventListener("input", (e) => renderUsers(e.target.value));

    msgInput.addEventListener("keydown", (e) => {
        if (e.key === "Enter" && !e.shiftKey) {
            e.preventDefault();
            sendText();
        }
    });
    btnSend.addEventListener("click", sendText);

    btnAttach?.addEventListener("click", () => fileInput?.click());
    fileInput?.addEventListener("change", () => {
        if (fileInput.files?.length) sendFiles(fileInput.files);
        fileInput.value = "";
    });

    // ——— Init ————————————————————————————————————————————————
    feather.replace?.();
    if (!me.id)
        console.warn(
            "⚠️ No authenticated user id found. Set body[data-user-id] or window.userId."
        );
    loadUsers();
    setupPresence();
})();
