<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, router } from "@inertiajs/vue3";
import { ref, computed } from "vue";
import {
    Pencil,
    Save,
    X,
    Trash2,
    ChevronLeft,
    FileDown,
} from "lucide-vue-next";
import Swal from "sweetalert2";
import dayjs from "dayjs";

const props = defineProps({
    biblioteca: Object,
    rol: String,
    libro: Object,
    ejemplares: Array,
});

const puedeEditar = ["propietario", "admin"].includes(props.rol);

const edits = ref(
    Object.fromEntries(
        (props.ejemplares || []).map((e) => [
            e.idEjemplar,
            { editing: false, ubicacion: e.ubicacion ?? "" },
        ])
    )
);

function startEdit(ej) {
    edits.value[ej.idEjemplar] = {
        editing: true,
        ubicacion: ej.ubicacion ?? "",
    };
}

function cancelEdit(ej) {
    edits.value[ej.idEjemplar].editing = false;
    edits.value[ej.idEjemplar].ubicacion = ej.ubicacion ?? "";
}

function saveEdit(ej) {
    const payload = { ubicacion: edits.value[ej.idEjemplar].ubicacion || null };
    router.patch(
        route("ejemplares.update", {
            biblioteca: props.biblioteca.idBiblioteca,
            ejemplar: ej.idEjemplar,
        }),
        payload,
        {
            preserveScroll: true,
            onSuccess: () => {
                ej.ubicacion = payload.ubicacion;
                edits.value[ej.idEjemplar].editing = false;
            },
        }
    );
}

function eliminarEjemplar(ej) {
    Swal.fire({
        title: "Eliminar ejemplar",
        text: "Esta copia se eliminará de la biblioteca.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Eliminar",
        cancelButtonText: "Cancelar",
        buttonsStyling: false,
        customClass: {
            confirmButton:
                "bg-red-600 text-white rounded-full px-6 py-2 text-sm font-semibold",
            cancelButton:
                "bg-gray-300 text-gray-800 rounded-full px-6 py-2 text-sm font-semibold",
            popup: "rounded-xl",
            title: "text-xl font-bold",
        },
    }).then((r) => {
        if (!r.isConfirmed) return;
        router.delete(
            route("ejemplares.destroy", {
                biblioteca: props.biblioteca.idBiblioteca,
                ejemplar: ej.idEjemplar,
            }),
            { preserveScroll: true }
        );
    });
}

const abrirModal = ref(false);
const ubicacionNueva = ref("");

function crearCopia() {
    router.post(
        route("ejemplares.storeCopia", {
            biblioteca: props.biblioteca.idBiblioteca,
            libro: props.libro.idLibro,
        }),
        { ubicacion: ubicacionNueva.value || null },
        {
            preserveScroll: true,
            onSuccess: () => {
                abrirModal.value = false;
                ubicacionNueva.value = "";
            },
        }
    );
}

const formatDate = (date) => {
    return date ? dayjs(date).format("DD-MM-YYYY") : "—";
};

function eliminarLibro() {
    Swal.fire({
        title: "Quitar libro de la biblioteca",
        text: "Se eliminarán todas las copias de este libro en esta biblioteca",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Sí, eliminar",
        cancelButtonText: "Cancelar",
        buttonsStyling: false,
        customClass: {
            confirmButton:
                "bg-red-600 text-white rounded-full px-6 py-2 text-sm font-semibold",
            cancelButton:
                "bg-gray-300 text-gray-800 rounded-full px-6 py-2 text-sm font-semibold",
            popup: "rounded-xl border border-red-500 shadow-sm",
            title: "text-xl font-bold text-red-700",
            htmlContainer: "text-gray-700",
        },
    }).then((result) => {
        if (!result.isConfirmed) return;

        router.delete(
            route("libros.destroy", {
                biblioteca: props.biblioteca.idBiblioteca,
                idLibro: props.libro.idLibro,
            }),
            {
                preserveScroll: true,
                onSuccess: () => {
                    router.visit(
                        route("bibliotecas.show", props.biblioteca.idBiblioteca)
                    );
                },
            }
        );
    });
}
</script>

