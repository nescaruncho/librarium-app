<script setup>
import { Head, Link, router, usePage } from "@inertiajs/vue3";
import { onMounted, computed, ref, watch } from "vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import {
    Settings,
    Pencil,
    Trash2,
    Users2,
    UserPlus2,
    X,
    Eye,
} from "lucide-vue-next";
import Swal from "sweetalert2";
import BibliotecaIcon from "@/Components/Icons/BibliotecaIcon.vue";
import LibroActionsMenu from "@/Components/LibroActionsMenu.vue";
import axios from "axios";

const props = defineProps({
    biblioteca: { type: Object, required: true },
    libros: { type: Array, default: () => [] },
    rol: { type: String, required: true },
});

const biblioteca = props.biblioteca;
const { flash } = usePage().props;

const searchQuery = ref("");
const searchResults = ref([]);
const isSearching = ref(false);
const showResults = ref(false);
const displayedLibros = computed(() => {
    return searchQuery.value.length >= 2 && showResults.value
        ? searchResults.value
        : props.libros;
});

function debounce(func, wait) {
    let timeout;
    return function (...args) {
        const context = this;
        clearTimeout(timeout);
        timeout = setTimeout(() => func.apply(context, args), wait);
    };
}

const performSearch = debounce(async () => {
    if (searchQuery.value.length < 2) {
        searchResults.value = [];
        showResults.value = false;
        isSearching.value = false;
        return;
    }

    isSearching.value = true;
    try {
        const response = await axios.get(
            route("libros.searchEnBiblioteca", {
                biblioteca: biblioteca.idBiblioteca,
                query: searchQuery.value,
            })
        );
        searchResults.value = response.data.libros;
        showResults.value = true;
    } catch (error) {
        console.error("Error buscando libros:", error);
        searchResults.value = [];
    } finally {
        isSearching.value = false;
    }
}, 300);

watch(searchQuery, (newValue) => {
    if (newValue.length >= 2) {
        performSearch();
    } else {
        showResults.value = false;
        searchResults.value = [];
    }
});

const clearSearch = () => {
    searchQuery.value = "";
    searchResults.value = [];
    showResults.value = false;
};

onMounted(() => {
    if (flash?.success) {
        Swal.fire({
            title: "¡ÉXITO!",
            text: flash.success,
            icon: "success",
            confirmButtonText: "ACEPTAR",
            customClass: {
                popup: "rounded-xl border border-brandblue shadow-sm",
                confirmButton:
                    "bg-brandgold text-white rounded-full px-6 py-2 text-sm font-semibold",
                title: "text-2xl font-bold text-brandblue",
                htmlContainer: "text-gray-700",
            },
            buttonsStyling: false,
        });
    }
});

function autoresToString(autores) {
    if (!Array.isArray(autores)) return "";
    return autores
        .map((a) => {
            if (typeof a === "string") return a;
            const nombre = a?.nombre || "";
            const ap1 = a?.apellido1 ?? "";
            return `${nombre} ${ap1}`.trim();
        })
        .filter(Boolean)
        .join(", ");
}

function confirmarEliminar() {
    Swal.fire({
        title: "¿Estás seguro?",
        text: "Esta acción eliminará la biblioteca y todos sus libros.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Sí, eliminar",
        cancelButtonText: "Cancelar",
        customClass: {
            popup: "rounded-xl border border-red-500 shadow-sm",
            confirmButton:
                "bg-red-600 text-white rounded-full px-6 py-2 text-sm font-semibold",
            cancelButton:
                "bg-gray-300 text-gray-800 rounded-full px-6 py-2 text-sm font-semibold",
            title: "text-2xl font-bold text-red-700",
            htmlContainer: "text-gray-700",
        },
        buttonsStyling: false,
    }).then((result) => {
        if (result.isConfirmed) {
            router.delete(
                route("bibliotecas.destroy", biblioteca.idBiblioteca),
                {
                    preserveScroll: true,
                    onSuccess: () => {
                        Swal.fire({
                            title: "¡ELIMINADA!",
                            text: "La biblioteca ha sido eliminada correctamente.",
                            icon: "success",
                            confirmButtonText: "ACEPTAR",
                            customClass: {
                                popup: "rounded-xl border border-brandblue shadow-sm",
                                confirmButton:
                                    "bg-brandgold text-white rounded-full px-6 py-2 text-sm font-semibold",
                                title: "text-2xl font-bold text-brandblue",
                                htmlContainer: "text-gray-700",
                            },
                        });
                    },
                }
            );
        }
    });
}

