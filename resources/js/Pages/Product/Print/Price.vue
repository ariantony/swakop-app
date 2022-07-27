
<script setup>
import { getCurrentInstance, nextTick, onMounted, onUpdated, ref } from 'vue'
import { Link, useForm } from '@inertiajs/inertia-vue3'
import { Inertia } from '@inertiajs/inertia'
import AppLayout from '@/Layouts/AppLayout.vue'
import Card from '@/Components/Card.vue'
import Swal from 'sweetalert2'

const { products } = defineProps({
  products: Object,
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

<template>
  <AppLayout title="Print Harga Produk">
    <Card>
      <template #header>
        <div class="flex items-center justify-between space-x-2 text-white p-2">
          <h1 class="ml-4 text-black text-2xl font-semibold capitalize">Print Harga Barang</h1>
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
        <div class="flex flex-wrap items-center space-x-2">
          <div v-for="(item, i) in products" :key="i" class="flex flex-col justify-start w-[32%] my-1 border rounded-md bg-white space-y-1 ml-2">
            <h3 class="flex items-center text-sm bg-red-500 text-white  rounded-sm p-2 font-bold h-14">{{ item.name }}</h3>
            <div class="flex items-center justify-between">
              <h3 class="text-xs rounded-sm p-2 font-bold">{{ item.group.code }}</h3>
              <h3 class="text-xs rounded-sm p-2 font-bold">{{ item.barcode }}</h3>
            </div>
            <div class="flex items-center justify-between p-3">
              <p class="text-5xl font-semibold">Rp</p>
              <h1 class="text-5xl font-bold">{{ item.price.price_per_unit.toLocaleString('id') }}</h1>
            </div>
          </div>
        </div>
      </template>

      <template #print>
        <div class="flex flex-wrap items-center space-x-2">
          <div v-for="(item, i) in products" :key="i" :index="i" class="flex flex-col justify-start w-[32%] my-1 border rounded-md bg-white space-y-1 ml-2">
            <h3 class="flex items-center  text-sm bg-red-500 text-white rounded-sm p-2 font-bold h-14">{{ item.name }}</h3>
            <div class="flex items-center justify-between">
              <h3 class="text-xs rounded-sm px-2 font-bold">{{ item.group.code }}</h3>
              <h3 class="text-xs rounded-sm px-2 font-bold">{{ item.barcode }}</h3>
            </div>
            <div class="flex items-center justify-between p-2">
              <p class="text-4xl font-semibold">Rp</p>
              <h1 class="text-4xl font-bold">{{ item.price.price_per_unit.toLocaleString('id') }}</h1>
            </div>
          </div>
        </div>
      </template>
    </Card>
  </AppLayout>
</template>