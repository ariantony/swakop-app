<script setup>
import Builder from '@/Components/DataTable/Builder.vue'
import Th from '@/Components/DataTable/Th.vue'

const { product } = defineProps({
  product: Object,
})
</script>

<template>
  <Builder :href="route('api.product.transactions.paginate', product.id)" :colspan="8">
    <template v-slot:thead="{table}">
      <tr>
        <Th class="px-1 py-2 uppercase border-b-2 border-r-2 border-slate-300" :sortable="false">no</Th>
        <Th class="px-3 py-2 uppercase border-b-2 border-x-2 border-slate-300" :table="table" name="user">user</Th>
        <Th class="px-3 py-2 uppercase border-b-2 border-x-2 border-slate-300" :table="table" name="qty_unit">qty unit</Th>
        <Th class="px-3 py-2 uppercase border-b-2 border-x-2 border-slate-300" :table="table" name="qty_box">qty box</Th>
        <Th class="px-3 py-2 uppercase border-b-2 border-x-2 border-slate-300" :table="table" name="qty_carton">qty carton</Th>
        <Th class="px-3 py-2 uppercase border-b-2 border-x-2 border-slate-300" :table="table" name="cost_unit">cost unit</Th>
        <Th class="px-3 py-2 uppercase border-b-2 border-x-2 border-slate-300" :table="table" name="cost_box">cost box</Th>
        <Th class="px-3 py-2 uppercase border-b-2 border-x-2 border-slate-300" :table="table" name="cost_carton">cost carton</Th>
      </tr>
    </template>
    <template #tfoot>
      <Th class="px-1 py-2 uppercase border-b-2 border-r-2 border-slate-300" :sortable="false">no</Th>
      <Th class="px-3 py-2 uppercase border-b-2 border-x-2 border-slate-300" :sortable="false">user</Th>
      <Th class="px-3 py-2 uppercase border-b-2 border-x-2 border-slate-300" :sortable="false">qty unit</Th>
      <Th class="px-3 py-2 uppercase border-b-2 border-x-2 border-slate-300" :sortable="false">qty box</Th>
      <Th class="px-3 py-2 uppercase border-b-2 border-x-2 border-slate-300" :sortable="false">qty carton</Th>
      <Th class="px-3 py-2 uppercase border-b-2 border-x-2 border-slate-300" :sortable="false">cost unit</Th>
      <Th class="px-3 py-2 uppercase border-b-2 border-x-2 border-slate-300" :sortable="false">cost box</Th>
      <Th class="px-3 py-2 uppercase border-b-2 border-x-2 border-slate-300" :sortable="false">cost carton</Th>
    </template>
    <template v-slot:tbody="{ index, item }">
      <tr>
        <td class="border p-2 border-x-2 border-slate-300 text-center">{{ index + 1 }}</td>
        <td class="border p-2 border-x-2 border-slate-300">{{ item.user.name }}</td>
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