<script setup>
import { Head, Link, useForm } from "@inertiajs/vue3";
import { ChevronLeft } from "lucide-vue-next";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Swal from "sweetalert2";

const props = defineProps({
    biblioteca: {
        type: Object,
        required: true,
    },
});

const defaults = {
    prestamosHabilitados: props.biblioteca.prestamosHabilitados ?? false,
    etiquetasHabilitadas: props.biblioteca.etiquetasHabilitadas ?? false,
    politica: {
        maxLibrosSimultaneos:
            props.biblioteca.politicaprestamo?.maxLibrosSimultaneos ?? 4,
        duracionPrestamoDias:
            props.biblioteca.politicaprestamo?.duracionPrestamoDias ?? 14,
        numeroMaxProrrogas:
            props.biblioteca.politicaprestamo?.numeroMaxProrrogas ?? 2,
        duracionProrrogaDias:
            props.biblioteca.politicaprestamo?.duracionProrrogaDias ?? 7,
        penalizacionDias:
            props.biblioteca.politicaprestamo?.penalizacionDias ?? 0,
    },
    configEtiqueta: {
        longitudMaxima:
            props.biblioteca.configuracionetiqueta?.longitudMaxima ?? 12,
        separador: props.biblioteca.configuracionetiqueta?.separador ?? "-",
        formato: props.biblioteca.configuracionetiqueta?.formato ?? [],
    },
};

const form = useForm({
    prestamosHabilitados: defaults.prestamosHabilitados,
    etiquetasHabilitadas: defaults.etiquetasHabilitadas,
    politica: { ...defaults.politica },
    configEtiqueta: { ...defaults.configEtiqueta },
});

const CAMPOS_ETIQUETA = [
    { key: "TITULO", label: "Título" },
    { key: "APELLIDO_AUTOR", label: "Apellido autor" },
    { key: "NOMBRE_AUTOR", label: "Nombre autor" },
    { key: "GENERO", label: "Género" },
    { key: "EDITORIAL", label: "Editorial" },
    { key: "IDIOMA", label: "Idioma" },
];

const previewEtiqueta = () => {
    if (!form.etiquetasHabilitadas || form.configEtiqueta.formato.length === 0)
        return "—";

    const tokens = form.configEtiqueta.formato;
    const sep = form.configEtiqueta.separador || "-";
    const n = tokens.length;

    const maxlength = Math.min(
        Number(form.configEtiqueta.longitudMaxima || 12),
        16
    );

    const sepLenTotal = sep.length * (n - 1);

    const baseBudget = Math.max(1, maxlength - sepLenTotal);

    const perToken = Math.max(1, Math.floor(baseBudget / n));
    const extra = baseBudget % n;

    const parts = tokens.map((t, i) => {
        const asignado = perToken + (i < extra ? 1 : 0);
        const abbr = t.slice(0, 3).toUpperCase();
        return abbr.slice(0, asignado);
    });

    let base = parts.join(sep);

    if (base.length > maxlength) {
        base = base.slice(0, maxlength);
    }

    return base;
};

const submit = () => {
    form.patch(
        route("bibliotecas.updateConfig", props.biblioteca.idBiblioteca),
        {
            preserveScroll: true,
            onSuccess: () => {
                Swal.fire({
                    icon: "success",
                    title: "Configuración actualizada",
                    text: "Los cambios se han guardado correctamente.",
                    confirmButtonText: "Aceptar",
                });
            },
            onError: (errors) => {
                let listaErrores = Object.values(errors).join("<br>");
                Swal.fire({
                    icon: "error",
                    title: "Error al actualizar",
                    text: "Ocurrió un error al guardar los cambios.",
                    html: listaErrores,
                    confirmButtonText: "Aceptar",
                });
            },
        }
    );
};

function regenerarEtiquetas() {
    Swal.fire({
        title: "¿Estás seguro?",
        text: "Esto regenerará todas las etiquetas de los libros en la biblioteca.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Sí, regenerar",
        cancelButtonText: "Cancelar",
    }).then((result) => {
        if (result.isConfirmed) {
            form.post(
                route("bibliotecas.regenerarEtiquetas", {
                    biblioteca: props.biblioteca.idBiblioteca,
                }),
                {
                    preserveScroll: true,
                    onSuccess: () => {
                        Swal.fire({
                            icon: "success",
                            title: "Etiquetas regeneradas",
                            text: "Las etiquetas se han regenerado correctamente.",
                            confirmButtonText: "Aceptar",
                        });
                    },
                    onError: (errors) => {
                        let listaErrores = Object.values(errors).join("<br>");
                        Swal.fire({
                            icon: "error",
                            title: "Error al regenerar etiquetas",
                            text: "Ocurrió un error al regenerar las etiquetas.",
                            html: listaErrores,
                            confirmButtonText: "Aceptar",
                        });
                    },
                }
            );
        }
    });
}
</script>

