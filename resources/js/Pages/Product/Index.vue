<script setup>
import { getCurrentInstance, nextTick, onMounted, ref } from 'vue'
import { Link, useForm } from '@inertiajs/inertia-vue3'
import { Inertia } from '@inertiajs/inertia'
import AppLayout from '@/Layouts/AppLayout.vue'
import Card from '@/Components/Card.vue'
import DataTable from './DataTable.vue'
import Swal from 'sweetalert2'
import Transaction from './Transaction.vue'

const self = getCurrentInstance()
const render = ref(true)
const open = ref(false)
const form = useForm({
  id: null,
  code: null,
  name: '',
  barcode: '',
})
const users = ref([])
const transaction = ref(null)

const show = () => {
  open.value = true

  nextTick(() => {
    if(form.errors.code) self.refs.code.focus()
    else if(form.errors.name) self.refs.name.focus()
    else if(form.errors.barcode) self.refs.barcode.focus()
  })
}

const close = () => open.value = false

const reset = () => {
  render.value = false
  nextTick(() => render.value = true)
  form.reset()
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

  show()
}

const update = () => {
  return form.patch(route('product.update', form.id), {
    onSuccess: () => reset(),
    onError: () => nextTick(show),
  })
}

const destroy = product => {
  return Swal.fire({
    title: 'Konfirmasi!',
    html: 'Anda yakin akan menghapus produk ini? Semua pencatatan data yang berhubungan produk ini akan ikut terhapus.',
    icon: 'question',
    showCancelButton: true,
  }).then(response => {
    if (response.isConfirmed) {
      return Inertia.delete(route('product.destroy', product.id), {
        onSuccess: () => reset(),
      })
    }
  })
}

const submit = () => form.id ? update() : store()

onMounted(() => {
  window.addEventListener('keyup', e => {
    if (e.key === 'Escape' && open.value) close() || form.reset()
  })
})
</script>

<template>
  <AppLayout title="Produk">
    <Card>
      <template #header>
        <div class="flex items-center justify-between space-x-2 text-white p-2">
          <h1 class="ml-4 text-black text-2xl font-semibold">Produk</h1>
          <button @click.prevent="show" class="bg-green-600 rounded-md px-3 py-1 font-semibold">
            <div class="flex items-center">
              <i class="bx bx-plus mr-1 text-xl"></i> Tambah
            </div>
          </button>
        </div>
      </template>
      <template #body>
        <DataTable v-if="render" :edit="edit" :destroy="destroy" :transaction="(product) => transaction = product" />
      </template>
    </Card>
  </AppLayout>

  <transition name="fade">
    <div  v-if="open || transaction" class="fixed top-0 left-0 w-full h-screen bg-slate-600 bg-opacity-70"></div>
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
                  <input ref="code" type="text" name="code" v-model="form.code" class="w-3/4 bg-transparent border border-slate-200  rounded-md uppercase placeholder:capitalize" autocomplete="off" placeholder="kode">
                </div>
                <div v-if="form.errors.code" class="text-right text-red-400 text-sm lowercase first-letter:capitalize">{{ form.errors.code }}</div>
              </div>

              <div class="flex flex-col space-y-2">
                <div class="flex items-center space-x-2">
                  <label for="name" class="lowercase first-letter:capitalize w-1/4">nama</label>
                  <input ref="name" type="text" name="name" v-model="form.name" class="w-3/4 bg-transparent border border-slate-200 rounded-md placeholder:capitalize" autocomplete="off" placeholder="nama">
                </div>
                <div v-if="form.errors.name" class="text-right text-red-400 text-sm lowercase first-letter:capitalize">{{ form.errors.name }}</div>
              </div>

              <div class="flex flex-col space-y-2">
                <div class="flex items-center space-x-2">
                  <label for="barcode" class="lowercase first-letter:capitalize w-1/4">barcode</label>
                  <input ref="barcode" type="text" name="barcode" v-model="form.barcode" class="w-3/4 bg-transparent border border-slate-200 rounded-md uppercase placeholder:capitalize" autocomplete="off" placeholder="barcode">
                </div>
                <div v-if="form.errors.barcode" class="text-right text-red-400 text-sm lowercase first-letter:capitalize">{{ form.errors.barcode }}</div>
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

  <Transaction :product="transaction" :close="() => transaction = null" />
</template>