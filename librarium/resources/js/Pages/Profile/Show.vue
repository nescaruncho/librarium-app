<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";

import { Head, Link, router, useForm } from "@inertiajs/vue3";
import { ref } from "vue";
import {
    Pencil,
    Trash2,
    Mail,
    MapPin,
    Calendar,
    User2,
    ChevronDown,
} from "lucide-vue-next";
import Swal from "sweetalert2";

const props = defineProps({
    auth: Object,
    mustVerifyEmail: Boolean,
    status: String,
    preferences: Object,
    userData: Object,
});

const user = props.userData || props.auth.user;

const editMode = ref(false);

const formatDate = (date) => {
    if (!date) return "";

    if (typeof date === "string" && date.includes("T")) {
        try {
            const dateObj = new Date(date);
            const day = String(dateObj.getDate()).padStart(2, "0");
            const month = String(dateObj.getMonth() + 1).padStart(2, "0");
            const year = dateObj.getFullYear();
            return `${day}/${month}/${year}`;
        } catch (e) {
            console.error("Error parsing date:", e);
            return "";
        }
    }

    if (typeof date === "string" && date.includes("-")) {
        const [year, month, day] = date.split("-");
        return `${day}/${month}/${year}`;
    }

    return date || "";
};

const formatDateForInput = (date) => {
    if (!date) return "";
    if (typeof date === "string" && date.includes("/")) {
        const [day, month, year] = date.split("/");
        return `${year}-${month}-${day}`;
    }
    return date;
};

function formatGenero(genero) {
    switch (genero.toLowerCase()) {
        case "masculino":
            return "Masculino";
        case "femenino":
            return "Femenino";
        default:
            return genero;
    }
}

const form = useForm({
    nombre: user.nombre || "",
    apellido1: user.apellido1 || "",
    apellido2: user.apellido2 || "",
    username: user.username || "",
    descripcion: user.descripcion || "",
    ciudad: user.ciudad || "",
    fechaNacimiento: formatDateForInput(user.fechaNacimiento) || "",
    genero: user.genero || "",
});

function toggleEdit() {
    editMode.value = !editMode.value;
}

function saveProfile() {
    form.patch(route("profile.update"), {
        preserveScroll: true,
        onSuccess: () => {
            editMode.value = false;
            window.location.href = route("profile.show");
        },
    });
}

const getFullName = () => {
    return [user.nombre, user.apellido1, user.apellido2]
        .filter(Boolean)
        .join(" ");
};

const toBool = (v) => v === true || v === 1 || v === "1" || v === "true";

const formPrefs = useForm({
    privacidad: toBool(user.privacidad) ? "publico" : "privado",
    notifEmail: toBool(user.notificacionesEmail),
    temaOscuro: toBool(user.temaOscuro) ? "oscuro" : "claro",
});

const saving = ref(false);

function savePref(field) {
    saving.value = true;

    const payload = {};
    if (field === "privacidad") {
        payload.privacidad = formPrefs.privacidad;
    } else if (field === "notifEmail") {
        payload.notifEmail = !!formPrefs.notifEmail;
    } else if (field === "temaOscuro") {
        payload.temaOscuro = formPrefs.temaOscuro;
    }

    formPrefs
        .transform(() => payload)
        .patch(route("profile.preferences.update"), {
            preserveScroll: true,
            onFinish: () => (saving.value = false),
        });
}
</script>

