<script setup>
import { onMounted, ref } from "vue"

const { price } = defineProps({
  price: Object,
})

const arr = ref({})

onMounted(() => {
  if (price?.variable_costs?.length) {
    arr.value = price.variable_costs
  }

  arr.value.push({
    qty: 1,
    min_qty: 1,
    price: price?.price_per_unit,
  })
})

</script>

<template>
  <table class="w-full text-sm">
    <tr v-for="(item, i) of arr" :key="i" :item="item">
      <td class="text-left">({{ item.qty }})</td>
      <td class="text-right">{{ item.min_qty }}</td>
      <td class="text-right">{{ rupiah(item.price) }}</td>
    </tr>
  </table>
</template>