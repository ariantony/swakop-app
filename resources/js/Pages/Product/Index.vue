<script setup>
import { getCurrentInstance, nextTick, onMounted, ref } from 'vue'
import { Link, useForm } from '@inertiajs/inertia-vue3'
import { Inertia } from '@inertiajs/inertia'
import AppLayout from '@/Layouts/AppLayout.vue'
import Card from '@/Components/Card.vue'
import DataTable from './DataTable.vue'
import Swal from 'sweetalert2'
import Detail from './Detail.vue'
import Price from './Price.vue'
import Select from '@vueform/multiselect'

const { groups } = defineProps({
  groups: Array,
})
const self = getCurrentInstance()
const page = ref(null)
const table = ref(null)
const render = ref(true)
const open = ref(false)
const openStock = ref(false)
const form = useForm({
  id: null,
  code: null,
  name: '',
  barcode: '',
  group_id: null,
  restock_limit: null,
})
const formStock = useForm({
  product: null,
  type: null,
  qty: null,
  name: null,
  exist: null,
})
const users = ref([])
const price = ref(null)
const transaction = ref(null)
const stock = ref(null)

const show = () => {
  open.value = true

  nextTick(() => {
    if(form.errors.code) self.refs.code.focus()
    else if(form.errors.name) self.refs.name.focus()
    else if(form.errors.barcode) self.refs.barcode.focus()
    else if(form.errors.restock_limit) self.refs.restock_limit.focus()
  })
}

const close = () => {
  open.value = false
  form.reset()
  price.value = null
  transaction.value = null
  stock.value = false
}

const closeDetail = () => {
  open.value = false
  form.reset()
  price.value = null
  transaction.value = null
  stock.value = null
}

const reset = () => {
  render.value = false
  nextTick(() => {
    render.value = true
    table.value.refresh(page.value)
  })
  form.reset()
  formStock.reset()
  close()
}

const store = () => {
  return form.post(route('product.store'), {
    onSuccess: () => reset(),
    onError: () => nextTick(show),
  })
}

const edit = product => {
  form.id = product.id
  form.code = product.code
  form.name = product.name
  form.barcode = product.barcode
  form.group_id = product.group.id
  form.restock_limit = product.restock_limit

  show()
}

const update = () => {
  return form.patch(route('product.update', form.id), {
    onSuccess: () => reset(),
    onError: () => nextTick(show),
  })
}

// const destroy = product => {
//   return Swal.fire({
//     title: 'Konfirmasi!',
//     html: 'Anda yakin akan menghapus produk ini? Semua pencatatan data yang berhubungan produk ini akan ikut terhapus.',
//     icon: 'question',
//     showCancelButton: true,
//   }).then(response => {
//     if (response.isConfirmed) {
//       return Inertia.delete(route('product.destroy', product.id), {
//         onSuccess: () => reset(),
//       })
//     }
//   })
// }

const submit = () => {
  page.value = table.value.data().current_page
  form.id ? update() : store()
}

const editStock = product => {
  formStock.product = product.id
  formStock.name = product.name
  formStock.exist = product.stock_unit
  stock.value = true
}

const submitStock = () => {
  if (formStock.type === 'sell' && formStock.exist - formStock.qty < 0) {
    return Swal.fire({
      title: 'Peringatan',
      text: 'Stok akan minus jika melakukan edit stok ini. Mohon periksa kembali.',
      icon: 'warning',
    }).then(response => {
      self.refs?.qty?.focus()
    })
  }

  return Swal.fire({
    title: 'Konfirmasi edit stok?',
    html: '<span class="text-red-600"> Harap berhati-hati, edit stok tidak bisa dikembalikan! </span>',
    icon: 'question',
    showCancelButton: true,
  }).then(async response => {
    if (response.isConfirmed) {
      response = await Swal.fire({
        title: 'Masukan kode',
        icon: 'question',
        input: 'password',
        inputAttributes: {
          autocomplete: 'new-password',
          autocorrect: 'off',
        },
        inputValidator: async code => {
          try {
            const response = await axios.post(route('api.compare'), { code })
          } catch (e) {
            return 'Kode tidak sesuai'
          }
        }
      })

      response.isConfirmed && (
        formStock.post(route('product.edit.stock'), {
          onSuccess: () => {
            reset()
          },
        })
      )
    }
  });
}

onMounted(() => {
  window.addEventListener('keyup', e => {
    if (e.key === 'Escape' && (open.value || stock.value)) close() || form.reset() || formStock.reset()
  })
})
</script>

