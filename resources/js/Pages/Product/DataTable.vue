<script setup>
import Builder from '@/Components/DataTable/Builder.vue'
import Th from '@/Components/DataTable/Th.vue'
import { ref } from 'vue'

const { edit, destroy, detail, editStock } = defineProps({
  edit: Function,
  destroy: Function,
  detail: Function,
  price: Function,
  editStock: Function,
})

const table = ref(null)

defineExpose({
  refresh: () => table.value?.refresh(),
})
</script>

<template>
  <Builder ref="table" :href="route('api.product.paginate')" :colspan="8">
    <template v-slot:thead="{table}">
      <tr>
        <Th class="px-1 py-2 uppercase border-b-2 border-r-2 border-slate-300" :sortable="false">no</Th>
        <Th class="px-3 py-2 uppercase border-b-2 border-x-2 border-slate-300" :table="table" name="code">kode</Th>
        <Th class="px-3 py-2 uppercase border-b-2 border-x-2 border-slate-300" :table="table" name="name">nama</Th>
        <Th class="px-3 py-2 uppercase border-b-2 border-x-2 border-slate-300" :table="table" name="barcode">barcode</Th>
        <Th class="px-3 py-2 uppercase border-b-2 border-x-2 border-slate-300" :table="table" name="group_id">kelompok barang</Th>
        <Th class="px-3 py-2 uppercase border-b-2 border-x-2 border-slate-300" :sortable="false">harga</Th>
        <Th class="px-3 py-2 uppercase border-b-2 border-x-2 border-slate-300" :sortable="false">stok</Th>
        <Th v-if="isAdmin()" class="px-3 py-2 uppercase border-b-2 border-l-2 border-slate-300" :sortable="false">aksi</Th>
      </tr>
    </template>
    <template #tfoot>
      <Th class="p-2 uppercase border-t-2 border-x-2 border-slate-300" :sortable="false">no</Th>
      <Th class="p-2 uppercase border-t-2 border-x-2 border-slate-300" :sortable="false">kode</Th>
      <Th class="p-2 uppercase border-t-2 border-x-2 border-slate-300" :sortable="false">nama</Th>
      <Th class="p-2 uppercase border-t-2 border-x-2 border-slate-300" :sortable="false">barcode</Th>
      <Th class="p-2 uppercase border-t-2 border-x-2 border-slate-300" :sortable="false">kelompok barang</Th>
      <Th class="p-2 uppercase border-t-2 border-x-2 border-slate-300" :sortable="false">harga</Th>
      <Th class="p-2 uppercase border-t-2 border-x-2 border-slate-300" :sortable="false">stok</Th>
      <Th v-if="isAdmin()" class="p-2 uppercase border-t-2 border-x-2 border-slate-300" :sortable="false">aksi</Th>
    </template>
    <template v-slot:tbody="{ index, item }">
      <tr>
        <td class="border p-2 border-x-2 border-slate-300 text-center">{{ index + 1 }}</td>
        <td class="border p-2 border-x-2 border-slate-300 uppercase">{{ item.code }}</td>
        <td class="border p-2 border-x-2 border-slate-300 uppercase whitespace-nowrap">{{ item.name }}</td>
        <td class="border p-2 border-x-2 border-slate-300 uppercase">{{ item.barcode }}</td>
        <td class="border p-2 border-x-2 border-slate-300 uppercase text-center">{{ item.group?.code }}</td>
        <td class="border p-2 border-x-2 border-slate-300 text-right">{{ item.price?.price_per_unit ? rupiah(item.price?.price_per_unit) : '-' }}</td>
        <td class="border p-2 border-x-2 border-slate-300 text-center">{{ item.stock_unit }}</td>
        <td v-if="isAdmin()" class="border p-1">
          <div class="flex items-center justify-center space-x-1 text-white">
            <button @click.prevent="editStock(item)" class="bg-purple-400 rounded-md px-3 py-1 text-sm font-semibold whitespace-nowrap">
              <div class="flex items-center">
                <i class="bx bx-message-square-edit mr-1 text-white text-sm"></i> Edit Stok
              </div>
            </button>

            <button @click.prevent="detail(item)" class="bg-cyan-600 rounded-md px-3 py-1 text-sm font-semibold">
              <div class="flex items-center">
                <i class="bx bx-list-ul mr-1 text-white text-sm"></i> Transaksi
              </div>
            </button>

            <button @click.prevent="price(item)" class="bg-orange-600 rounded-md px-3 py-1 text-sm font-semibold">
              <div class="flex items-center">
                <i class="bx bx-dollar mr-1 text-white text-sm"></i> Harga
              </div>
            </button>

            <button @click.prevent="edit(item)" class="bg-blue-600 rounded-md px-3 py-1 text-sm font-semibold">
              <div class="flex items-center">
                <i class="bx bx-edit mr-1 text-white text-sm"></i> Edit
              </div>
            </button>

            <!-- <button @click.prevent="destroy(item)" class="bg-red-600 rounded-md px-3 py-1 text-sm font-semibold">
              <div class="flex items-center">
                <i class="bx bx-trash mr-1 text-white text-sm"></i> Hapus
              </div>
            </button> -->
          </div>
        </td>
      </tr>
    </template>
  </Builder>
</template>