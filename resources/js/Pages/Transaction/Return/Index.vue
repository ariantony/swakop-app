<script setup>
import { getCurrentInstance, nextTick, onMounted, ref } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import Card from '@/Components/Card.vue';
import Detail from '../DataTableDetail.vue'
import { useForm } from '@inertiajs/inertia-vue3';
import Swal from 'sweetalert2';
import axios from 'axios';

const form = useForm({
  id: null,
})

const a = ref(true)
const help = ref(false)
const transaction = ref(null)

const reset = () => {
  form.reset()
  transaction.value = null
}

const find = async () => {
  if (!form.id) {
    return reset()
  }

  try {
    const response = await axios.get(route('api.transaction.find', Number(form.id)))
    transaction.value = response.data
    a.value = false
    nextTick(() => a.value = true)
  } catch (e) {
    const response = await Swal.fire({
      title: 'Tidak dapat mendapatkan transaksi',
      text: 'Apakah anda ingin mencoba lagi?',
      icon: 'question',
      showCloseButton: true,
      showCancelButton: true,
    })

    response.isConfirmed && find()
  }
}

const retur = async () => {
  let response = await Swal.fire({
    title: 'Keterangan',
    icon: 'question',
    input: 'text',
    inputValidator: value => {
      if (!value) {
        return 'Keterangan wajib diisi'
      }
    },
    showCancelButton: true,
    showCloseButton: true.valueOf,
  })
  
  if (response.isConfirmed) {
    const note = response.value

    response = await Swal.fire({
      title: 'Masukan kode',
      icon: 'question',
      input: 'password',
      inputValidator: async code => {
        try {
          const response = await axios.post(route('api.compare'), { code })
        } catch (e) {
          return 'Kode tidak sesuai'
        }
      }
    })

    response.isConfirmed && (
      useForm({ note }).patch(route('transaction.return', transaction.value.id), {
        onSuccess: () => {
          reset()
        }
      })
    )
  }
}

onMounted(() => {
  window.addEventListener('keyup', e => {
    if (e.key === 'Escape' && help) help.value = false
  })
})
</script>

<template>
  <AppLayout title="Void Penjualan">
    <Card>
      <template #header>
        <div class="flex items-center justify-between space-x-2 text-white p-2">
          <h1 class="ml-4 text-black text-2xl font-semibold">Void Penjualan</h1>
          <button @click.prevent="help = true" class="bg-slate-600 rounded-md px-3 py-1 font-semibold">
            <div class="flex items-center">
              <i class="bx bx-info-square mr-1 text-xl"></i> Bantuan
            </div>
          </button>
        </div>
      </template>
      <template #body>
        <div class="flex flex-col">
          <div class="flex items-center space-x-2">
            <label for="id" class="flex-none w-1/4">Invoice</label>
            <input v-model="form.id" @change.prevent="find" type="text" name="id" class="w-full rounded-md px-3 py-1" placeholder="Invoice">
          </div>
        </div>
      </template>
    </Card>

    <Card v-if="transaction && a">
      <template #header>
        <div class="flex items-center justify-start space-x-2 text-white p-2">
          <h1 class="ml-4 text-black text-2xl font-semibold">Detail transaksi</h1>
        </div>
      </template>
      <template #body>
        <Detail v-if="transaction" :transaction="transaction" />

        <div class="flex items-center space-x-2 justify-end mt-4">
          <button @click.prevent="retur" class="bg-blue-600 hover:bg-blue-700 rounded-md px-3 py-1 text-sm text-white transition-all">
            <div class="flex items-center space-x-1">
              <i class="bx bx-check"></i>
              <p class="uppercase font-semibold">retur</p>
            </div>
          </button>
        </div>
      </template>
    </Card>

    <transition name="fade">
      <div  v-if="help" class="fixed top-0 left-0 w-full h-screen bg-slate-600 bg-opacity-70"></div>
    </transition>

    <transition name="slide-fade">
      <div v-if="help" class="fixed top-0 left-0 w-full h-screen flex items-center justify-center">
        <Card>
          <template #header>
            <div class="flex items-center justify-between space-x-2 p-2">
              <h3 class="ml-2 font-semibold text-2xl">Alur Void Penjualan</h3>
              <button @click.prevent="help = false" type="button" class="rounded-md bg-red-500 text-white px-1">
                <i class="bx bx-x text-xl"></i>
              </button>
            </div>
          </template>
          <template #body>
            <ol type="number" class="rounded">
              <li>1. Masukan Nomor Inovice.</li>
              <li>2. Produk yang dibeli sebelumnya akan tampil.</li>
              <li>3. Klik tombol retur.</li>
              <li>4. Isi pop-up keterangan barang di retur, kemudian masukan PIN.</li>
              <li>5. Jika proses retur berhasil, halaman akan pindah ke halaman transaksi dengan data barang sebelumnya untuk di checkout kembali.</li>
            </ol>
          </template>
        </Card>
      </div>
    </transition>
  </AppLayout>
</template>