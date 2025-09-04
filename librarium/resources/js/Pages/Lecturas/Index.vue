<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, router } from "@inertiajs/vue3";
import { ref, computed } from "vue";
import Swal from "sweetalert2";
import { template } from "lodash";
import { map } from "lodash";

const props = defineProps({
    estado: { type: String, required: true },
    lecturas: Object,
});

const search = ref("");
const items = computed(() => props.lecturas?.data ?? []);
const filtered = computed(() => {
    if (!search.value) return items.value;
    const q = search.value.toLowerCase();
    return items.value.filter((i) => {
        const titulo = (i.libro?.titulo || "").toLowerCase();
        const matchAutorNombre = Array.isArray(i.libro?.autores)
            ? i.libro.autores.some(a => (a?.nombre || "").toLowerCase().includes(q))
            : false;
        const matchAutorApellido = Array.isArray(i.libro?.autores)
            ? i.libro.autores.some(a => (a?.apellido1 || "").toLowerCase().includes(q))
            : false;
        return titulo.includes(q) || matchAutorNombre || matchAutorApellido;
    });
});

const cover = (l) => (l?.portadaUrl ? l.portadaUrl : "/img/cover-placeholder.svg");

const marcarLeido = (idLibro) =>
    router.post(
        route("lecturas.marcar.leido", { idLibro }),
        {},
        {
            preserveScroll: true,
            onSuccess: () =>
                Swal.fire("Hecho", "Marcado como leído", "success"),
            onError: () => Swal.fire("Error", "No se pudo marcar", "error"),
        }
    );

const marcarLeyendo = async (idLibro) => {
    if (props.estado === "completado") {
        const r = await Swal.fire({
            title: "¿Volver a leer?",
            text: "Se reabrirá tu lectura y se actualizarán las fechas.",
            icon: "question",
            showCancelButton: true,
            confirmButtonText: "Sí, continuar",
            cancelButtonText: "Cancelar",
            buttonsStyling: false,
            customClass: {
                confirmButton:
                    "bg-brandblue text-white rounded-full px-4 py-2 text-sm font-semibold",
                cancelButton:
                    "bg-gray-300 text-gray-800 rounded-full px-4 py-2 text-sm font-semibold",
                popup: "rounded-xl",
            },
        });
        if (!r.isConfirmed) return;
    }

    router.post(
        route("lecturas.marcar.leyendo", { idLibro }),
        {},
        {
            preserveScroll: true,
            onSuccess: () => Swal.fire("Hecho", "Lectura iniciada", "success"),
            onError: () => Swal.fire("Error", "No se pudo iniciar", "error"),
        }
    );
};

const abandonar = async (idLibro) => {
    const r = await Swal.fire({
        title: "¿Abandonar lectura?",
        text: "La lectura pasará a 'abandonado'.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Sí, abandonar",
        cancelButtonText: "Cancelar",
        buttonsStyling: false,
        customClass: {
            confirmButton:
                "bg-red-600 text-white rounded-full px-4 py-2 text-sm font-semibold",
            cancelButton:
                "bg-gray-300 text-gray-800 rounded-full px-4 py-2 text-sm font-semibold",
            popup: "rounded-xl",
        },
    });
    if (!r.isConfirmed) return;

    router.post(
        route("lecturas.marcar.abandonado", { idLibro }),
        {},
        {
            preserveScroll: true,
            onSuccess: () =>
                Swal.fire("Hecho", "Lectura abandonada", "success"),
            onError: () => Swal.fire("Error", "No se pudo abandonar", "error"),
        }
    );
};
</script>

<template>
    <AuthenticatedLayout>
        <Head :title="estado === 'leyendo' ? 'Leyendo' : 'Leídos'" />

        <div class="px-8 py-6 space-y-6">
            <div class="flex gap-2">
                <Link
                    :href="route('lecturas.leyendo')"
                    class="px-4 py-2 rounded-full border"
                    :class="
                        estado === 'leyendo'
                            ? 'bg-brandblue text-white border-brandblue dark:bg-brandgold dark:text-white'
                            : 'hover:bg-slate-50 dark:bg-slate-100'
                    "
                    >Leyendo</Link
                >
                <Link
                    :href="route('lecturas.leidos')"
                    class="px-4 py-2 rounded-full border"
                    :class="
                        estado === 'completado'
                            ? 'bg-brandblue text-white border-brandblue dark:bg-brandgold dark:text-white'
                            : 'hover:bg-slate-50 dark:bg-slate-100'
                    "
                    >Leídos</Link
                >
            </div>

            <div class="max-w-3xl">
                <input
                    v-model="search"
                    type="text"
                    placeholder="Buscar libro"
                    class="w-full h-10 pl-4 pr-4 rounded-full border shadow-sm focus:ring-2 focus:ring-brandgold"
                />
            </div>

            <div
                v-if="filtered.length"
                class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-6"
            >
                <div
                    v-for="it in filtered"
                    :key="it.idLectura"
                    class="w-40 sm:w-44 text-center"
                >
                    <div
                        class="group relative rounded-2xl overflow-hidden shadow-sm"
                    >
                        <img
                            :src="cover(it.libro)"
                            :alt="it.libro?.titulo"
                            class="w-40 sm:w-44 h-56 sm:h-60 object-cover rounded-2xl"
                        />
                    </div>

                    <h2
                        class="mt-2 font-semibold text-brandblue dark:text-white text-sm line-clamp-2"
                    >
                        {{ it.libro?.titulo }}
                    </h2>
                    <p class="text-xs text-gray-500">{{ it.libro?.autores?.length ? it.libro.autores.map(a => `${a.nombre} ${a.apellido1}`).join(", ") : "Desconocido" }}</p>

                    <div class="mt-2">
                        <template v-if="estado === 'leyendo'">
                            <div
                                class="flex flex-col items-center gap-2 "
                            >
                                <button
                                    v-if="estado === 'leyendo'"
                                    @click="marcarLeido(it.libro.idLibro)"
                                    class="px-3 py-1 rounded-full border text-xs hover:bg-emerald-50 dark:bg-emerald-600 dark:hover:bg-emerald-700 dark:text-white"
                                >
                                    ✅ Marcar leído
                                </button>
                                <button
                                    @click="abandonar(it.libro.idLibro)"
                                    class="px-3 py-1 rounded-full border text-xs hover:bg-red-50 dark:bg-red-600 dark:hover:bg-red-700 dark:text-white"
                                >
                                    ⛔ Abandonar
                                </button>
                            </div>
                        </template>

                        <button
                            v-else
                            @click="marcarLeyendo(it.libro.idLibro)"
                            class="px-3 py-1 rounded-full border text-xs hover:bg-slate-50 dark:bg-brandblue dark:hover:bg-brandblue/80 dark:text-white"
                        >
                            📖 Volver a leer
                        </button>
                    </div>
                </div>
            </div>

            <div v-else class="py-12 text-center text-gray-500 dark:text-gray-400">
                No hay libros en esta sección.
            </div>

            <div
                v-if="props.lecturas?.links?.length"
                class="flex flex-wrap gap-2 justify-center"
            >
                <Link
                    v-for="link in props.lecturas.links"
                    :key="link.label"
                    :href="link.url || ''"
                    v-html="link.label"
                    class="px-3 py-1 rounded border"
                    :class="[
                        {
                            'bg-brandblue text-white border-brandblue':
                                link.active,
                        },
                        'hover:bg-slate-50 dark:bg-gray-600 dark:hover:bg-slate-700 dark:text-white',
                    ]"
                />
            </div>
        </div>
    </AuthenticatedLayout>
</template>
