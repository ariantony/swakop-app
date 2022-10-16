<script setup>
const { detail } = defineProps({
  detail: Object,
  price: Function,
})
</script>

<template>
  <tr>
    <td class="px-1 text-right whitespace-nowrap" colspan="2">{{ detail.qty_unit }}</td>
    <td class="px-1 text-right">{{ price(detail.cost_unit) }}</td>
    <td class="px-1 text-right">{{ price(detail.variable_cost || detail.total_cost_all) }}</td>
  </tr>
</template>