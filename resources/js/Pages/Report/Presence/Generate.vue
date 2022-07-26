
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

const { data, cashier, from, to } = defineProps({
  data: Array,
  cashier: Object,
  from: Date,
  to: Date,
  length: Number,
})

const self = getCurrentInstance()

const print = () => {
  Swal.fire({
    title: 'Menyiapkan file...',
    text: 'Mohon tunggu hingga preview file muncul',
    showConfirmButton: false,
    allowOutsideClick: false,
    allowEscapeKey: false,
    allowEnterKey: false,
    didOpen : () => {
      Swal.showLoading()
    }
  })
  setTimeout(Swal.close, 800)
  setTimeout(window.print, 1000)
}

</script>

<style src="@vueform/multiselect/themes/default.css"></style>

<template>
  <AppLayout title="Laporan Absensi">
    <Card>
      <template #header>
        <div class="flex items-center justify-between space-x-2 text-white p-2">
          <h1 class="ml-4 text-black text-2xl font-semibold">Laporan Absensi "{{ cashier.name }}"</h1>
          <div class="flex flex-none space-x-2">
            <button @click.prevent="print" type="button" class="bg-pink-600 rounded-md px-3 py-1 font-semibold">
              <div class="flex items-center">
                <i class="bx bx-download mr-1 text-xl"></i> Download
              </div>
            </button>
            <Link :href="route('presence.index')" class="bg-slate-600 rounded-md px-3 py-1 font-semibold">
              <div class="flex items-center">
                <i class="bx bx-arrow-back mr-1 text-xl"></i> Kembali
              </div>
            </Link>
          </div>
        </div>
      </template>
      <template #body>
        <div class="flex items-center justify-end text-lg font-semibold mb-4">
          <p>Rentang Tanggal : {{ dateindo(from) }} - {{ dateindo(to) }} </p>
        </div>
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
        </table>
        <div class="flex items-center justify-between text-black mt-6">
          <p>Jumlah Hadir : {{ length }}</p>
        </div>
      </template>

      <template #print>
        <div class="flex items-center justify-center font-semibold mb-10">
          <h1 class="text-black text-2xl">Laporan Absensi "{{ cashier.name }}"</h1>
        </div>
        <div class="flex items-center justify-end text-lg font-semibold mb-4">
          <p>Rentang Tanggal : {{ dateindo(from) }} - {{ dateindo(to) }} </p>
        </div>
        <table class="w-full border-collapse border-2 border-slate-500">
          <thead class="bg-slate-100">
            <th class="px-1 py-2 uppercase border-b-2 border-r-2 border-slate-500">No</th>
            <th class="px-1 py-2 uppercase border-b-2 border-r-2 border-slate-500">Tanggal</th>
            <th class="px-1 py-2 uppercase border-b-2 border-r-2 border-slate-500">Jam Masuk</th>
            <th class="px-1 py-2 uppercase border-b-2 border-r-2 border-slate-500">Jam Keluar</th>
          </thead>
          <tbody>
            <tr v-for="(item, i, index) in data" :key="i" :index="index" :item="item" class="text-center">
              <td class="border p-2 border-l-2 border-y-slate-300 border-slate-500">{{ index + 1 }}</td>
              <td class="border p-2 border-x-2 border-y-slate-300 border-slate-500">{{ dateindo(i) }}</td>
              <td class="border p-2 border-x-2 border-y-slate-300 border-slate-500">{{ item.in_time }}</td>
              <td class="border p-2 border-r-2 border-y-slate-300 border-slate-500">{{ item.out_time }}</td>
            </tr>
          </tbody>
        </table>
        <div class="flex items-center justify-start text-black space-y-2 mt-6">
          <div>Jumlah Hadir : {{ length }}</div>
        </div>
        <div class="flex items-center justify-start text-black space-y-2 mt-2">
          <div>Laporan absensi dicetak pada {{ dateindo(new Date(), true) }}</div>
        </div>
      </template>
    </Card>
  </AppLayout>
</template>