<script setup>
import { getCurrentInstance, onMounted, onUnmounted, ref } from 'vue';
import Builder from '@/Components/DataTable/Builder.vue'
import Th from '@/Components/DataTable/Builder.vue'
import DataTable from './DataTableDetail.vue'

const { transaction, close } = defineProps({
  transaction: Object,
  close: Function,
})

const hide = e => {
  if (e.key === 'Escape') {
    close()
  
    window.addEventListener('keyup', hide)
  }
}

onMounted(() => window.addEventListener('keyup', hide))
onUnmounted(() => window.removeEventListener('keyup', hide))
</script>

<template>
  <div class="fixed top-0 left-0 w-full h-screen flex items-center justify-center">
    <div class="flex flex-col bg-slate-50 w-full max-w-6xl rounded-md">
      <div class="flex items-center justify-between space-x-2 bg-slate-100 rounded-t-md py-2 px-4">
        <div class="capitalize text-2xl font-semibold">detail transaksi</div>

        <button @click.prevent="close" type="button" class="rounded-md bg-red-500 text-white px-1">
          <i class="bx bx-x text-xl"></i>
        </button>
      </div>

      <div class="p-4 max-h-[28rem] overflow-auto">
        <div class="flex items-center justify-between py-2 mb-4 border-b-2 border-slate-300">
          <h2 class="text-xl">Tanggal Transaksi : {{ dateindo(transaction.created_at) }}</h2>
          <div v-if="transaction.details[0].type === 'return sell'" class="bg-red-500 rounded-md p-2 px-6 text-white font-bold">
            <p class="uppercase">RETUR</p>
          </div>
          <h2 class="text-xl">Kasir : {{ transaction.user.name }}</h2>
        </div>
        <DataTable :transaction="transaction" />
        <div class="flex items-center justify-end py-2 mt-4">
          <h2 class="text-3xl font-bold">Total : {{ rupiah(transaction.total_cost) }}</h2>
        </div>
      </div>
    </div>
  </div>
</template>