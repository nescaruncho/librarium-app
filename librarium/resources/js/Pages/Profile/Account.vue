<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head } from "@inertiajs/vue3";
import UpdatePasswordForm from "./Partials/UpdatePasswordForm.vue";
import DeleteUserForm from "./Partials/DeleteUserForm.vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import { useForm } from "@inertiajs/vue3";

const props = defineProps({
    mustVerifyEmail: Boolean,
    status: String,
    auth: Object,
});

const emailForm = useForm({
    email: props.auth?.user?.email ?? "",
});

const submitEmail = () => {
    emailForm.patch(route("account.email.update"), { preserveScroll: true });
};
</script>

<template>
    <AuthenticatedLayout>
        <Head title="Ajustes de cuenta" />

        <div class="max-w-3xl mx-auto px-4 py-8 space-y-8">
            <section
                class="rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-[#383838] p-6"
            >
                <h2 class="text-lg text-brandblue dark:text-white font-semibold mb-4">Actualizar correo electrónico</h2>
                <form @submit.prevent="submitEmail" class="space-y-4">
                    <InputLabel
                        for="email"
                        value="Correo electrónico"
                    />

                    <TextInput
                        v-model="emailForm.email"
                        name="email"
                        type="email"
                        autocomplete="email"
                        class="mt-1 block w-full"
                    />
                    <p
                        v-if="emailForm.errors.email"
                        class="text-sm text-red-600"
                    >
                        {{ emailForm.errors.email }}
                    </p>

                    <button
                        type="submit"
                        :disabled="emailForm.processing"
                        class="px-4 py-2 rounded-full bg-brandgold text-white hover:bg-brandgold/80 disabled:opacity-60"
                    >
                        Guardar email
                    </button>

                    <p
                        v-if="mustVerifyEmail"
                        class="text-xs text-slate-500 mt-2"
                    >
                        Si cambias el correo, te enviaremos un email de
                        verificación.
                    </p>
                    <p
                        v-if="
                            $page.props.flash?.status ===
                            'verification-link-sent'
                        "
                        class="text-xs text-emerald-600 mt-2"
                    >
                        Te hemos enviado un enlace de verificación.
                    </p>
                </form>
            </section>

            <section
                class="rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-[#383838] p-6"
            >
                <UpdatePasswordForm />
            </section>

            <section
                class="rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-[#383838] p-6"
            >

                <DeleteUserForm />
            </section>
        </div>
    </AuthenticatedLayout>
</template>
