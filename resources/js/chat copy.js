 
// Optional: Listen for connection status
window.Echo.connector.pusher.connection.bind("connected", () => {
    console.log("Echo connected to Reverb!");
});

window.Echo.connector.pusher.connection.bind("disconnected", () => {
    console.log("Echo disconnected from Reverb!");
});

window.Echo.connector.pusher.connection.bind("error", (err) => {
    console.error("Echo connection error:", err);
});

console.log("✅ Echo (Reverb) initialized from Chatroom:", window.Echo);

const userId = window.userId;
const csrf = window.csrfToken;
let selectedUserId = null;
let selectedGroupId = null;
let selectedChatType = null;
let DOM = {};
let typingTimeout = null; // To manage the typing indicator display
let replyToId = null;
let replyPreviewText = "";
let editingMessageId = null;
const seenMessageIds = new Set();
let mediaRecorder = null;
let recordedChunks = [];
let isRecording = false;

let _lastRenderedDay = null; // resets when you load a chat
 


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
            `<a href="${m}" target="_blank" rel="noopener noreferrer" class="underline break-words">${m}</a>`
    );
}

// convert \n to <br>
function nl2br(text = "") {
    return text.replace(/\n/g, "<br>");
}

// 🗓️ Date helpers
function isSameDay(a, b) {
  return a.getFullYear() === b.getFullYear()
    && a.getMonth() === b.getMonth()
    && a.getDate() === b.getDate();
}

function startOfDay(d) {
  return new Date(d.getFullYear(), d.getMonth(), d.getDate());
}