<template>
    <Head title="Perfil" />

    <AuthenticatedLayout>
        <div class="py-8">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <section
                    class="rounded-2xl border border-slate-200 bg-white/90 shadow-sm dark:border-slate-800 dark:bg-[#383838]"
                >
                    <div
                        class="flex flex-col gap-8 p-6 md:flex-row md:items-start md:p-8"
                    >
                        <div class="flex items-center justify-center">
                            <div
                                class="size-40 md:size-48 rounded-full ring-2 ring-slate-200 dark:ring-slate-700 border border-slate-300/70 dark:border-white flex items-center justify-center overflow-hidden"
                            >
                                <div
                                    class="flex size-full items-center justify-center bg-slate-100 dark:bg-gray-800"
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
                            </div>
                        </div>

                        <div class="flex-1">
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <template v-if="!editMode">
                                        <h1
                                            class="text-2xl md:text-3xl font-semibold text-slate-800 dark:text-slate-100"
                                        >
                                            {{
                                                getFullName() ||
                                                "Nombre Apellido Apellido"
                                            }}
                                        </h1>

                                        <p class="mt-1 text-slate-300">
                                            @{{ user.username }}
                                        </p>

                                        <p
                                            v-if="user.descripcion"
                                            class="mt-4 max-w-3xl text-slate-700 dark:text-slate-400"
                                        >
                                            {{ user.descripcion }}
                                        </p>
                                        <p
                                            v-else
                                            class="mt-4 max-w-3xl italic text-slate-400"
                                        >
                                            Aquí puedes agregar una breve
                                            descripción sobre ti.
                                        </p>
                                    </template>

                                    <template v-else>
                                        <div
                                            class="grid grid-cols-1 gap-3 md:grid-cols-3"
                                        >
                                            <div>
                                                <label
                                                    for="nombre"
                                                    class="mb-1 block text-sm text-slate-600 dark:text-slate-300"
                                                    >Nombre</label
                                                >
                                                <input
                                                    id="nombre"
                                                    v-model.trim="form.nombre"
                                                    type="text"
                                                    class="w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-white dark:bg-gray-800 dark:text-slate-100"
                                                    placeholder="Nombre"
                                                />
                                                <p
                                                    v-if="form.errors.nombre"
                                                    class="mt-1 text-sm text-red-600"
                                                >
                                                    {{ form.errors.nombre }}
                                                </p>
                                            </div>

                                            <div>
                                                <label
                                                    for="apellido1"
                                                    class="mb-1 block text-sm text-slate-600 dark:text-slate-300"
                                                    >1º Apellido</label
                                                >
                                                <input
                                                    id="apellido1"
                                                    v-model.trim="
                                                        form.apellido1
                                                    "
                                                    type="text"
                                                    class="w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-white dark:bg-gray-800 dark:text-slate-100"
                                                    placeholder="Primer apellido"
                                                />
                                                <p
                                                    v-if="form.errors.apellido1"
                                                    class="mt-1 text-sm text-red-600"
                                                >
                                                    {{ form.errors.apellido1 }}
                                                </p>
                                            </div>

                                            <div>
                                                <label
                                                    for="apellido2"
                                                    class="mb-1 block text-sm text-slate-600 dark:text-slate-300"
                                                    >2º Apellido</label
                                                >
                                                <input
                                                    id="apellido2"
                                                    v-model.trim="
                                                        form.apellido2
                                                    "
                                                    type="text"
                                                    class="w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-white dark:bg-gray-800 dark:text-slate-100"
                                                    placeholder="Segundo apellido"
                                                />
                                                <p
                                                    v-if="form.errors.apellido2"
                                                    class="mt-1 text-sm text-red-600"
                                                >
                                                    {{ form.errors.apellido2 }}
                                                </p>
                                            </div>
                                        </div>

                                        <div class="mt-4">
                                            <label
                                                for="username"
                                                class="mb-1 block text-sm text-slate-600 dark:text-slate-300"
                                                >Usuario</label
                                            >
                                            <div
                                                class="flex items-center gap-2"
                                            >
                                                <span class="text-slate-500"
                                                    >@</span
                                                >
                                                <input
                                                    id="username"
                                                    v-model.trim="form.username"
                                                    type="text"
                                                    class="w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-white dark:bg-gray-800 dark:text-slate-100"
                                                    placeholder="tu_usuario"
                                                />
                                            </div>
                                            <p
                                                v-if="form.errors.username"
                                                class="mt-1 text-sm text-red-600"
                                            >
                                                {{ form.errors.username }}
                                            </p>
                                        </div>

                                        <div class="mt-4">
                                            <label
                                                for="descripcion"
                                                class="mb-1 block text-sm text-slate-600 dark:text-slate-300"
                                                >Descripción</label
                                            >
                                            <textarea
                                                id="descripcion"
                                                v-model.trim="form.descripcion"
                                                rows="4"
                                                class="w-full resize-y rounded-md border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-white dark:bg-gray-800 dark:text-slate-100"
                                                placeholder="Cuéntanos algo sobre ti…"
                                            ></textarea>
                                            <p
                                                v-if="form.errors.descripcion"
                                                class="mt-1 text-sm text-red-600"
                                            >
                                                {{ form.errors.descripcion }}
                                            </p>
                                        </div>
                                    </template>
                                </div>

                                <div class="flex gap-2">
                                    <Link
                                        :href="
                                            route('profile.account.settings')
                                        "
                                        class="inline-flex items-center px-3 py-1.5 rounded-full border border-blue-500 text-blue-600 bg-white hover:bg-blue-50 dark:border-brandgold dark:bg-black dark:text-brandgold dark:hover:bg-brandgold/20"
                                    >
                                        <User2 class="w-4 h-4 mr-1" />
                                        Cuenta
                                    </Link>

                                    <button
                                        v-if="!editMode"
                                        @click="toggleEdit"
                                        class="inline-flex items-center px-3 py-1.5 rounded-full border border-blue-500 text-blue-600 bg-white hover:bg-blue-50 dark:border-brandgold dark:bg-black dark:text-brandgold dark:hover:bg-brandgold/20"
                                    >
                                        <Pencil class="w-4 h-4 mr-1" />
                                        Editar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div
                        class="divide-y divide-slate-200 dark:divide-slate-800"
                    >
                        <div
                            class="grid grid-cols-1 gap-10 p-6 md:grid-cols-2 md:p-8"
                        >
                            <div>
                                <h2
                                    class="text-xl font-semibold text-slate-800 dark:text-slate-100"
                                >
                                    Información personal
                                </h2>

                                <ul class="mt-6 space-y-4">
                                    <li
                                        class="flex items-center gap-3 text-slate-700 dark:text-slate-300"
                                    >
                                        <Mail class="h-5 w-5 text-amber-500" />

                                        <span>{{ user.email }}</span>
                                    </li>

                                    <li
                                        class="flex items-center gap-3 text-slate-700 dark:text-slate-300"
                                    >
                                        <span
                                            class="inline-flex h-5 w-5 items-center justify-center text-amber-500"
                                            >⚥</span
                                        >
                                        <template v-if="!editMode">
                                            <span>{{
                                                user.genero
                                                    ? formatGenero(user.genero)
                                                    : "No especificado"
                                            }}</span>
                                        </template>
                                        <template v-else>
                                            <div class="relative w-full">
                                                <select
                                                    v-model="form.genero"
                                                    class="block appearance-none rounded-md border border-slate-300 py-1.5 px-3 pr-10 text-sm focus:border-blue-500 focus:ring-blue-500 dark:border-white dark:bg-gray-800 dark:text-slate-100"
                                                >
                                                    <option value="">
                                                        -- Selecciona --
                                                    </option>
                                                    <option value="masculino">
                                                        Masculino
                                                    </option>
                                                    <option value="femenino">
                                                        Femenino
                                                    </option>
                                                </select>
                                            </div>
                                        </template>
                                    </li>

                                    <li
                                        class="flex items-center gap-3 text-slate-700 dark:text-slate-300"
                                    >
                                        <Calendar
                                            class="h-5 w-5 text-amber-500"
                                        />
                                        <template v-if="!editMode">
                                            <span>{{
                                                formatDate(user.fechaNacimiento)
                                            }}</span>
                                        </template>
                                        <template v-else>
                                            <input
                                                v-model="form.fechaNacimiento"
                                                type="date"
                                                class="rounded-md border border-slate-300 py-1.5 px-3 text-sm focus:border-blue-500 focus:ring-blue-500 dark:border-white dark:bg-gray-800 dark:text-slate-100"
                                            />
                                        </template>
                                    </li>

                                    <li
                                        class="flex items-center gap-3 text-slate-700 dark:text-slate-300"
                                    >
                                        <MapPin
                                            class="h-5 w-5 text-amber-500"
                                        />
                                        <template v-if="!editMode">
                                            <span>{{ user.ciudad }}</span>
                                        </template>
                                        <template v-else>
                                            <input
                                                v-model="form.ciudad"
                                                type="text"
                                                class="rounded-md border-gray-300 focus:border-blue-500 focus:ring-blue-500 text-sm dark:border-white dark:bg-gray-800 dark:text-slate-100"
                                            />
                                        </template>
                                    </li>
                                </ul>
                            </div>

                            <div>
                                <h2
                                    class="text-xl font-semibold text-slate-800 dark:text-slate-100"
                                >
                                    Ajustes y preferencias
                                </h2>

                                <div class="mt-6 space-y-5">
                                    <div
                                        class="flex items-center justify-between gap-4"
                                    >
                                        <label
                                            for="privacidad"
                                            class="text-slate-700 dark:text-slate-300"
                                        >
                                            Privacidad del perfil
                                        </label>
                                        <div class="relative">
                                            <select
                                                id="privacidad"
                                                v-model="formPrefs.privacidad"
                                                @change="savePref('privacidad')"
                                                class="appearance-none rounded-full border border-slate-300 bg-white px-8 py-1.5 text-sm font-medium text-slate-800 shadow-sm focus:outline-none focus:ring-2 focus:ring-slate-400 dark:border-white dark:bg-gray-800 dark:text-slate-100"
                                            >
                                                <option value="publico">
                                                    Público
                                                </option>
                                                <option value="privado">
                                                    Privado
                                                </option>
                                            </select>
                                            <svg
                                                class="pointer-events-none absolute right-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-700 dark:text-slate-100"
                                                fill="none"
                                                viewBox="0 0 24 24"
                                                stroke="currentColor"
                                                stroke-width="2"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    d="M19 9l-7 7-7-7"
                                                />
                                            </svg>
                                        </div>
                                    </div>

                                    <div
                                        class="flex items-center justify-between gap-4"
                                    >
                                        <span
                                            class="text-slate-700 dark:text-slate-300"
                                            >Notificaciones por email</span
                                        >
                                        <button
                                            type="button"
                                            role="switch"
                                            :aria-checked="formPrefs.notifEmail"
                                            @click="
                                                formPrefs.notifEmail =
                                                    !formPrefs.notifEmail;
                                                savePref('notifEmail');
                                            "
                                            :class="[
                                                'relative inline-flex h-6 w-11 items-center rounded-full border transition-colors',
                                                formPrefs.notifEmail
                                                    ? 'bg-blue-500/80 border-blue-500 dark:bg-green-700 dark:border-white'
                                                    : 'bg-slate-200 border-slate-300 dark:bg-black dark:border-white',
                                            ]"
                                            class="focus:outline-none focus:ring-2 focus:ring-slate-400"
                                        >
                                            <span
                                                :class="[
                                                    'inline-block h-5 w-5 transform rounded-full bg-white shadow ring-1 ring-slate-300 transition',
                                                    formPrefs.notifEmail
                                                        ? 'translate-x-5'
                                                        : 'translate-x-1',
                                                ]"
                                            />
                                        </button>
                                    </div>

                                    <div
                                        class="flex items-center justify-between gap-4"
                                    >
                                        <span
                                            class="text-slate-700 dark:text-slate-300"
                                            >Tema por defecto</span
                                        >
                                        <div class="flex items-center gap-3">
                                            <span
                                                class="text-slate-900 dark:text-slate-100 capitalize"
                                            >
                                                {{ formPrefs.temaOscuro }}
                                            </span>
                                            <button
                                                type="button"
                                                role="switch"
                                                :aria-checked="
                                                    formPrefs.temaOscuro ===
                                                    'oscuro'
                                                "
                                                @click="
                                                    formPrefs.temaOscuro =
                                                        formPrefs.temaOscuro ===
                                                        'oscuro'
                                                            ? 'claro'
                                                            : 'oscuro';
                                                    savePref('temaOscuro');
                                                "
                                                :class="[
                                                    'relative inline-flex h-6 w-11 items-center rounded-full border transition-colors',
                                                    formPrefs.temaOscuro ===
                                                    'oscuro'
                                                        ? 'bg-blue-500/80 border-blue-500 dark:bg-gray-800 dark:border-white'
                                                        : 'bg-slate-200 border-slate-300 dark:bg-gray-300 dark:border-white',
                                                ]"
                                                class="focus:outline-none focus:ring-2 focus:ring-slate-400"
                                            >
                                                <span
                                                    :class="[
                                                        'inline-block h-5 w-5 transform rounded-full bg-white shadow ring-1 ring-slate-300 transition',
                                                        formPrefs.temaOscuro ===
                                                        'oscuro'
                                                            ? 'translate-x-5'
                                                            : 'translate-x-1',
                                                    ]"
                                                />
                                            </button>
                                        </div>
                                    </div>
                                    <p
                                        v-show="saving"
                                        class="text-right text-xs text-slate-500"
                                    >
                                        Guardando…
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div
                        v-if="editMode"
                        class="flex justify-center gap-20 px-6 py-8 border-t border-slate-200 dark:border-slate-800"
                    >
                        <button
                            @click="toggleEdit"
                            class="px-14 py-2 rounded-full bg-gray-100 border border-gray-300 text-gray-700 hover:bg-gray-200 font-medium transition-colors"
                        >
                            Cancelar
                        </button>
                        <button
                            @click="saveProfile"
                            class="px-14 py-2 rounded-full bg-brandgold text-white hover:bg-brandgold/80 font-medium transition-colors"
                        >
                            Guardar
                        </button>
                    </div>
                </section>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
