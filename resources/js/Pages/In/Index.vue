<script setup>
import { getCurrentInstance, ref, nextTick, onMounted, watch } from 'vue'
import AppLayout from '@/Layouts/AppLayout.vue'
import Select from '@vueform/multiselect'
import { useForm } from '@inertiajs/inertia-vue3'
import Swal from 'sweetalert2'
import Card from '@/Components/Card.vue'
import DataTable from './DataTable.vue'
import Detail from './Detail.vue'
import { Inertia } from '@inertiajs/inertia'
import axios from 'axios'

const self = getCurrentInstance()
const { groups } = defineProps({
  groups: Array,
})

const timeout = ref(null)
const search = ref('')

watch(search, () => {
  products.value = []
  clearTimeout(timeout.value)
  setTimeout(() => {
    fetch()
  }, 200)
})

const products = ref([])
const product = ref(null)
const a = ref(true)
const rr = () => {
  a.value = false
  nextTick(() => a.value = true)
}

const form = useForm({
  product: null,
  qty: '',
})

const create = useForm({
  code: '',
  name: '',
  barcode: '',
  group_id: null,
  qty: 1,
  price: {
    buy: {
      unit: 0,
      box: 0,
      carton: 0,
    },
    sell: {
      unit: 0,
      box: 0,
      carton: 0,
    },
  },
  variables: [],
})

const del = useForm({
  id: null,
})

const open = ref(false)
const detail = ref(null)
const selected = ref({})
const toBeDeleted = ref({})

const show = () => {
  open.value = true
}

const close = () => {
  open.value = false
}

const option = e => {
  const product = products.value.find(p => p.barcode === e)

  if (product) {
    form.product = product.id

    return add()
  }

  create.barcode = e
  open.value = true
}

const reformat = (e, target, initial) => {
  const value = initial || e.target.value
  let val = new String(value),
      replaced = val.replace(/[^,\d]/g, '').toString(),
      split = replaced.split(','),
      remaining = split[0].length % 3,
      result = split[0].substring(0, remaining),
      thousand = split[0].substring(remaining).match(/\d{3}/gi),
      separator;

  if (thousand) {
    separator = remaining ? '.' : '';
    result += separator + thousand.join('.');
  }

  result = split[1] != undefined ? result + ',' + split[1] : result;

  e.target.value = `Rp ${result}`
  return parseFloat(result.replaceAll('.', '').replaceAll(',', '.'))
}

const add = async () => {
  const selected = products.value.find(p => p.id === form.product || p.barcode === form.product)

  if (typeof selected === 'undefined') {
    return Swal.fire({
      icon: 'error',
      title: 'Oops...',
      text: 'Produk tidak ditemukan!',
    })
  }

  return Swal.fire({
    title: 'Konfirmasi input stok',
    icon: 'question',
    html:
      `Produk <span class="capitalize font-bold"> ${selected?.name} </span> telah ditambahkan sebelumnya dan saat ini memilki stok sebanyak <span class="font-bold text-blue-600">${selected?.stock_unit}</span> unit. <br>
      Anda yakin akan menambahkan stok sebanyak <span class="font-bold text-green-600">${form.qty}</span> sehingga total stok produk menjadi <span class="font-bold text-blue-600">${form.qty + selected?.stock_unit}</span> ?`,
    showCancelButton: true,
  }).then(({isConfirmed}) => {
    if (isConfirmed) {
      form.product = typeof form.product === 'string' ? selected.id : form.product
      
      form.post(route('in.add'), {
        onSuccess: () => {
          form.reset()
          fetch()
        },
      })
    }
  })
}

const store = () => {
  return create.post(route('in.store'), {
    onSuccess: () => {
      form.reset()
      create.reset()
      close()
    },

    onError: () => nextTick(show),
  })
}

