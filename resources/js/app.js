 

// ✅ Only ONE Bootstrap include (bundle already has Popper v1)
import "bootstrap/dist/js/bootstrap.bundle.min.js";

// Your files last
import "./bootstrap";
import "./notification";
import "./chat";

import Echo from "laravel-echo";
import Pusher from "pusher-js"; // Make sure this is here

window.Pusher = Pusher; // Make sure this is here

// kill any previous init from vendor files
if (window.Echo?.connector?.pusher?.disconnect) {
    try {
        window.Echo.connector.pusher.disconnect();
    } catch {}
}
window.Echo = undefined;

const host = import.meta.env.VITE_REVERB_HOST ?? "127.0.0.1";
const port = Number(import.meta.env.VITE_REVERB_PORT ?? 6001);
const scheme = import.meta.env.VITE_REVERB_SCHEME ?? "http";

console.log("[Echo cfg]", { host, port, scheme });

window.Echo = new Echo({
    broadcaster: "reverb",
    key: import.meta.env.VITE_REVERB_APP_KEY ?? "local",

    wsHost: host,
    wsPort: port,
    wssPort: port, // same, but we disable wss anyway
    forceTLS: false, // ⬅️ local: force OFF, ignore https
    enabledTransports: ["ws"], // ⬅️ no wss attempts

    authEndpoint: "/broadcasting/auth",
    auth: {
        headers: {
            "X-CSRF-TOKEN": document
                .querySelector('meta[name="csrf-token"]')
                .getAttribute("content"),
        },
        withCredentials: true,
    },
});

// Optional: Listen for connection status
window.Echo.connector.pusher.connection.bind("connected", () => {
    console.log("Echo connected from App to Reverb!");
});

window.Echo.connector.pusher.connection.bind("disconnected", () => {
    console.log("Echo disconnected from Reverb!");
});

window.Echo.connector.pusher.connection.bind("error", (err) => {
    console.error("Echo connection error:", err);
});
// NEW: import at the end (order is OK because the file waits for DOM + window.Echo)
import "./user-notifications";

import initializeIdsListener from "./ids-listener";

initializeIdsListener();
