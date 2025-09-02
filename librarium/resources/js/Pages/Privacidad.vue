<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head } from "@inertiajs/vue3";
</script>

<template>
    <Head title="Política de Privacidad" />
    <AuthenticatedLayout>
        <main class="p-6">
            <section class="p-6 max-w-4xl mx-auto">
                <div
                    class="max-w-3xl mx-auto bg-white shadow-md rounded-lg p-8 dark:bg-[#383838]"
                >
                    <h1 class="text-3xl font-bold mb-6 dark:text-white">
                        Política de Privacidad
                    </h1>
                    <p class="mb-4 dark:text-white">
                        La presente política de privacidad establece los
                        términos en los que se tratarán los datos personales de
                        los usuarios de la aplicación.
                    </p>

                    <h2 class="text-2xl font-semibold mt-8 mb-2 dark:text-white">
                        🔍 Datos recogidos
                    </h2>
                    <ul class="list-disc list-inside mb-4 dark:text-white">
                        <li>Nombre de usuario</li>
                        <li>Correo electrónico</li>
                        <li>Registro de libros añadidos/leídos/prestados</li>
                    </ul>

                    <h2 class="text-2xl font-semibold mt-8 mb-2 dark:text-white">
                        🎯 Finalidad del tratamiento
                    </h2>
                    <ul class="list-disc list-inside mb-4 dark:text-white">
                        <li>
                            Gestión de cuentas y funcionalidades de la
                            aplicación
                        </li>
                        <li>
                            Fines estadísticos internos y mejora de la
                            experiencia de usuario
                        </li>
                    </ul>

                    <h2 class="text-2xl font-semibold mt-8 mb-2 dark:text-white">
                        📜 Base legal
                    </h2>
                    <p class="mb-4 dark:text-white">
                        Consentimiento expreso del usuario al aceptar esta
                        política.
                    </p>

                    <h2 class="text-2xl font-semibold mt-8 mb-2 dark:text-white">
                        📆 Conservación de los datos
                    </h2>
                    <p class="mb-4 dark:text-white">
                        Los datos se conservarán mientras el usuario mantenga su
                        cuenta activa o hasta que solicite su eliminación.
                    </p>

                    <h2 class="text-2xl font-semibold mt-8 mb-2 dark:text-white">
                        🚫 Cesión a terceros
                    </h2>
                    <p class="mb-4 dark:text-white">
                        No se cederán datos a terceros salvo obligación legal.
                    </p>

                    <h2 class="text-2xl font-semibold mt-8 mb-2 dark:text-white">
                        🛡️ Medidas de seguridad
                    </h2>
                    <ul class="list-disc list-inside mb-4 dark:text-white">
                        <li>Cifrado de contraseñas con bcrypt</li>
                        <li>Uso de HTTPS</li>
                        <li>Acceso restringido a los datos personales</li>
                    </ul>

                    <h2 class="text-2xl font-semibold mt-8 mb-2 dark:text-white">
                        🗃️ Derechos del usuario
                    </h2>
                    <ul class="list-disc list-inside mb-4 dark:text-white">
                        <li>
                            Acceder, rectificar, eliminar o limitar el
                            tratamiento de sus datos
                        </li>
                        <li>Solicitar la portabilidad de sus datos</li>
                        <li>Retirar el consentimiento en cualquier momento</li>
                    </ul>

                    <p class="mt-6 dark:text-white">
                        <strong
                            >📧 Para ejercer estos derechos puede contactarme
                            en: </strong
                        >
                        <a
                            href="mailto:nescaruncho@gmail.com"
                            class="underline hover:text-brandgold"
                            >nescaruncho@gmail.com</a
                        >
                    </p>
                </div>
            </section>
        </main>
    </AuthenticatedLayout>
</template>
