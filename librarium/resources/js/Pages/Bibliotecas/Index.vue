<script setup>
import { Head, Link, router } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { usePage } from "@inertiajs/vue3";
import { Pencil, Trash2, Users2, Settings, UserPlus2 } from "lucide-vue-next";
import BibliotecaIcon from "@/Components/Icons/BibliotecaIcon.vue";
import Swal from "sweetalert2";

const props = defineProps({
    bibliotecas: Array,
    biblioteca: Object,
});

const rolesTraducidos = {
    propietario: "Propietario",
    admin: "Admin",
    lector: "Lector",
};

const { flash } = usePage().props;

function confirmarEliminar(biblioteca) {
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

const abandonarBiblioteca = (idBiblioteca) => {
    Swal.fire({
        title: "¿Abandonar biblioteca?",
        text: "Esta acción te eliminará de la biblioteca.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Sí, abandonar",
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
            router.delete(route("miembro.leave", idBiblioteca), {
                preserveScroll: true,
                onSuccess: () => {
                    Swal.fire({
                        title: "¡ABANDONADA!",
                        text: "Has abandonado la biblioteca correctamente.",
                        icon: "success",
                        confirmButtonText: "ACEPTAR",
                        customClass: {
                            popup:
                                "rounded-xl border border-brandblue shadow-sm",
                            confirmButton:
                                "bg-brandgold text-white rounded-full px-6 py-2 text-sm font-semibold",
                            title:
                                "text-2xl font-bold text-brandblue",
                            htmlContainer: "text-gray-700",
                        },
                    });
                },
            });
        }
    });
};

</script>

<template>
    <AuthenticatedLayout>
        <Head title="Mis bibliotecas" />

        <div class="px-8 py-6 space-y-4">
            <div class="flex justify-between items-center mb-4">
                <h1 class="text-2xl font-bold text-brandblue dark:text-white">
                    Mis bibliotecas
                </h1>
                <Link
                    :href="route('bibliotecas.create')"
                    class="bg-brandgold text-white px-4 py-2 rounded-full font-semibold text-sm hover:bg-brandgold/80"
                >
                    CREAR
                </Link>
            </div>

            <div class="space-y-4">
                <div
                    v-for="(b, index) in bibliotecas"
                    :key="b.idBiblioteca"
                    :class="[
                        'rounded-xl p-4 shadow-sm flex items-center justify-between',
                        index % 2 === 0 ? 'bg-yellow-50 dark:bg-[#383838]' : 'bg-white dark:bg-[#383838]',
                    ]"
                >
                    <div class="flex items-center gap-4">
                        <BibliotecaIcon
                            :class="
                                index % 2 === 0
                                    ? 'text-brandblue dark:text-white'
                                    : 'text-brandgold dark:text-white'
                            "
                        />
                        <div>
                            <h2 class="text-lg font-bold text-brandblue dark:text-white">
                                {{ b.nombre }}
                            </h2>
                            <p class="text-xs font-semibold text-brandblue dark:text-white">
                                {{ rolesTraducidos[b.rol] || "Miembro" }}
                            </p>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                {{ b.descripcion }}
                            </p>
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        <Link
                            v-if="b.rol === 'propietario'"
                            :href="
                                route(
                                    'bibliotecas.edit',
                                    b.idBiblioteca
                                )
                            "
                            class="ml-2 text-brandblue hover:text-brandgold transition dark:text-white dark:hover:text-brandgold"
                            title="Editar biblioteca"
                        >
                            <Pencil class="w-5 h-5" />
                        </Link>
                        <button
                            v-if="b.rol === 'propietario'"
                            @click="confirmarEliminar(b)"
                            class="ml-2 text-brandblue hover:text-red-700 transition dark:text-white dark:hover:text-red-700"
                            title="Eliminar biblioteca"
                        >
                            <Trash2 class="w-5 h-5" />
                        </button>
                        <Link
                            v-if="b.rol !== 'lector'"
                            :href="route('miembro.index', b.idBiblioteca)"
                            title="Miembros"
                            class="text-brandblue hover:text-brandgold transition dark:text-white dark:hover:text-brandgold"
                        >
                            <Users2 class="w-5 h-5" />
                        </Link>
                        <Link>

                        </Link>
                        <Link
                            v-if="b.rol !== 'lector'"
                            :href="route('solicitud-union.index', b.idBiblioteca)"
                            title="Solicitudes"
                            class="text-brandblue hover:text-brandgold transition dark:text-white dark:hover:text-brandgold"
                        >
                            <UserPlus2 class="w-5 h-5" />
                        </Link>
                        <Link
                            v-if="b.rol !== 'lector'"
                            :href="route('bibliotecas.editConfig', b.idBiblioteca)"
                            title="Configuración"
                            class="text-brandblue hover:text-brandgold transition dark:text-white dark:hover:text-brandgold"
                        >
                            <Settings class="w-5 h-5" />
                        </Link>
                        <Link
                            :href="route('bibliotecas.show', b.idBiblioteca)"
                            class="bg-brandblue text-white font-bold text-xs px-4 py-2 rounded-full hover:bg-brandgold transition dark:bg-brandgold dark:text-white dark:hover:bg-brandgold/80"
                        >
                            VER
                        </Link>
                        <button
                            v-if="b.rol !== 'propietario'"
                            @click="abandonarBiblioteca(b.idBiblioteca)"
                            class="bg-red-500 text-white font-bold text-xs px-4 py-2 rounded-full hover:bg-red-600 transition"
                            title="Abandonar biblioteca"
                        >
                            ABANDONAR
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
