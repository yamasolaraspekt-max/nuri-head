import Echo from "laravel-echo";
import axios from "axios";

// ✅ Sound removed completely
let disabledTypes = [];

const CATEGORY_MAP = {
    appointment: "appointment",
    task: "task",
    customer: "customer",
    lead: "lead",
    project: "project",
    offer: "offer",
    rest: "rest",
};

export default function initializeNotifications(userId) {
    console.log("[🔔] Notifications Init for:", userId);

    try {
        window.Echo.private(`notifications.user.${userId}`)
            .listen(
                ".Illuminate\\Notifications\\Events\\BroadcastNotificationCreated",
                handleNotificationEvent
            )
            .error((err) =>
                console.error("[❌] Notification channel error:", err)
            );

        updateNotifications();
    } catch (err) {
        console.error("[❌] Echo init failed:", err);
    }
}

function handleNotificationEvent(e) {
    const data = e?.notification?.data || {};
    const type = data?.type || "rest";

    if (disabledTypes.includes(type)) {
        console.log("[🔕] Skipping disabled type:", type);
        return;
    }

    console.log("[📡] Notification received:", type, data.title);
    updateNotifications();

    // 🚫 No sound. Just visual toast.
    if (data.title || data.message) {
        showToast(data.title || "Benachrichtigung", data.message || "");
    }
}

function updateNotifications() {
    axios
        .get("/get/notification/list")
        .then(({ data }) => updateNotificationUI(data))
        .catch((err) => console.error("[🚫] Notification fetch failed:", err));
}

function updateNotificationUI(data) {
    let total = 0;

    for (const [type, items] of Object.entries(data)) {
        const key = CATEGORY_MAP[type];
        if (!key) continue;

        const section = document.getElementById(`collapse-${key}`);
        const badge = document.getElementById(`badge-${key}`);
        if (!section || !badge) continue;

        const list = section.querySelector("ul");
        if (!list) continue;

        list.innerHTML = "";

        items.forEach((n) => {
            const li = document.createElement("li");
            li.className =
                "list-group-item list-group-item-action d-flex align-items-start";
            li.title = "Klicken um als gelesen zu markieren";
            li.style.cursor = "pointer";

            if (!n.read_at) li.classList.add("font-weight-bold"); // keep BS4 class if your theme uses it

            li.innerHTML = `
        <i class="bi bi-info-circle-fill text-primary mr-2 mt-1"></i>
        <span class="font-weight-normal">
          <strong>${n.title ?? ""}</strong><br>
          ${n.message ?? ""}
        </span>
      `;

            li.onclick = () => markNotificationAsRead(n.id);
            list.appendChild(li);
        });

        badge.textContent = items.length;
        total += items.length;
    }

    const bellBadge = document.getElementById("notificationBellBadge");
    if (bellBadge) {
        bellBadge.textContent = total;
        bellBadge.style.display = total > 0 ? "inline-block" : "none";
    }
}

function markNotificationAsRead(id) {
    axios.post(`/notification/mark-as-read/${id}`).then(updateNotifications);
}

function markAllAsRead() {
    axios.post("/notification/mark-all-read").then(updateNotifications);
}

function showToast(title, message) {
    const toastBody = document.getElementById("notifToastBody");
    if (toastBody) {
        toastBody.innerHTML = `<strong>${title}</strong><br>${message}`;
        $("#notifToast").toast("show");
    }
}

// Keep the settings object stable; sound toggle is a no-op now.
window.notificationSettings = {
    toggleSound() {
        console.log("[🔇] Sound is disabled globally. Toggle ignored.");
    },
    toggleType(type) {
        if (disabledTypes.includes(type)) {
            disabledTypes = disabledTypes.filter((t) => t !== type);
        } else {
            disabledTypes.push(type);
        }
        console.log(`[⚙️] Toggled type: ${type}`);
    },
};