const coverSrc = (libro) => {
    return libro.portadaUrl || coverPlaceholder;
};

const coverPlaceholder = "/img/cover-placeholder.svg";

function goToLibro(libroId) {
    router.visit(
        route("libros.showEnBiblioteca", {
            biblioteca: biblioteca.idBiblioteca,
            idLibro: libroId,
        }),
        {
            preserveScroll: true,
        }
    );
}

function onEditar(libro, e) {
    e.stopPropagation();
    router.visit(
        route("ejemplares.edit", {
            biblioteca: biblioteca.idBiblioteca,
            idLibro: libro.idLibro,
        })
    );
}

function onEliminar(libro, e) {
    e.stopPropagation();
    Swal.fire({
        title: "Quitar libro de la biblioteca",
        text: "Se eliminarán las copias de este libro en esta biblioteca",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Sí, quitar",
        cancelButtonText: "Cancelar",
        customClass: {
            popup: "rounded-xl border border-red-500 shadow-sm",
            confirmButton:
                "bg-red-600 text-white rounded-full px-6 py-2 text-sm font-semibold",
            cancelButton:
                "bg-gray-300 text-gray-800 rounded-full px-6 py-2 text-sm font-semibold",
            title: "text-2xl font-bold text-red-700",
            htmlContainer: "text-gray-700",
        },
        buttonsStyling: false,
    }).then((r) => {
        if (!r.isConfirmed) return;
        router.delete(
            route("libros.destroy", {
                biblioteca: biblioteca.idBiblioteca,
                idLibro: libro.idLibro,
            }),
            { preserveScroll: true }
        );
    });
}
</script>

