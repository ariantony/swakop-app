<script setup>
import Builder from '@/Components/DataTable/Builder.vue'
import Th from '@/Components/DataTable/Th.vue'

const { product } = defineProps({
  product: Object,
})
</script>

<template>
  <Builder :href="route('api.product.detail.paginate', product.id)" :colspan="7">
    <template v-slot:thead="{table}">
      <tr>
        <Th class="px-1 py-2 uppercase border-2 border-slate-300" :sortable="false">no</Th>
        <Th class="px-3 py-2 uppercase border-2 border-slate-300" :table="table" name="qty_unit">qty per unit</Th>
        <Th class="px-3 py-2 uppercase border-2 border-slate-300" :table="table" name="qty_box">qty per box / renceng</Th>
        <Th class="px-3 py-2 uppercase border-2 border-slate-300" :table="table" name="qty_carton">qty per karton</Th>
        <Th class="px-3 py-2 uppercase border-2 border-slate-300" :table="table" name="cost_unit">subtotal per unit</Th>
        <Th class="px-3 py-2 uppercase border-2 border-slate-300" :table="table" name="cost_box">subtotal per box / renceng</Th>
        <Th class="px-3 py-2 uppercase border-2 border-slate-300" :table="table" name="cost_carton">subtotal per carton</Th>
      </tr>
    </template>
    <template #tfoot>
      <Th class="px-1 py-2 uppercase border-2 border-slate-300" :sortable="false">no</Th>
      <Th class="px-3 py-2 uppercase border-2 border-slate-300" :sortable="false">qty per unit</Th>
      <Th class="px-3 py-2 uppercase border-2 border-slate-300" :sortable="false">qty per box / renceng</Th>
      <Th class="px-3 py-2 uppercase border-2 border-slate-300" :sortable="false">qty per karton</Th>
      <Th class="px-3 py-2 uppercase border-2 border-slate-300" :sortable="false">subtotal per unit</Th>
      <Th class="px-3 py-2 uppercase border-2 border-slate-300" :sortable="false">subtotal per box / renceng</Th>
      <Th class="px-3 py-2 uppercase border-2 border-slate-300" :sortable="false">subtotal per carton</Th>
    </template>
    <template v-slot:tbody="{ index, item }">
      <tr>
        <td class="border p-2 border-x-2 border-slate-300 text-center">{{ index + 1 }}</td>
        <td class="border p-2 border-x-2 border-slate-300">{{ item.qty_unit }}</td>
        <td class="border p-2 border-x-2 border-slate-300">{{ item.qty_box }}</td>
        <td class="border p-2 border-x-2 border-slate-300">{{ item.qty_carton }}</td>
        <td class="border p-2 border-x-2 border-slate-300">{{ item.cost_unit }}</td>
        <td class="border p-2 border-x-2 border-slate-300">{{ item.cost_box }}</td>
        <td class="border p-2 border-x-2 border-slate-300">{{ item.cost_carton }}</td>
      </tr>
    </template>
  </Builder>
</template>