<script setup>

const { detail } = defineProps({
  detail: Object,
})

const {name, qty_total, total, ...arr} = detail

</script>

<template>
  <table v-for="(item, i) of arr" :key="i" :item="item" class="w-full text-sm">
    <tr v-for="(list, j) of item" :key="j" :item="list">
      <td class="text-left">({{ list.perqty }})</td>
      <td class="text-right">{{ list.qty }}</td>
      <td class="text-right">{{ rupiah(list.perprice) }}</td>
      <td class="text-right">{{ rupiah(list.subtotal) }}</td>
    </tr>
  </table>
</template>