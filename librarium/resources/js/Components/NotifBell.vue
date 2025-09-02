<script setup>
import { ref, onMounted, onBeforeUnmount } from "vue";
import axios from "axios";
import { router } from "@inertiajs/vue3";

const open = ref(false);
const count = ref(0);
const latest = ref([]);
const loading = ref(false);

let pollInterval = null;
let enterTimer = null;
let leaveTimer = null;

const OPEN_DELAY = 80;
const CLOSE_DELAY = 150;

const fetchCount = async () => {
    const { data } = await axios.get(route("notificaciones.unread"));
    count.value = data.count ?? 0;
};

const fetchLatest = async () => {
    if (loading.value) return;
    loading.value = true;
    try {
        const { data } = await axios.get(route("notificaciones.index"), {
            headers: { Accept: "application/json" },
            params: { perPage: 6 },
        });

        const rows = data?.data ?? [];
        latest.value = rows
            .map((n) => ({
                ...n,
                id: n.id ?? n.idNotificacion,
                leido: Boolean(n.leido),
                go_url:
                    n.go_url ??
                    route("notificaciones.go", n.id ?? n.idNotificacion),
            }))
            .filter((n) => !n.leido);
    } finally {
        loading.value = false;
    }
};

const marcarTodas = async () => {
    await axios.patch(route("notificaciones.leerTodas"));
    count.value = 0;
    latest.value = [];
};

const onMouseEnter = () => {
    if (leaveTimer) window.clearTimeout(leaveTimer);
    enterTimer = window.setTimeout(async () => {
        if (!open.value) {
            open.value = true;
            await fetchLatest();
        }
    }, OPEN_DELAY);
};

const onMouseLeave = () => {
    if (enterTimer) window.clearTimeout(enterTimer);
    leaveTimer = window.setTimeout(() => {
        open.value = false;
    }, CLOSE_DELAY);
};

const onClickBell = (e) => {
    e.preventDefault();
    router.get(route("notificaciones.index"));
};

const toggleDropdown = async () => {
    open.value = !open.value;
    if (open.value) await fetchLatest();
};

const verTodas = () => {
    open.value = false;
    router.get(route("notificaciones.index"));
};

onMounted(() => {
    fetchCount();
    pollInterval = window.setInterval(fetchCount, 30000);
});

onBeforeUnmount(() => {
    if (pollInterval) window.clearInterval(pollInterval);
    if (enterTimer) window.clearTimeout(enterTimer);
    if (leaveTimer) window.clearTimeout(leaveTimer);
});
</script>

<template>
    <div class="relative" @mouseenter="onMouseEnter" @mouseleave="onMouseLeave">
        <button
            @click.prevent="onClickBell"
            @keydown.enter.prevent="toggleDropdown"
            @keydown.space.prevent="toggleDropdown"
            class="relative rounded-full hover:bg-slate-100 dark:hover:bg-slate-800"
            aria-label="Notificaciones"
            aria-haspopup="menu"
            :aria-expanded="open"
        >
            <svg
                class="w-6 h-6"
                viewBox="0 0 24 24"
                fill="none"
                aria-hidden="true"
            >
                <path
                    d="M12 22a2 2 0 0 0 2-2H10a2 2 0 0 0 2 2ZM18 16v-5a6 6 0 1 0-12 0v5l-2 2v1h16v-1l-2-2Z"
                    stroke="currentColor"
                    stroke-width="1.5"
                />
            </svg>
            <span
                v-if="count"
                class="absolute -top-1 -right-1 min-w-5 h-5 px-1 text-[10px] grid place-content-center rounded-full bg-red-600 text-white"
            >
                {{ count > 99 ? "99+" : count }}
            </span>
        </button>

        <div
            v-show="open"
            class="absolute right-0 mt-2 w-80 z-[60] bg-white dark:bg-[#383838] rounded-xl shadow-lg ring-1 ring-black/5 overflow-hidden"
            role="menu"
            @mouseenter="onMouseEnter"
            @mouseleave="onMouseLeave"
        >
            <div
                class="flex items-center justify-between px-3 py-2 border-b border-slate-200 dark:white"
            >
                <p class="text-sm font-medium">Notificaciones</p>
                <button @click="marcarTodas" class="text-xs hover:underline">
                    Marcar todas
                </button>
            </div>

            <div v-if="loading" class="p-4 text-sm text-slate-500">
                Cargando…
            </div>

            <template v-else>
                <ul
                    v-if="latest.length"
                    class="max-h-96 overflow-y-auto divide-y divide-slate-200 dark:divide-white"
                >
                    <li
                        v-for="n in latest"
                        :key="n.id"
                        class="p-3 hover:bg-slate-50 dark:hover:bg-gray-800"
                    >
                        <a :href="n.go_url" @click="open = false" class="block">
                            <p
                                class="text-sm font-semibold"
                                :class="
                                    !n.leido
                                        ? 'text-slate-900 dark:text-white'
                                        : 'text-slate-500'
                                "
                            >
                                {{ n.titulo || "(Sin título)" }}
                            </p>
                            <p
                                class="text-xs mt-1 text-slate-600 dark:text-slate-300 line-clamp-2"
                            >
                                {{ n.mensaje }}
                            </p>
                            <p class="text-[10px] mt-1 text-slate-400">
                                {{ new Date(n.created_at).toLocaleString() }}
                            </p>
                        </a>
                    </li>
                </ul>
                <div v-else class="p-4 text-sm text-slate-500">
                    No hay notificaciones.
                </div>
            </template>

            <div
                class="px-3 py-2 text-right border-t border-slate-200 dark:border-white"
            >
                <button @click="verTodas" class="text-xs hover:underline">
                    Ver todas
                </button>
            </div>
        </div>
    </div>
</template>
