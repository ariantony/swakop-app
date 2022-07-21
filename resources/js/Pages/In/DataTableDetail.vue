<script setup>
import Builder from '@/Components/DataTable/Builder.vue'
import Th from '@/Components/DataTable/Th.vue'

const { transaction } = defineProps({
  transaction: Object,
})
</script>

<template>
  <Builder :href="route('api.in.detail.paginate', transaction.id)" :colspan="9">
    <template v-slot:thead="{table}">
      <tr>
        <Th class="px-1 py-2 uppercase border-2 border-slate-300" :sortable="false" rowspan="2">no</Th>
        <Th class="px-1 py-2 uppercase border-2 border-slate-300" :sortable="false" rowspan="2">produk</Th>
        <Th class="px-1 py-2 uppercase border-2 border-slate-300" :sortable="false" colspan="1">qty per</Th>
        <Th class="px-1 py-2 uppercase border-2 border-slate-300" :sortable="false" colspan="1">subtotal per</Th>
        <Th class="px-3 py-2 uppercase border-2 border-slate-300" :sortable="false" rowspan="2">subtotal</Th>
      </tr>
      
      <tr>
        <Th class="px-3 py-2 uppercase border-2 border-slate-300" :table="table" name="qty_unit">satuan</Th>
        <!-- <Th class="px-3 py-2 uppercase border-2 border-slate-300" :table="table" name="qty_box">box / renceng</Th>
        <Th class="px-3 py-2 uppercase border-2 border-slate-300" :table="table" name="qty_carton">karton</Th> -->
        <Th class="px-3 py-2 uppercase border-2 border-slate-300" :table="table" name="cost_unit">satuan</Th>
        <!-- <Th class="px-3 py-2 uppercase border-2 border-slate-300" :table="table" name="cost_box">box / renceng</Th>
        <Th class="px-3 py-2 uppercase border-2 border-slate-300" :table="table" name="cost_carton">carton</Th> -->
      </tr>
    </template>
    <template #tfoot>
      <Th class="px-1 py-2 uppercase border-2 border-slate-300" :sortable="false">no</Th>
      <Th class="px-1 py-2 uppercase border-2 border-slate-300" :sortable="false">produk</Th>
      <Th class="px-1 py-2 uppercase border-2 border-slate-300" :sortable="false" colspan="1">qty per</Th>
      <Th class="px-1 py-2 uppercase border-2 border-slate-300" :sortable="false" colspan="1">subtotal per</Th>
      <!-- <Th class="px-3 py-2 uppercase border-2 border-slate-300" :sortable="false">unit</Th>
      <Th class="px-3 py-2 uppercase border-2 border-slate-300" :sortable="false">box / renceng</Th>
      <Th class="px-3 py-2 uppercase border-2 border-slate-300" :sortable="false">karton</Th>
      <Th class="px-3 py-2 uppercase border-2 border-slate-300" :sortable="false">unit</Th>
      <Th class="px-3 py-2 uppercase border-2 border-slate-300" :sortable="false">box / renceng</Th>
      <Th class="px-3 py-2 uppercase border-2 border-slate-300" :sortable="false">carton</Th> -->
      <Th class="px-3 py-2 uppercase border-2 border-slate-300" :sortable="false">subtotal</Th>
    </template>
    <template v-slot:tbody="{ index, item }">
      <tr>
        <td class="border p-2 border-x-2 border-slate-300 text-center">{{ index + 1 }}</td>
        <td class="border p-2 border-x-2 border-slate-300">{{ item.product.name }}</td>
        <td class="border p-2 border-x-2 border-slate-300 text-right">{{ item.qty_unit }}</td>
        <!-- <td class="border p-2 border-x-2 border-slate-300 text-right">{{ item.qty_box }}</td>
        <td class="border p-2 border-x-2 border-slate-300 text-right">{{ item.qty_carton }}</td> -->
        <td class="border p-2 border-x-2 border-slate-300 text-right">{{ rupiah(item.total_cost_unit) }}</td>
        <!-- <td class="border p-2 border-x-2 border-slate-300 text-right">{{ rupiah(item.total_cost_box) }}</td>
        <td class="border p-2 border-x-2 border-slate-300 text-right">{{ rupiah(item.total_cost_carton) }}</td> -->
        <td class="border p-2 border-x-2 border-slate-300 text-right">{{ rupiah(item.total_cost_all) }}</td>
      </tr>
    </template>
  </Builder>
</template>