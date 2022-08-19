
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
const title = ref([])

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


const shrink = (i, size = null) => {
  const el = title.value[i];
  if (el) {
    if (!size) {
      size = 12;
      el.style.fontSize = size + 'px';
    }
    do {
      size--;
      el.style.padding = '0.25rem';
      el.style.fontSize = size + 'px';
    } while (isOverflow(el));
  }
  return 'text-xs'
}

const isOverflow = (el) => {
  return el.clientWidth > el.parentElement.clientWidth;
}
</script>

<style scoped>
  @font-face {
    font-family: 'ArmWrestler';
    src: url('../../../../../public/assets/fonts/ArmWrestler.woff') format('woff');
    font-weight: lighter;
    font-style: normal;
    font-display: swap;
  }

  @font-face {
    font-family: 'Boogaloo';
    src: url('../../../../../public/assets/fonts/Boogaloo-Regular.woff') format('woff');
    font-weight: normal;
    font-style: normal;
    font-display: swap;
  }

  @font-face {
    font-family: 'Amaranth';
    src: url('../../../../../public/assets/fonts/Amaranth-Bold.woff') format('woff');
    font-weight: bold;
    font-style: normal;
    font-display: swap;
  }

  .product {
    font-family: 'ArmWrestler';
  }

  .barcode {
    font-family: 'Boogaloo';
  }

  .price {
    font-family: 'Amaranth';
  }
</style>

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
          <div v-for="(item, i) in products" :key="i" class="flex flex-col justify-start w-5cm h-3cm my-1 border border-black bg-white space-y-2 ml-2">
            <table class="border-0"><tr class="border-0"><td class="border-0 p-0">
              <div class="max-w-[188px] relative mb-[36px]">
                <h3 ref="title" class="absolute min-w-full h-[36px] flex items-center justify-center bg-red-500 text-white p-2 border-b-black border-b product" :class="item.name.length > 20 ? shrink(i) : 'text-sm'">{{ item.name }}</h3>
              </div>
              <div class="flex items-center justify-between mb-1">
                <h3 class="text-xs rounded-sm px-2 font-bold">{{ item.group.code }}</h3>
                <h3 class="text-xs rounded-sm px-2 barcode">{{ item.barcode }}</h3>
              </div>
              <div class="flex items-center justify-between px-3 pb-1 price">
                <p class="text-2xl font-semibold">Rp</p>
                <h1 class="text-2xl font-bold">{{ item.price.price_per_unit.toLocaleString('id') }}</h1>
              </div>
              <div class="flex items-center justify-center border-t-red-600 border-t">
                <img :src="url('assets/images/logo-swakop.png')" class="w-16 h-5" alt="Logo Swakop">
              </div>
            </td></tr></table>
          </div>
        </div>
      </template>

      <template #print>
        <div class="flex flex-wrap items-center space-x-2">
          <div v-for="(item, i) in products" :key="i" class="flex flex-col justify-start w-5cm h-3cm my-1 border border-black bg-white space-y-2 ml-2">
            <table class="border-0"><tr class="border-0"><td class="border-0 p-0">
              <div class="max-w-[188px] relative mb-[36px]">
                <h3 ref="title" class="absolute min-w-full h-[36px] flex items-center justify-center bg-red-500 text-white p-2 border-b-black border-b product" :class="item.name.length > 20 ? shrink(i) : 'text-sm'">{{ item.name }}</h3>
              </div>
              <div class="flex items-center justify-between mb-1">
                <h3 class="text-xs rounded-sm px-2 font-bold">{{ item.group.code }}</h3>
                <h3 class="text-xs rounded-sm px-2 barcode">{{ item.barcode }}</h3>
              </div>
              <div class="flex items-center justify-between px-3 pb-1 price">
                <p class="text-2xl font-semibold">Rp</p>
                <h1 class="text-2xl font-bold">{{ item.price.price_per_unit.toLocaleString('id') }}</h1>
              </div>
              <div class="flex items-center justify-center border-t-red-600 border-t">
                <img :src="url('assets/images/logo-swakop.png')" class="w-6 pt-1" alt="Logo Swakop">
              </div>
            </td></tr></table>
          </div>
        </div>
      </template>
    </Card>
  </AppLayout>
</template>