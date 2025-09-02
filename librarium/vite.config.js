import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import vue from "@vitejs/plugin-vue";

export default defineConfig({
    server: {
        host: "0.0.0.0",
        port: 5173,
        hmr: {
            host: "localhost",
            clientPort: 5173,
        },
        watch: {
            usePolling: true,
            interval: 500,
            ignored: ["**/vendor/**"],
        },
    },
    optimizeDeps: {
        include: ["vue", "@inertiajs/vue3"]
    },
    build: {
        rollupOptions: {
            output: {
                manualChunks: {
                    vendor: ["vue", "@inertiajs/vue3"]
                }
            }
        }
    },
    resolve: {
        alias: {
            vue: "vue/dist/vue.esm-bundler.js",
        },
    },

    plugins: [
        laravel({
            input: "resources/js/app.js",
            refresh: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
    ],
});
