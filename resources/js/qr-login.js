// resources/js/qr-login.js
import { Html5Qrcode } from "html5-qrcode";

/**
 * Minimal QR scanner wrapper.
 * Usage:
 *   const qr = createQrScanner("qrRegion", text => { ... });
 *   qr.start(); // inside a user gesture (click) for iOS Safari
 */
export function createQrScanner(regionId, onSuccess, onError) {
    const html5Qr = new Html5Qrcode(regionId);
    const config = {
        fps: 12,
        qrbox: (v) => {
            const min = Math.min(v.width, v.height);
            return { width: min * 0.55, height: min * 0.55 };
        },
    };

    async function start() {
        try {
            // IMPORTANT: call from a user gesture (click) for mobile Safari
            await html5Qr.start(
                { facingMode: "environment" },
                config,
                (decodedText) => {
                    try {
                        onSuccess?.(decodedText);
                    } finally {
                        stop();
                    }
                }
            );
        } catch (e) {
            console.error("QR start error:", e);
            onError?.(e);
            alert(
                "Could not start QR scanner. Check HTTPS/permissions. " +
                    (e?.message || "")
            );
        }
    }

    async function stop() {
        try {
            await html5Qr.stop();
        } catch {}
        try {
            await html5Qr.clear();
        } catch {}
    }

    return { start, stop };
}