<template>
    <AuthenticatedLayout>
        <Head :title="biblioteca.nombre" />

        <div class="px-8 py-6 dark:bg-black bg-white space-y-6">
            <div
                class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4"
            >
                <div class="flex items-center gap-4">
                    <BibliotecaIcon
                        class="w-16 h-16 text-brandblue dark:text-white"
                    />
                    <h1
                        class="text-3xl font-bold text-brandblue dark:text-white"
                    >
                        {{ biblioteca.nombre }}
                    </h1>

                    <Link
                        v-if="rol === 'propietario'"
                        :href="
                            route('bibliotecas.edit', biblioteca.idBiblioteca)
                        "
                        class="ml-2 text-brandblue hover:text-brandgold transition dark:text-white"
                        title="Editar biblioteca"
                    >
                        <Pencil class="w-5 h-5" />
                    </Link>

                    <button
                        v-if="rol === 'propietario'"
                        @click="confirmarEliminar"
                        class="ml-2 text-brandblue hover:text-red-700 transition dark:text-white"
                        title="Eliminar biblioteca"
                    >
                        <Trash2 class="w-5 h-5" />
                    </button>

                    <Link
                        v-if="rol !== 'lector'"
                        :href="route('miembro.index', biblioteca.idBiblioteca)"
                        class="ml-2 text-brandblue hover:text-brandgold transition dark:text-white"
                        title="Miembros de la biblioteca"
                    >
                        <Users2 class="w-5 h-5" />
                    </Link>

                    <Link
                        v-if="rol !== 'lector'"
                        :href="
                            route(
                                'solicitud-union.index',
                                biblioteca.idBiblioteca
                            )
                        "
                        class="ml-2 text-brandblue hover:text-brandgold transition dark:text-white"
                        title="Solicitudes de unión"
                    >
                        <UserPlus2 class="w-5 h-5" />
                    </Link>

                    <Link
                        v-if="rol !== 'lector'"
                        :href="
                            route(
                                'bibliotecas.editConfig',
                                biblioteca.idBiblioteca
                            )
                        "
                        class="ml-2 text-brandblue hover:text-brandgold transition dark:text-white"
                        title="Configuración de la biblioteca"
                    >
                        <Settings class="w-5 h-5" />
                    </Link>
                </div>

                <div class="flex items-center gap-2 w-full md:w-auto">
                    <Link
                        v-if="['propietario', 'admin'].includes(rol)"
                        :href="
                            route('ejemplares.create', {
                                biblioteca: biblioteca.idBiblioteca,
                            })
                        "
                        class="bg-brandblue text-white px-4 py-2 rounded-full font-semibold text-sm hover:bg-brandgold transition dark:bg-brandgold dark:hover:bg-brandgold/80"
                    >
                        AÑADIR LIBRO
                    </Link>
                </div>
            </div>

            <div class="flex justify-center mt-4">
                <div class="relative w-full max-w-4xl mx-6">
                    <div class="relative">
                        <input
                            type="text"
                            placeholder="Buscar en la biblioteca"
                            v-model="searchQuery"
                            class="w-full h-10 pl-4 pr-10 rounded-full border border-gray-300 shadow-md focus:outline-none focus:ring-2 focus:ring-yellow-400"
                        />
                        <button
                            v-if="searchQuery"
                            @click="clearSearch"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-700"
                        >
                            <X class="w-4 h-4" />
                        </button>

                        <div
                            v-if="isSearching"
                            class="absolute right-10 top-1/2 -translate-y-1/2"
                        >
                            <div
                                class="w-4 h-4 border-2 border-gray-300 border-t-blue-500 rounded-full animate-spin"
                            ></div>
                        </div>
                    </div>
                </div>
            </div>

            <div
                v-if="searchQuery.length >= 2 && showResults"
                class="mb-4 text-center"
            >
                <p class="text-sm text-gray-500 mb-2">
                    <span v-if="searchResults.length"
                        >{{ searchResults.length }} resultados</span
                    >
                    <span v-else
                        >No se encontraron resultados para "{{
                            searchQuery
                        }}"</span
                    >
                </p>
            </div>

            <div
                v-if="displayedLibros.length"
                class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-8 justify-items-center"
            >
                <div
                    v-for="libro in displayedLibros"
                    :key="libro.idLibro"
                    class="w-40 sm:w-44 text-center cursor-pointer select-none"
                    @click="goToLibro(libro.idLibro)"
                >
                    <div
                        class="group relative rounded-2xl overflow-hidden shadow-sm"
                    >
                        <img
                            :src="coverSrc(libro)"
                            :alt="libro.titulo"
                            @error="(e) => (e.target.src = coverPlaceholder)"
                            class="w-40 sm:w-44 h-56 sm:h-60 object-cover rounded-2xl transition group-hover:scale-[1.02] group-hover:shadow-lg"
                        />

                        <LibroActionsMenu :id-libro="libro.idLibro"
                        :id-biblioteca="biblioteca.idBiblioteca"
                        :estado-lectura="libro.estadoLectura || null" trigger="click"
                        />

                        <div
                            class="pointer-events-none absolute inset-0 rounded-2xl bg-black/0 group-hover:bg-black/40 transition"
                        />

                        <button
                            title="Ver"
                            @click.stop="goToLibro(libro.idLibro)"
                            class="pointer-events-auto absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 opacity-0 group-hover:opacity-100 transition bg-white/95 p-3 rounded-full shadow"
                        >
                            <Eye class="w-5 h-5 text-slate-800" />
                        </button>

                        <button
                            v-if="['propietario', 'admin'].includes(rol)"
                            title="Quitar de la biblioteca"
                            @click="onEliminar(libro, $event)"
                            class="pointer-events-auto absolute right-1 bottom-3 -translate-x-1/2 opacity-0 group-hover:opacity-100 transition bg-white/95 p-2 rounded-full shadow"
                        >
                            <Trash2 class="w-4 h-4 text-red-600" />
                        </button>
                    </div>

                    <h2
                        class="mt-2 font-semibold text-brandblue dark:text-white text-base leading-snug line-clamp-2"
                    >
                        {{ libro.titulo }}
                    </h2>
                    <p class="text-xs text-gray-500 line-clamp-1">
                        {{ autoresToString(libro.autores) }}
                    </p>
                </div>
            </div>

            <div
                v-else-if="!searchQuery || searchQuery.length < 2"
                class="text-center py-12"
            >
                <p class="text-gray-500">No hay libros en esta biblioteca.</p>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