function formatDayLabel(date) {
  const today = startOfDay(new Date());
  const d = startOfDay(date);

  const oneDayMs = 24 * 60 * 60 * 1000;
  const diffDays = Math.round((today - d) / oneDayMs);

  if (diffDays === 0) return "Heute";       // Today
  if (diffDays === 1) return "Gestern";     // Yesterday

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
  chip.className = "mx-3 text-xs px-2 py-1 rounded-full bg-gray-200 text-gray-700";
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

    attachBtn.addEventListener("click", () => fileInput.click());

    fileInput.addEventListener("change", () => {
        if (!fileInput.files || fileInput.files.length === 0) return;
        sendFiles(Array.from(fileInput.files));
        fileInput.value = ""; // reset selection
    });

    async function sendFiles(files) {
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
                    true
                );
            });
            scrollToBottom();
        } catch (e) {
            console.error("Upload failed", e);
        }
    }


    // Voice Control and send: 

    // Pick a supported mime
    function pickAudioMime() {
        const candidates = [
            'audio/webm;codecs=opus',
            'audio/webm',
            'audio/ogg;codecs=opus',
            'audio/ogg',
            'audio/mp4',            // Safari/iOS
            'audio/mpeg'            // mp3 (rare from MediaRecorder)
        ];
        for (const t of candidates) {
            if (MediaRecorder.isTypeSupported && MediaRecorder.isTypeSupported(t)) return t;
        }
        return ''; // let browser decide
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
        sendVoice(f, ext).catch(err => console.error("voice upload (fallback) failed", err));
        voiceFileInput.value = "";
        });

       function hasMicSupport() {
           const secure =
               window.isSecureContext || location.hostname === "localhost";
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
               if (!legacy)
                   return reject(new Error("getUserMedia not available"));
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
                   "No mic support or insecure context – using file picker fallback"
               );
               voiceFileInput.click();
               return;
           }
           if (!hasMediaRecorder()) {
               console.warn(
                   "MediaRecorder not supported – using file picker fallback"
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
               mimeType ? { mimeType } : undefined
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
                   const type =
                       mediaRecorder.mimeType || mimeType || "audio/webm";
                   const blob = new Blob(recordedChunks, { type });
                   let ext = "webm";
                   if (type.includes("ogg")) ext = "ogg";
                   else if (type.includes("mpeg")) ext = "mp3";
                   else if (type.includes("mp4") || type.includes("m4a"))
                       ext = "m4a";
                   sendVoice(blob, ext);
               } finally {
                   stream.getTracks().forEach((t) => t.stop());
                   isRecording = false;
                   voiceBtn?.classList.remove("bg-red-50");
                   voiceBtn &&
                       (voiceBtn.innerHTML = '<i data-feather="mic"></i>');
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
            true
        );
        scrollToBottom();
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
    ];
    ids.forEach((id) => (DOM[id] = document.getElementById(id)));

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
    // ←–– Add this:
    function autogrow(el) {
        el.style.height = "auto";
        el.style.height = Math.min(el.scrollHeight, 160) + "px"; // cap at ~8 lines
    }

    DOM.messageInput.addEventListener("input", () => {
        autogrow(DOM.messageInput);
        sendTyping();
    });

    // Enter to send, Shift+Enter for newline (WhatsApp-like on desktop)
    DOM.messageInput.addEventListener("keydown", (e) => {
        if (e.key === "Enter" && !e.shiftKey) {
            e.preventDefault();
            sendMessage();
        }
    });
    // Debounce helper
    function debounce(fn, delay = 200) {
        let timer;
        return (...args) => {
            clearTimeout(timer);
            timer = setTimeout(() => fn.apply(this, args), delay);
        };
    }

    // Wire user list search
    DOM.searchInput.addEventListener("input", debounce(filterUsers));

    // Wire message search
    DOM.messageSearchInput.addEventListener("input", debounce(filterMessages));

    // … rest of your existing initChatUI logic (emoji, notifications, listeners) …
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
    DOM.sendButton.addEventListener("click", sendMessage);
    DOM.messageInput.addEventListener("input", sendTyping);

    // Load initial data & subscribe
    loadUsers();
    loadUnreadCounts();
    window.Echo.private(`chat.user.${userId}`)
        .listen(".message-sent", handleIncoming)
        .listen(".message-read", (e) => {
            DOM.chatBox.querySelectorAll("[data-msg-id]").forEach((msg) => {
                if (!msg.classList.contains("ml-auto")) return;
                const timeEl = msg.querySelector("div.text-xs");
                const raw = timeEl.textContent.trim().split(" ")[0];
                timeEl.innerHTML = `${raw} <span class="text-blue-500 ml-1">✓✓</span>`;
            });
        });
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
        `small.preview[data-id="${message.from_user_id}"]`
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
            const employees = data.employees || [];
            const groups = data.groups || [];

            // Combine all into one list and mark types
            const allChats = [
                ...employees.map((user) => ({ ...user, isGroup: false })),
                ...groups.map((group) => ({ ...group, isGroup: true })),
            ];

            // Sort by last_msg_time descending
            allChats.sort((a, b) => {
                const aTime = new Date(a.last_msg_time || 0).getTime();
                const bTime = new Date(b.last_msg_time || 0).getTime();
                return bTime - aTime;
            });

            // Clear current list
            DOM.userList.innerHTML = "";

            // Render each user/group
            allChats.forEach((chat) => {
                const li = document.createElement("li");

                if (chat.isGroup) {
                    // ─── GROUP ENTRY ─────────────────────────────
                    li.dataset.groupId = chat.id;
                    li.className =
                        "group-entry flex items-center gap-2 p-2 cursor-pointer hover:bg-gray-50 rounded-md transition";
                    li.innerHTML = `
                        <div class="relative w-full flex items-center gap-2">
                            <img src="${defaultLocation}/${
                                chat.avatar ?? defaultPic
                            }"
                                class="w-8 h-8 rounded-full border object-cover" />
                            <div class="flex-1">
                                <div class="font-semibold text-gray-800">${
                                    chat.name
                                }</div>
                                <small class="preview text-xs text-gray-500">${
                                    chat.last_msg || "Keine Nachrichten"
                                }</small>
                            </div>
                            <div class="flex gap-1">
                                <button class="editGroupBtn text-yellow-500 hover:text-yellow-600" data-id="${
                                    chat.id
                                }" data-name="${chat.name}">
                                    <i data-feather="edit-2"></i>
                                </button>
                                <button class="deleteGroupBtn text-red-500 hover:text-red-600" data-id="${
                                    chat.id
                                }">
                                    <i data-feather="trash-2"></i>
                                </button>
                            </div>
                        </div>
                    `;

                    li.addEventListener("click", () =>
                        openGroupChat(chat.id, chat.name)
                    );
                    DOM.userList.appendChild(li);

                    // ✅ Subscribe to group realtime channel
                    subscribeToGroup(chat.id);
                } else {
                    // ─── USER ENTRY ──────────────────────────────
                    li.dataset.id = chat.id;
                    li.className =
                        "user-entry flex items-center gap-2 p-2 cursor-pointer hover:bg-gray-50 rounded-md transition";
                    li.innerHTML = `
                        <div class="relative">
                            <img src="${
                                chat.image
                            }" class="avatar w-8 h-8 rounded-full border object-cover" />
                            <span class="status-dot absolute bottom-0 right-0 w-3 h-3 rounded-full border-2 border-white bg-gray-400"></span>
                        </div>
                        <div class="flex-1">
                            <div class="font-semibold text-gray-800">${
                                chat.name
                            } ${chat.lastname}</div>
                            <small class="preview text-xs text-gray-500 preview" data-id="${
                                chat.id
                            }">
                                ${
                                    chat.last_msg?.slice(0, 40) ||
                                    "Keine Nachrichten"
                                }
                            </small>
                        </div>
                        <span class="badge hidden text-xs bg-red-500 text-white rounded-full px-2 py-0.5 min-w-[20px] text-center">0</span>
                    `;

                    li.addEventListener("click", () =>
                        openChat(chat.id, `${chat.name} ${chat.lastname}`)
                    );
                    DOM.userList.appendChild(li);

                    // ✅ Subscribe to 1-on-1 chat channel regardless of click
                    if (chat.id !== userId) {
                        subscribeToChat(chat.id);
                    }
                }
            });

            // ✅ Setup presence indicators
            setupPresence();

            // ✅ Refresh feather icons
            if (window.feather) feather.replace();
        })
        .catch((err) => {
            console.error("❌ Fehler beim Laden der Nutzer/Gruppen:", err);
        });
}


