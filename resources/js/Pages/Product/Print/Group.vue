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
const form = useForm({
  so: [],
})

const calculate = (productId) => {
  const real = self.refs[`real-${productId}`][0].value

  if (real === '') {
    self.refs[`note-${productId}`][0].innerHTML = ''
    return
  }

  const stock_unit = self.refs[`stock-${productId}`][0].innerHTML
  const diff = Number(real) - Number(stock_unit)
  let note = self.refs[`note-${productId}`][0]

  note.innerHTML = diff
  
  if (diff === 0) {
    note.classList.add('text-green-500')
    note.classList.remove('text-red-500')
    note.innerHTML = '✓'
  } else {
    note.classList.remove('text-green-500')
    note.classList.add('text-red-500')
  }

  add(productId, {
    real: real,
    note: note.innerHTML,
  })
}

const add = (productId, { real, note }) => {
  const index = form.so.findIndex((item) => item.product_id === productId)

  if (index === -1) {
    form.so.push({
      product_id: productId,
      real,
      note,
    })
  } else {
    form.so[index].real = real
    form.so[index].note = note
  }
}
</script>

<style src="@vueform/multiselect/themes/default.css"></style>
<style>
  input::-webkit-outer-spin-button,
  input::-webkit-inner-spin-button {
    -webkit-appearance: none;
    margin: 0;
  }
  input[type=number] {
    -moz-appearance: textfield;
    appearance: textfield;
  }
</style>

<template>
  <AppLayout title="Print Produk">
    <Card>
      <template #header>
        <div class="flex items-center justify-between space-x-2 text-white p-2">
          <h1 class="ml-4 text-black text-2xl font-semibold capitalize">Produk dengan Kelompok Barang "{{ group.code }} - {{ new String(group.name).toUpperCase() }}"</h1>
          <div class="flex flex-none space-x-2">
            <a :href="route('product.iframe.group', { group_id: group.id, so: form.so })" target="_blank" type="button" class="bg-pink-600 rounded-md px-3 py-1 font-semibold">
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
                <th class="px-1 py-2 uppercase border-b-2 border-x-2 border-slate-300 w-16">Limit</th>
                <th class="px-1 py-2 uppercase border-b-2 border-x-2 border-slate-300 w-16">Stok</th>
                <th class="px-1 py-2 uppercase border-b-2 border-x-2 border-slate-300 w-20">Fisik</th>
                <th class="px-1 py-2 uppercase border-b-2 border-x-2 border-slate-300 w-16">Ket</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(item, i) in products" :key="i" :item="item" :class="(item.stock_unit <= Number(item.restock_limit) ? 'bg-yellow-300' : '')">
                <td class="border p-2 border-x-2 border-slate-300 text-center">{{ i + 1 }}</td>
                <td class="border p-2 border-x-2 border-slate-300 capitalize">{{ item.code }}</td>
                <td class="border p-2 border-x-2 border-slate-300 capitalize">{{ item.name }}</td>
                <td class="border p-2 border-x-2 border-slate-300 capitalize">{{ item.barcode }}</td>
                <td class="border p-2 border-x-2 border-slate-300 text-right">{{ item.price?.price_per_unit ? rupiah(item?.price?.price_per_unit) : '-' }}</td>
                <td class="border p-2 border-x-2 border-slate-300 text-center">{{ item.restock_limit }}</td>
                <td class="border p-2 border-x-2 border-slate-300 text-center" :ref="`stock-${item.id}`">{{ item.stock_unit }}</td>
                <td class="border p-2 border-x-2 border-slate-300 text-center">
                  <input @input.prevent="calculate(item.id)" :ref="`real-${item.id}`" type="number" class="rounded border border-slate-300 w-full text-center" />
                </td>
                <td class="border p-2 border-x-2 border-slate-300 text-center font-bold text-lg">
                  <div :ref="`note-${item.id}`"></div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </template>
    </Card>
  </AppLayout>
</template>