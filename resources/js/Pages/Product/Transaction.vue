<script setup>
import { getCurrentInstance, onMounted, onUnmounted, ref } from 'vue';
import Builder from '@/Components/DataTable/Builder.vue'
import Th from '@/Components/DataTable/Builder.vue'
import DataTable from './DataTableTransaction.vue'

const { product, close } = defineProps({
  product: Object,
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
  <transition name="slide-fade">
    <div v-if="product !== null" class="fixed top-0 left-0 w-full h-screen flex items-center justify-center">
      <div class="flex flex-col bg-slate-50 w-full max-w-5xl rounded-md">
        <div class="flex items-center justify-between space-x-2 bg-slate-100 rounded-t-md py-2 px-4">
          <div class="lowercase first-letter:capitalize text-2xl font-semibold">transaksi produk "{{ product.name }}"</div>

          <button @click.prevent="close" type="button" class="rounded-md bg-red-500 text-white px-1">
            <i class="bx bx-x text-xl"></i>
          </button>
        </div>

        <div class="p-4 max-h-96 overflow-auto">
          <DataTable :product="product" />
        </div>
      </div>
    </div>
  </transition>
</template>