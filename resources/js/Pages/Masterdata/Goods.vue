<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import Welcome from '@/Jetstream/Welcome.vue';
import ButtonVue from '@/Jetstream/Button.vue';
</script>

<template>
    <AppLayout title="Barang">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Barang
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-4">
                    <ButtonVue class="bg-cyan-800 hover:bg-cyan-700 active:bg-cyan-900 focus:bg-cyan-900">
                        <span>Create</span>
                    </ButtonVue>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
