function updateClock() {
    const clockElement = document.getElementById("clock");
    const clockContainer = document.getElementById("clock-container");
    const now = new Date();
    const hours = String(now.getHours()).padStart(2, "0");
    const minutes = String(now.getMinutes()).padStart(2, "0");
    clockElement.textContent = `${hours}:${minutes} Uhr`;

    // Blink the clock container every minute
    if (now.getSeconds() === 0) {
        clockContainer.style.opacity = 0;
        setTimeout(() => {
            clockContainer.style.opacity = 1;
        }, 500); // Blink for 500ms
    }
}

// Update the clock every second
setInterval(updateClock, 1000);

// Initialize clock immediately
updateClock();
