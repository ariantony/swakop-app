<script setup>
import { getCurrentInstance, nextTick, onMounted, onUnmounted, ref } from 'vue';
import { useForm } from '@inertiajs/inertia-vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Card from '@/Components/Card.vue';
import Select from '@vueform/multiselect'
import axios from 'axios';
import Swal from 'sweetalert2';
import DataTable from './DataTable.vue'
import DataTableDetail from './DataTableDetail.vue';

const self = getCurrentInstance()

const product = ref({
  name: '',
})
const a = ref(true)
const transaction = ref(null)
const products = ref([])
const form = useForm({
  product: null,
  qty: 0,
  note: '',
})

const open = ref(false)
const show = () => {
  product.value = products.value.find(p => p.id === form.product)
  
  nextTick(() => open.value = true)
}

const close = () => {
  open.value = false
  transaction.value = null
  form.reset()

  a.value = false
  nextTick(() => a.value = true)
}

const fetch = async () => {
  try {
    Swal.fire({
      title: 'Mengambil data produk',
      text: 'Mohon tunggu ...',
      allowOutsideClick: false,
      allowEscapeKey: false,
      allowEnterKey: false,
      showConfirmButton: false,
      didOpen: () => {
        Swal.showLoading()
      },
    })
    const response = await axios.get(route('api.product.where.has.stock'))
    products.value = response.data
    Swal.close()
  } catch (e) {
    const response = await Swal.fire({
      title: 'Tidak dapat mendapatkan produk',
      text: 'Apakah anda ingin mencoba kembali?',
      icon: 'question',
      showCancelButton: true,
      showCloseButton: true,
    })

    response.isConfirmed && fetch()
  }
}

const submit = async () => {
  const response = await Swal.fire({
    title: 'Masukan kode',
    icon: 'question',
    showCloseButton: true,
    showCancelButton: true,
    input: 'password',
    inputPlaceholder: 'Masukan kode',
    inputValidator: async code => {
      try {
        const response = await axios.post(route('api.compare'), { code })
      } catch (e) {
        console.log(e)
        return 'Kode salah'
      }
    },
  })

  response.isConfirmed && form.post(route('return-stock.store'), {
    onSuccess: () => {
      fetch()
      close()
    },
    onError: () => show(),
  })
}

const detail = t => {
  transaction.value = t
}

const esc = e => e.key === 'Escape' && close()

onMounted(fetch)
onMounted(() => window.addEventListener('keydown', esc))
onUnmounted(() => window.removeEventListener('keydown', esc))
</script>

<style src="@vueform/multiselect/themes/default.css"></style>

<template>
  <AppLayout title="Retur Pembelian">
    <Card>
      <template #header>
        <div class="flex items-center justify-start space-x-2 text-white p-2">
          <h1 class="ml-4 text-black text-2xl font-semibold">Retur Pembelian</h1>
        </div>
      </template>

      <template #body>
        <div class="flex flex-col space-y-2">
          <label for="product" class="lowercase first-letter:capitalize flex-none">kode produk / nama produk / barcode</label>
          <Select 
            v-model="form.product"
            :options="products?.map(p => ({
              label: `${p.code ? p.code + '-' : ''} ${p.barcode} - ${p.name}`,
              value: p.id,
            }))"
            :searchable="true"
            noOptionsText="Mohon tunggu..."
            @change="nextTick(show)" />
        </div>
      </template>
    </Card>

    <Card v-if="a">
      <template #header>
        <div class="flex items-center justify-start space-x-2 text-white p-2">
          <h1 class="ml-4 text-black text-2xl font-semibold">Riwayat Retur Pengembalian Pembelian</h1>
        </div>
      </template>

      <template #body>
        <DataTable :detail="detail" />
      </template>
    </Card>
  </AppLayout>

  <transition name="fade">
    <div v-if="open" class="fixed top-0 left-0 w-full h-screen bg-slate-600 bg-opacity-70"></div>
  </transition>

  <transition name="slide-fade">
    <div v-if="open" class="fixed top-0 left-0 w-full h-screen flex items-center justify-center">
      <form @submit.prevent="submit" class="relative w-full max-w-3xl bg-slate-100 rounded-md z-10">
        <Card>
          <template #header>
            <div class="flex items-center justify-between space-x-2 text-white p-2">
              <h1 class="ml-4 text-black text-2xl font-semibold">Retur Pembelian</h1>
              <i @click.prevent="close" class="bx bx-x px-2 py-1 rounded-md bg-red-500 hover:bg-red-600 text-white transition-all cursor-pointer"></i>
            </div>
          </template>

          <template #body>
            <div class="flex flex-col space-y-2">
              <div class="flex flex-col space-y-1">
                <div class="flex items-center space-x-2">
                  <label class="flex-none w-1/4">Nama Produk</label>
                  <div class="font-semibold">{{ product.name }}</div>
                </div>
              </div>

              <div class="flex flex-col space-y-1">
                <div class="flex items-center space-x-2">
                  <label class="flex-none w-1/4">Barcode</label>
                  <div class="font-semibold">{{ product.barcode }}</div>
                </div>
              </div>

              <div class="flex flex-col space-y-1">
                <div class="flex items-center space-x-2">
                  <label class="flex-none w-1/4">Sisa stok</label>
                  <div class="font-semibold">{{ product.stock_unit }}</div>
                </div>
              </div>

              <div class="flex flex-col space-y-1">
                <div class="flex items-center space-x-2">
                  <label class="flex-none w-1/4">Jumlah yang akan di retur</label>
                  <input v-model="form.qty" type="number" :max="product.stock_unit" min="1" class="w-full rounded-md font-semibold" placeholder="Jumlah stock yang akan di retur" required />
                </div>

                <p v-if="form.errors.qty" class="text-sm text-right text-red-500">{{ form.errors.qty }}</p>
              </div>

              <div class="flex flex-col space-y-1">
                <div class="flex items-center space-x-2">
                  <label class="flex-none w-1/4">Keterangan</label>
                  <input v-model="form.note" type="text" name="note" class="w-full rounded-md" placeholder="Keterangan" required />
                </div>

                <p v-if="form.errors.note" class="text-sm text-right text-red-500">{{ form.errors.note }}</p>
              </div>
            </div>
          </template>

          <template #footer>
            <div class="flex items-center space-x-2 justify-end px-2 py-1">
              <button type="submit" class="bg-green-600 hover:bg-green-700 rounded-md px-3 py-1 text-sm text-white transition-all">
                <div class="flex items-center space-x-1">
                  <i class="bx bx-check"></i>
                  <p class="uppercase font-semibold">retur</p>
                </div>
              </button>
            </div>
          </template>
        </Card>
      </form>
    </div>
  </transition>

  <transition name="fade">
    <div v-if="transaction" class="fixed top-0 left-0 w-full h-screen bg-black bg-opacity-40 flex items-center justify-center overflow-auto">
      <div class="w-full max-w-5xl rounded-md">
        <Card>
          <template #header>
            <div class="flex items-center justify-between space-x-2 text-white p-2">
              <h1 class="ml-4 text-black text-2xl font-semibold">Detail transaksi</h1>
              <i @click.prevent="close" class="bx bx-x px-2 py-1 rounded-md bg-red-500 hover:bg-red-600 text-white transition-all cursor-pointer"></i>
            </div>
          </template>

          <template #body>
            <DataTableDetail v-if="transaction" :transaction="transaction" />
          </template>
        </Card>
      </div>
    </div>
  </transition>
</template>