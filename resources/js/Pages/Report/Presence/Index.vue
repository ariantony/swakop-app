
<script setup>
import { getCurrentInstance, nextTick, onMounted, onUpdated, ref } from 'vue'
import { Link, useForm } from '@inertiajs/inertia-vue3'
import { Inertia } from '@inertiajs/inertia'
import AppLayout from '@/Layouts/AppLayout.vue'
import Card from '@/Components/Card.vue'
import Swal from 'sweetalert2'
import Datepicker from '@vuepic/vue-datepicker';
import { id } from 'date-fns/locale';
import '@vuepic/vue-datepicker/dist/main.css'
import Select from '@vueform/multiselect'

const { users } = defineProps({
  users: Array,
})

const self = getCurrentInstance()
const form = useForm({
  user_id : '',
  daterange: []
})

const generate = () => form.post(route('presence.generate'))

</script>

<style src="@vueform/multiselect/themes/default.css"></style>

<template>
  <AppLayout title="Laporan Absensi">
    <Card>
      <template #header>
        <div class="flex items-center justify-between space-x-2 text-white p-2">
          <h1 class="ml-4 text-black text-2xl font-semibold">Laporan Absensi</h1>
        </div>
      </template>
      <template #body>
        <form @submit.prevent="generate">
          <div class="flex items-center space-x-4">
            <div class="flex flex-col items-center w-full">
              <div class="flex flex-col space-y-2 mb-2 w-full">
                <div class="flex items-center space-x-4 w-full">
                  <label for="user_id" class="w-1/4">User</label>
                  <Select
                    v-model="form.user_id"
                    :options="users.map(u => ({
                      label: `${u.name}`,
                      value: u.id,
                    }))"
                    :searchable="true"
                    class="w-3/4"
                  />
                </div>
                <div class="text-red-500 text-right text-sm first-letter:capitalize" v-html="form.errors.user_id || '&nbsp;'"></div>
              </div>
              <div class="flex flex-col space-y-2 w-full">
                <div class="flex items-center space-x-4 w-full">
                  <label for="date" class="w-1/4">Rentang Tanggal</label>
                  <div class="w-full">
                    <Datepicker 
                      v-model="form.daterange" 
                      range
                      autoApply 
                      :closeOnAutoApply="false" 
                      :enableTimePicker="false" 
                      position="left" 
                      locale="id"
                      :format-locale="id"
                      format="d MMMM y"
                      :dayNames="['Sn', 'Sl', 'Rb', 'Km', 'Jm', 'Sb', 'Mn']"
                      :monthChangeOnScroll="false"
                    />
                  </div>
                </div>
                <div class="text-red-500 text-right text-sm first-letter:capitalize" v-html="form.errors.daterange || '&nbsp;'"></div>
              </div>
            </div>
          </div>
          <div class="flex items-center space-x-2 mt-6 justify-end">
            <button type="submit" class="bg-green-600 hover:bg-green-700 rounded-md px-3 py-1 text-white text-sm transition-all">
              <div class="flex items-center space-x-1">
                <i class="bx bx-search-alt"></i>
                <p class="font-semibold">Tampilkan</p>
              </div>
            </button>
          </div>
        </form>
      </template>
    </Card>
  </AppLayout>
</template>