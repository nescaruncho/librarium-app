<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, router, Link } from "@inertiajs/vue3";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import Swal from "sweetalert2";
import { CheckCircle, XCircle, ChevronLeft } from "lucide-vue-next";

const props = defineProps({
    solicitudes: Array,
    biblioteca: Object,
});

const gestionarSolicitud = (idSolicitudUnion, accion) => {
    router.patch(
        route("solicitud-union.update", {
            biblioteca: props.biblioteca.idBiblioteca,
            idSolicitudUnion: idSolicitudUnion,
        }),
        { accion },
        {
            preserveScroll: true,
            onSuccess: () => {
                Swal.fire({
                    title:
                        accion === "aceptar"
                            ? "SOLICITUD ACEPTADA"
                            : "SOLICITUD RECHAZADA",
                    text:
                        accion === "aceptar"
                            ? "El usuario ha sido añadido a la biblioteca."
                            : "La solicitud ha sido rechazada.",
                    icon: "success",
                    confirmButtonText: "ACEPTAR",
                    customClass: {
                        popup: "rounded-xl border border-brandblue shadow-sm",
                        confirmButton:
                            "bg-brandgold text-white rounded-full px-6 py-2 text-sm font-semibold",
                        title: "text-2xl font-bold text-brandblue",
                    },
                });
            },
            onError: (errors) => {
                console.error("🔥 Errores recibidos:", errors);
                Swal.fire({
                    title: "ERROR",
                    text: "No se pudo procesar la solicitud. Inténtalo de nuevo.",
                    icon: "error",
                    confirmButtonText: "ACEPTAR",
                });
            },
        }
    );
};
</script>

<template>
    <AuthenticatedLayout>
        <Head :title="`Solicitudes - ${props.biblioteca.nombre}`" />
        <Link
            :href="route('bibliotecas.show', biblioteca.idBiblioteca)"
            class="group inline-flex items-center gap-2 text-brandblue hover:text-brandgold dark:text-white dark:hover:text-brandgold pointer-events-auto relative z-10 focus:outline-none focus-visible:ring-2 focus-visible:ring-brandgold"
        >
            <ChevronLeft
                class="w-5 h-5 -ml-1 transition-transform group-hover:-translate-x-0.5 text-brandblue dark:text-white"
            />
            <span>Volver a {{ biblioteca.nombre }}</span>
        </Link>
        <div class="max-w-4xl mx-auto py-10 px-4">
            <h1 class="text-2xl font-bold text-brandblue dark:text-white mb-6">
                Solicitudes de unión a {{ props.biblioteca.nombre }}
            </h1>

            <div
                v-if="props.solicitudes.length === 0"
                class="text-gray-500 text-center py-10"
            >
                No hay solicitudes pendientes.
            </div>

            <div v-else class="space-y-6">
                <div
                    v-for="solicitud in props.solicitudes"
                    :key="solicitud.idSolicitudUnion"
                    class="bg-white rounded-lg shadow p-6 flex items-center justify-between"
                >
                    <div>
                        <h2 class="text-lg font-semibold text-brandblue">
                            {{ solicitud.usuario.nombre }}
                        </h2>
                        <p class="text-gray-600">
                            @{{ solicitud.usuario.username }}
                        </p>
                    </div>
                    <div class="flex space-x-4">
                        <PrimaryButton
                            @click="
                                gestionarSolicitud(
                                    solicitud.idSolicitudUnion,
                                    'aceptar'
                                )
                            "
                            class="bg-green-600 hover:bg-green-700 text-white"
                        >
                            <CheckCircle class="w-5 h-5 inline-block" />
                        </PrimaryButton>
                        <PrimaryButton
                            @click="
                                gestionarSolicitud(
                                    solicitud.idSolicitudUnion,
                                    'rechazar'
                                )
                            "
                            class="bg-red-600 hover:bg-red-700 text-white"
                        >
                            <XCircle class="w-5 h-5 inline-block" />
                        </PrimaryButton>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
