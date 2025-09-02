<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, router } from "@inertiajs/vue3";
import { ref, watch } from "vue";
import axios from "axios";

const props = defineProps({
    items: Object,
    filters: Object,
    tipos: Array,
});

const q = ref(props.filters.q ?? "");
const leido = ref(props.filters.leido ?? "");
const tipo = ref(props.filters.tipo ?? "");
const busy = ref(false);

const go = async (url = route("notificaciones.index")) => {
    busy.value = true;
    try {
        await router.get(
            url,
            { q: q.value, leido: leido.value, tipo: tipo.value },
            {
                preserveState: true,
                preserveScroll: true,
                replace: true,
            }
        );
    } finally {
        busy.value = false;
    }
};

let t;
watch([q, leido, tipo], () => {
    clearTimeout(t);
    t = setTimeout(() => go(), 300);
});

const marcarLeida = async (id) => {
    await axios.patch(route("notificaciones.leer", id));
    go();
};

const borrar = async (id) => {
    await axios.delete(route("notificaciones.destroy", id));
    go();
};
</script>

<template>
    <AuthenticatedLayout>
        <Head title="Notificaciones" />
        <div class="max-w-5xl mx-auto p-4 md:p-8">
            <h1
                class="text-2xl md:text-3xl font-semibold text-slate-900 dark:text-white"
            >
                Notificaciones
            </h1>

            <div class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-3">
                <input
                    v-model="q"
                    type="text"
                    placeholder="Buscar por título o mensaje…"
                    class="w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-transparent px-3 py-2 outline-none focus:ring dark:text-white dark:bg-gray-800"
                />
                <select
                    v-model="leido"
                    class="w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-transparent px-3 py-2 dark:text-white dark:bg-gray-800"
                >
                    <option value="">Todas</option>
                    <option value="0">No leídas</option>
                    <option value="1">Leídas</option>
                </select>
                <select
                    v-model="tipo"
                    class="w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-transparent px-3 py-2 dark:text-white dark:bg-gray-800"
                >
                    <option value="">Todos los tipos</option>
                    <option v-for="t in props.tipos" :key="t" :value="t">
                        {{ t }}
                    </option>
                </select>
            </div>

            <div
                class="mt-6 bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 overflow-hidden"
            >
                <div v-if="busy" class="p-4 text-sm text-slate-500">
                    Cargando…
                </div>

                <ul
                    v-else
                    class="divide-y divide-slate-200 dark:divide-white"
                >
                    <li
                        v-for="n in props.items.data"
                        :key="n.idNotificacion"
                        class="p-4 flex gap-4 items-start dark:bg-[#383838]"
                    >
                        <div class="mt-1 ">
                            <span
                                class="inline-block text-[10px] px-2 py-0.5 rounded-full border dark:bg-slate-600"
                                :class="
                                    n.leido
                                        ? 'text-slate-500 border-slate-300 dark:border-slate-700 dark:text-slate-200'
                                        : 'text-emerald-700 border-emerald-300 dark:border-emerald-300 dark:text-emerald-300'
                                "
                            >
                                {{ n.tipo }}
                            </span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p
                                class="text-sm font-semibold"
                                :class="
                                    n.leido
                                        ? 'text-slate-600 dark:text-slate-500'
                                        : 'text-slate-900 dark:text-white'
                                "
                            >
                                <a
                                    :href="
                                        n.go_url ??
                                        route(
                                            'notificaciones.go',
                                            n.idNotificacion
                                        )
                                    "
                                    class="hover:underline"
                                >
                                    {{ n.titulo || "(Sin título)" }}
                                </a>
                            </p>
                            <p
                                class="text-sm mt-1 text-slate-600 dark:text-slate-300"
                            >
                                {{ n.mensaje }}
                            </p>
                            <p class="text-xs mt-1 text-slate-400">
                                {{ new Date(n.created_at).toLocaleString() }}
                            </p>
                        </div>
                        <div class="flex gap-2 shrink-0">
                            <button
                                @click="
                                    router.visit(
                                        n.go_url ??
                                            route(
                                                'notificaciones.go',
                                                n.idNotificacion
                                            )
                                    )
                                "
                                class="text-xs font-semibold px-3 py-1 rounded-lg border hover:bg-accent dark:bg-brandgold/60 dark:text-gray-200 dark:hover:bg-brandgold/80"
                            >
                                Abrir
                            </button>
                            <button
                                v-if="!n.leido"
                                @click="marcarLeida(n.idNotificacion)"
                                class="text-xs font-semibold px-3 py-1 rounded-lg border border-emerald-300 hover:bg-emerald-50 dark:bg-emerald-900 dark:text-gray-200 dark:hover:bg-emerald-800"
                            >
                                Marcar leída
                            </button>
                            <button
                                @click="borrar(n.idNotificacion)"
                                class="text-xs font-semibold px-3 py-1 rounded-lg border border-red-300 text-red-600 hover:bg-red-50 dark:bg-red-900 dark:text-gray-200 dark:hover:bg-red-800"
                            >
                                Borrar
                            </button>
                        </div>
                    </li>

                    <li
                        v-if="props.items.data.length === 0"
                        class="p-4 text-sm text-slate-500"
                    >
                        No hay notificaciones.
                    </li>
                </ul>

                <div class="flex items-center justify-between px-4 py-2 dark:bg-gray-800">
                    <span class="text-xs text-slate-500">
                        Mostrando {{ props.items.from ?? 0 }}–{{
                            props.items.to ?? 0
                        }}
                        de {{ props.items.total ?? 0 }}
                    </span>
                    <div class="flex gap-2">
                        <button
                            :disabled="!props.items.prev_page_url"
                            @click="go(props.items.prev_page_url)"
                            class="text-xs px-3 py-1 rounded-lg border disabled:opacity-50 dark:text-white"
                        >
                            Anterior
                        </button>
                        <button
                            :disabled="!props.items.next_page_url"
                            @click="go(props.items.next_page_url)"
                            class="text-xs px-3 py-1 rounded-lg border disabled:opacity-50 dark:text-white"
                        >
                            Siguiente
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
