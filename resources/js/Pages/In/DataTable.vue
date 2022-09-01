<script setup>
import Builder from '@/Components/DataTable/Builder.vue'
import Th from '@/Components/DataTable/Th.vue'

const { detail, destroy } = defineProps({
  detail: Function,
  destroy: Function,
})

</script>

<template>
  <Builder :href="route('api.in.paginate')" :colspan="7">
    <template v-slot:thead="{table}">
      <tr>
        <Th class="px-1 py-2 uppercase border-b-2 border-r-2 border-slate-300" :sortable="false">no</Th>
        <Th class="px-3 py-2 uppercase border-b-2 border-x-2 border-slate-300" :table="table" :sortable="false">barcode</Th>
        <Th class="px-3 py-2 uppercase border-b-2 border-x-2 border-slate-300" :table="table" :sortable="false">produk</Th>
        <Th class="px-3 py-2 uppercase border-b-2 border-x-2 border-slate-300" :table="table" name="qty_unit">satuan</Th>
        <Th class="px-3 py-2 uppercase border-b-2 border-x-2 border-slate-300" :table="table" name="cost_unit">total belanja</Th>
        <Th class="px-3 py-2 uppercase border-b-2 border-x-2 border-slate-300" :table="table" name="created_at">tanggal transaksi</Th>
        <Th class="px-3 py-2 uppercase border-b-2 border-x-2 border-slate-300" :table="table" :sortable="false">aksi</Th>
      </tr>
    </template>
    <template #tfoot>
      <Th class="p-2 uppercase border-t-2 border-x-2 border-slate-300" :sortable="false">no</Th>
      <Th class="p-2 uppercase border-t-2 border-x-2 border-slate-300" :sortable="false">barcode</Th>
      <Th class="p-2 uppercase border-t-2 border-x-2 border-slate-300" :sortable="false">produk</Th>
      <Th class="p-2 uppercase border-t-2 border-x-2 border-slate-300" :sortable="false">satuan</Th>
      <Th class="p-2 uppercase border-t-2 border-x-2 border-slate-300" :sortable="false">total belanja</Th>
      <Th class="p-2 uppercase border-t-2 border-x-2 border-slate-300" :sortable="false">tanggal transaksi</Th>
      <Th class="p-2 uppercase border-t-2 border-x-2 border-slate-300" :sortable="false">aksi</Th>
    </template>
    <template v-slot:tbody="{ index, item }">
      <tr>
        <td class="border p-2 border-x-2 border-slate-300 text-center">{{ index + 1 }}</td>
        <td class="border p-2 border-x-2 border-slate-300 capitalize">{{ item.product.barcode }}</td>
        <td class="border p-2 border-x-2 border-slate-300 capitalize">{{ item.product.name }}</td>
        <td class="border p-2 border-x-2 border-slate-300 text-center capitalize">{{ item.qty_unit }}</td>
        <td class="border p-2 border-x-2 border-slate-300 text-right capitalize">{{ rupiah(item.total_cost_unit) }}</td>
        <td class="border p-2 border-x-2 border-slate-300 capitalize">{{ dateindo(item.created_at, true) }}</td>
        <td class="border p-2 border-x-2 border-slate-300">
          <button @click.prevent="destroy(item)" class="bg-red-600 rounded-md px-3 py-1 text-md font-semibold text-white">
              <div class="flex items-center">
                <i class="bx bx-trash mr-1 text-sm"></i> Hapus
              </div>
            </button>
        </td>
      </tr>
    </template>
  </Builder>
</template>