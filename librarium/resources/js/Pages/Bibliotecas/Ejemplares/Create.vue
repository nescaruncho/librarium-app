<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import InputError from "@/Components/InputError.vue";
import { useForm, Head, Link } from "@inertiajs/vue3";
import { ChevronLeft } from "lucide-vue-next";

defineProps({
  biblioteca: Object,
});

const form = useForm({
  isbn: "",
  ubicacion: "",
});
</script>

<template>
  <Head title="Añadir ejemplar" />

  <AuthenticatedLayout>
        <div class="p-4 lg:p-10 space-y-6">

            <div class="flex items-center justify-between">
                <h1 class="text-2xl lg:text-3xl font-semibold text-brandblue dark:text-white tracking-tight">
                    Añadir libro a {{ biblioteca.nombre }}
                </h1>
            </div>
        </div>
    <form
      @submit.prevent="
        form.post(route('ejemplares.store', { biblioteca: biblioteca.idBiblioteca }))
      "
      class="max-w-5xl mx-auto rounded-2xl shadow-md p-8 bg-gray-50 dark:bg-[#383838]"
    >
      <div class="space-y-8">
        <div>
          <InputLabel for="isbn" value="ISBN" />
          <TextInput
            id="isbn"
            type="text"
            v-model="form.isbn"
            required
            maxlength="13"
            placeholder="Máx 100 caracteres"
            class="mt-2 w-full rounded-full px-6 py-3 border focus:ring-2 focus:ring-offset-0"
          />
          <InputError class="mt-2" :message="form.errors.isbn" />
        </div>

        <div>
          <InputLabel for="ubicacion" value="Ubicación (opcional)" />
          <TextInput
            id="ubicacion"
            type="text"
            v-model="form.ubicacion"
            maxlength="100"
            placeholder="Máx 100 caracteres"
            class="mt-2 w-full rounded-full px-6 py-3 border focus:ring-2 focus:ring-offset-0"
          />
          <InputError class="mt-2" :message="form.errors.ubicacion" />
        </div>
      </div>

      <div class="mt-10 flex justify-center gap-6">
        <Link
          :href="route('bibliotecas.show', biblioteca.idBiblioteca)"
          class="px-8 py-3 rounded-full border-2 bg-white hover:bg-gray-50 transition text-brandblue font-semibold dark:border-white dark:bg-gray-100 dark:text-gray-800 dark:hover:bg-gray-300"
        >
          CANCELAR
        </Link>

        <button
          type="submit"
          class="px-8 py-3 rounded-full font-semibold hover:opacity-90 bg-brandgold text-white"
        >
          ACEPTAR
        </button>
      </div>
    </form>
  </AuthenticatedLayout>
</template>
