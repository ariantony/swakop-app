<script setup>
import { useForm } from '@inertiajs/inertia-vue3';
import JetButton from '@/Jetstream/Button.vue';
import JetFormSection from '@/Jetstream/FormSection.vue';
import JetInput from '@/Jetstream/Input.vue';
import JetInputError from '@/Jetstream/InputError.vue';
import JetLabel from '@/Jetstream/Label.vue';
import JetActionMessage from '@/Jetstream/ActionMessage.vue';

const form = useForm({
    password_before: '',
    password_new: '',
    password_confirmation: '',
});

const updateMasterPassword = () => {
    form.patch(route('setting.master-password.update'), {
        errorBag: 'updateMasterPassword',
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
        },
    });
};
</script>

<template>
    <JetFormSection @submitted="updateMasterPassword">
        <template #title>
            Master Password
        </template>

        <template #description>
            Master Password digunakan oleh admin untuk melakukan cancel transaksi yang diajukan oleh kasir. Tindakan ini akan mengembalikan stok barang.
        </template>

        <template #form>
            <!-- Before -->
            <div class="col-span-6 sm:col-span-4">
                <JetLabel for="before" value="Password Sebelumnya" />
                <JetInput
                    id="before"
                    v-model="form.password_before"
                    type="password"
                    class="mt-1 block w-full"
                />
                <JetInputError v-if="form.errors.password_before" :message="form.errors.password_before" class="mt-2" />
            </div>

            <!-- New -->
            <div class="col-span-6 sm:col-span-4">
                <JetLabel for="new" value="Password Baru" />
                <JetInput
                    id="new"
                    v-model="form.password_new"
                    type="password"
                    class="mt-1 block w-full"
                />
                <JetInputError v-if="form.errors.password_new" :message="form.errors.password_new" class="mt-2" />
            </div>

            <!-- Confirm New -->
            <div class="col-span-6 sm:col-span-4">
                <JetLabel for="confirm" value="Konfirmasi Password" />
                <JetInput
                    id="confirm"
                    v-model="form.password_confirmation"
                    type="password"
                    class="mt-1 block w-full"
                />
                <JetInputError v-if="form.errors.password_confirmation" :message="form.errors.password_confirmation" class="mt-2" />
            </div>
        </template>

        <template #actions>
            <JetActionMessage :on="form.recentlySuccessful" class="mr-3">
                Tersimpan.
            </JetActionMessage>

            <JetButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                Simpan
            </JetButton>
        </template>
    </JetFormSection>
</template>
