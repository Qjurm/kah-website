<script setup>
import DeleteUserForm from './Partials/DeleteUserForm.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
import { Head } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
    userInstruments: {
        type: Array,
        default: () => [],
    },
    availableInstruments: {
        type: Array,
        default: () => [],
    },
});
</script>

<template>
    <AuthenticatedLayout>
        <Head title="Mijn Profiel" />

        <template #header>
            <div class="flex flex-col gap-1 text-left">
                <h2 class="text-xl font-black leading-tight text-blue-950 italic">Profielinstellingen</h2>
                <p class="text-[10px] font-black uppercase tracking-[0.2em] text-gray-400">Account management en beveiliging</p>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl space-y-10 px-4 sm:px-6 lg:px-8">
                <div class="bg-white rounded-[2.5rem] p-10 shadow-sm border border-gray-100">
                    <div class="max-w-xl text-left">
                        <UpdateProfileInformationForm
                            :must-verify-email="mustVerifyEmail"
                            :status="status"
                        />
                    </div>
                </div>

                <div class="bg-white rounded-[2.5rem] p-10 shadow-sm border border-gray-100">
                    <div class="max-w-xl text-left">
                        <UpdatePasswordForm />
                    </div>
                </div>

                <div class="bg-white rounded-[2.5rem] p-10 shadow-sm border border-red-50">
                    <div class="max-w-xl text-left">
                        <DeleteUserForm />
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
