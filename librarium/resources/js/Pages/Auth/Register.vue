<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';

const form = useForm({
    username: '',
    email: '',
    nombre: '',
    apellidos: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
  <GuestLayout>
    <Head title="Registro" />

    <div class="min-h-screen flex items-center justify-center px-4">
      <div class="w-full max-w-4xl bg-white rounded-lg py-12 px-8 grid place-items-center">
        <ApplicationLogo class="w-16 h-16 mb-4" />

        <h1 class="text-3xl font-bold font-montserrat text-brandblue text-center mb-10">
          REGISTRO
        </h1>

        <form @submit.prevent="submit" class="w-full">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
              <InputLabel for="nombre" value="Nombre" />
              <TextInput
                id="nombre"
                v-model="form.nombre"
                type="text"
                class="mt-1 block w-full rounded-full border border-brandblue"
                required
                autocomplete="given-name"
              />
              <InputError class="mt-2" :message="form.errors.nombre" />
            </div>

            <div>
              <InputLabel for="apellidos" value="Apellidos" />
              <TextInput
                id="apellidos"
                v-model="form.apellidos"
                type="text"
                class="mt-1 block w-full rounded-full border border-brandblue"
                required
                autocomplete="family-name"
              />
              <InputError class="mt-2" :message="form.errors.apellidos" />
            </div>

            <div>
              <InputLabel for="username" value="Usuario" />
              <TextInput
                id="username"
                v-model="form.username"
                type="text"
                class="mt-1 block w-full rounded-full border border-brandblue"
                required
              />
              <InputError class="mt-2" :message="form.errors.username" />
            </div>

            <div>
              <InputLabel for="email" value="Correo electrónico" />
              <TextInput
                id="email"
                v-model="form.email"
                type="email"
                class="mt-1 block w-full rounded-full border border-brandblue"
                required
                autocomplete="email"
              />
              <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div>
              <InputLabel for="password" value="Contraseña" />
              <TextInput
                id="password"
                v-model="form.password"
                type="password"
                class="mt-1 block w-full rounded-full border border-brandblue"
                required
                autocomplete="new-password"
              />
              <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div>
              <InputLabel for="password_confirmation" value="Repetir contraseña" />
              <TextInput
                id="password_confirmation"
                v-model="form.password_confirmation"
                type="password"
                class="mt-1 block w-full rounded-full border border-brandblue"
                required
                autocomplete="new-password"
              />
              <InputError class="mt-2" :message="form.errors.password_confirmation" />
            </div>
          </div>

          <div class="mt-10 flex justify-center">
            <PrimaryButton
              class="bg-brandgold text-white font-bold px-8 py-2 rounded-full hover:bg-yellow-500 transition"
              :class="{ 'opacity-25': form.processing }"
              :disabled="form.processing"
            >
              CREAR CUENTA
            </PrimaryButton>
          </div>

          <div class="mt-6 text-center">
            <Link
              href="/"
              class="text-brandblue font-semibold underline hover:text-brandgold"
            >
              ¿Ya tienes una cuenta?
            </Link>
          </div>
        </form>
      </div>
    </div>
  </GuestLayout>
</template>
