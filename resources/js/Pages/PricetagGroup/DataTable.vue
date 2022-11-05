<script setup>
import Builder from '@/Components/DataTable/Builder.vue'
import Th from '@/Components/DataTable/Th.vue'
import ProductBadge from './ProductBadge.vue';

const { edit, destroy, remove } = defineProps({
  edit: Function,
  destroy: Function,
  remove: Function,
})
</script>

<template>
  <Builder :href="route('api.pricetag-group.paginate')" :colspan="5">
    <template v-slot:thead="{table}">
      <tr>
        <Th class="px-1 py-2 uppercase border-b-2 border-r-2 border-slate-300" :sortable="false">no</Th>
        <Th class="px-3 py-2 uppercase border-b-2 border-x-2 border-slate-300" :table="table" name="name">nama grup</Th>
        <Th class="px-3 py-2 uppercase border-b-2 border-x-2 border-slate-300" :sortable="false">produk</Th>
        <Th class="px-3 py-2 uppercase border-b-2 border-l-2 border-slate-300" :sortable="false">aksi</Th>
      </tr>
    </template>
    <template #tfoot>
      <Th class="p-2 uppercase border-t-2 border-x-2 border-slate-300" :sortable="false">no</Th>
      <Th class="p-2 uppercase border-t-2 border-x-2 border-slate-300" :sortable="false">nama grup</Th>
      <Th class="p-2 uppercase border-t-2 border-x-2 border-slate-300" :sortable="false">produk</Th>
      <Th class="p-2 uppercase border-t-2 border-x-2 border-slate-300" :sortable="false">aksi</Th>
    </template>
    <template v-slot:tbody="{ index, item }">
      <tr>
        <td class="border p-2 border-x-2 border-slate-300 text-center">{{ index + 1 }}</td>
        <td class="border p-2 border-x-2 border-slate-300 uppercase whitespace-nowrap">{{ item.name }}</td>
        <td class="border p-2 border-x-2 border-slate-300 space-x-1 flex flex-wrap">
          <ProductBadge v-for="product in item.products" :key="product.id" :group="item" :product="product" :remove="remove" />
        </td>
        <td class="border p-1 ">
          <div class="flex items-center justify-center space-x-1 text-white">
            <button @click.prevent="edit(item)" class="bg-blue-600 rounded-md px-3 py-1 text-sm font-semibold">
              <div class="flex items-center">
                <i class="bx bx-edit mr-1 text-white text-sm"></i> Edit
              </div>
            </button>
            <button @click.prevent="destroy(item)" class="bg-red-600 rounded-md px-3 py-1 text-sm font-semibold">
              <div class="flex items-center">
                <i class="bx bx-trash mr-1 text-white text-sm"></i> Hapus
              </div>
            </button>
          </div>
        </td>
      </tr>
    </template>
  </Builder>
</template>