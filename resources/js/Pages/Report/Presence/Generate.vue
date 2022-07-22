
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

const { data, user, from, to } = defineProps({
  data: Array,
  user: Object,
  from: Date,
  to: Date,
})

const self = getCurrentInstance()
const form = useForm({
  user_id : '',
  daterange: []
})

console.log(data)
</script>

<style src="@vueform/multiselect/themes/default.css"></style>

<template>
  <AppLayout title="Laporan Absensi">
    <Card>
      <template #header>
        <div class="flex items-center justify-between space-x-2 text-white p-2">
          <h1 class="ml-4 text-black text-2xl font-semibold">Laporan Absensi "{{ user.name }}"</h1>
          <Link :href="route('presence.index')" class="bg-slate-600 rounded-md px-3 py-1 font-semibold">
            <div class="flex items-center">
              <i class="bx bx-arrow-back mr-1 text-xl"></i> Kembali
            </div>
          </Link>
        </div>
      </template>
      <template #body>
        <table class="w-full border-collapse border-2 border-slate-300">
        <thead class="bg-slate-100">
          <th class="px-1 py-2 uppercase border-b-2 border-r-2 border-slate-300">No</th>
          <th class="px-1 py-2 uppercase border-b-2 border-r-2 border-slate-300">Tanggal</th>
          <th class="px-1 py-2 uppercase border-b-2 border-r-2 border-slate-300">Jam Masuk</th>
          <th class="px-1 py-2 uppercase border-b-2 border-r-2 border-slate-300">Jam Keluar</th>
        </thead>

        <tbody>
        <tr v-for="(item, i, index) in data" :key="i" :index="index" :item="item" class="text-center">
            <td class="border p-2 border-x-2 border-slate-300">{{ index + 1 }}</td>
            <td class="border p-2 border-x-2 border-slate-300">{{ dateindo(i) }}</td>
            <td class="border p-2 border-x-2 border-slate-300">{{ item.in_time }}</td>
            <td class="border p-2 border-x-2 border-slate-300">{{ item.out_time }}</td>
          </tr>
        </tbody>

        <tfoot class="bg-slate-100">
          <th class="px-1 py-2 uppercase border-b-2 border-r-2 border-slate-300">No</th>
          <th class="px-1 py-2 uppercase border-b-2 border-r-2 border-slate-300">Tanggal</th>
          <th class="px-1 py-2 uppercase border-b-2 border-r-2 border-slate-300">Jam Masuk</th>
          <th class="px-1 py-2 uppercase border-b-2 border-r-2 border-slate-300">Jam Keluar</th>
        </tfoot>
      </table>
      </template>
    </Card>
  </AppLayout>
</template>