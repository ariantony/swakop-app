
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

</script>

<style src="@vueform/multiselect/themes/default.css"></style>

<template>
  <AppLayout title="Print Produk">
    <Card>
      <template #header>
        <div class="flex items-center justify-between space-x-2 text-white p-2">
          <h1 class="ml-4 text-black text-2xl font-semibold capitalize">Produk dengan Kelompok Barang "{{ group.code }} - {{ new String(group.name).toUpperCase() }}"</h1>
          <div class="flex flex-none space-x-2">
            <a :href="route('product.iframe.group', { group_id: group.id })" target="_blank" type="button" class="bg-pink-600 rounded-md px-3 py-1 font-semibold">
              <div class="flex items-center">
                <i class="bx bx-download mr-1 text-xl"></i> Download
              </div>
            </a>
            <Link :href="route('product.print')" class="bg-slate-600 rounded-md px-3 py-1 font-semibold">
              <div class="flex items-center">
                <i class="bx bx-arrow-back mr-1 text-xl"></i> Kembali
              </div>
            </Link>
          </div>
        </div>
      </template>
      <template #body>
        <div class="h-[28rem] overflow-y-auto">
          <table class="w-full border-collapse border-2 border-slate-300">
            <thead class="bg-slate-100">
              <tr class="bg-slate-100">
                <th class="px-1 py-2 uppercase border-b-2 border-x-2 border-slate-300">No</th>
                <th class="px-1 py-2 uppercase border-b-2 border-x-2 border-slate-300">Kode</th>
                <th class="px-1 py-2 uppercase border-b-2 border-x-2 border-slate-300">Nama</th>
                <th class="px-1 py-2 uppercase border-b-2 border-x-2 border-slate-300">Barcode</th>
                <th class="px-1 py-2 uppercase border-b-2 border-x-2 border-slate-300">Harga</th>
                <th class="px-1 py-2 uppercase border-b-2 border-x-2 border-slate-300 w-16">Stok</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(item, i) in products" :key="i" :item="item">
                <td class="border p-2 border-x-2 border-slate-300 text-center">{{ i + 1 }}</td>
                <td class="border p-2 border-x-2 border-slate-300 capitalize">{{ item.code }}</td>
                <td class="border p-2 border-x-2 border-slate-300 capitalize">{{ item.name }}</td>
                <td class="border p-2 border-x-2 border-slate-300 capitalize">{{ item.barcode }}</td>
                <td class="border p-2 border-x-2 border-slate-300 text-right">{{ rupiah(item?.price?.price_per_unit) }}</td>
                <td class="border p-2 border-x-2 border-slate-300 text-center">{{ item.stock_unit }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </template>
    </Card>
  </AppLayout>
</template>