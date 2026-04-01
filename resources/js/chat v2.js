import Echo from "laravel-echo";
import Pusher from "pusher-js"; // Make sure this is here

window.Pusher = Pusher; // Make sure this is here

window.Echo = new Echo({
    broadcaster: 'reverb',
    key: import.meta.env.VITE_REVERB_APP_KEY ?? 'local',
    wsHost: import.meta.env.VITE_REVERB_HOST ?? 'localhost',
    wsPort: import.meta.env.VITE_REVERB_PORT ?? 6001,
    wssPort: import.meta.env.VITE_REVERB_PORT ?? 6001,
    forceTLS: false,
    enabledTransports: ['ws'],
    authEndpoint: '/broadcasting/auth',
    auth: {
        headers: {
            'X-CSRF-TOKEN': window.csrfToken
        }
    }
});

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

console.log("✅ Echo (Reverb) initialized:", window.Echo);

const userId = window.userId;
const csrf = window.csrfToken;
let selectedUserId = null;
let DOM = {};
let typingTimeout = null; // To manage the typing indicator display
let replyToId = null;
let replyPreviewText = "";
let editingMessageId = null;

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

    const emojiBtn = document.getElementById("emojiBtn");

    // ✅ Setup emoji toggle + insertion
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

    // ✅ Notifications
    Notification.requestPermission();

    // ✅ Event listeners
    DOM.sendButton.addEventListener("click", sendMessage);
    DOM.messageInput.addEventListener("input", sendTyping);

    // ✅ Load data
    loadUsers();
    loadUnreadCounts();

    // ✅ Subscribe to personal channel
    window.Echo.private(`chat.user.${userId}`).listen(
        ".message-sent",
        handleIncoming
    );

    window.Echo.private(`chat.user.${userId}`).listen(".message-read", (e) => {
        console.log("📩 Read update received:", e);

        // Find all my messages to this user and update ticks
        const msgs = DOM.chatBox.querySelectorAll("[data-msg-id]");
        msgs.forEach((msg) => {
            const isMine = msg.classList.contains("ml-auto");
            if (!isMine) return;

            const timeEl = msg.querySelector("div.text-xs");
            const rawTime = timeEl.textContent.trim().split(" ")[0]; // keep original time
            timeEl.innerHTML = `${rawTime} <span class="text-blue-500 ml-1">✓✓</span>`;
        });
    });

}

// Initialize the chat UI once the DOM is fully loaded
document.addEventListener("DOMContentLoaded", initChatUI);

/**
 * Loads the list of users/employees from the backend and populates the sidebar.
 */
function loadUsers() {
    fetch("/chat/employees")
        .then((res) => res.json())
        .then((users) => {
            DOM.userList.innerHTML = ""; // Clear existing list
            users.forEach((user) => {
                const li = document.createElement("li");
                li.dataset.id = user.id;
                li.className =
                    "user-entry flex items-center gap-2 p-2 cursor-pointer hover:bg-gray-50 rounded-md transition-colors duration-200"; // Added hover effect
                li.innerHTML = `
                    <div class="relative">
                        <img src="${
                            user.image
                        }" class="avatar w-8 h-8 rounded-full border object-cover" alt="User Avatar" />
                        <span class="status-dot absolute bottom-0 right-0 w-3 h-3 rounded-full border-2 border-white bg-gray-400"></span>
                    </div>
                    <div class="flex-1">
                        <div class="font-semibold text-gray-800">${user.name} ${
                    user.lastname
                }</div>
                        <small class="preview text-xs text-gray-500" data-id="${
                            user.id
                        }">
                            ${
                                user.last_msg?.slice(0, 40) ||
                                "Keine Nachrichten"
                            }
                        </small>
                    </div>
                    <span class="badge hidden text-xs bg-red-500 text-white rounded-full px-2 py-0.5 min-w-[20px] text-center">0</span>
                `;
                li.addEventListener("click", () =>
                    openChat(user.id, `${user.name} ${user.lastname}`)
                );
                DOM.userList.appendChild(li);
                subscribeToChat(user.id); // Subscribe to chat channels for each user
            });
            setupPresence(); // Set up presence channel for online status
        })
        .catch((error) => console.error("Error loading users:", error));
}

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
function subscribeToChat(otherId) {
    // Sort IDs to ensure a consistent channel name (e.g., chat.1.2 always, not chat.2.1)
    const [a, b] = [userId, otherId].sort((x, y) => x - y);
    window.Echo.private(`chat.${a}.${b}`)
        // Listen for new messages on this specific chat channel
        // IMPORTANT: Reverted event name back to '.message-sent' (lowercase with hyphen)
        // .listen(".message-sent", handleIncoming)  Corrected event name
        // Listen for whisper events (e.g., typing indicators)
        .listenForWhisper("typing", (e) => showTyping(otherId, e.user));
}

/**
 * Shows the typing indicator for the currently selected chat.
 * @param {number} fromId - The ID of the user who is typing.
 * @param {number} typingUserId - The ID of the user who sent the whisper (should be fromId).
 */
