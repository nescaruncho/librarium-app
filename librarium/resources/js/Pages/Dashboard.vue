<script setup>
import { Head, Link } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
const props = defineProps({
    usuario: Object,
    bibliotecas: Array,
    lecturas: Array,
});
function formatAutores(autores) {
    if (!autores || !autores.length) return "Desconocido";
    return autores
        .map((a) => [a?.nombre, a?.apellido1].filter(Boolean).join(" "))
        .join(", ");
}
</script>
<template>
    <Head title="Panel" />

    <AuthenticatedLayout>
        <div
            class="max-w-screen-2xl mx-auto px-4 sm:px-6 lg:px-8 py-6 grid grid-cols-1 lg:grid-cols-12 gap-6"
        >
            <div class="lg:col-span-8 xl:col-span-9">
                <section class="mb-8">
                    <h2
                        class="text-xl md:text-2xl text-brandblue font-semibold mb-4 dark:text-white"
                    >
                        Bibliotecas
                    </h2>

                    <div
                        v-if="bibliotecas.length === 0"
                        class="text-gray-500 dark:text-gray-300"
                    >
                        Aún no formas parte de ninguna biblioteca.
                        <br />
                        <Link
                            href="/bibliotecas/create"
                            class="mt-4 inline-block w-full sm:w-auto text-center bg-brandgold text-white px-4 py-2 rounded-full hover:bg-yellow-500 transition"
                        >
                            Crear una biblioteca
                        </Link>
                    </div>

                    <div v-else>
                        <div
                            class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 xl:grid-cols-6 gap-3 sm:gap-4"
                        >
                            <Link
                                v-for="bib in bibliotecas"
                                :key="bib.idBiblioteca"
                                :href="`/bibliotecas/${bib.idBiblioteca}`"
                                class="bg-white dark:bg-[#383838] p-4 rounded-lg shadow hover:shadow-md focus:ring-2 ring-brandgold transition cursor-pointer text-center"
                            >
                                <div class="text-3xl md:text-4xl mb-2">📚</div>
                                <div
                                    class="font-semibold text-brandblue dark:text-white line-clamp-2"
                                >
                                    {{ bib.nombre }}
                                </div>
                                <div
                                    class="text-xs md:text-sm text-gray-500 dark:text-gray-300"
                                >
                                    {{ bib.rol }}
                                </div>
                            </Link>
                        </div>

                        <div class="mt-4">
                            <Link
                                href="/bibliotecas"
                                class="inline-block w-full sm:w-auto text-center bg-yellow-400 text-white px-4 py-2 rounded hover:bg-yellow-500 transition"
                            >
                                Ver todo
                            </Link>
                        </div>
                    </div>
                </section>

                <section>
                    <h2
                        class="text-xl md:text-2xl text-brandblue font-semibold mb-4 dark:text-white"
                    >
                        Lecturas
                    </h2>

                    <div
                        v-if="!lecturas.length"
                        class="text-gray-500 dark:text-gray-300"
                    >
                        Todavía no tienes lecturas en curso.
                        <br />
                        <Link
                            href="/bibliotecas"
                            class="mt-4 inline-block w-full sm:w-auto text-center bg-brandgold text-white px-4 py-2 rounded-full hover:bg-yellow-500 transition"
                        >
                            Explorar bibliotecas
                        </Link>
                    </div>

                    <div v-else>
                        <div
                            class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-3 sm:gap-4"
                        >
                            <div
                                v-for="it in lecturas"
                                :key="it.idLectura"
                                class="rounded-xl overflow-hidden bg-white/40 dark:bg-white/5"
                            >
                                <div
                                    class="relative aspect-[2/3] rounded-xl overflow-hidden bg-gray-100 dark:bg-gray-800"
                                >
                                    <img
                                        v-if="it.libro?.portadaUrl"
                                        :src="it.libro.portadaUrl"
                                        :alt="`Portada de ${it.libro.titulo}`"
                                        class="absolute inset-0 w-full h-full object-cover"
                                        loading="lazy"
                                        decoding="async"
                                        :sizes="`(min-width: 1280px) 16vw, (min-width: 1024px) 20vw, (min-width: 768px) 25vw, 45vw`"
                                    />
                                    <div
                                        v-else
                                        class="absolute inset-0 flex items-center justify-center text-gray-400 text-sm"
                                    >
                                        Sin portada
                                    </div>
                                </div>

                                <div class="px-2 pb-3 pt-2 text-center">
                                    <p
                                        class="font-semibold text-sm md:text-base text-brandblue line-clamp-2 dark:text-white"
                                    >
                                        {{ it.libro.titulo }}
                                    </p>
                                    <p
                                        class="text-xs md:text-sm text-gray-500 dark:text-gray-300 line-clamp-1"
                                    >
                                        {{ formatAutores(it.libro.autores) }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="mt-6">
                            <Link
                                href="/lecturas"
                                class="inline-block w-full sm:w-auto text-center bg-yellow-400 text-white px-4 py-2 rounded hover:bg-yellow-500 transition"
                            >
                                Ver todo
                            </Link>
                        </div>
                    </div>
                </section>
            </div>

            <aside
                class="lg:col-span-4 xl:col-span-3 w-full bg-white dark:bg-[#1f1f1f] rounded-lg p-4 shadow text-center self-center"
            >
                <h3
                    class="text-lg md:text-xl text-brandblue font-semibold dark:text-white"
                >
                    Hola, {{ usuario.nombre }}
                </h3>

                <div
                    class="w-24 h-24 mx-auto my-4 rounded-full bg-gray-300 dark:bg-gray-600 flex items-center justify-center"
                    aria-hidden="true"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="w-12 h-12 text-white"
                        viewBox="0 0 24 24"
                        fill="currentColor"
                    >
                        <path
                            d="M12 12c2.7 0 5-2.3 5-5s-2.3-5-5-5-5 2.3-5 5 2.3 5 5 5zm0 2c-3.3 0-10 1.7-10 5v3h20v-3c0-3.3-6.7-5-10-5z"
                        />
                    </svg>
                </div>

                <p class="text-sm text-gray-500 dark:text-gray-300">
                    @{{ usuario.username }}
                </p>

                <Link
                    href="/profile"
                    class="mt-4 inline-block w-full sm:w-auto text-center bg-brandgold text-white px-8 py-2 rounded-full hover:bg-yellow-500 transition"
                >
                    Perfil
                </Link>
            </aside>
        </div>
    </AuthenticatedLayout>
</template>
