
<script setup>
import { getCurrentInstance, nextTick, onMounted, onUpdated, ref } from 'vue'
import { Link, useForm } from '@inertiajs/inertia-vue3'
import { Inertia } from '@inertiajs/inertia'
import AppLayout from '@/Layouts/AppLayout.vue'
import Card from '@/Components/Card.vue'
import Swal from 'sweetalert2'

const { products, group } = defineProps({
  products: Object,
  group: Object
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
  <AppLayout title="Print Produk">
    <Card>
      <template #header>
        <div class="flex items-center justify-between space-x-2 text-white p-2">
          <h1 class="ml-4 text-black text-2xl font-semibold capitalize">Produk dengan Kelompok Barang "{{ group.code }} - {{ new String(group.name).toUpperCase() }}"</h1>
          <div class="flex flex-none space-x-2">
            <button @click.prevent="print" type="button" class="bg-pink-600 rounded-md px-3 py-1 font-semibold">
              <div class="flex items-center">
                <i class="bx bx-download mr-1 text-xl"></i> Download
              </div>
            </button>
            <Link :href="route('product.print')" class="bg-slate-600 rounded-md px-3 py-1 font-semibold">
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
              <th class="px-1 py-2 uppercase border-b-2 border-x-2 border-slate-300" rowspan="2">Kode</th>
              <th class="px-1 py-2 uppercase border-b-2 border-x-2 border-slate-300" rowspan="2">Nama</th>
              <th class="px-1 py-2 uppercase border-b-2 border-x-2 border-slate-300" rowspan="2">Barcode</th>
              <th class="px-1 py-2 uppercase border-b-2 border-x-2 border-slate-300" colspan="1">Harga Jual</th>
              <th class="px-1 py-2 uppercase border-b-2 border-x-2 border-slate-300" colspan="1">Stok</th>
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
            <tr v-for="(item, i) in products" :key="i" :item="item">
              <td class="border p-2 border-x-2 border-slate-300 text-center">{{ i + 1 }}</td>
              <td class="border p-2 border-x-2 border-slate-300 capitalize">{{ item.code }}</td>
              <td class="border p-2 border-x-2 border-slate-300 capitalize">{{ item.name }}</td>
              <td class="border p-2 border-x-2 border-slate-300 capitalize">{{ item.barcode }}</td>
              <td class="border p-2 border-x-2 border-slate-300 text-right">{{ rupiah(item.price.price_per_unit) }}</td>
              <td class="border p-2 border-x-2 border-slate-300 text-center">{{ item.stock_unit }}</td>
            </tr>
          </tbody>
        </table>
      </template>

      <template #print>
        <div class="flex items-center justify-center font-semibold mb-10">
          <h1 class="text-black text-2xl">Produk dengan Kelompok Barang "{{ group.code }}"</h1>
        </div>
        <table class="w-full border-2 border-slate-300">
          <thead class="bg-slate-100">
            <tr>
              <th class="px-1 py-2 uppercase border-b-2 border-x-2 border-slate-300" rowspan="2">No</th>
              <th class="px-1 py-2 uppercase border-b-2 border-x-2 border-slate-300" rowspan="2">Kode</th>
              <th class="px-1 py-2 uppercase border-b-2 border-x-2 border-slate-300" rowspan="2">Nama</th>
              <th class="px-1 py-2 uppercase border-b-2 border-x-2 border-slate-300" rowspan="2">Barcode</th>
              <th class="px-1 py-2 uppercase border-b-2 border-x-2 border-slate-300" colspan="1">Harga Jual</th>
              <th class="px-1 py-2 uppercase border-b-2 border-x-2 border-slate-300" colspan="1">Stok</th>
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
            <tr v-for="(item, i) in products" :key="i" :item="item" class="print:break-inside-avoid">
              <td class="border p-2 border-x-2 border-slate-300 text-center">{{ i + 1 }}</td>
              <td class="border p-2 border-x-2 border-slate-300 capitalize">{{ item.code }}</td>
              <td class="border p-2 border-x-2 border-slate-300 capitalize">{{ item.name }}</td>
              <td class="border p-2 border-x-2 border-slate-300 capitalize">{{ item.barcode }}</td>
              <td class="border p-2 border-x-2 border-slate-300 text-right">{{ rupiah(item.price.price_per_unit) }}</td>
              <td class="border p-2 border-x-2 border-slate-300 text-center">{{ item.stock_unit }}</td>
            </tr>
          </tbody>
        </table>
        <div class="flex items-center justify-start text-black space-y-2 mt-2">
          <div>File ini dicetak pada {{ dateindo(new Date(), true) }}</div>
        </div>
      </template>
    </Card>
  </AppLayout>
</template>