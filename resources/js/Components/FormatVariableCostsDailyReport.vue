<script setup>

const { detail } = defineProps({
  detail: Object,
})

const { name, qty_total, total, ...arr } = detail

</script>

<template>
  <div v-for="(item, i) of arr" :key="i" :item="item" class="w-full text-sm print:text-xs py-1 border-b border-slate-400 last:border-b-0">
    <table class="w-full">
      <tr v-for="(list, j) of item" :key="j" :item="list" class="border-y first:border-t-0 last:border-b-0">
        <td class="p-1 border-slate-200 border-r text-left w-[10%]">({{ list.perqty }})</td>
        <td class="p-1 border-slate-200 border-x text-right">{{ list.qty }}</td>
        <td class="p-1 border-slate-200 border-x text-right">{{ rupiah(list.perprice) }}</td>
        <td class="p-1 border-slate-200 border-l text-right">{{ rupiah(list.subtotal) }}</td>
      </tr>
    </table>
  </div>
</template>