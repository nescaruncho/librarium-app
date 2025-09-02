<script setup>
import { ref } from "vue";
import { Head, Link, router } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import Swal from "sweetalert2";

const props = defineProps({
    bibliotecas: Array,
    query: String,
    visibilidad: String,
    orden: String,
    miembroships: Object,
    solicitudesPendientes: Array,
});

const search = ref(props.query ?? "");

const handleSearch = () => {
    if (search.value.trim()) {
        router.get(route("bibliotecas.search"), {
            query: search.value.trim(),
        });
    }
};

const getRol = (idBiblioteca) => {
    for (const [rol, ids] of Object.entries(props.miembroships ?? {})) {
        if (ids.includes(idBiblioteca)) return rol;
    }
    return null;
};

const tieneSolicitudPendiente = (idBiblioteca) => {
    return props.solicitudesPendientes?.includes(idBiblioteca) || false;
};

const unirseABiblioteca = (idBiblioteca) => {
    router.post(
        route("miembro.store", idBiblioteca),
        {},
        {
            preserveScroll: true,
            onSuccess: () => {
                Swal.fire({
                    title: "UNIDO A LA BIBLIOTECA",
                    text: "Te has unido a la biblioteca correctamente.",
                    icon: "success",
                    confirmButtonText: "Aceptar",
                    customClass: {
                        popup: "rounded-xl border border-brandblue shadow-sm",
                        confirmButton:
                            "bg-brandblue text-white rounded-full px-6 py-2 text-sm font-semibold",
                    },
                    buttonsStyling: false,
                });
            },
        }
    );
};

const solicitarUnion = (idBiblioteca) => {
    router.post(
        route("solicitud-union.store", idBiblioteca),
        {},
        {
            preserveScroll: true,
            onSuccess: () => {
                Swal.fire({
                    title: "SOLICITUD ENVIADA",
                    text: "Tu solicitud para unirte a la biblioteca ha sido enviada.",
                    icon: "success",
                    confirmButtonText: "Aceptar",
                    customClass: {
                        popup: "rounded-xl border border-brandblue shadow-sm",
                        confirmButton:
                            "bg-brandblue text-white rounded-full px-6 py-2 text-sm font-semibold",
                    },
                    buttonsStyling: false,
                });
            },
        }
    );
};

const cancelarSolicitud = (idBiblioteca) => {
    router.delete(route("solicitud-union.destroyPropia", idBiblioteca), {
        preserveScroll: true,
        onSuccess: () => {
            Swal.fire({
                title: "SOLICITUD CANCELADA",
                text: "Tu solicitud para unirte a la biblioteca ha sido cancelada.",
                icon: "success",
                confirmButtonText: "Aceptar",
                customClass: {
                    popup: "rounded-xl border border-brandblue shadow-sm",
                    confirmButton:
                        "bg-brandblue text-white rounded-full px-6 py-2 text-sm font-semibold",
                },
                buttonsStyling: false,
            });
        },
    });
};
</script>

<template>
    <AuthenticatedLayout>
        <Head title="Resultados de búsqueda" />
        <div class="max-w-3xl mx-auto py-10">
            <h1 class="text-2xl font-bold mb-4 text-brandblue dark:text-white">
                Resultados de búsqueda para:
                <span class="text-brandblue dark:text-white">"{{ props.query }}"</span>
            </h1>

            <div
                v-if="props.bibliotecas.length === 0"
                class="text-gray-500 dark:text-gray-200 text-center py-10"
            >
                No se encontraron bibliotecas.
            </div>

            <div v-else class="space-y-4">
                <div
                    v-for="biblioteca in props.bibliotecas"
                    :key="biblioteca.idBiblioteca"
                    class="p-4 bg-white dark:bg-[#383838] rounded-lg shadow flex flex-col md:flex-row md:items-center justify-between"
                >
                    <div>
                        <h2 class="text-lg font-semibold text-brandblue dark:text-white">
                            {{ biblioteca.nombre }}
                        </h2>
                        <p class="text-gray-600 dark:text-gray-300">
                            {{ biblioteca.descripcion }}
                        </p>
                        <span
                            class="text-xs px-2 py-1 rounded bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200 mr-2"
                        >
                            {{
                                biblioteca.visibilidad === true
                                    ? "Pública"
                                    : "Privada"
                            }}
                        </span>
                    </div>
                    <div class="mt-4 md:mt-0 flex gap-2">
                        <PrimaryButton v-if="getRol(biblioteca.idBiblioteca)">
                            <Link :href="route('bibliotecas.show', biblioteca.idBiblioteca)">
                                Ver
                            </Link>
                        </PrimaryButton>

                        <PrimaryButton
                            v-else-if="biblioteca.visibilidad === true"
                            @click="() => unirseABiblioteca(biblioteca.idBiblioteca)"
                        >
                            Unirse
                        </PrimaryButton>

                        <PrimaryButton
                            v-else-if="biblioteca.visibilidad === false && !tieneSolicitudPendiente(biblioteca.idBiblioteca)"
                            @click="() => solicitarUnion(biblioteca.idBiblioteca)"
                        >
                            Solicitar unirse
                        </PrimaryButton>

                        <PrimaryButton
                            v-else-if="biblioteca.visibilidad === false && tieneSolicitudPendiente(biblioteca.idBiblioteca)"
                            class="bg-red-600 hover:bg-red-700"
                            @click="() => cancelarSolicitud(biblioteca.idBiblioteca)"
                        >
                            Cancelar solicitud
                        </PrimaryButton>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
