
<script setup>
import { getCurrentInstance, nextTick, onMounted, onUpdated, ref } from 'vue'
import { Link, useForm } from '@inertiajs/inertia-vue3'
import { Inertia } from '@inertiajs/inertia'
import AppLayout from '@/Layouts/AppLayout.vue'
import Card from '@/Components/Card.vue'
import Swal from 'sweetalert2'

const { products, ids, pricetagGroups } = defineProps({
  products: Object,
  ids: Object,
  pricetagGroups: Object,
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

  const iframe = document.createElement('iframe')
  iframe.style.display = 'none'
  iframe.src = route('product.iframe.price', {
    products: ids,
  })
  document.body.appendChild(iframe)

  iframe.onload = () => {
    Swal.close()
  }
  
  setTimeout(() => { iframe.remove() }, 10000);
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

  @font-face {
    font-family: 'Kingthings-Exeter';
    src: url('../../../../../public/assets/fonts/Kingthings-Exeter.woff') format('woff');
    font-weight: normal;
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

  .kingthings {
    font-family: 'Kingthings-Exeter';
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
        <div class="flex flex-wrap items-center">
          <div v-for="(item, i) in products" :key="i" class="flex flex-col justify-start w-5cm my-1 border border-black bg-white kingthings">
            <table class="border-0"><tr class="border-0"><td class="border-0 p-0">
              <div class="max-w-5cm relative mb-[36px]">
                <h3 ref="title" class="absolute min-w-full h-[36px] flex items-center justify-center bg-red-500 text-center text-white p-2 border-b-black border-b font-bold text-sm">{{ item.name }}</h3>
              </div>
              <div class="px-1">
                <div class="flex items-center justify-center px-3 pb-1 border-b border-b-red-500">
                  <p class="text-2xl font-semibold">Rp &nbsp;</p>
                  <h1 class="text-2xl font-bold">{{ item.price?.price_per_unit ? item.price.price_per_unit.toLocaleString('id') : new Number(0).toLocaleString('id') }}</h1>
                </div>
                <table class="w-full">
                  <template v-if="item.price.variable_costs.length > 0">
                    <tr v-for="(row, j) in item.price.variable_costs.slice().reverse()" :key="j" class="text-black border-t border-t-red-500">
                      <td class="text-sm px-2 text-left">{{ row.qty }} pcs</td>
                      <td class="text-sm px-2 text-center"> - </td>
                      <td class="text-sm px-2 text-right">Rp. {{ row?.price ? row.price.toLocaleString('id') : new Number(0).toLocaleString('id') }}</td>
                    </tr>
                  </template>
                  <template v-else>
                    <tr v-for="(row, j) in [0, 1, 2]" :key="j" class="text-black border-t border-t-red-500">
                      <td class="text-sm text-white"> - </td>
                    </tr>
                  </template>
                </table>
              </div>
              <div class="flex items-center justify-between text-xs text-center border-t border-t-black">
                <h3 class="w-1/3 px-1 text-white bg-red-500 border-r border-r-black">{{ item?.group.code }}</h3>
                <h3 class="w-2/3 px-1 text-white bg-red-500">{{ item.barcode ? item.barcode : '&nbsp;' }}</h3>
              </div>
            </td></tr></table>
          </div>
        </div>
        <div class="flex flex-wrap items-center">
          <div v-for="(item, i) in pricetagGroups" :key="i" class="flex flex-col justify-start w-10cm my-1 border border-black bg-white kingthings">
            <table class="border-0"><tr class="border-0"><td class="border-0 p-0">
              <div class="max-w-10cm relative mb-[36px]">
                <h3 ref="title" class="absolute min-w-full h-[36px] flex items-center justify-center bg-red-500 text-center text-white p-2 border-b-black border-b font-bold text-lg">{{ String(item.name).toUpperCase() }}</h3>
              </div>
              <div class="px-2">
                <div class="flex items-center justify-center px-3 pb-1 border-b-2 border-b-red-500">
                  <p class="text-2xl font-semibold">Rp &nbsp;</p>
                  <h1 class="text-2xl font-bold">{{ item.sample.price?.price_per_unit ? item.sample.price.price_per_unit.toLocaleString('id') : new Number(0).toLocaleString('id') }}</h1>
                </div>
                <table class="w-full">
                  <template v-if="item.variable_costs.length > 0">
                    <tr v-for="(row, j) in item.variable_costs.slice().reverse()" :key="j" class="text-black border-t border-t-red-500">
                      <td class="text-sm text-left">Beli {{ row.qty }} pcs </td>
                      <td class="text-sm text-center">Rp. {{ row?.price ? row.price.toLocaleString('id') : new Number(0).toLocaleString('id') }}</td>
                      <td class="text-sm text-right">Rp. {{ (new Number(row?.price) * new Number(row?.qty)) ? (new Number(row?.price) * new Number(row?.qty)).toLocaleString('id') : new Number(0).toLocaleString('id') }}</td>
                    </tr>
                  </template>
                  <template v-else>
                    <tr v-for="(row, j) in [0, 1, 2]" :key="j" class="text-red-600 border-t border-t-red-600">
                      <td class="text-sm text-white"> - </td>
                    </tr>
                  </template>
                </table>
              </div>
              <div class="flex items-center justify-between text-xs text-center border-t border-t-black">
                <h3 class="w-1/3 px-1 text-white bg-red-500">{{ item.sample?.group.code }}</h3>
                <h3 class="w-1/3 px-1 bg-[#FFF14F]  capitalize border-x border-x-black">varian tidak bisa campur</h3>
                <h3 class="w-1/3 px-1 text-white bg-red-500">{{ item.barcode }}</h3>
              </div>
            </td></tr></table>
          </div>
        </div>
      </template>
    </Card>
  </AppLayout>
</template>