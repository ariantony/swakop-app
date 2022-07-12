<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import MasterPassword from '@/Pages/Setting/Partials/MasterPassword.vue';
</script>

<template>
    <AppLayout title="Pengaturan">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Pengaturan
            </h2>
        </template>

        <div>
            <div class="max-w-7xl mx-auto py-6 sm:px-4 lg:px-">
                <MasterPassword />
                <JetSectionBorder />

                <!-- <UpdatePasswordForm class="mt-10 sm:mt-0" /> -->
                <!-- <JetSectionBorder /> -->
            </div>
        </div>
    </AppLayout>
</template>
