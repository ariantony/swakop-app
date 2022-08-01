<script setup>
import Builder from '@/Components/DataTable/Builder.vue'
import Th from '@/Components/DataTable/Th.vue'

const { product } = defineProps({
  product: Object,
})

const redaction = (type) => {
  if (type === 'buy') return 'Beli'
  if (type === 'sell') return 'Jual'
  if (type === 'return buy') return 'Retur'
}
console.log(product)
</script>

<template>
  <Builder :href="route('api.product.detail.paginate', product.id)" :colspan="7">
    <template v-slot:thead="{table}">
      <tr>
        <Th class="px-1 py-2 uppercase border-2 border-slate-300" :sortable="false" rowspan="2">no</Th>
        <Th class="px-1 py-2 uppercase border-2 border-slate-300" :table="table" name="type" rowspan="2">tipe</Th>
        <Th class="px-1 py-2 uppercase border-2 border-slate-300" :sortable="false" colspan="1">qty</Th>
        <Th class="px-1 py-2 uppercase border-2 border-slate-300" :sortable="false" colspan="1">subtotal</Th>
        <Th class="px-3 py-2 uppercase border-2 border-slate-300" :sortable="false" rowspan="2">total</Th>
        <Th class="px-3 py-2 uppercase border-2 border-slate-300" :sortable="false" rowspan="2">tanggal transaksi</Th>
        <Th class="px-3 py-2 uppercase border-2 border-slate-300" :sortable="false" rowspan="2">kasir</Th>
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
      <Th class="px-1 py-2 uppercase border-2 border-slate-300" :sortable="false">tipe</Th>
      <Th class="px-3 py-2 uppercase border-2 border-slate-300" :sortable="false">satuan</Th>
      <!-- <Th class="px-3 py-2 uppercase border-2 border-slate-300" :sortable="false">box / renceng</Th>
      <Th class="px-3 py-2 uppercase border-2 border-slate-300" :sortable="false">karton</Th> -->
      <Th class="px-3 py-2 uppercase border-2 border-slate-300" :sortable="false">satuan</Th>
      <!-- <Th class="px-3 py-2 uppercase border-2 border-slate-300" :sortable="false">box / renceng</Th>
      <Th class="px-3 py-2 uppercase border-2 border-slate-300" :sortable="false">carton</Th> -->
      <Th class="px-3 py-2 uppercase border-2 border-slate-300" :sortable="false">total</Th>
      <Th class="px-3 py-2 uppercase border-2 border-slate-300" :sortable="false">tanggal transaksi</Th>
      <Th class="px-3 py-2 uppercase border-2 border-slate-300" :sortable="false">kasir</Th>
    </template>
    <template v-slot:tbody="{ index, item }">
      <tr>
        <td class="border p-2 border-x-2 border-slate-300 text-center">{{ index + 1 }}</td>
        <td class="border p-2 border-x-2 border-slate-300 text-center uppercase">{{ redaction(item.type) }}</td>
        <td class="border p-2 border-x-2 border-slate-300 text-center">{{ item.qty_unit }}</td>
        <!-- <td class="border p-2 border-x-2 border-slate-300 text-center">{{ item.qty_box }}</td>
        <td class="border p-2 border-x-2 border-slate-300 text-center">{{ item.qty_carton }}</td> -->
        <td class="border p-2 border-x-2 border-slate-300 text-right">{{ rupiah(item.total_cost_unit) }}</td>
        <!-- <td class="border p-2 border-x-2 border-slate-300 text-right">{{ rupiah(item.total_cost_box) }}</td>
        <td class="border p-2 border-x-2 border-slate-300 text-right">{{ rupiah(item.total_cost_carton) }}</td> -->
        <td class="border p-2 border-x-2 border-slate-300 text-right">{{ rupiah(item.total_cost_all) }}</td>
        <td class="border p-2 border-x-2 border-slate-300">{{ dateindo(item.transaction.created_at, true) }}</td>
        <td class="border p-2 border-x-2 border-slate-300 text-center">{{ item.transaction.user.name }}</td>
      </tr>
    </template>
  </Builder>
</template>