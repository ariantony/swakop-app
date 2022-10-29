
<script setup>
import { getCurrentInstance } from 'vue'
import { Link } from '@inertiajs/inertia-vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import Card from '@/Components/Card.vue'
import Swal from 'sweetalert2'
import FormatVariableCostsDailyReport from '../../../Components/FormatVariableCostsDailyReport.vue'

const { sell, total, cashier, day } = defineProps({
  sell: Array,  
  total: Number,
  cashier: Object,
  day: String,
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

  const iframe = document.createElement('iframe')
  iframe.style.display = 'none'
  iframe.src = route('daily.report.iframe', {
    user_id: cashier.id,
    date: day,
  })
  document.body.appendChild(iframe)

  iframe.onload = () => {
    Swal.close()
  }
  
  setTimeout(() => { iframe.remove() }, 10000);
}

</script>

<style src="@vueform/multiselect/themes/default.css"></style>

<template>
  <AppLayout title="Laporan Penjualan Harian">
    <Card>
      <template #header>
        <div class="flex items-center justify-between space-x-2 text-white p-2">
          <h1 class="ml-4 text-black text-2xl font-semibold capitalize">Laporan Penjualan Harian "{{ cashier.name }}"</h1>
          <div class="flex flex-none space-x-2">
            <button @click.prevent="print" type="button" class="bg-pink-600 rounded-md px-3 py-1 font-semibold">
              <div class="flex items-center">
                <i class="bx bx-download mr-1 text-xl"></i> Download
              </div>
            </button>
            <Link :href="route('daily.report.index')" class="bg-slate-600 rounded-md px-3 py-1 font-semibold">
              <div class="flex items-center">
                <i class="bx bx-arrow-back mr-1 text-xl"></i> Kembali
              </div>
            </Link>
          </div>
        </div>
      </template>
      <template #body>
        <div class="h-[28rem] overflow-y-auto">
          <div class="flex items-center justify-end text-lg font-semibold mb-4">
            <p>Tanggal : {{ dateindo(day) }} </p>
          </div>
          <table class="w-full border-collapse border-2 border-slate-300">
            <thead class="bg-slate-100">
              <tr>
                <th class="px-1 py-2 uppercase border-b-2 border-x-2 border-slate-300">No</th>
                <th class="px-1 py-2 uppercase border-b-2 border-x-2 border-slate-300">Produk</th>
                <th class="px-1 py-2 uppercase border-b-2 border-x-2 border-slate-300 w-16">Qty</th>
                <th class="px-1 py-2 uppercase border-b-2 border-x-2 border-slate-300">Subtotal</th>
                <th class="px-1 py-2 uppercase border-b-2 border-x-2 border-slate-300">Total</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(item, i) in sell" :key="i" :index="i" :item="item" :class="item.total_cost_all === 0 ? 'bg-sky-200' : ''">
                <td class="border p-2 border-x-2 border-slate-300 text-center">{{ i + 1 }}</td>
                <td class="border p-2 border-x-2 border-slate-300 capitalize">{{ item.name }}</td>
                <td class="border p-2 border-x-2 border-slate-300 text-right">{{ item.qty_total }}</td>
                <td class="border p-2 border-x-2 border-slate-300 text-right"><FormatVariableCostsDailyReport :detail="item" /></td>
                <td class="border p-2 border-x-2 border-slate-300 text-right">{{ rupiah(item.total) }}</td>
              </tr>
              <tr class="text-xl font-bold bg-yellow-300">
                <td class="border p-2 border-x-2 border-slate-300 text-center" colspan="4">Total Penjualan</td>
                <td class="border p-2 border-x-2 border-slate-300 text-right">{{ rupiah(total) }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </template>
    </Card>
  </AppLayout>
</template>