function showTyping(fromId, typingUserId) {
    // Only show typing indicator if the currently selected chat is with the user who is typing
    if (selectedUserId === fromId) {
        DOM.typingIndicator.textContent = `✍️ ${DOM.chatTitle.dataset.title} schreibt...`;
        DOM.typingIndicator.classList.remove("hidden");

        // Clear any existing timeout to keep the indicator visible while typing continues
        if (typingTimeout) {
            clearTimeout(typingTimeout);
        }

        // Set a new timeout to hide the indicator after a short delay
        typingTimeout = setTimeout(() => {
            DOM.typingIndicator.classList.add("hidden");
        }, 2000); // Hide after 2 seconds of no new typing whispers
    }
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
    console.log("Received event payload:", e);

    if (!e.message || !e.from_user || !e.from_user.id) return;
    if (e.from_user.id === userId) return;

    const name = e.from_user.name;

    // ✅ 🔊 Always play sound on any incoming message
    playNotification();

    // 🖥️ Browser Notification
    if (Notification.permission === "granted") {
        new Notification(`${name} hat dir geschrieben`, {
            body: e.message,
            icon: e.from_user.image || "/favicon.ico",
        });
    }

    if (selectedUserId === e.from_user.id) {
        addMessage(
            {
                message: e.message,
                from_user_id: e.from_user.id,
                created_at: e.created_at,
            },
            false
        );
        markAsRead(e.from_user.id);
        DOM.typingIndicator.classList.add("hidden");
    } else {
        showBadge(e.from_user.id);
        updatePreview(e.from_user.id, e.message, true);
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
        console.warn('Notification sound play failed (user interaction needed):', error);
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
function openChat(otherId, name) {
    selectedUserId = otherId; // Set the currently selected chat user
    DOM.chatTitle.textContent = name; // Update chat header title
    DOM.chatTitle.dataset.title = name; // Store original name for typing indicator
    DOM.chatBox.innerHTML = ""; // Clear existing messages

    // Highlight the selected user in the sidebar
    DOM.userList.querySelectorAll("li").forEach((li) => {
        const img = li.querySelector("img.avatar");
        const isSelected = +li.dataset.id === otherId;
        img.classList.toggle("ring-2", isSelected);
        img.classList.toggle("ring-blue-400", isSelected); // Changed ring color for better visibility
    });

    // Fetch historical messages for the selected chat
    fetch(`/chat/fetch/${otherId}`)
        .then((res) => res.json())
        .then((messages) => {
            messages.forEach((m) => addMessage(m, m.from_user_id === userId));
            DOM.chatBox.scrollTop = DOM.chatBox.scrollHeight; // Scroll to bottom after loading
        })
        .catch((error) =>
            console.error("Error fetching chat messages:", error)
        );

    markAsRead(otherId); // Mark messages from this user as read
}

/**
 * Adds a message to the chat box display.
 * @param {object} msg - The message object (e.g., { message: "...", created_at: "..." }).
 * @param {boolean} isMine - True if the message was sent by the current user, false otherwise.
 */
function addMessage(msg, isMine) {
    const div = document.createElement("div");
    div.className = `group relative p-2 mb-1 max-w-lg rounded-lg ${
        isMine
            ? "ml-auto bg-primary text-white"
            : "mr-auto bg-gray-200 text-gray-800"
    }`;
    div.dataset.msgId = msg.id;

    const content = document.createElement("div");
    content.className = "message-content";

    // ✅ Reply preview (quoted original)
    if (msg.reply_to_preview) {
        const quote = document.createElement("div");
        quote.className =
            "border-l-4 pl-3 pr-1 py-1 mb-2 italic text-sm bg-white/10 text-gray-300 dark:text-gray-200";
        quote.innerHTML = `
            <i data-feather="corner-up-left" class="inline-block w-4 h-4 mr-1"></i>
            ${msg.reply_to_preview}
        `;
        content.appendChild(quote);
    }

    // ✅ Actual message text
        const msgText = document.createElement("div");
        if (msg.deleted_at) {
            msgText.innerHTML = `<i class="italic text-sm text-gray-500">Diese Nachricht wurde gelöscht</i>`;
        } else {
            msgText.textContent = msg.message;
        }
        content.appendChild(msgText);

    // ✅ Timestamp
    const time = document.createElement("div");
    time.className = `text-xs ${
        isMine ? "text-blue-200" : "text-gray-500"
    } text-right mt-1`;
    const timeString = new Date(msg.created_at).toLocaleTimeString([], {
        hour: "2-digit",
        minute: "2-digit",
    });

    let ticks = "";
    if (isMine) {
        if (msg.is_read) {
            ticks = `<span class="text-blue-500 ml-1">✓✓</span>`; // Read
        } else {
            ticks = `<span class="text-gray-400 ml-1">✓</span>`; // Sent
        }
    }

    time.innerHTML = `${timeString} ${ticks}`;

    content.appendChild(time);

    div.appendChild(content);

    // ✅ Hover buttons container
    const hover = document.createElement("div");
    hover.className = `hover-buttons absolute flex gap-2  ${
        isMine ? "right-[20px]" : "right-[-60px]"
    }`;

    // 🔁 Reply button
    const replyBtn = document.createElement("button");
    replyBtn.className =
        "text-white text-xs opacity-70 hover:opacity-100 hover:text-blue-400 transition-opacity";
    replyBtn.innerHTML = `<i data-feather="corner-up-left" class="w-4 h-4"></i>`;
    replyBtn.addEventListener("click", () => enableReply(msg.id, msg.message));
    hover.appendChild(replyBtn);


    // 📝 Edit button (only if own message)
    if (isMine) {
        const iconBaseClass =
            "text-white text-xs opacity-70 hover:opacity-100 transition-opacity";

        const editBtn = document.createElement("button");
        editBtn.className = `${iconBaseClass} hover:text-yellow-400`;
        editBtn.innerHTML = `<i data-feather="edit-2" class="w-4 h-4"></i>`;
        editBtn.addEventListener("click", () => editMessage(msg.id));
        hover.appendChild(editBtn);

        const delBtn = document.createElement("button");
        delBtn.className = `${iconBaseClass} hover:text-red-400`;
        delBtn.innerHTML = `<i data-feather="trash-2" class="w-4 h-4"></i>`;
        delBtn.addEventListener("click", () => deleteMessage(msg.id));
        hover.appendChild(delBtn);
    }


    div.appendChild(hover);
    DOM.chatBox.appendChild(div);

    // Auto-scroll
    DOM.chatBox.scrollTop = DOM.chatBox.scrollHeight;

    // ✅ Re-render feather icons
    if (window.feather) feather.replace();
}


/**
 * Sends a message to the selected user via the backend API.
 */
function sendMessage() {
    const msg = DOM.messageInput.value.trim();
    if (!msg || !selectedUserId) return;
    DOM.messageInput.classList.remove("border-yellow-400", "ring-2");

    const payload = {
        to_user_id: selectedUserId,
        message: msg,
        type: "text",
        reply_to_id: replyToId,
    };

    if (editingMessageId) {
        payload.edit_id = editingMessageId;
    }

    fetch("/chat/send", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": csrf,
        },
        body: JSON.stringify(payload),
    })
        .then((res) => res.json())
        .then((data) => {
            if (editingMessageId) {
                const el = DOM.chatBox.querySelector(
                    `div[data-msg-id="${editingMessageId}"] .message-content > div:nth-child(2)`
                );
                if (el) el.textContent = data.message.message;
            } else {
                addMessage(data.message, true);
            }

            updatePreview(selectedUserId, data.message.message);
            DOM.messageInput.value = "";
            editingMessageId = null;
            cancelReply();
        })
        .catch((error) => {
            console.error("Error sending message:", error);
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

    const msgText = msgEl.querySelector(
        ".message-content > div:nth-child(2)"
    ).textContent;

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
    window.Echo.private(`chat.${a}.${b}`).whisper("typing", { user: userId });
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
                const previewEl = DOM.userList.querySelector(`.preview[data-id="${id}"]`);
                if (previewEl) {
                    previewEl.classList.toggle("font-bold", count > 0);
                }
            });
        })
        .catch((error) => console.error("Error loading unread counts:", error));
}
 
