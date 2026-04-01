// resources/js/user-notifications.js

function getUserIdFromMeta() {
    const tag = document.querySelector('meta[name="user-id"]');
    if (!tag) return null;
    return tag.getAttribute("content");
}

function escapeHtml(str) {
    if (!str) return "";
    return String(str)
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#39;");
}

function formatTime(value) {
    if (!value) return "";
    try {
        const d = new Date(value);
        if (!isNaN(d.getTime())) {
            return d.toLocaleTimeString("de-DE", {
                hour: "2-digit",
                minute: "2-digit",
            });
        }
    } catch (e) {}
    return value;
}

function hideToast(el) {
    if (!el) return;
    el.style.opacity = "0";
    el.style.transform = "translateX(16px)";
    setTimeout(() => {
        if (el && el.parentNode) {
            el.parentNode.removeChild(el);
        }
    }, 200);
}

function showSystemNotification(notification) {
    const containerId = "sa-system-notification-container";
    let container = document.getElementById(containerId);

    if (!container) {
        container = document.createElement("div");
        container.id = containerId;
        container.style.position = "fixed";
        container.style.top = "1rem";
        container.style.right = "1rem";
        container.style.zIndex = "9999";
        container.style.display = "flex";
        container.style.flexDirection = "column";
        container.style.gap = "0.5rem";
        container.style.pointerEvents = "none";
        document.body.appendChild(container);
    }

    const el = document.createElement("div");
    el.className = "sa-system-toast";
    el.style.minWidth = "260px";
    el.style.maxWidth = "360px";
    el.style.borderRadius = "0.75rem";
    el.style.background = "#111827";
    el.style.color = "#f9fafb";
    el.style.padding = "0.75rem 1rem";
    el.style.boxShadow = "0 14px 30px rgba(15,23,42,0.45)";
    el.style.opacity = "0";
    el.style.transform = "translateX(16px)";
    el.style.transition = "opacity .18s ease-out, transform .18s ease-out";
    el.style.pointerEvents = "auto";

    const type = notification.type || "Info";
    const title = notification.title || "Benachrichtigung";
    const message = notification.message || "";
    const performedAt =
        notification.performed_at || notification.created_at || null;

    el.innerHTML = `
        <div style="display:flex;align-items:flex-start;gap:.5rem;">
            <div style="width:6px;border-radius:9999px;background:#93c21c;margin-top:2px;"></div>
            <div style="flex:1;">
                <div style="font-size:.75rem;text-transform:uppercase;letter-spacing:.08em;opacity:.6;">
                    ${escapeHtml(type)}
                </div>
                <div style="font-weight:600;margin-top:2px;">
                    ${escapeHtml(title)}
                </div>
                <div style="font-size:.85rem;margin-top:2px;opacity:.9;">
                    ${escapeHtml(message)}
                </div>
                <div style="font-size:.7rem;margin-top:4px;opacity:.6;">
                    ${performedAt ? escapeHtml(formatTime(performedAt)) : ""}
                </div>
            </div>
            <button type="button" aria-label="Schließen"
                style="border:none;background:transparent;color:#9ca3af;font-size:1.1rem;line-height:1;cursor:pointer;padding:0;margin:0 0 0 .25rem;">
                &times;
            </button>
        </div>
    `;

    const closeBtn = el.querySelector("button");
    closeBtn.addEventListener("click", () => hideToast(el));

    container.appendChild(el);

    requestAnimationFrame(() => {
        el.style.opacity = "1";
        el.style.transform = "translateX(0)";
    });

    setTimeout(() => hideToast(el), 6000);
}

function initSystemNotifications() {
    const userId = getUserIdFromMeta();
    if (!userId) {
        console.warn("[system notifications] No user-id meta, skip.");
        return;
    }

    if (!window.Echo) {
        console.warn("[system notifications] window.Echo not ready yet.");
        // Small retry if you want, but usually Echo is ready.
        return;
    }

    const channelName = `App.Models.User.${userId}`;
    console.log(
        "[system notifications] subscribing private channel:",
        channelName
    );

    window.Echo.private(channelName).notification((notification) => {
        console.log("[system notifications] incoming", notification);
        showSystemNotification(notification);
    });
}

// Wait for DOM
if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", initSystemNotifications);
} else {
    initSystemNotifications();
}
