<script setup>
import FormatVariableCostsDailyReport from '../../../Components/FormatVariableCostsDailyReport.vue'

const { sell, total, cashier, day } = defineProps({
  sell: Array,  
  total: Number,
  cashier: Object,
  day: String,
})

  const nprogress = document.getElementById('nprogress')
  if (nprogress) {
    nprogress.style.display = 'none'
  }

  window.print()
</script>

<template>
  <div class="px-8">
    <div class="flex items-center justify-center text-white mb-10">
      <h1 class="ml-4 text-black text-2xl font-semibold capitalize">Laporan Penjualan Harian "{{ cashier.name }}"</h1>
    </div>
    <div class="flex items-center justify-end text-lg font-semibold mb-4">
      <p>Tanggal : {{ dateindo(day) }} </p>
    </div>
    <table class="w-full text-sm border-collapse border-2 border-slate-300">
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
          <td class="border p-2 border-x-2 border-slate-300 text-right text-sm"><FormatVariableCostsDailyReport :detail="item" /></td>
          <td class="border p-2 border-x-2 border-slate-300 text-right">{{ rupiah(item.total) }}</td>
        </tr>
        <tr class="text-xl font-bold bg-yellow-300">
          <td class="border p-2 border-x-2 border-slate-300 text-center" colspan="4">Total Penjualan</td>
          <td class="border p-2 border-x-2 border-slate-300 text-right">{{ rupiah(total) }}</td>
        </tr>
      </tbody>
    </table>
    <div class="flex items-center justify-start text-black space-y-2 mt-2">
      <div>File ini dicetak pada {{ dateindo(new Date(), true) }}</div>
    </div>
  </div>
</template>