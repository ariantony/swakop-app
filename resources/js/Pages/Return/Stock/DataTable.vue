<script setup>
import { getCurrentInstance, ref } from 'vue';
import Builder from '@/Components/DataTable/Builder.vue';

const { detail } = defineProps({
  detail: Function,
})
</script>

<template>
  <Builder :href="route('api.return-stock.history')">
    <template v-slot:thead="{table}">
      <tr>
        <Th class="px-1 py-2 uppercase border-b-2 border-r-2 border-slate-300" :sortable="false">no</Th>
        <Th class="px-3 py-2 uppercase border-b-2 border-x-2 border-slate-300" :table="table" name="user_id">kasir</Th>
        <Th class="px-3 py-2 uppercase border-b-2 border-x-2 border-slate-300" :table="table" name="total_cost">total belanja</Th>
        <Th class="px-3 py-2 uppercase border-b-2 border-x-2 border-slate-300" :table="table" name="created_at">tanggal transaksi</Th>
        <Th class="px-3 py-2 uppercase border-b-2 border-l-2 border-slate-300" :sortable="false">aksi</Th>
      </tr>
    </template>
    <template #tfoot>
      <Th class="p-2 uppercase border-t-2 border-x-2 border-slate-300" :sortable="false">no</Th>
      <Th class="p-2 uppercase border-t-2 border-x-2 border-slate-300" :sortable="false">kasir</Th>
      <Th class="p-2 uppercase border-t-2 border-x-2 border-slate-300" :sortable="false">total belanja</Th>
      <Th class="p-2 uppercase border-t-2 border-x-2 border-slate-300" :sortable="false">tanggal transaksi</Th>
      <Th class="p-2 uppercase border-t-2 border-x-2 border-slate-300" :sortable="false">aksi</Th>
    </template>
    <template v-slot:tbody="{ index, item }">
      <tr>
        <td class="border p-2 border-x-2 border-slate-300 text-center">{{ index + 1 }}</td>
        <td class="border p-2 border-x-2 border-slate-300 capitalize">{{ item.user.name }}</td>
        <td class="border p-2 border-x-2 border-slate-300 text-right">{{ rupiah(item.total_cost) }}</td>
        <td class="border p-2 border-x-2 border-slate-300 text-right">{{ new Date(item.created_at).toLocaleDateString('id') }}</td>
        <td class="border p-1 ">
          <div class="flex items-center justify-center space-x-1 text-white">
            <button @click.prevent="detail(item)" class="bg-blue-600 rounded-md px-3 py-1 text-sm font-semibold">
              <div class="flex items-center">
                <i class="bx bx-list-ul mr-1 text-white text-sm"></i> Detail Transaksi
              </div>
            </button>
          </div>
        </td>
      </tr>
    </template>
  </Builder>
</template>