<style src="@vueform/multiselect/themes/default.css"></style>

<template>
  <AppLayout title="Produk">
    <Card>
      <template #header>
        <div class="flex items-center justify-between space-x-2 text-white p-2">
          <h1 class="ml-4 text-black text-2xl font-semibold">Produk</h1>
          <div v-if="isAdmin()" class="flex flex-none space-x-2">
            <Link :href="route('product.print')" class="bg-pink-600 rounded-md px-3 py-1 font-semibold">
              <div class="flex items-center">
                <i class="bx bx-printer mr-1 text-xl"></i> Print
              </div>
            </Link>
            <button @click.prevent="show" class="bg-green-600 rounded-md px-3 py-1 font-semibold">
              <div class="flex items-center">
                <i class="bx bx-plus mr-1 text-xl"></i> Tambah
              </div>
            </button>
          </div>
        </div>
      </template>
      <template #body>
        <DataTable
          ref="table" 
          :edit="edit" 
          :destroy="destroy" 
          :detail="(product) => transaction = product" 
          :price="(product) => price = product" 
          :editStock="editStock"/>
      </template>
    </Card>
  </AppLayout>

  <transition name="fade">
    <div  v-if="open || transaction || price || stock" class="fixed top-0 left-0 w-full h-screen bg-slate-600 bg-opacity-70"></div>
  </transition>

  <transition name="slide-fade">
    <div v-if="open" class="fixed top-0 left-0 w-full h-screen flex items-center justify-center">
      <form @submit.prevent="submit" class="relative w-full max-w-3xl bg-slate-100 rounded-md z-10">
        <Card>
          <template #header>
            <div class="flex items-center justify-between space-x-2 p-2">
              <h1 class="ml-2 text-black text-2xl font-semibold">{{ form.id ? 'Edit' : 'Tambah' }} Produk</h1>
              <button @click.prevent="close" type="button" class="rounded-md bg-red-500 text-white px-1">
                <i class="bx bx-x text-xl"></i>
              </button>
            </div>
          </template>

          <template #body>
            <div class="flex flex-col space-y-2">
              <div class="flex flex-col space-y-2">
                <div class="flex items-center space-x-2">
                  <label for="code" class="lowercase first-letter:capitalize w-1/4">kode</label>
                  <input ref="code" type="text" name="code" v-model="form.code" class="w-3/4 bg-white border border-slate-200 rounded uppercase placeholder:capitalize" autocomplete="off" placeholder="kode">
                </div>
                <div v-if="form.errors.code" class="text-right text-red-400 text-sm lowercase first-letter:capitalize">{{ form.errors.code }}</div>
              </div>

              <div class="flex flex-col space-y-2">
                <div class="flex items-center space-x-2">
                  <label for="name" class="lowercase first-letter:capitalize w-1/4">nama</label>
                  <input ref="name" type="text" name="name" v-model="form.name" class="w-3/4 bg-white border border-slate-200 rounded uppercase placeholder:capitalize" autocomplete="off" placeholder="nama">
                </div>
                <div v-if="form.errors.name" class="text-right text-red-400 text-sm lowercase first-letter:capitalize">{{ form.errors.name }}</div>
              </div>

              <div class="flex flex-col space-y-2">
                <div class="flex items-center space-x-2">
                  <label for="group_id" class="capitalize w-1/3">Kelompok barang</label>
                  <Select
                    v-model="form.group_id"
                    :options="groups.map(g => ({
                      label: `${String(g.code).toUpperCase()} - ${String(g.name).toUpperCase()}`,
                      value: g.id,
                    }))"
                    :searchable="true" />
                </div>
                <div v-if="form.errors.group_id" class="text-right text-red-400 text-sm capitalize">{{ form.errors.group_id }}</div>
              </div>

              <div class="flex flex-col space-y-2">
                <div class="flex items-center space-x-2">
                  <label for="barcode" class="lowercase first-letter:capitalize w-1/4">barcode</label>
                  <input ref="barcode" type="text" name="barcode" v-model="form.barcode" class="w-3/4 bg-white border border-slate-200 rounded uppercase placeholder:capitalize" autocomplete="off" placeholder="barcode">
                </div>
                <div v-if="form.errors.barcode" class="text-right text-red-400 text-sm lowercase first-letter:capitalize">{{ form.errors.barcode }}</div>
              </div>

              <div class="flex flex-col space-y-2">
                <div class="flex items-center space-x-2">
                  <label for="restock_limit" class="capitalize w-1/4">Restock Limit</label>
                  <input ref="restock_limit" type="number" name="restock_limit" v-model="form.restock_limit" class="w-3/4 bg-white border border-slate-200 rounded uppercase placeholder:capitalize" autocomplete="off" placeholder="Restock Limit">
                </div>
                <div v-if="form.errors.restock_limit" class="text-right text-red-400 text-sm capitalize">{{ form.errors.restock_limit }}</div>
              </div>

            </div>
          </template>

          <template #footer>
            <div class="flex items-center justify-end space-x-2 p-2 text-white">
              <button class="flex items-center bg-green-600 rounded-md px-3 py-1 font-semibold">
                <i class="bx bx-save text-xl mr-1"></i>
                {{ form.id ? 'Perbarui' : 'Tambah' }}
              </button>
            </div>
          </template>
        </Card>
      </form>
    </div>
  </transition>

  <!-- Modal Edit Stock -->
  <transition name="slide-fade">
    <div v-if="stock" class="fixed top-0 left-0 w-full h-screen flex items-center justify-center">
      <form @submit.prevent="submitStock" class="relative w-full max-w-3xl bg-slate-100 rounded-md z-10">
        <Card>
          <template #header>
            <div class="flex items-center justify-between space-x-2 p-2">
              <h1 class="ml-2 text-black text-2xl font-semibold">Edit Stok Produk <span class="uppercase">"{{ formStock.name }}"</span></h1>
              <button @click.prevent="close" type="button" class="rounded-md bg-red-500 text-white px-1">
                <i class="bx bx-x text-xl"></i>
              </button>
            </div>
          </template>

          <template #body>
            <div class="flex flex-col space-y-2">

              <div class="flex flex-col space-y-2">
                <div class="flex items-center space-x-2">
                  <label for="group_id" class="capitalize w-1/3">tipe edit</label>
                  <Select
                    v-model="formStock.type"
                    :options="[
                      {
                        label: 'Penambahan',
                        value: 'buy',
                      },
                      {
                        label: 'Pengurangan',
                        value: 'sell',
                      },
                    ]"
                    :searchable="true" />
                </div>
                <div v-if="formStock.errors.type" class="text-right text-red-400 text-sm lowercase first-letter:capitalize">{{ formStock.errors.type }}</div>
              </div>

              <div class="flex flex-col space-y-2">
                <div class="flex items-center space-x-2">
                  <label for="qty" class="lowercase first-letter:capitalize w-1/4">qty</label>
                  <input ref="qty" type="number" name="qty" v-model="formStock.qty" class="w-3/4 bg-white border border-slate-200 rounded uppercase placeholder:capitalize" autocomplete="off" :placeholder="`${formStock.type ? 'Jumlah ' + (formStock.type === 'buy' ? 'Penambahan' : 'Pengurangan') : ''}`">
                </div>
                <div v-if="formStock.errors.qty" class="text-right text-red-400 text-sm lowercase first-letter:capitalize">{{ formStock.errors.qty }}</div>
              </div>

              <div class="border-y-2 border-gray-500">
                <div class="text-center font-semibold text-red-600 my-2">
                  Melakukan edit stok akan membuat transaksi baru tanpa ada pemasukan / pengeluaran uang.
                </div>
              </div>
              <div class="text-center font-semibold mt-2">
                Stok produk <span class="uppercase text-blue-600">{{ formStock.name }}</span> akan menjadi <span class="text-yellow-600 font-bold">{{ formStock.type && formStock.qty ? (formStock.type === 'buy' ? parseInt(formStock.exist) + parseInt(formStock.qty) : parseInt(formStock.exist) - parseInt(formStock.qty)) : formStock.exist }}</span> setelah pengeditan.
              </div>
            </div>
          </template>

          <template #footer>
            <div class="flex items-center justify-end space-x-2 p-2 text-white">
              <button class="flex items-center bg-green-600 rounded-md px-3 py-1 font-semibold">
                <i class="bx bx-save text-xl mr-1"></i>
                Simpan
              </button>
            </div>
          </template>
        </Card>
      </form>
    </div>
  </transition>

  <transition name="slide-fade">
    <Detail v-if="transaction" :product="transaction" :close="closeDetail" />
  </transition>
  <transition name="slide-fade">
    <Price v-if="price" :product="price" :close="close" />
  </transition>
</template>