<template>
    <AuthenticatedLayout>
        <Head :title="`${libro.titulo} – ${biblioteca.nombre}`" />

        <div class="px-8 py-6 space-y-8">
            <div class="flex items-center gap-4">
                <Link
                    :href="route('bibliotecas.show', biblioteca.idBiblioteca)"
                    class="group inline-flex items-center gap-2 text-brandblue hover:text-brandgold dark:text-white dark:hover:text-brandgold pointer-events-auto relative z-10 focus:outline-none focus-visible:ring-2 focus-visible:ring-brandgold"
                >
                    <ChevronLeft
                        class="w-5 h-5 -ml-1 transition-transform group-hover:-translate-x-0.5 text-brandblue dark:text-white"
                    />
                    <span>Volver a {{ biblioteca.nombre }}</span>
                </Link>
            </div>

            <div
                class="grid grid-cols-1 md:grid-cols-[220px_1fr] gap-8 items-start"
            >
                <div class="group relative rounded-xl overflow-hidden shadow">
                    <img
                        :src="libro.portadaUrl"
                        :alt="libro.titulo"
                        @error="
                            (e) => (e.target.src = '/img/cover-placeholder.svg')
                        "
                        class="w-[220px] h-[350px] object-cover rounded-xl transition group-hover:scale-[1.02] group-hover:shadow-lg"
                    />

                    <div
                        class="pointer-events-none absolute inset-0 rounded-xl bg-black/0 group-hover:bg-black/40 transition"
                    ></div>

                    <button
                        v-if="puedeEditar"
                        title="Quitar de la biblioteca"
                        @click="eliminarLibro"
                        class="pointer-events-auto absolute right-3 top-3 opacity-0 group-hover:opacity-100 transition bg-white/95 p-2 rounded-full shadow"
                    >
                        <Trash2 class="w-4 h-4 text-red-600" />
                    </button>
                </div>
                <div>
                    <h1 class="text-4xl font-extrabold text-brandblue dark:text-white mb-2">
                        {{ libro.titulo }}
                    </h1>
                    <div class="text-lg text-slate-600 mb-4 dark:text-gray-300">
                        {{ (libro.autores || []).join(", ") }}
                    </div>

                    <dl
                        class="grid grid-cols-2 gap-x-8 gap-y-2 text-sm text-slate-600"
                    >
                        <div>
                            <dt class="font-semibold text-slate-700 dark:text-gray-500">ISBN</dt>
                            <dd class="dark:text-gray-300">{{ libro.isbn || "—" }}</dd>
                        </div>
                        <div>
                            <dt class="font-semibold text-slate-700 dark:text-gray-500">
                                Editorial
                            </dt>
                            <dd class="dark:text-gray-300">{{ libro.editorial || "—" }}</dd>
                        </div>
                        <div>
                            <dt class="font-semibold text-slate-700 dark:text-gray-500">
                                Género(s)
                            </dt>
                            <dd class="dark:text-gray-300">
                                {{ (libro.generos || []).join(", ") || "—" }}
                            </dd>
                        </div>
                        <div>
                            <dt class="font-semibold text-slate-700 dark:text-gray-500">Idioma</dt>
                            <dd class="dark:text-gray-300">{{ libro.idioma || "—" }}</dd>
                        </div>
                        <div>
                            <dt class="font-semibold text-slate-700 dark:text-gray-500">
                                Fecha de publicación
                            </dt>
                            <dd class="dark:text-gray-300">
                                {{ formatDate(libro.fechaPublicacion) || "—" }}
                            </dd>
                        </div>
                    </dl>

                    <div class="mt-6">
                        <h3 class="text-brandgold font-bold mb-1">Sinopsis</h3>
                        <p class="text-slate-700 dark:text-gray-300 leading-relaxed line-clamp-5">
                            {{ libro.sinopsis || "—" }}
                        </p>
                    </div>
                </div>
            </div>

            <div>
                <h2 class="text-2xl font-bold text-brandblue dark:text-white mb-3">
                    Ejemplares
                </h2>

                <div class="overflow-x-auto rounded-xl border">
                    <table class="w-full text-sm">
                        <thead class="bg-brandblue dark:bg-[#383838] text-white">
                            <tr>
                                <th class="px-4 py-3 text-left w-16">#</th>
                                <th class="px-4 py-3 text-left">Etiqueta</th>
                                <th class="px-4 py-3 text-left">Ubicación</th>
                                <th class="px-4 py-3 text-center w-40">
                                    Disponible
                                </th>
                                <th
                                    class="px-4 py-3 text-center w-36"
                                    v-if="puedeEditar"
                                >
                                    Acciones
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="(ej, idx) in ejemplares"
                                :key="ej.idEjemplar"
                                class="odd:bg-white even:bg-slate-50 dark:odd:bg-gray-600 dark:even:bg-gray-500 dark:text-gray-300"
                            >
                                <td class="px-4 py-3">{{ idx + 1 }}</td>
                                <td class="px-4 py-3 font-mono">
                                    {{ ej.etiqueta || "—" }}
                                </td>

                                <td class="px-4 py-3">
                                    <div
                                        v-if="edits[ej.idEjemplar]?.editing"
                                        class="flex items-center gap-2"
                                    >
                                        <input
                                            v-model="
                                                edits[ej.idEjemplar].ubicacion
                                            "
                                            type="text"
                                            class="w-full border rounded-md px-3 py-1.5 dark:bg-gray-700 dark:text-gray-300"
                                            placeholder="Ej: Estante B3"
                                        />
                                    </div>
                                    <div v-else class="text-slate-700 dark:text-gray-300">
                                        {{ ej.ubicacion || "—" }}
                                    </div>
                                </td>

                                <td class="px-4 py-3 text-center">
                                    <span
                                        v-if="ej.disponible === true"
                                        class="inline-block px-2 py-1 rounded-full bg-green-100 text-green-700 text-xs font-semibold"
                                        >Sí</span
                                    >
                                    <span
                                        v-else-if="ej.disponible === false"
                                        class="inline-block px-2 py-1 rounded-full bg-red-100 text-red-700 text-xs font-semibold"
                                        >No</span
                                    >
                                    <span v-else class="text-slate-400">—</span>
                                </td>

                                <td
                                    class="px-4 py-3 text-center"
                                    v-if="puedeEditar"
                                >
                                    <div
                                        class="flex items-center justify-center gap-2"
                                    >
                                        <button
                                            v-if="
                                                !edits[ej.idEjemplar]?.editing
                                            "
                                            @click="startEdit(ej)"
                                            class="p-2 rounded-full hover:bg-brandgold/60"
                                            title="Editar ubicación"
                                        >
                                            <Pencil
                                                class="w-4 h-4 text-slate-700 dark:text-gray-300"
                                            />
                                        </button>
                                        <template v-else>
                                            <button
                                                @click="saveEdit(ej)"
                                                class="p-2 rounded-full hover:bg-emerald-100"
                                                title="Guardar"
                                            >
                                                <Save
                                                    class="w-4 h-4 text-emerald-500"
                                                />
                                            </button>
                                            <button
                                                @click="cancelEdit(ej)"
                                                class="p-2 rounded-full hover:bg-slate-100"
                                                title="Cancelar"
                                            >
                                                <X
                                                    class="w-4 h-4 text-slate-700"
                                                />
                                            </button>
                                        </template>

                                        <button
                                            @click="eliminarEjemplar(ej)"
                                            class="p-2 rounded-full hover:bg-red-100"
                                            title="Eliminar copia"
                                        >
                                            <Trash2
                                                class="w-4 h-4 text-red-600"
                                            />
                                        </button>
                                        <a
                                            v-if="
                                                biblioteca?.etiquetasHabilitadas &&
                                                ej?.etiqueta
                                            "
                                            :href="
                                                route(
                                                    'ejemplares.etiqueta.show',
                                                    {
                                                        biblioteca:
                                                            biblioteca.idBiblioteca,
                                                        ejemplar: ej.idEjemplar,
                                                    }
                                                )
                                            "
                                            target="_blank"
                                            rel="noopener"
                                            class="p-2 rounded-full hover:bg-slate-100"
                                            title="Descargar etiqueta PDF"
                                        >
                                            <FileDown
                                                class="w-4 h-4 text-slate-700"
                                            />
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="mt-4" v-if="puedeEditar">
                    <button
                        class="inline-flex items-center bg-brandgold text-white px-5 py-2 rounded-full font-semibold text-sm hover:opacity-90"
                        @click="abrirModal = true"
                    >
                        AGREGAR EJEMPLAR
                    </button>

                    <div
                        v-if="abrirModal"
                        class="fixed inset-0 bg-black/50 flex items-center justify-center z-50"
                    >
                        <div
                            class="bg-white rounded-2xl p-6 w-full max-w-md shadow-xl"
                        >
                            <h3 class="text-lg font-bold mb-4">
                                Añadir copia de "{{ libro.titulo }}"
                            </h3>
                            <label
                                class="block text-sm font-medium text-gray-700 mb-1"
                                >Ubicación (opcional)</label
                            >
                            <input
                                v-model="ubicacionNueva"
                                type="text"
                                class="w-full border rounded-md px-3 py-2 mb-4"
                                placeholder="Ej: Estante B3"
                            />
                            <div class="flex justify-end gap-2">
                                <button
                                    class="px-4 py-2 rounded-full border"
                                    @click="abrirModal = false"
                                >
                                    Cancelar
                                </button>
                                <button
                                    class="px-4 py-2 rounded-full bg-brandblue text-white"
                                    @click="crearCopia"
                                >
                                    Guardar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
