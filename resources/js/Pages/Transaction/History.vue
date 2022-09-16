<script setup>
import { getCurrentInstance, nextTick, onMounted, ref } from 'vue'
import AppLayout from '@/Layouts/AppLayout.vue'
import Card from '@/Components/Card.vue'
import DataTable from './DataTable.vue'
import Detail from './Detail.vue';
import Swal from 'sweetalert2';

const self = getCurrentInstance()
const render = ref(true)
const open = ref(false)
const detail = ref(null)

const show = () => {
  open.value = true
}

const close = () => open.value = false

const print = async (item) => {
  try {
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
    iframe.src = route('api.transaction.print', item.id)
    iframe.style.width = '0'
    iframe.style.height = '0'

    document.body.appendChild(iframe)

    iframe.onload = () => {
      Swal.close()
    }
  
  } catch (e) {
    const response = await Swal.fire({
      title: 'Tidak dapat mencetak',
      text: 'Apakah anda ingin mencoba kembali?',
      icon: 'question',
      showCancelButton: true,
      showCloseButton: true,
    })
    response.isConfirmed && print(item)
  }
}

onMounted(() => {
  window.addEventListener('keyup', e => {
    if (e.key === 'Escape' && open.value) close()
  })
})
</script>

<template>
  <AppLayout title="Riwayat Transaksi Penjualan">
    <Card>
      <template #header>
        <div class="flex items-center justify-start space-x-2 text-white p-2">
          <h1 class="ml-4 text-black text-2xl font-semibold">Riwayat Transaksi Penjualan</h1>
        </div>
      </template>
      <template #body>
        <DataTable
          v-if="render"
          :detail="(transaction) => detail = transaction"
          :print="print" />
      </template>
    </Card>
  </AppLayout>

  <transition name="fade">
    <div  v-if="detail" class="fixed top-0 left-0 w-full h-screen bg-slate-600 bg-opacity-70"></div>
  </transition>

  <transition name="slide-fade">
    <Detail v-if="detail" :transaction="detail" :close="() => detail = null" />
  </transition>
</template>