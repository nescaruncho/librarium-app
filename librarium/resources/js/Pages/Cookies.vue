<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head } from "@inertiajs/vue3";
</script>

<template>
    <Head title="Política de Cookies" />
    <AuthenticatedLayout>
        <main class="p-6">
            <div class="max-w-3xl mx-auto bg-white shadow-md rounded-lg p-8 dark:bg-[#383838]">
                <h1 class="text-2xl font-bold mb-4 dark:text-white">Política de Cookies</h1>
                <p class="mb-4 dark:text-white">
                    Esta aplicación puede utilizar cookies técnicas estrictamente necesarias para su funcionamiento.
                </p>
                <p class="mb-4 text-semibold dark:text-white">
                    No se utilizan con fines publicitarios ni se instalan cookies de terceros sin consentimiento expreso.
                </p>
                <p class="mb-4 dark:text-white">
                    El usuario puede configurar su navegador para rechazar o eliminar cookies en cualquier momento.
                </p>
            </div>
        </main>
    </AuthenticatedLayout>
</template>