function moveUserToTop(userId) {
    const li = DOM.userList.querySelector(`li[data-id='${userId}']`);
    if (li) DOM.userList.prepend(li);
}

function moveGroupToTop(groupId) {
    const li = DOM.userList.querySelector(`li[data-group-id='${groupId}']`);
    if (li) DOM.userList.prepend(li);
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
                users.map((u) => u.id)
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
            showTyping(otherId, e.typing_user_id)
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

    // ❌ don't require e.message — voice/image may have no text
    if (!e || !e.from_user || !e.from_user.id) return;

    // ✅ de-dup
    if (seenMessageIds.has(e.id)) return;
    seenMessageIds.add(e.id);

    const isMine = e.from_user.id === userId;
    const name =
        (e.from_user.name || "") +
        (e.from_user.lastname ? " " + e.from_user.lastname : "");
    const fromId = e.from_user.id;
    const toId = e.to_user_id;

    if (!isMine) {
        playNotification();
        if (Notification.permission === "granted") {
            new Notification(`${name || "Neuer Chat"}`, {
                body:
                    e.message ||
                    (e.type === "voice"
                        ? "🎤 Sprachnachricht"
                        : "Neue Nachricht"),
                icon: e.from_user.image || "/favicon.ico",
            });
        }
    }

    const created = new Date(e.created_at || Date.now());

    const isCurrentPrivateChat =
        !!selectedUserId &&
        ((fromId === selectedUserId && toId === userId) ||
            (fromId === userId && toId === selectedUserId));

    const isCurrentGroupChat =
        !!selectedGroupId && e.group_id && selectedGroupId == e.group_id;

    if (isCurrentPrivateChat || isCurrentGroupChat) {
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
            isMine
        );

        if (!isMine && selectedUserId) markAsRead(selectedUserId);
        DOM.typingIndicator.classList.add("hidden");
    } else {
        if (e.group_id) {
            showUnread(e.group_id, true);
        } else {
            showBadge(fromId);
            updatePreview(
                fromId,
                e.message ||
                    (e.type === "voice"
                        ? "🎤 Sprachnachricht"
                        : "Neue Nachricht"),
                true
            );
            updateUserListPreview(e);
        }
    }
}

