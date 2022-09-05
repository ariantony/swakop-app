<script setup>
import { getCurrentInstance, ref } from 'vue'

const { table, name, sortable } = defineProps({
  table: {
    type: Object,
  },
  name: {
    type: String,
  },
  sortable: {
    type: Boolean,
    default: true,
  },
})

const isSelf = () => table?.form?.order?.key === name
const isAsc = () => table?.form?.order?.dir === 'asc'

const click = () => {
  if (sortable && table) {
    const form = table.form

    if (form.order.key === name) {
      form.order.dir = form.order.dir === 'asc' ? 'desc' : 'asc'
    } else {
      form.order.key = name
      form.order.dir = 'asc'
    }
    
    table.refresh()
  }
}
</script>

<template>
  <th :class="`${$props.class} ${sortable ? 'cursor-pointer' : 'cursor-default'}`" @click.prevent="click">
    <div class="flex items-center justify-center space-x-2">
      <div class="w-auto">
        <slot />
      </div>
      <div class="flex flex-col items-center justify-center h-full text-xs">
        <i v-if="sortable" class="bx bx-caret-up p-0 m-0" :class="isSelf() && (isAsc() ? 'text-slate-800' : 'text-slate-400') || 'text-slate-400'"></i>
        <i v-if="sortable" class="bx bx-caret-down p-0 m-0" :class="isSelf() && (isAsc() ? 'text-slate-400' : 'text-slate-800') || 'text-slate-400'"></i>
      </div>
    </div>
  </th>
</template>