<script setup>
import { onMounted, ref } from "vue"

const { detail } = defineProps({
  detail: Object,
})

const min_qty = ref(1)
const varcost = ref(null)

onMounted(() => {
  const price = detail.price
  let qty = detail.qty_unit || 0

  if (price?.variable_costs?.length) {
    let variable = price?.variable_costs?.find(v => v.min_qty <= qty)
    varcost.value = variable ? variable.price : price?.price_per_unit || detail.cost_unit
    min_qty.value = variable ? variable.qty : 1
  } else {
    varcost.value = price?.price_per_unit || detail.cost_unit
  }
})

</script>

<template>
  <table class="w-full text-sm">
    <tr>
      <td class="text-left">({{ min_qty }})</td>
      <td class="text-right">{{ rupiah(varcost) }}</td>
    </tr>
  </table>
</template>