
<script setup>
import { onMounted } from 'vue';

  const { products, group } = defineProps({
    products: Object,
    group: Object
  })

  const nprogress = document.getElementById('nprogress')
  if (nprogress) {
    nprogress.style.display = 'none'
  }

  const print = () => {
    window.print()
  }

  onMounted(() => {
    setTimeout(() => {
      window.print()
    }, 2000);
  })
</script>

<template>
  <div class="px-8">
    <div class="flex items-center justify-center font-semibold mb-10">
      <h1 class="text-black text-2xl">Produk dengan Kelompok Barang "{{ group.code }}"</h1>
      <button @click="print" type="button" class="print:hidden bg-pink-600 rounded-md rounded-r-none px-5 py-4 font-semibold text-white fixed top-0 right-0">
        <div class="flex items-center">
          <i class="bx bx-download mr-1 text-xl"></i> Print
        </div>
      </button>
    </div>
    <table class="w-full border-2 border-slate-300">
      <tr class="bg-slate-100">
        <th class="px-1 py-2 uppercase border-b-2 border-x-2 border-slate-300">No</th>
        <th class="px-1 py-2 uppercase border-b-2 border-x-2 border-slate-300">Kode</th>
        <th class="px-1 py-2 uppercase border-b-2 border-x-2 border-slate-300">Nama</th>
        <th class="px-1 py-2 uppercase border-b-2 border-x-2 border-slate-300">Barcode</th>
        <th class="px-1 py-2 uppercase border-b-2 border-x-2 border-slate-300">Harga</th>
        <th class="px-1 py-2 uppercase border-b-2 border-x-2 border-slate-300 w-16">Stok</th>
      </tr>
      <tbody>
        <tr v-for="(item, i) in products" :key="i" :item="item">
          <td class="border p-2 border-x-2 border-slate-300 text-center">{{ i + 1 }}</td>
          <td class="border p-2 border-x-2 border-slate-300 capitalize">{{ item.code }}</td>
          <td class="border p-2 border-x-2 border-slate-300 capitalize">{{ item.name }}</td>
          <td class="border p-2 border-x-2 border-slate-300 capitalize">{{ item.barcode }}</td>
          <td class="border p-2 border-x-2 border-slate-300 text-right">{{ item.price?.price_per_unit ? rupiah(item.price?.price_per_unit) : '-' }}</td>
          <td class="border p-2 border-x-2 border-slate-300 text-center">{{ item.stock_unit }}</td>
        </tr>
      </tbody>
    </table>
    <div class="flex items-center justify-start text-black space-y-2 mt-2">
      <div>File ini dicetak pada {{ dateindo(new Date(), true) }}</div>
    </div>
  </div>
</template>