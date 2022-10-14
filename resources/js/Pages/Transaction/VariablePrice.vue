<script setup>
import { onMounted, ref } from "vue"

const { detail } = defineProps({
  detail: Object,
  price: Function,
})

const arr = ref({})

onMounted(() => {
  if (detail.price?.variable_costs?.length) {
    let qty = detail.qty_unit || 0, histories = []
    arr.value.qty = qty

    while (qty > 0) {
      let variable = detail.price?.variable_costs?.find(v => v.qty <= qty)
      let q = 1, p = 0

      if (variable) {
        q = Math.floor(qty / variable.qty)
        p = variable.price
        qty -= q * variable.qty
        histories.unshift({perqty: variable.qty, qty: q * variable.qty, price: p, subtotal: p * q * variable.qty})
      } else {
        q = qty
        p = detail.price?.price_per_unit
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
  <tr>
    <td colspan="4">
      <table class="w-full text-sm">
        <tr v-for="(item, i) of arr.histories" :key="i" :item="item">
          <td class="px-1 text-left">({{ item.perqty }})</td>
          <td class="px-1 text-right">{{ item.qty }}</td>
          <td class="px-1 text-right">{{ price(item.price) }}</td>
          <td class="px-1 text-right">{{ price(item.subtotal) }}</td>
        </tr>
        <tr v-if="arr.count > 1">
          <td class="px-1 text-right" colspan="2">{{ arr.qty }}</td>
          <td class="px-1 text-right" colspan="2">{{ price(arr.total) }}</td>
        </tr>
      </table>
    </td>
  </tr>
</template>