function updateUserListPreview(message) {
    const fromId = message.from_user?.id;
    const groupId = message.group_id;

    let entrySelector = groupId
        ? `[data-group-id="${groupId}"]`
        : `[data-id="${fromId}"]`;

    const entry = document.querySelector(entrySelector);
    if (!entry) return;

    const preview = entry.querySelector(".preview");
    const badge = entry.querySelector(".badge");

    if (preview) preview.textContent = message.message.slice(0, 40);
    if (badge) {
        badge.textContent = parseInt(badge.textContent || "0") + 1;
        badge.classList.remove("hidden");
    }
}




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
    { once: true }
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
            error
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

function openChat(userId, userName) {
    selectedUserId = userId;
    selectedGroupId = null;

    document.getElementById("chatTitle").textContent = userName;

    // Highlight active
    document.querySelectorAll(".user-entry, .group-entry").forEach((el) => {
        el.classList.remove("bg-gray-100", "font-bold");
    });
    const current = document.querySelector(`.user-entry[data-id='${userId}']`);
    if (current) {
        current.classList.add("bg-gray-100", "font-bold");
    }

 
    const sidebar = document.getElementById("rightSidebar");
    const nameEl = document.getElementById("activeChatName");
    const metaEl = document.getElementById("activeChatMeta");

    if (sidebar && nameEl && metaEl) {
        sidebar.classList.remove("hidden");
        nameEl.textContent = userName;
        metaEl.textContent = "Privater Chat";
    }

    // 🔻 remove this, because `members` is not defined in private chat
    // renderMembers(members);

    // ⏬ Instead, you could clear the member list
    document.getElementById("groupMembers").innerHTML = "";
    _lastRenderedDay = null; 

    loadMessages();
}

function openGroupChat(groupId, groupName, memberCount) {
    selectedUserId = null;
    selectedGroupId = groupId;

    document.getElementById("chatTitle").textContent = groupName;

    document.querySelectorAll(".user-entry, .group-entry").forEach((el) => {
        el.classList.remove("bg-gray-100", "font-bold");
    });
    const current = document.querySelector(
        `.group-entry[data-id='${groupId}']`
    );
    if (current) {
        current.classList.add("bg-gray-100", "font-bold");
    }

    const sidebar = document.getElementById("rightSidebar");
    const nameEl = document.getElementById("activeChatName");
    const metaEl = document.getElementById("activeChatMeta");

    if (sidebar && nameEl && metaEl) {
        sidebar.classList.remove("hidden");
        nameEl.textContent = groupName;
        metaEl.textContent = `${memberCount} Mitglieder`;
    }

    // ✅ Now fetch group members from server and then render
    fetch(`/chat/group/users/${groupId}`)
        .then((res) => res.json())
        .then((members) => {
            renderMembers(members);
        });
    _lastRenderedDay = null;  

    loadMessages();
}

function renderMembers(members) {
    const list = document.getElementById("groupMembers");
    list.innerHTML = "";

    members.forEach((member) => {
        const li = document.createElement("li");
        const avatar = member.avatar || "/default-avatar.png";
        li.innerHTML = `
            <div class="flex items-center gap-2">
                <img src="${employeeLocation}/${avatar}" class="w-6 h-6 rounded-full object-cover" />
                <span>${member.name} ${member.lastname ?? ""}</span>
            </div>
        `;
        list.appendChild(li);
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

    modal.classList.remove("hidden");
    select.innerHTML = ""; // Clear previous options

    // 🔁 Fetch and load users
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

            // ✅ Now initialize Select2 after all options are added
            $("#addUserSelect").select2({
                dropdownParent: $("#addMemberModal"),
                templateResult: formatUserOption,
                templateSelection: formatUserOption,
                escapeMarkup: (m) => m,
            });
        });

    // 🎨 Avatar render
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

    // Store groupId for later submission
    select.dataset.groupId = groupId;
}