<template>
    <Head :title="`Configurar biblioteca — ${biblioteca.nombre}`" />
    <AuthenticatedLayout>
        <div class="p-4 lg:p-10 space-y-6">
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
            <div class="flex items-center justify-between">
                <h1 class="text-2xl lg:text-3xl font-semibold text-brandblue dark:text-white tracking-tight">
                    Configuración - {{ biblioteca.nombre }}
                </h1>
            </div>

            <form
                @submit.prevent="submit"
                class="grid grid-cols-1 lg:grid-cols-2 gap-8"
            >
                <section class="rounded-2xl bg-white shadow p-6 space-y-6 dark:bg-[#383838]">
                    <div class="flex items-center justify-between">
                        <h2 class="text-lg font-semibold text-brandblue dark:text-white">
                            Políticas de préstamo
                        </h2>
                        <label class="inline-flex items-center gap-3">
                            <span class="text-sm text-gray-600 dark:text-gray-300">Activar</span>
                            <input
                                type="checkbox"
                                v-model="form.prestamosHabilitados"
                                class="h-5 w-10 appearance-none rounded-full bg-gray-300 checked:bg-emerald-500 relative transition"
                            />
                        </label>
                    </div>

                    <div
                        :class="{
                            'opacity-50 pointer-events-none':
                                !form.prestamosHabilitados,
                        }"
                        class="grid gap-4"
                    >
                        <div>
                            <label class="block text-sm font-medium text-brandblue dark:text-white"
                                >Límite de libros</label
                            >
                            <input
                                type="number"
                                min="1"
                                max="100"
                                v-model.number="
                                    form.politica.maxLibrosSimultaneos
                                "
                                class="mt-1 w-full rounded-xl border px-3 py-2 dark:border-white dark:bg-gray-800 dark:text-slate-100"
                            />
                            <InputError
                                :msg="
                                    form.errors['politica.maxLibrosSimultaneos']
                                "
                            />
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-brandblue dark:text-white"
                                >Duración préstamo (días)</label
                            >
                            <input
                                type="number"
                                min="1"
                                max="365"
                                v-model.number="
                                    form.politica.duracionPrestamoDias
                                "
                                class="mt-1 w-full rounded-xl border px-3 py-2 dark:border-white dark:bg-gray-800 dark:text-slate-100"
                            />
                            <InputError
                                :msg="
                                    form.errors['politica.duracionPrestamoDias']
                                "
                            />
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-brandblue dark:text-white"
                                    >Nº de prórrogas</label
                                >
                                <input
                                    type="number"
                                    min="0"
                                    max="10"
                                    v-model.number="
                                        form.politica.numeroMaxProrrogas
                                    "
                                    class="mt-1 w-full rounded-xl border px-3 py-2 dark:border-white dark:bg-gray-800 dark:text-slate-100"
                                />
                                <InputError
                                    :msg="
                                        form.errors[
                                            'politica.numeroMaxProrrogas'
                                        ]
                                    "
                                />
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-brandblue dark:text-white"
                                    >Duración prórroga (días)</label
                                >
                                <input
                                    type="number"
                                    min="1"
                                    max="180"
                                    v-model.number="
                                        form.politica.duracionProrrogaDias
                                    "
                                    class="mt-1 w-full rounded-xl border px-3 py-2 dark:border-white dark:bg-gray-800 dark:text-slate-100"
                                />
                                <InputError
                                    :msg="
                                        form.errors[
                                            'politica.duracionProrrogaDias'
                                        ]
                                    "
                                />
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-brandblue dark:text-white"
                                >Días de penalización</label
                            >
                            <input
                                type="number"
                                min="0"
                                max="365"
                                v-model.number="form.politica.penalizacionDias"
                                class="mt-1 w-full rounded-xl border px-3 py-2 dark:border-white dark:bg-gray-800 dark:text-slate-100"
                            />
                            <InputError
                                :msg="form.errors['politica.penalizacionDias']"
                            />
                        </div>
                    </div>
                </section>


                <section class="rounded-2xl bg-white shadow p-6 space-y-6 dark:bg-[#383838]">
                    <div class="flex items-center justify-between">
                        <h2 class="text-lg font-semibold text-brandblue dark:text-white">
                            Formato de etiquetas
                        </h2>
                        <label class="inline-flex items-center gap-3">
                            <span class="text-sm text-gray-600 dark:text-gray-300">Activar</span>
                            <input
                                type="checkbox"
                                v-model="form.etiquetasHabilitadas"
                                class="h-5 w-10 appearance-none rounded-full bg-gray-300 checked:bg-emerald-500 relative transition"
                            />
                        </label>
                    </div>

                    <div
                        :class="{
                            'opacity-50 pointer-events-none':
                                !form.etiquetasHabilitadas,
                        }"
                        class="space-y-5"
                    >
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-brandblue dark:text-white"
                                    >Nº de caracteres (máx. 12)
                                </label>
                                <input
                                    type="number"
                                    min="4"
                                    max="12"
                                    v-model.number="
                                        form.configEtiqueta.longitudMaxima
                                    "
                                    class="mt-1 w-full rounded-xl border px-3 py-2 dark:border-white dark:bg-gray-800 dark:text-slate-100"
                                />
                                <InputError
                                    :msg="
                                        form.errors[
                                            'configEtiqueta.longitudMaxima'
                                        ]
                                    "
                                />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-brandblue dark:text-white"
                                    >Separador</label
                                >
                                <input
                                    type="text"
                                    maxlength="5"
                                    v-model="form.configEtiqueta.separador"
                                    class="mt-1 w-full rounded-xl border px-3 py-2 dark:border-white dark:bg-gray-800 dark:text-slate-100"
                                />
                                <InputError
                                    :msg="
                                        form.errors['configEtiqueta.separador']
                                    "
                                />
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-2 text-brandblue dark:text-white"
                                >Selección de campos (máx. 4)</label
                            >
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                <label
                                    v-for="c in CAMPOS_ETIQUETA"
                                    :key="c.key"
                                    class="flex items-center gap-2 text-brandblue dark:text-gray-200"
                                >
                                    <input
                                        type="checkbox"

                                        :value="c.key"
                                        v-model="form.configEtiqueta.formato"
                                        :disabled="
                                            form.configEtiqueta.formato
                                                .length >= 5 &&
                                            !form.configEtiqueta.formato.includes(
                                                c.key
                                            )
                                        "
                                    />
                                    <span class="text-sm">{{ c.label }}</span>
                                </label>
                            </div>
                            <InputError
                                :msg="form.errors['configEtiqueta.formato']"
                            />
                            <InputError
                                :msg="form.errors['configEtiqueta.formato.*']"
                            />
                        </div>

                        <div
                            class="rounded-xl bg-gray-50 border px-4 py-3 text-sm text-gray-700 dark:border-white dark:bg-gray-800 dark:text-slate-100"
                        >
                            <span class="font-medium">Vista previa:</span>
                            <span class="ml-2 font-mono">{{
                                previewEtiqueta()
                            }}</span>
                        </div>
                    </div>
                </section>

                <div class="lg:col-span-2 flex items-center justify-between">
                    <Link
                        :href="
                            route('bibliotecas.show', biblioteca.idBiblioteca)
                        "
                        class="px-6 py-2 rounded-full border hover:bg-gray-50 transition text-brandblue font-semibold dark:border-white dark:bg-gray-100 dark:text-gray-800 dark:hover:bg-gray-300"
                    >
                        Cancelar
                    </Link>

                    <button
                        type="submit"
                        class="px-8 py-2 rounded-full bg-amber-500 text-white font-semibold hover:bg-amber-600"
                    >
                        Guardar
                    </button>
                </div>
            </form>
            <button
                @click="regenerarEtiquetas"
                class="bg-yellow-500 text-white px-4 py-2 rounded-md hover:bg-yellow-600 font-semibold"
            >
                Regenerar etiquetas
            </button>
        </div>
    </AuthenticatedLayout>
</template>

<script>
export default {
    components: {
        InputError: {
            props: { msg: String },
            template: `<p v-if="msg" class="mt-1 text-sm text-red-600">{{ msg }}</p>`,
        },
    },
};
</script>
