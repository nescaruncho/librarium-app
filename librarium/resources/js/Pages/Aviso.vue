<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head } from "@inertiajs/vue3";
</script>

<template>
    <Head title="Aviso Legal" />
    <AuthenticatedLayout>
        <main class="p-6">
            <div class="max-w-3xl mx-auto bg-white shadow-md rounded-lg p-8 dark:bg-[#383838]">
                <h1 class="text-2xl font-bold mb-4 dark:text-white">Aviso Legal</h1>
                <h2 class="font-semibold mt-8 mb-2 dark:text-white">
                    Responsable del sitio web
                </h2>
                <p class="mb-4 dark:text-white">Andrés Caruncho</p>
                <h2 class="font-semibold mt-8 mb-2 dark:text-white">
                    Finalidad
                </h2>
                <p class="mb-4 dark:text-white">
                    La finalidad de este sitio web es el desarrollo de un proyecto de gestión de bibliotecas personales, familiares o comunitarias en el marco del TFC del Ciclo Superior de Desarrollo de Aplicaciones Multiplataforma del IES San Clemente.
                </p>
                <p class="mb-4 mt-8 dark:text-white">
                    Si tienes preguntas o quieres aportar al proyecto, puedes
                    contactarme en
                    <a
                        href="mailto:nescaruncho@gmail.com"
                        class="underline hover:text-brandgold"
                        >nescaruncho@gmail.com</a
                    >.
                </p>
                <p class="text-sm text-gray-600 mt-3 dark:text-gray-300">
                    © 2025 Librarium. Todos los derechos reservados.
                </p>
            </div>
        </main>
    </AuthenticatedLayout>
</template>
