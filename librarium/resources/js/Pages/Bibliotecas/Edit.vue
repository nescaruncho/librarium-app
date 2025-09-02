<script setup>
import { Head, Link, useForm } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import InputError from "@/Components/InputError.vue";
import Swal from "sweetalert2";
import Textfield from "@/Components/Textfield.vue";

const props = defineProps({
    biblioteca: Object,
});

const form = useForm({
    nombre: props.biblioteca.nombre,
    descripcion: props.biblioteca.descripcion,
    visibilidad: props.biblioteca.visibilidad,
});

function submit() {
    form.put(route("bibliotecas.update", props.biblioteca.idBiblioteca), {
        onSuccess: () => {
            Swal.fire({
                title: "¡ACTUALIZADA!",
                text: "La biblioteca se ha actualizado correctamente.",
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
}
</script>

<template>
    <AuthenticatedLayout>
        <Head :title="`Editar ${biblioteca.nombre}`" />
        <h1 class="text-3xl font-bold text-brandblue mb-6 dark:text-white">
            Editar {{ biblioteca.nombre }}
        </h1>
        <form
            @submit.prevent="submit"
            class="bg-gray-100 p-6 rounded-xl shadow-md max-w-5xl mx-auto dark:bg-[#383838]"
        >
            <div class="flex flex-col md:flex-row gap-4 mb-6">
                <div class="w-full md:w-1/2">
                    <InputLabel for="nombre" value="Nombre" />
                    <TextInput
                        id="nombre"
                        type="text"
                        class="mt-1 block w-full rounded-full"
                        v-model="form.nombre"
                        required
                        maxlength="100"
                        placeholder="Máx 100 caracteres"
                    />
                    <InputError class="mt-2" :message="form.errors.nombre" />
                </div>

                <div class="w-full md:w-1/2">
                    <InputLabel for="visibilidad" value="Visibilidad" />
                    <div class="relative mt-2">
                        <select
                            id="visibilidad"
                            v-model="form.visibilidad"
                            required
                            class="block w-full appearance-none rounded-full h-11 px-4 pr-12 text-sm border border-brandblue focus:border-brandgold focus:ring-brandgold dark:border-white dark:bg-gray-800 dark:text-slate-100"
                        >
                            <option disabled value="">
                                Selecciona una opción
                            </option>
                            <option :value="true">Pública</option>
                            <option :value="false">Privada</option>
                        </select>
                    </div>
                    <InputError
                        class="mt-2"
                        :message="form.errors.visibilidad"
                    />
                </div>
            </div>
            <div class="mb-6">
                <div class="flex items-center gap-2">
                    <InputLabel for="descripcion" value="Descripción" />
                    <span class="text-sm text-gray-500">(opcional)</span>
                </div>
                <Textfield
                    id="descripcion"
                    class="mt-1 block w-full rounded-3xl"
                    v-model="form.descripcion"
                    placeholder="Máx 255 caracteres"
                    maxlength="255"
                />
                <InputError class="mt-2" :message="form.errors.descripcion" />
            </div>

            <div class="flex justify-center gap-6 mt-8">
                <Link
                    :href="route('bibliotecas.show', biblioteca.idBiblioteca)"
                    class="px-6 py-2 w-full text-center mx-20 rounded-full border-2 border-brandblue text-brandblue font-bold hover:bg-brandblue hover:text-white transition dark:border-white dark:bg-gray-100 dark:text-gray-800 dark:hover:bg-gray-300"
                >
                    CANCELAR
                </Link>
                <button
                    type="submit"
                    class="px-6 py-2 w-full mx-20 rounded-full bg-brandgold text-white font-bold hover:brightness-110 transition"
                >
                    ACEPTAR
                </button>
            </div>
        </form>
    </AuthenticatedLayout>
</template>
