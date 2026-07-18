import { defineConfig } from "vite";
import react from "@vitejs/plugin-react";
import { resolve } from "node:path";

// ticket — Hausplaner-Insel-Bundle (Heimat jetzt ticket). Baut resources/planner/hausplaner/main.tsx
// zu FESTEN Dateinamen nach public/hausplaner/, damit die Blade-Seite /hausplaner/hausplaner.js
// statisch einbindet. VÖLLIG GETRENNT von der Vue-Haupt-Build (vite.config.js) — eigener Output,
// eigenes Entry; berührt weder resources/js noch das Laravel-Vite-Manifest.
// emptyOutDir=false: public/hausplaner/models/ (Katalog-GLBs) bleibt erhalten.
export default defineConfig({
  plugins: [react()],
  base: "/hausplaner/",
  build: {
    outDir: resolve(__dirname, "public/hausplaner"),
    emptyOutDir: false,
    rollupOptions: {
      input: resolve(__dirname, "resources/planner/hausplaner/main.tsx"),
      output: {
        entryFileNames: "hausplaner.js",
        chunkFileNames: "hausplaner-[name].js",
        assetFileNames: (assetInfo) => {
          const name = assetInfo.name ?? "";
          if (name.endsWith(".css")) return "hausplaner.css";
          return "hausplaner.[ext]";
        },
      },
    },
  },
});