function submitAddMembers() {
    const select = document.getElementById("addUserSelect");
    const groupId = select.dataset.groupId;
    const selected = [...select.selectedOptions].map((opt) => opt.value);

    if (selected.length === 0) {
        return Swal.fire(
            "Bitte mindestens einen Benutzer auswählen.",
            "",
            "warning"
        );
    }

    fetch(`/chat/group/add-members/${groupId}`, {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": csrf,
        },
        body: JSON.stringify({ members: selected }),
    })
        .then((res) => res.json())
        .then((data) => {
            closeAddMemberModal();
            renderMembers(data.members); // refresh sidebar
            Swal.fire(
                "Erfolgreich!",
                "Mitglieder wurden hinzugefügt.",
                "success"
            );
        })
        .catch((err) => {
            console.error(err);
            Swal.fire("Fehler", "Konnte Mitglieder nicht hinzufügen.", "error");
        });
}

window.submitAddMembers = submitAddMembers;

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
                    // ✅ Insert day divider if needed
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
                        isMine
                    );

                    if (msg.id) seenMessageIds.add(msg.id); // keep de-dup
                });

            scrollToBottom();
            filterMessages();
        })
        .catch((err) => {
            console.error("❌ Fehler beim Laden der Nachrichten:", err);
            chatBox.innerHTML =
                '<div class="text-center text-red-400">Fehler beim Laden.</div>';
        });
}


function scrollToBottom() {
    DOM.chatBox.scrollTop = DOM.chatBox.scrollHeight;
}
/**
 * Adds a message to the chat box display.
 * @param {object} msg - The message object (e.g., { message: "...", created_at: "..." }).
 * @param {boolean} isMine - True if the message was sent by the current user, false otherwise.
 */