const destroy = async (item) => {
  const selected = products.value.find(p => p.id === item.product_id)
  try {
    const response = await axios.post(route('api.in.show', { detail: item.id }))
    toBeDeleted.value = response.data
    del.id = item.id

    if ((selected?.stock_unit - toBeDeleted.value.qty_unit) < 0) {
      return Swal.fire({
        title: 'Peringatan',
        icon: 'warning',
        html: `Stok produk <span class="capitalize font-bold">${selected?.name}</span> saat ini hanya tersisa <span class="font-bold text-blue-600">${selected?.stock_unit}</span> unit. <br>
        Anda tidak dapat menghapus data ini karena akan mengakibatkan stok produk menjadi minus.`
      })
    }

    Swal.fire({
      title: 'Konfirmasi hapus stok',
      icon: 'question',
      html:
        `Produk <span class="capitalize font-bold"> ${selected?.name} </span> saat ini memilki stok sebanyak <span class="font-bold text-blue-600">${selected?.stock_unit}</span> unit. <br>
        Anda yakin akan mengurangi stok sebanyak <span class="font-bold text-red-600">${toBeDeleted.value.qty_unit}</span> sehingga total stok produk menjadi <span class="font-bold text-blue-600">${selected?.stock_unit - toBeDeleted.value.qty_unit}</span> ?`,
      showCancelButton: true,
    }).then(({ isConfirmed }) => {
      if (isConfirmed) {
        del.post(route('in.delete'), {
          onSuccess: () => {
            fetch()
          },
        })
      }
    })
  } catch (e) {
    const response = await Swal.fire({
      title: 'Proses hapus gagal',
      text: 'Apakah anda ingin mencoba lagi?',
      icon: 'question',
      showCancelButton: true,
      showCloseButton: true,
    })

    response.isConfirmed && destroy(item)
  }
}

const addVariable = () => create.variables.push({
  qty: 1,
  price: 0,
})

Inertia.on('finish', () => rr())

const fetch = async () => {
  try {
    const response = await axios.get(route('api.product.without.group.and.price', {
      q: search.value,
    }))
    products.value = response.data
  } catch (e) {
    const response = await Swal.fire({
      title: 'Pengambilan data produk gagal',
      text: 'Apakah anda ingin mencoba lagi?',
      icon: 'question',
      showCancelButton: true,
      showCloseButton: true,
    })

    response.isConfirmed && fetch()
  }
  self?.refs?.product?.focus()
}

onMounted(fetch)
</script>

<style src="@vueform/multiselect/themes/default.css"></style>
<style scoped>
  input::-webkit-outer-spin-button, input::-webkit-inner-spin-button {
    -webkit-appearance: none !important;
  }
  input[type="number"] {
    -moz-appearance: textfield;
  }
  </style>

