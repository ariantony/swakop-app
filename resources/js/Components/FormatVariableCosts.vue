<script setup>
import { onMounted, ref } from "vue"

const { detail } = defineProps({
  detail: Object,
})

const arr = ref({})

onMounted(() => {
  const price = detail.price
  let qty = detail.qty_unit || 0, histories = []

  if (price?.variable_costs?.length) {
    arr.value.qty = qty

    while (qty > 0) {
      let variable = price?.variable_costs?.find(v => v.qty <= qty)
      let q = 1, p = 0

      if (variable) {
        q = Math.floor(qty / variable.qty)
        p = variable.price
        qty -= q * variable.qty
        histories.unshift({perqty: variable.qty, qty: q * variable.qty, price: p, subtotal: p * q * variable.qty})
      } else {
        q = qty
        p = price?.price_per_unit
        qty -= q
        histories.unshift({perqty: 1, qty: q, price: p, subtotal: p * q})
      }
    }
    arr.value.histories = histories
    arr.value.total = histories.reduce((a, b) => a + b.subtotal, 0)
    arr.value.count = histories.length
  }
})

</script>

<template>
  <table class="w-full text-sm">
    <tr v-for="(item, i) of arr.histories" :key="i" :item="item">
      <td class="text-left">({{ item.perqty }})</td>
      <td class="text-right">{{ item.qty }}</td>
      <td class="text-right">{{ rupiah(item.price) }}</td>
      <td class="text-right">{{ rupiah(item.subtotal) }}</td>
    </tr>
  </table>
</template>