<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, router, Link } from "@inertiajs/vue3";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import Swal from "sweetalert2";
import { ref } from "vue";
import { Trash2, Star, ChevronLeft } from "lucide-vue-next";

const rolesTraducidos = {
    propietario: "Propietario",
    admin: "Admin",
    lector: "Lector",
};

const props = defineProps({
    miembros: Array,
    biblioteca: Object,
    rolUsuario: String,
    auth: Object,
});

const confirmarEliminar = (miembro) => {
    Swal.fire({
        title: "ELIMINAR MIEMBRO",
        text: `Esta acción eliminará a ${miembro.nombre} de la biblioteca.`,
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "ELIMINAR",
        cancelButtonText: "CANCELAR",
        customClass: {
            popup: "rounded-xl border border-red-500 shadow-sm",
            confirmButton:
                "bg-red-600 text-white rounded-full px-6 py-2 text-sm font-semibold",
            cancelButton:
                "bg-gray-300 text-gray-800 rounded-full px-6 py-2 text-sm font-semibold",
            title: "text-2xl font-bold text-red-700",
            htmlContainer: "text-gray-700",
        },
    }).then((result) => {
        if (result.isConfirmed) {
            router.delete(
                route("miembro.destroy", {
                    biblioteca: props.biblioteca.idBiblioteca,
                    miembro: miembro.idMiembro,
                }),
                {
                    preserveScroll: true,
                    onSuccess: () => {
                        Swal.fire({
                            title: "¡ELIMINADO!",
                            text: `${miembro.nombre} ha sido eliminado correctamente.`,
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
};

const toggleRol = (miembro) => {
    router.patch(
        route("miembro.update", [
            props.biblioteca.idBiblioteca,
            miembro.idMiembro,
        ]),
        {},
        {
            preserveScroll: true,
            onSuccess: () => {
                miembro.rol = miembro.rol === "admin" ? "lector" : "admin";
                Swal.fire({
                    title: "ROL ACTUALIZADO",
                    text: `El rol de ${miembro.nombre} ha sido actualizado.`,
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
        }
    );
};
</script>

<template>
    <AuthenticatedLayout>
        <Head title="Gestión de miembros" />
        <div class="flex items-center gap-3">
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
        <div class="max-w-3xl mx-auto py-10">
            <h1 class="text-2xl text-brandblue dark:text-white font-bold mb-4">
                Miembros de {{ props.biblioteca.nombre }}
            </h1>

            <div
                v-if="props.miembros.length === 0"
                class="text-gray-500 text-center py-10"
            >
                No hay miembros en esta biblioteca.
            </div>

            <div v-else class="space-y-4">
                <div
                    v-for="miembro in props.miembros"
                    :key="miembro.id"
                    class="p-4 bg-white dark:bg-[#383838] rounded-lg shadow flex flex-col md:flex-row md:items-center justify-between"
                >
                    <div>
                        <h2
                            class="text-lg text-brandblue dark:text-white font-semibold"
                        >
                            {{ miembro.nombre }}
                        </h2>
                        <p class="text-gray-600 dark:text-gray-400">
                            {{ miembro.username }}
                        </p>
                        <p class="text-sm text-gray-500 dark:text-gray-500">
                            {{ rolesTraducidos[miembro.rol] || miembro.rol }}
                        </p>
                    </div>
                    <div class="mt-4 md:mt-0 flex space-x-2">
                        <button
                            v-if="
                                props.rolUsuario !== 'lector' &&
                                miembro.rol !== 'propietario'
                            "
                            @click="toggleRol(miembro)"
                            class="[ 'p-2 rounded-full border transition' miembro.rol === 'admin' ? 'border-brandblue text-brandblue' : 'border-brandblue text-brandblue' ]"
                            title="Cambiar rol admin"
                        >
                            <Star
                                class="w-5 h-5 text-brandblue"
                                :fill="
                                    miembro.rol === 'admin'
                                        ? '#FFD700'
                                        : 'white'
                                "
                            />
                        </button>
                        <button
                            v-if="
                                props.rolUsuario !== 'lector' &&
                                miembro.rol !== 'propietario' &&
                                miembro.idUsuario !== props.auth.user.idUsuario
                            "
                            @click.prevent="confirmarEliminar(miembro)"
                            class="text-red-500 hover:text-red-700"
                        >
                            <Trash2 class="w-5 h-5" />
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
