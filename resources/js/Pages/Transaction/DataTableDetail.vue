<script setup>
import Builder from '@/Components/DataTable/Builder.vue'
import Th from '@/Components/DataTable/Th.vue'

const { transaction } = defineProps({
  transaction: Object,
})

const rupiah = value => {
  return new Intl.NumberFormat('id-ID', {
          style: 'currency',
          currency: 'IDR',
          minimumFractionDigits: 0,
        }).format(value)
}

const formatVariableCosts = transaction => {
  const product = transaction.product
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
  const product = transaction.product
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
        <td class="border p-2 border-x-2 border-slate-300 text-right" v-html="formatVariableCosts(item)"></td>
        <td class="border p-2 border-x-2 border-slate-300 text-right">{{ rupiah(getPriceFromVariables(item)) }}</td>
      </tr>
    </template>
  </Builder>
</template>