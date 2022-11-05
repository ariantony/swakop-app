<script setup>

const { group, product, remove } = defineProps({
  group: Object,
  product: Object,
  remove: Function,
})

</script>

<template>
  <div class="flex items-center w-fit mb-1 bg-teal-400 rounded-md px-3 py-1 text-sm font-semibold uppercase text-white">
    <div>{{ product.name }}</div>
    <i @click.prevent="remove(group, product)" class="ml-2 bx bx-x text-lg leading-none bg-teal-600 hover:bg-teal-800 rounded-md"></i>
  </div>
</template>