function addMessage(msg, isMine) {
    const div = document.createElement("div");
    div.className = `group relative mb-3 max-w-lg ${
        isMine ? "ml-auto text-right" : "mr-auto text-left"
    }`;
    div.dataset.msgId = msg.id;

    // ── Sender name (supports name/lastname or employee fallback) ───────────────
    const sender = document.createElement("div");
    sender.className = "text-xs font-semibold text-gray-600 mb-0 px-1";
    const from = msg.from_user || {};
    const displayName =
        (from.employee?.name || from.name || "") +
        (from.employee?.lastname || from.lastname
            ? ` ${from.employee?.lastname || from.lastname}`
            : "");

    sender.textContent = isMine ? "Du" : displayName.trim() || "Unbekannt";
    div.appendChild(sender);

    // ── Bubble ─────────────────────────────────────────────────────────────────
    const bubble = document.createElement("div");
    bubble.className = `relative p-2 rounded-lg ${
        isMine ? "bg-mine" : "bg-gray-200 text-gray-800"
    }`;

    const content = document.createElement("div");
    content.className = "message-content";

    // ── Reply preview (click to jump) ──────────────────────────────────────────
    if (msg.reply_to_preview) {
        const quote = document.createElement("div");
        quote.className =
            "border-l-4 pl-3 pr-1 py-1 mb-2 italic text-sm bg-white/10 text-gray-600";
        quote.innerHTML = `<i data-feather="corner-up-left" class="inline-block w-4 h-4 mr-1"></i>${escapeHtml(
            msg.reply_to_preview
        )}`;
        quote.addEventListener("click", () =>
            replyToMessage(msg.reply_to_id, msg.reply_to_preview)
        );
        content.appendChild(quote);
    }

    // ── Text (safe + links + newlines) ─────────────────────────────────────────
        if (msg.message && !msg.deleted_at) {
            const msgText = document.createElement("div");
            msgText.className = "message-text"; // ✅ add a stable class
            msgText.innerHTML = linkify(nl2br(escapeHtml(msg.message)));
            content.appendChild(msgText);
        }


    // ── Voice / Audio (accepts "voice" or "audio", supports audio_url or file_path) ─
    const t = (msg.type || "").toLowerCase();
    const isVoice = t === "voice" || t === "audio";
    let audioUrl = msg.audio_url || msg.file_url;

    // If backend only returned file_path from public disk, build /storage/… URL
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

    // ── Attachments (image/file) ───────────────────────────────────────────────
    if (Array.isArray(msg.attachments) && msg.attachments.length) {
        const list = document.createElement("div");
        list.className = "mt-1 space-y-1";
        msg.attachments.forEach((att) => {
            // If API didn’t send mime but type is image, still render as image
            const isImg = att.is_image || att.mime?.startsWith?.("image/");
            if (isImg) {
                const img = document.createElement("img");
                img.src = att.url;
                img.alt = att.name || "Bild";
                img.className = "mt-1 max-w-xs rounded cursor-pointer";
                img.addEventListener("click", () =>
                    window.open(att.url, "_blank", "noopener")
                );
                list.appendChild(img);
            } else {
                const a = document.createElement("a");
                a.href = att.url;
                a.target = "_blank";
                a.rel = "noopener";
                a.className =
                    "inline-flex items-center gap-1 underline break-all";
                a.innerHTML = `<i data-feather="file"></i> ${escapeHtml(
                    att.name || "Datei"
                )}`;
                list.appendChild(a);
            }
        });
        content.appendChild(list);
    }

    // ── Time + ticks ───────────────────────────────────────────────────────────
    const time = document.createElement("div");
    time.className = `text-xs mt-1 ${
        isMine ? "text-blue-600" : "text-gray-500"
    } text-right`;
    const timeString = new Date(
        msg.created_at || Date.now()
    ).toLocaleTimeString([], { hour: "2-digit", minute: "2-digit" });
    let ticks = "";
    if (isMine) {
        ticks = msg.is_read
            ? `<span class="ml-1">✓✓</span>`
            : `<span class="ml-1 text-gray-400">✓</span>`;
    }
    time.innerHTML = `${timeString} ${ticks}`;
    content.appendChild(time);

    // ── Hover actions ──────────────────────────────────────────────────────────
    const hover = document.createElement("div");
    hover.className = `hover-buttons absolute flex gap-2 top-1 ${
        isMine ? "right-2" : "right-[-50px]"
    }`;

    const replyBtn = document.createElement("button");
    replyBtn.className =
        "text-xs opacity-70 hover:opacity-100 hover:text-blue-500 transition-opacity";
    replyBtn.innerHTML = `<i data-feather="corner-up-left" class="w-4 h-4"></i>`;
    replyBtn.addEventListener("click", () =>
        enableReply(msg.id, msg.message || "")
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

    bubble.appendChild(content);
    bubble.appendChild(hover);
    div.appendChild(bubble);

    if (!DOM.chatBox) {
        console.warn("⚠️ chatBox is not ready. Skipping addMessage.");
        return;
    }

    DOM.chatBox.appendChild(div);
    scrollToBottom();
    if (window.feather) feather.replace();
}

/**
 * Sends a message to the selected user via the backend API.
 */
let _isSending = false;

function sendMessage() {
    if (_isSending) return;

    // Keep newlines, just trim edges
    const raw = (DOM.messageInput.value || "").replace(/\r/g, "");
    if (!raw.trim()) return;

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
                    `div[data-msg-id="${editingMessageId}"] .message-content .message-text`
                );
                if (el)
                    el.innerHTML = linkify(
                        nl2br(escapeHtml(msg.message || ""))
                    );

            } else {
                // Swap temp -> real id + time
                if (tempId) {
                    const node = DOM.chatBox.querySelector(
                        `div[data-msg-id="${tempId}"]`
                    );
                    if (node) node.dataset.msgId = msg.id;
                }
            }

            updatePreview(
                selectedUserId || payload.group_id,
                msg.message || ""
            );
        })
        .catch((err) => {
            console.error("Error sending message:", err);

            // Rollback optimistic message on error
            if (!editingMessageId && tempId) {
                const node = DOM.chatBox.querySelector(
                    `div[data-msg-id="${tempId}"]`
                );
                if (node) node.remove();
                seenMessageIds.delete(tempId);
            }

            Swal?.fire?.(
                "Senden fehlgeschlagen",
                err.message || "Unbekannter Fehler",
                "error"
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
                    ".message-content > div:nth-child(2)"
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
    if (!selectedUserId) return;
    // Sort IDs to ensure a consistent channel name for the whisper
    const [a, b] = [userId, selectedUserId].sort((x, y) => x - y);
    window.Echo.private(`chat.${a}.${b}`).whisper("typing", { typing_user_id: userId });

    if (!selectedUserId) {
        const selectedGroup = document.querySelector(
            "li.bg-gray-100[data-group-id]"
        );
        if (selectedGroup) {
            const groupId = selectedGroup.dataset.groupId;
            window.Echo.private(`chat.group.${groupId}`).whisper("typing", {
                typing_user_id: userId, // ✅ not just `user`
            });

        }
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
function subscribeToGroup(groupId) {
    if (window.subscribedGroups.includes(groupId)) return;
    window.subscribedGroups.push(groupId);

    window.Echo.private(`chat.group.${groupId}`).listen(
        ".message-sent",
        (data) => {
             if (seenMessageIds.has(data.id)) return; // ✅
             seenMessageIds.add(data.id);
            console.log("📩 New group message:", data);
            if (selectedGroupId == groupId) {
                addMessage(data, false);
            } else {
                showUnread(groupId, true);
                playNotificationSound();
            }
        }
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
            console.error("Error marking messages as read:", error)
        );
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
                    `li[data-id="${id}"] .badge`
                );
                if (badge) {
                    badge.textContent = count;
                    badge.classList.toggle("hidden", count == 0); // Hide badge if count is zero
                }
                // Also update the preview in case it was highlighted as unread
                const previewEl = DOM.userList.querySelector(
                    `.preview[data-id="${id}"]`
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

    // 🛡 Validation
    const selectedCheckboxes = Array.from(
        document.querySelectorAll(".member-checkbox:checked")
    );

    if (!groupName || selectedCheckboxes.length === 0) {
        return Swal.fire({
            icon: "warning",
            title: "Fehlende Angaben",
            text: "Bitte Gruppenname und mindestens ein Mitglied auswählen.",
        });
    }

    // 📦 Build members array
    const members = selectedCheckboxes.map((checkbox) => {
        const userId = checkbox.value;
        const roleSelect = document.getElementById(`role-${userId}`);
        const role = roleSelect?.value || "member";
        return { id: userId, role };
    });

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
                Accept: "application/json", // 🛡 Force JSON from Laravel
            },
            body: JSON.stringify({ name: groupName, members }),
        });

        const contentType = response.headers.get("content-type") || "";

        if (!response.ok) {
            const errorText = await response.text();
            throw new Error(
                `Fehler ${response.status}: ${errorText.slice(0, 300)}`
            );
        }

        if (!contentType.includes("application/json")) {
            const html = await response.text();
            throw new Error(
                "❌ Server hat HTML statt JSON zurückgegeben:\n" +
                    html.slice(0, 300)
            );
        }

        await response.json();

        Swal.fire({
            icon: "success",
            title: isEdit ? "Gruppe aktualisiert!" : "Gruppe erstellt!",
            timer: 1500,
            showConfirmButton: false,
        });

        // ✅ Reset modal and reload
        modal.classList.add("hidden");
        groupNameInput.value = "";
        submitBtn.removeAttribute("data-id");
        userList.innerHTML = "";
        loadUsers();
    } catch (err) {
        console.error("❌ Fehler beim Speichern:", err.message);
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
                    }
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
                                `role-${u.id}`
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
 