<script setup>
import Builder from '@/Components/DataTable/Builder.vue'
import Th from '@/Components/DataTable/Th.vue'
import FormatVariableCosts from '../../Components/FormatVariableCosts.vue';

const { transaction } = defineProps({
  transaction: Object,
})

</script>

<template>
  <Builder :href="route('api.transaction.detail.paginate', transaction.id)" :colspan="9">
    <template #thead>
      <tr>
        <Th class="px-1 py-2 uppercase border-2 border-slate-300" :sortable="false">no</Th>
        <Th class="px-1 py-2 uppercase border-2 border-slate-300" :sortable="false">produk</Th>
        <Th class="px-1 py-2 uppercase border-2 border-slate-300" :sortable="false">qty </Th>
        <Th class="px-1 py-2 uppercase border-2 border-slate-300" :sortable="false">harga </Th>
        <Th class="px-1 py-2 uppercase border-2 border-slate-300" :sortable="false">subtotal</Th>
      </tr>
    </template>
    <template #tfoot>
      <Th class="px-1 py-2 uppercase border-2 border-slate-300" :sortable="false">no</Th>
      <Th class="px-1 py-2 uppercase border-2 border-slate-300" :sortable="false">produk</Th>
      <Th class="px-1 py-2 uppercase border-2 border-slate-300" :sortable="false">qty</Th>
      <Th class="px-1 py-2 uppercase border-2 border-slate-300" :sortable="false">harga </Th>
      <Th class="px-3 py-2 uppercase border-2 border-slate-300" :sortable="false">subtotal</Th>
    </template>
    <template v-slot:tbody="{ index, item }">
      <tr>
        <td class="border p-2 border-x-2 border-slate-300 text-center">{{ index + 1 }}</td>
        <td class="border p-2 border-x-2 border-slate-300">{{ item.product?.name }}</td>
        <td class="border p-2 border-x-2 border-slate-300 text-center">{{ item.qty_unit }}</td>
        <td class="border p-2 border-x-2 border-slate-300 text-right"><FormatVariableCosts :detail="item"/></td>
        <td class="border p-2 border-x-2 border-slate-300 text-right">{{ rupiah(item.variable_cost || item.total_cost_all) }}</td>
      </tr>
    </template>
  </Builder>
</template>