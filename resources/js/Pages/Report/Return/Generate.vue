
<script setup>
import { getCurrentInstance } from 'vue'
import { Link, useForm } from '@inertiajs/inertia-vue3'
import { Inertia } from '@inertiajs/inertia'
import AppLayout from '@/Layouts/AppLayout.vue'
import Card from '@/Components/Card.vue'
import Swal from 'sweetalert2'

const { retur, total, period } = defineProps({
  retur: Array,  
  total: Number,
  period: String,
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
  <AppLayout title="Laporan Retur Pembelian Barang">
    <Card>
      <template #header>
        <div class="flex items-center justify-between space-x-2 text-white p-2">
          <h1 class="ml-4 text-black text-2xl font-semibold capitalize">Laporan Retur Pembelian Barang Bulan "{{ periodindo(period) }}"</h1>
          <div class="flex flex-none space-x-2">
            <button @click.prevent="print" type="button" class="bg-pink-600 rounded-md px-3 py-1 font-semibold">
              <div class="flex items-center">
                <i class="bx bx-download mr-1 text-xl"></i> Download
              </div>
            </button>
            <Link :href="route('return.report.index')" class="bg-slate-600 rounded-md px-3 py-1 font-semibold">
              <div class="flex items-center">
                <i class="bx bx-arrow-back mr-1 text-xl"></i> Kembali
              </div>
            </Link>
          </div>
        </div>
      </template>
      <template #body>
        <table class="w-full border-collapse border-2 border-slate-300">
          <thead class="bg-slate-100">
            <tr>
              <th class="px-1 py-2 uppercase border-b-2 border-x-2 border-slate-300" rowspan="2">No</th>
              <th class="px-1 py-2 uppercase border-b-2 border-x-2 border-slate-300" rowspan="2">Produk</th>
              <th class="px-1 py-2 uppercase border-b-2 border-x-2 border-slate-300" colspan="1">Qty </th>
              <th class="px-1 py-2 uppercase border-b-2 border-x-2 border-slate-300" colspan="1">Subtotal </th>
              <th class="px-1 py-2 uppercase border-b-2 border-x-2 border-slate-300" rowspan="2">Subtotal</th>
            </tr>
            <tr>
              <th class="px-3 py-2 uppercase border-2 border-slate-300">satuan</th>
              <!-- <Th class="px-3 py-2 uppercase border-2 border-slate-300" :table="table" name="qty_box">box / renceng</Th>
              <Th class="px-3 py-2 uppercase border-2 border-slate-300" :table="table" name="qty_carton">karton</Th> -->
              <th class="px-3 py-2 uppercase border-2 border-slate-300">satuan</th>
              <!-- <Th class="px-3 py-2 uppercase border-2 border-slate-300" :table="table" name="cost_box">box / renceng</Th>
              <Th class="px-3 py-2 uppercase border-2 border-slate-300" :table="table" name="cost_carton">carton</Th> -->
            </tr>
          </thead>
          <tbody>
            <tr v-for="(item, i, index) in retur" :key="i" :index="index" :item="item">
              <td class="border p-2 border-x-2 border-slate-300 text-center">{{ index + 1 }}</td>
              <td class="border p-2 border-x-2 border-slate-300 capitalize">{{ item.name }}</td>
              <td class="border p-2 border-x-2 border-slate-300 text-right">{{ item.qty_unit }}</td>
              <td class="border p-2 border-x-2 border-slate-300 text-right">{{ rupiah(item.cost_unit) }}</td>
              <td class="border p-2 border-x-2 border-slate-300 text-right">{{ rupiah(item.total_cost_all) }}</td>
            </tr>
            <tr class="text-xl font-bold bg-yellow-300">
              <td class="border p-2 border-x-2 border-slate-300 text-center" colspan="4">Total Nilai Retur</td>
              <td class="border p-2 border-x-2 border-slate-300 text-right">{{ rupiah(total) }}</td>
            </tr>
          </tbody>
        </table>
      </template>

      <template #print>
        <div class="flex items-center justify-center font-semibold mb-10">
          <h1 class="text-black text-2xl">Laporan Retur Pembelian Barang Bulan "{{ periodindo(period) }}"</h1>
        </div>
        <table class="w-full border-collapse border-2 border-slate-300">
          <thead class="bg-slate-100">
            <tr>
              <th class="px-1 py-2 uppercase border-b-2 border-x-2 border-slate-300" rowspan="2">No</th>
              <th class="px-1 py-2 uppercase border-b-2 border-x-2 border-slate-300" rowspan="2">Produk</th>
              <th class="px-1 py-2 uppercase border-b-2 border-x-2 border-slate-300" colspan="1">Qty </th>
              <th class="px-1 py-2 uppercase border-b-2 border-x-2 border-slate-300" colspan="1">Subtotal </th>
              <th class="px-1 py-2 uppercase border-b-2 border-x-2 border-slate-300" rowspan="2">Subtotal</th>
            </tr>
            <tr>
              <th class="px-3 py-2 uppercase border-2 border-slate-300">satuan</th>
              <!-- <Th class="px-3 py-2 uppercase border-2 border-slate-300" :table="table" name="qty_box">box / renceng</Th>
              <Th class="px-3 py-2 uppercase border-2 border-slate-300" :table="table" name="qty_carton">karton</Th> -->
              <th class="px-3 py-2 uppercase border-2 border-slate-300">satuan</th>
              <!-- <Th class="px-3 py-2 uppercase border-2 border-slate-300" :table="table" name="cost_box">box / renceng</Th>
              <Th class="px-3 py-2 uppercase border-2 border-slate-300" :table="table" name="cost_carton">carton</Th> -->
            </tr>
          </thead>
          <tbody>
            <tr v-for="(item, i, index) in retur" :key="i" :index="index" :item="item">
              <td class="border p-2 border-x-2 border-slate-300 text-center">{{ index + 1 }}</td>
              <td class="border p-2 border-x-2 border-slate-300 capitalize">{{ item.name }}</td>
              <td class="border p-2 border-x-2 border-slate-300 text-right">{{ item.qty_unit }}</td>
              <td class="border p-2 border-x-2 border-slate-300 text-right">{{ rupiah(item.cost_unit) }}</td>
              <td class="border p-2 border-x-2 border-slate-300 text-right">{{ rupiah(item.total_cost_all) }}</td>
            </tr>
            <tr class="text-xl font-bold bg-yellow-300">
              <td class="border p-2 border-x-2 border-slate-300 text-center" colspan="4">Total Nilai Retur</td>
              <td class="border p-2 border-x-2 border-slate-300 text-right">{{ rupiah(total) }}</td>
            </tr>
          </tbody>
        </table>
        <div class="flex items-center justify-start text-black space-y-2 mt-2">
          <div>Laporan pengembalian barang dicetak pada {{ dateindo(new Date(), true) }}</div>
        </div>
      </template>
    </Card>
  </AppLayout>
</template>