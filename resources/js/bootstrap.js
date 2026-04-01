import 'bootstrap';
  import "./qr-login.js";
/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */

import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
 
 

import Echo from "laravel-echo";
import Pusher from "pusher-js";

window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: "reverb",
    key: import.meta.env.VITE_REVERB_APP_KEY ?? "local",

    // Connect back to whatever host your page is served from:
    wsHost: window.location.hostname,
    wsPort: import.meta.env.VITE_REVERB_PORT ?? 6001,
    wssPort: import.meta.env.VITE_REVERB_PORT ?? 6001,

    // If you ever serve over HTTPS, auto‑switch to secure:
    forceTLS: location.protocol === "https:",

    // Allow both ws and wss transports:
    enabledTransports: ["ws", "wss"],

    // Tell Echo to hit your Laravel auth endpoint:
    authEndpoint: "/broadcasting/auth",

    auth: {
        // Include your CSRF token (ensure you have <meta name="csrf-token"> in your page <head>)
        headers: {
            "X-CSRF-TOKEN": document
                .querySelector('meta[name="csrf-token"]')
                .getAttribute("content"),
        },
        // Crucial: send your laravel_session cookie so Laravel can authenticate you
        withCredentials: true,
    },
});

// Optional: Listen for connection status
window.Echo.connector.pusher.connection.bind("connected", () => {
    console.log("Echo connected Boostrap to Reverb!");
});

window.Echo.connector.pusher.connection.bind("disconnected", () => {
    console.log("Echo disconnected from Reverb!");
});

window.Echo.connector.pusher.connection.bind("error", (err) => {
    console.error("Echo connection error:", err);
});

console.log("✅ Echo (Reverb) initialized Ramin:", window.Echo);


import initializeNotifications from './notification.js';
initializeNotifications(window.userId); // Set in your Blade layout



if (window.Echo && window.currentEmployeeId) {
    window.Echo.private("employees." + window.currentEmployeeId).listen(
        ".task.reminder",
        function (e) {
            const title = e.title || "Aufgabe";
            const due = (e.due_date || "") + " " + (e.due_time || "");

            Swal.fire({
                icon: "info",
                title: "Aufgaben-Erinnerung",
                text: `„${title}“ steht in Kürze an (${due}).`,
            });
        }
    );
}

 


function initializeChatNotifications(userId) {
    const badge = document.querySelector(".unread-message-count");
    const sound = document.getElementById("chatNotificationSound");
    let unreadCount = 0;

    // Initial unread count from server
    axios
        .get("/chat/unread-count")
        .then((response) => {
            unreadCount = response.data.count || 0;
            updateBadge();
        })
        .catch(() => console.warn("Could not fetch unread count"));

    // Reverb channel for private messages
        window.Echo.private(`chat.user.${userId}`).listen(
            ".message-sent",
            (data) => {
                if (parseInt(data.to_user_id) === parseInt(userId)) {
                    unreadCount++;
                    updateBadge();
                    if (sound) sound.play();

                    showToast(data);

                    // ✅ TRIGGER CHAT.JS FUNCTION DIRECTLY!
                    if (window.handleIncoming) {
                        window.handleIncoming(data);
                    }
                }
            }
        );



    function showToast(data) {
        const toast = document.getElementById("chatToast");
        document.getElementById("chatToastSender").innerText =
            data.from_user.name || "Unbekannt";
        document.getElementById("chatToastBody").innerText =
            data.message || "Neue Nachricht erhalten";
        document.getElementById("chatToastImage").src = data.from_user.image
            ? data.from_user.image
            : "/images/gender/users.png";



        $(toast).toast("show");
    }


    function updateBadge() {
        if (unreadCount > 0) {
            badge.innerText = unreadCount;
            badge.style.display = "inline-block";
        } else {
            badge.style.display = "none";
        }
    }

    // Optional: reset on click
    document
        .querySelector(".message_view_icon")
        ?.addEventListener("click", () => {
            unreadCount = 0;
            updateBadge();
            axios.post("/chat/mark-as-read"); // Optional endpoint
        });
}

function appendLiveMessage(data) {
    const container = document.getElementById("chat-messages-container");
    if (!container) return;

    const msg = document.createElement("div");
    msg.classList.add("chat-message", "incoming");
    msg.innerHTML = `
        <div class="message-bubble">
            <img class="avatar" src="${empImage} ${
        data.from_user.image
    }" width="30" height="30">
            <div>
                <strong>${data.from_user.name}</strong>
                <p>${data.message}</p>
                <small>${new Date(data.created_at).toLocaleTimeString()}</small>
            </div>
        </div>
    `;
    container.appendChild(msg);
    container.scrollTop = container.scrollHeight;
}



window.initializeChatNotifications = initializeChatNotifications;
 


import initializeIdsListener from "./ids-listener";

initializeIdsListener();
