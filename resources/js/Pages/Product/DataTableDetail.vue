<script setup>
import Builder from '@/Components/DataTable/Builder.vue'
import Th from '@/Components/DataTable/Th.vue'
import FormatVariableCosts from '../../Components/FormatVariableCosts.vue';

const { product } = defineProps({
  product: Object,
})

const redaction = (type) => {
  if (type === 'buy') return 'Beli'
  if (type === 'sell') return 'Jual'
  if (type === 'return buy') return 'Retur Stock'
  if (type === 'return sell') return 'Retur Penjualan'
}

</script>

<template>
  <Builder :href="route('api.product.detail.paginate', product.id)" :colspan="7">
    <template #thead>
      <tr>
        <Th class="px-1 py-2 uppercase border-2 border-slate-300" :sortable="false">no</Th>
        <Th class="px-1 py-2 uppercase border-2 border-slate-300" :table="table" name="type">tipe</Th>
        <Th class="px-1 py-2 uppercase border-2 border-slate-300" :sortable="false">qty</Th>
        <Th class="px-1 py-2 uppercase border-2 border-slate-300" :sortable="false">harga</Th>
        <Th class="px-3 py-2 uppercase border-2 border-slate-300" :sortable="false">total</Th>
        <Th class="px-3 py-2 uppercase border-2 border-slate-300" :sortable="false">tanggal transaksi</Th>
        <Th class="px-3 py-2 uppercase border-2 border-slate-300" :sortable="false">kasir</Th>
      </tr>
    </template>
    <template #tfoot>
      <Th class="px-1 py-2 uppercase border-2 border-slate-300" :sortable="false">no</Th>
      <Th class="px-1 py-2 uppercase border-2 border-slate-300" :sortable="false">tipe</Th>
      <Th class="px-3 py-2 uppercase border-2 border-slate-300" :sortable="false">qty</Th>
      <Th class="px-3 py-2 uppercase border-2 border-slate-300" :sortable="false">harga</Th>
      <Th class="px-3 py-2 uppercase border-2 border-slate-300" :sortable="false">total</Th>
      <Th class="px-3 py-2 uppercase border-2 border-slate-300" :sortable="false">tanggal transaksi</Th>
      <Th class="px-3 py-2 uppercase border-2 border-slate-300" :sortable="false">kasir</Th>
    </template>
    <template v-slot:tbody="{ index, item }">
      <tr :class="item.transaction.note ? (item.transaction.note === 'EDIT BUY' ? 'bg-green-300' : 'bg-red-300') : ''">
        <td class="border p-2 border-x-2 border-slate-300 text-center">{{ index + 1 }}</td>
        <td class="border p-2 border-x-2 border-slate-300 text-center uppercase">{{ redaction(item.type) }}</td>
        <td class="border p-2 border-x-2 border-slate-300 text-center">{{ item.qty_unit }}</td>
        <td class="border p-2 border-x-2 border-slate-300 text-right">
          <FormatVariableCosts v-if="['sell', 'return sell'].includes(item.type)" :detail="item" />
          <span v-else>{{ rupiah(item.total_cost_all) }}</span>
        </td>
        <td class="border p-2 border-x-2 border-slate-300 text-right">{{ ['sell', 'return sell'].includes(item.type) ? rupiah(item.variable_cost || item.total_cost_all) : rupiah(item.total_cost_all) }}</td>
        <td class="border p-2 border-x-2 border-slate-300">{{ dateindo(item.transaction.created_at, true) }}</td>
        <td class="border p-2 border-x-2 border-slate-300 text-center">{{ item.transaction.user.name }}</td>
      </tr>
    </template>
  </Builder>
</template>