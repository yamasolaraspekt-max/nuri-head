import { defineConfig, loadEnv } from "vite";
import laravel from "laravel-vite-plugin";
import vue from "@vitejs/plugin-vue";

export default defineConfig(({ mode }) => {
    const env = loadEnv(mode, process.cwd(), "VITE_"); // ✅ this loads .env

    return {
        plugins: [
            laravel({
                input: [
                    "resources/js/app.js",
                    "resources/js/chat.js",
                    "resources/js/bootstrap.js",
                    "resources/js/notification.js",
                    "resources/js/mini_chat.js",
                ],
                refresh: true,
            }),
            vue(),
        ],
        server: {
            hmr: {
                host: "localhost",
            },
        },
        define: {
            "import.meta.env.VITE_REVERB_HOST": JSON.stringify(
                env.VITE_REVERB_HOST
            ),
            "import.meta.env.VITE_REVERB_PORT": JSON.stringify(
                env.VITE_REVERB_PORT
            ),
            "import.meta.env.VITE_REVERB_SCHEME": JSON.stringify(
                env.VITE_REVERB_SCHEME
            ),
            "import.meta.env.VITE_REVERB_APP_KEY": JSON.stringify(
                env.VITE_REVERB_APP_KEY
            ),
        },
    };
});
