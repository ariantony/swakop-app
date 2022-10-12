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

const rupiah = value => {
  return new Intl.NumberFormat('id-ID', {
          style: 'currency',
          currency: 'IDR',
          minimumFractionDigits: 0,
        }).format(value)
}

const formatVariableCosts = transaction => {
  let qty = transaction.qty_unit || 0
  
  if (product.price?.variable_costs?.length) {
    let s = '<table class="w-full text-sm">'
    while (qty > 0) {
      let variable = product.price?.variable_costs?.find(v => v.qty <= qty)
      
      let q = 1, p = 0
      if (variable) {
        q = Math.floor(qty / variable.qty)
        p = variable.price
        s += `<tr><td class="text-left">(${variable.qty})</td><td class="text-right">${q} x </td><td class="text-right">${rupiah(p)}</td><td class="text-right">${rupiah(p * q * variable.qty)}</td></tr>`
        qty -= q * variable.qty
      } else {
        q = qty
        p = product.price?.price_per_unit
        s += `<tr><td class="text-left">(1)</td><td class="text-right">${qty} x </td><td class="text-right">${rupiah(p)}</td><td class="text-right">${rupiah(p * qty)}</td></tr>`
        qty -= q
      }
    }
    s += '</table>'
    return s
  }
  return product.price?.price_per_unit
}

const getPriceFromVariables = transaction => {
  let qty = transaction.qty_unit || 0

  if (product.price?.variable_costs?.length) {
    let total = 0
    
    while (qty > 0) {
      let variable = product.price?.variable_costs?.find(v => v.qty <= qty)
      
      if (variable) {
        total += variable.price * variable.qty
        qty -= variable.qty
      } else {
        total += product.price?.price_per_unit
        qty -= 1
      }
    }
    return total
  }
  return product.price?.price_per_unit * qty
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
      <tr>
        <td class="border p-2 border-x-2 border-slate-300 text-center">{{ index + 1 }}</td>
        <td class="border p-2 border-x-2 border-slate-300 text-center uppercase">{{ redaction(item.type) }}</td>
        <td class="border p-2 border-x-2 border-slate-300 text-center">{{ item.qty_unit }}</td>
        <td class="border p-2 border-x-2 border-slate-300 text-right" v-html="['sell', 'return sell'].includes(item.type) ? formatVariableCosts(item) : rupiah(item.total_cost_all)"></td>
        <td class="border p-2 border-x-2 border-slate-300 text-right">{{ ['sell', 'return sell'].includes(item.type) ? rupiah(getPriceFromVariables(item)) : rupiah(item.total_cost_all) }}</td>
        <td class="border p-2 border-x-2 border-slate-300">{{ dateindo(item.transaction.created_at, true) }}</td>
        <td class="border p-2 border-x-2 border-slate-300 text-center">{{ item.transaction.user.name }}</td>
      </tr>
    </template>
  </Builder>
</template>