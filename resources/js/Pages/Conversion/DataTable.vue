<script setup>
import Builder from '@/Components/DataTable/Builder.vue'
import Th from '@/Components/DataTable/Th.vue'

const { destroy } = defineProps({
  destroy: Function,
})
</script>

<template>
  <Builder :href="route('api.conversion.paginate')" :colspan="6">
    <template v-slot:thead="{table}">
      <tr>
        <Th class="px-1 py-2 uppercase border-b-2 border-r-2 border-slate-300" :sortable="false">no</Th>
        <Th class="px-3 py-2 uppercase border-b-2 border-x-2 border-slate-300" :table="table" name="from_id">dari</Th>
        <Th class="px-3 py-2 uppercase border-b-2 border-x-2 border-slate-300" :table="table" name="to_id">ke</Th>
        <Th class="px-3 py-2 uppercase border-b-2 border-x-2 border-slate-300" :table="table" name="large">Qty besar yang di konversi</Th>
        <Th class="px-3 py-2 uppercase border-b-2 border-x-2 border-slate-300" :table="table" name="small_per_large">Jumlah qty kecil dalam satu qty besar</Th>
        <Th class="px-3 py-2 uppercase border-b-2 border-x-2 border-slate-300" :table="table" name="created_at">Waktu</Th>
        <Th class="px-3 py-2 uppercase border-b-2 border-x-2 border-slate-300" :table="table" :sortable="false">aksi</Th>
      </tr>
    </template>
    <template #tfoot>
      <Th class="p-2 uppercase border-t-2 border-x-2 border-slate-300" :sortable="false">no</Th>
      <Th class="p-2 uppercase border-t-2 border-x-2 border-slate-300" :sortable="false">dari</Th>
      <Th class="p-2 uppercase border-t-2 border-x-2 border-slate-300" :sortable="false">ke</Th>
      <Th class="p-2 uppercase border-t-2 border-x-2 border-slate-300" :sortable="false">Qty besar yang di konversi</Th>
      <Th class="p-2 uppercase border-t-2 border-x-2 border-slate-300" :sortable="false">Jumlah qty kecil dalam satu qty besar</Th>
      <Th class="p-2 uppercase border-t-2 border-x-2 border-slate-300" :sortable="false">Waktu</Th>
      <Th class="p-2 uppercase border-t-2 border-x-2 border-slate-300" :sortable="false">aksi</Th>
    </template>
    <template v-slot:tbody="{ index, item }">
      <tr>
        <td class="border p-2 border-x-2 border-slate-300 text-center">{{ index + 1 }}</td>
        <td class="border p-2 border-x-2 border-slate-300 uppercase">{{ item.from?.name }}</td>
        <td class="border p-2 border-x-2 border-slate-300 uppercase">{{ item.to?.name }}</td>
        <td class="border p-2 border-x-2 border-slate-300 text-center">{{ item.large }}</td>
        <td class="border p-2 border-x-2 border-slate-300 text-center">{{ item.small_per_large }}</td>
        <td class="border p-2 border-x-2 border-slate-300 text-center whitespace-nowrap">{{ dateindo(item.created_at, true) }}</td>
        <td class="border p-2 border-x-2 border-slate-300">
          <button @click.prevent="destroy(item)" class="bg-red-600 rounded-md px-3 py-1 text-sm font-semibold text-white">
            <div class="flex items-center">
              <i class="bx bx-trash mr-1 text-sm"></i> Hapus
            </div>
          </button>
        </td>
      </tr>
    </template>
  </Builder>
</template>