feather.replace(); // after any new icons are injected
 


document.getElementById("createGroupBtn").addEventListener("click", () => {
    document.getElementById("groupModal").classList.remove("hidden");
    populateUserCheckboxList();
});

document.getElementById("cancelGroupBtn").addEventListener("click", () => {
    document.getElementById("groupModal").classList.add("hidden");
});

document.getElementById("submitGroupBtn").addEventListener("click", () => {
    const name = document.getElementById("groupName").value.trim();
    const selected = [
        ...document.querySelectorAll("#userCheckboxList input:checked"),
    ].map((c) => c.value);

    if (!name || selected.length === 0) {
        alert("Bitte Gruppenname und Mitglieder wählen.");
        return;
    }

    fetch("/chat/group/create", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": csrf,
        },
        body: JSON.stringify({ name, members: selected }),
    })
        .then((res) => res.json())
        .then((data) => {
            alert("Gruppe erstellt!");
            document.getElementById("groupModal").classList.add("hidden");
            loadUsers(); // Optional: reload user list or group list
        })
        .catch((err) => console.error("Group creation failed:", err));
});

function populateUserCheckboxList() {
    fetch("/chat/employees")
        .then((res) => res.json())
        .then((users) => {
            const list = document.getElementById("userCheckboxList");
            list.innerHTML = "";

            users.forEach((user) => {
                if (user.id === userId) return; // skip self
                const div = document.createElement("div");
                div.className = "flex items-center gap-2 mb-1";

                div.innerHTML = `
                    <input type="checkbox" value="${user.id}" id="user-${user.id}" class="form-checkbox" />
                    <label for="user-${user.id}" class="text-sm">${user.name} ${user.lastname}</label>
                `;
                list.appendChild(div);
            });
        });
}
