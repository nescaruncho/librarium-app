<script setup>
import { router, Head, useForm, Link } from "@inertiajs/vue3";

const form = useForm({
    email: "",
    password: "",
    remember: false,
});

function submit() {
    form.post("login", {
        onFinish: () => form.reset("password"),
    });
}

function goToRegister() {
    router.visit("/register");
}
</script>

<template>
    <Head title="Bienvenido a Librarium" />
    <div
        class="relative min-h-screen flex flex-col lg:flex-row font-sans overflow-x-hidden"
    >
        <!-- Título arriba - responsivo -->
        <div
            class="absolute top-4 lg:top-16 left-1/2 transform -translate-x-1/2 text-center z-10"
        >
            <img
                src="/LIBRARIUM.svg"
                alt="Titulo Librarium"
                class="w-32 sm:w-40 md:w-48 lg:w-56 xl:w-64 h-auto"
            />
        </div>

        <!-- Logo en el centro - responsivo -->
        <div
            class="absolute top-20 sm:top-24 lg:top-1/2 left-1/2 transform -translate-x-1/2 lg:-translate-y-1/2 z-10"
        >
            <img
                src="/logo_app3_index.png"
                alt="Logo Librarium"
                class="w-16 h-16 sm:w-20 sm:h-20 md:w-24 md:h-24 lg:w-32 lg:h-32 xl:w-36 xl:h-36"
            />
        </div>

        <!-- Lado izquierdo: login -->
        <div
            class="w-full lg:w-1/2 flex flex-col justify-center items-center bg-white px-6 sm:px-8 pt-32 sm:pt-36 lg:pt-0 pb-8 lg:pb-0 min-h-[50vh] lg:min-h-screen"
        >
            <h1
                class="text-lg sm:text-xl lg:text-2xl text-brandblue font-bold mb-4 lg:mb-6 text-center"
            >
                Si estás de vuelta...
            </h1>
            <form
                @submit.prevent="submit"
                class="w-full max-w-sm space-y-4 lg:space-y-6"
            >
                <div>
                    <label
                        for="email"
                        class="block text-sm font-medium text-brandblue"
                    >
                        Correo electrónico
                    </label>
                    <input
                        id="email"
                        v-model="form.email"
                        type="email"
                        required
                        autofocus
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brandblue focus:ring-brandblue font-sans text-sm lg:text-base py-2 px-3"
                    />
                    <p
                        v-if="form.errors.email"
                        class="text-sm text-red-500 mt-1"
                    >
                        {{ form.errors.email }}
                    </p>
                </div>

                <div>
                    <label
                        for="password"
                        class="block text-sm font-medium text-brandblue"
                    >
                        Contraseña
                    </label>
                    <input
                        id="password"
                        v-model="form.password"
                        type="password"
                        required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brandblue focus:ring-brandblue font-sans text-sm lg:text-base py-2 px-3"
                    />
                    <p
                        v-if="form.errors.password"
                        class="text-sm text-red-500 mt-1"
                    >
                        {{ form.errors.password }}
                    </p>
                </div>

                <div class="flex items-center justify-between">
                    <label class="flex items-center">
                        <input
                            type="checkbox"
                            v-model="form.remember"
                            class="rounded border-gray-300 text-brandblue shadow-sm"
                        />
                        <span class="ml-2 text-sm text-brandblue">
                            Recordarme
                        </span>
                    </label>
                </div>

                <button
                    type="submit"
                    class="w-full py-2 lg:py-3 px-4 bg-brandblue text-white font-semibold rounded-md shadow hover:bg-opacity-90 transition text-sm lg:text-base"
                >
                    INICIAR SESIÓN
                </button>
                <p class="text-center mt-4">
                    <Link
                        :href="route('password.request')"
                        class="text-sem text-brandblue underline hover:text-brandgold"
                    >
                        ¿Olvidaste tu contraseña?
                    </Link>
                </p>
            </form>
        </div>

        <!-- Lado derecho: registro -->
        <div
            class="w-full lg:w-1/2 flex flex-col justify-center items-center bg-brandblue text-white px-6 sm:px-8 py-8 lg:py-0 min-h-[50vh] lg:min-h-screen"
        >
            <h1
                class="text-lg sm:text-xl lg:text-2xl font-bold mb-4 lg:mb-6 text-center"
            >
                ... o si acabas de llegar
            </h1>
            <button
                @click="goToRegister"
                class="py-2 lg:py-3 px-4 lg:px-6 bg-white text-brandblue font-bold rounded-md shadow hover:bg-gray-100 transition font-montserrat text-sm lg:text-base"
            >
                REGISTRARSE
            </button>
        </div>
    </div>
</template>