<template>
  <AppLayout title="Stock masuk">
    <Card>
      <template #header>
        <div class="flex items-center justify-start space-x-2 text-white p-2">
          <h1 class="ml-4 text-black text-2xl font-semibold">Transaksi Pembelian</h1>
        </div>
      </template>
      <template #body>
        <form @submit.prevent="add" v-if="open === false" class="flex flex-col space-y-2">
          <div class="flex items-center space-x-4">
            <div class="flex items-center space-x-4 w-full">
              <div class="flex flex-col space-y-2 w-full">
                <div class="flex items-center space-x-4 w-full">
                  <label for="product" class="capitalize flex-none">nama produk / barcode</label>
                  <Select 
                    ref="product"
                    v-model="form.product"
                    :options="products.map(p => ({
                      label: `${p.code ? p.code + '-' : ''} ${p.barcode} - ${p.name}`,
                      value: p.id,
                    }))"
                    :searchable="true"
                    :createOption="true"
                    @searchChange="search = $event"
                    @option="option"
                    noOptionsText="Mohon tunggu..." />
                </div>

                <div class="text-red-500 text-right text-sm" v-html="form.errors.product || '&nbsp;'"></div>
              </div>
            </div>

            <div class="flex flex-col space-y-2">
              <div class="flex items-center space-x-4">
                <label for="qty" class="uppercase flex-none">qty</label>
                <input type="number" name="qty" v-model="form.qty" min="1" placeholder="QTY" class="rounded border border-slate-300" required>
              </div>

              <div class="text-red-500 text-right text-sm" v-html="form.errors.qty || '&nbsp;'"></div>
            </div>
          </div>

          <div class="flex items-center space-x-2 justify-end">
            <button type="submit" class="bg-green-600 hover:bg-green-700 rounded-md px-3 py-1 text-white text-sm transition-all">
              <div class="flex items-center space-x-1">
                <i class="bx bx-plus"></i>
                <p class="capitalize font-semibold">tambah</p>
              </div>
            </button>
          </div>
        </form>
      </template>
    </Card>

    <Card v-if="a">
      <template #header>
        <div class="flex items-center justify-start space-x-2 text-white p-2">
          <h1 class="ml-4 text-black text-2xl font-semibold">Riwayat Transaksi Pembelian</h1>
        </div>
      </template>
      <template #body>
        <DataTable v-if="open === false" :detail="(transaction) => detail = transaction" :destroy="destroy" type="buy" />
      </template>
    </Card>
  </AppLayout>

  <transition name="fade">
    <div  v-if="detail || open" class="fixed top-0 left-0 w-full h-screen bg-slate-600 bg-opacity-70"></div>
  </transition>

  <transition name="slide-fade">
    <Detail v-if="detail" :transaction="detail" :close="() => detail = null" />
  </transition>

  <transition name="slide-fade">
    <div v-if="open" class="fixed top-0 left-0 w-full h-screen flex items-center justify-center">
      <form @submit.prevent="store" class="w-full max-w-6xl bg-slate-50 rounded-md max-h-[32rem] overflow-y-auto">
        <div class="flex flex-col rounded-md">
          <div class="flex items-center justify-between space-x-2 bg-slate-100 rounded-t-md p-2">
            <h1 class="text-bold text-2xl font-semibold">Tambah Produk Baru + Pembelian</h1>
            <button @click.prevent="close" type="button" class="rounded-md bg-red-500 text-white px-1">
              <i class="bx bx-x text-xl"></i>
            </button>
          </div>

          <div class="flex flex-col space-y-4 p-4">
            <div class="flex flex-col space-y-2">
              <div class="flex items-center space-x-2">
                <label for="code" class="lowercase first-letter:capitalize w-1/4">kode produk</label>
                <input ref="code" type="text" name="code" v-model="create.code" class="w-3/4 bg-white border border-slate-200 rounded-md placeholder:capitalize" placeholder="kode produk">
              </div>

              <div v-if="create.errors.code" class="text-right text-sm text-red-500">{{ create.errors.code }}</div>
            </div>

            <div class="flex flex-col space-y-2">
              <div class="flex items-center space-x-2">
                <label for="name" class="lowercase first-letter:capitalize w-1/4">nama produk</label>
                <input ref="name" type="text" name="name" v-model="create.name" class="w-3/4 bg-white border border-slate-200 rounded-md placeholder:capitalize" placeholder="nama produk" required>
              </div>

              <div v-if="create.errors.name" class="text-right text-sm text-red-500">{{ create.errors.name }}</div>
            </div>

            <div class="flex flex-col space-y-2">
              <div class="flex items-center space-x-2">
                <label for="barcode" class="lowercase first-letter:capitalize w-1/4">barcode</label>
                <input ref="barcode" type="text" name="barcode" v-model="create.barcode" class="w-3/4 bg-white border border-slate-200 rounded-md placeholder:capitalize" placeholder="barcode" disabled>
              </div>

              <div v-if="create.errors.barcode" class="text-right text-sm text-red-500">{{ create.errors.barcode }}</div>
            </div>

            <div class="flex flex-col space-y-2">
              <div class="flex items-center space-x-2">
                <label for="group_id" class="lowercase first-letter:capitalize w-1/3">Kelompok</label>
                <Select
                  v-model="create.group_id"
                  :options="groups.map(g => ({
                  label: `${g.code} - ${g.name}`,
                  value: g.id,
                  }))"
                  :searchable="true" />
              </div>

              <div v-if="create.errors.group_id" class="text-right text-sm text-red-500">{{ create.errors.group_id }}</div>
            </div>

            <div class="flex flex-col space-y-2">
              <div class="flex items-center space-x-2">
                <label for="price_buy_unit" class="lowercase first-letter:capitalize w-1/4">harga beli per unit</label>
                <input ref="price_buy_unit" type="text" name="price_buy_unit" @input.prevent="create.price.buy.unit = reformat($event)" class="w-3/4 bg-white border border-slate-200 rounded-md placeholder:capitalize text-right" placeholder="harga beli per unit" required>
              </div>

              <div v-if="create.errors.price?.buy?.unit" class="text-right text-sm text-red-500">{{ create.errors.price?.buy?.unit }}</div>
            </div>

            <div class="flex flex-col space-y-2">
              <div class="flex items-center space-x-2">
                <label for="price_sell_unit" class="lowercase first-letter:capitalize w-1/4">harga jual per unit</label>
                <input ref="price_sell_unit" type="text" name="price_sell_unit" @input.prevent="create.price.sell.unit = reformat($event)" class="w-3/4 bg-white border border-slate-200 rounded-md placeholder:capitalize text-right" placeholder="harga jual per unit" required>
              </div>

              <div v-if="create.errors.price?.sell?.unit" class="text-right text-sm text-red-500">{{ create.errors.price?.sell?.unit }}</div>
            </div>

            <template v-for="(variable, i) in create.variables" :key="i">
              <div class="flex flex-col space-y-2">
                <div class="flex items-center space-x-2">
                  <label :for="`variables[${i}]`" class="w-1/4 flex items-center space-x-2">
                    Harga jual <input type="number" v-model="variable.qty" min="1" class="w-12 p-0 ml-2 rounded text-center" required> &nbsp; unit
                  </label>
                  <input :ref="`variables[${i}]`" type="text" :name="`variables[${i}]`" @input.prevent="variable.price = reformat($event)" class="w-3/4 bg-white border border-slate-200 rounded-md placeholder:capitalize text-right" :placeholder="`harga jual ${variable.qty} unit`" required>
                </div>

                <div v-if="create.errors[`variables.${i}.price`]" class="text-right text-sm text-red-500">{{ create.errors.price?.sell?.unit }}</div>
              </div>
            </template>

            <div class="flex flex-col space-y-2">
              <div class="flex items-center space-x-2">
                <label for="qty" class="lowercase first-letter:capitalize w-1/4">qty</label>
                <input ref="qty" type="number" name="qty" v-model="create.qty" class="w-3/4 bg-white border border-slate-200 rounded-md placeholder:capitalize" placeholder="qty" min="1" required>
              </div>

              <div v-if="create.errors.qty" class="text-right text-sm text-red-500">{{ create.errors.qty }}</div>
            </div>
          </div>

          <div class="flex items-center justify-end space-x-2 px-2 py-1 bg-slate-100 rounded-b-md">
            <button @click.prevent="addVariable" type="button" class="bg-blue-600 hover:bg-blue-700 rounded-md px-3 py-1 text-white transition-all">
              <div class="flex items-center space-x-1">
                <i class="bx bx-plus"></i>
                <p class="capitalize font-semibold">tambah harga</p>
              </div>
            </button>

            <button class="bg-green-600 hover:bg-green-700 rounded-md px-3 py-1 text-white transition-all">
              <div class="flex items-center space-x-1">
                <i class="bx bx-check"></i>
                <p class="capitalize font-semibold">buat</p>
              </div>
            </button>
          </div>
        </div>
      </form>
    </div>
  </transition>
</template>