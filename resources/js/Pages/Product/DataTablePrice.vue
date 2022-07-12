<script setup>
import Builder from '@/Components/DataTable/Builder.vue'
import Th from '@/Components/DataTable/Th.vue'

const { product } = defineProps({
  product: Object,
  edit: Function,
})
</script>

<template>
  <Builder :href="route('api.product.price.paginate', product.id)" :colspan="8">
    <template v-slot:thead="{table}">
      <tr>
        <Th class="px-1 py-2 uppercase border-b-2 border-r-2 border-slate-300" :sortable="false">no</Th>
        <Th class="px-3 py-2 uppercase border-b-2 border-x-2 border-slate-300" :table="table" name="cost_selling_per_unit">cost selling per unit</Th>
        <Th class="px-3 py-2 uppercase border-b-2 border-x-2 border-slate-300" :table="table" name="cost_selling_per_box">cost selling per box</Th>
        <Th class="px-3 py-2 uppercase border-b-2 border-x-2 border-slate-300" :table="table" name="cost_selling_per_carton">cost selling per carton</Th>
        <Th class="px-3 py-2 uppercase border-b-2 border-x-2 border-slate-300" :table="table" name="price_per_unit">price per unit</Th>
        <Th class="px-3 py-2 uppercase border-b-2 border-x-2 border-slate-300" :table="table" name="price_per_box">price per box</Th>
        <Th class="px-3 py-2 uppercase border-b-2 border-x-2 border-slate-300" :table="table" name="price_per_carton">price per carton</Th>
        <Th class="px-3 py-2 uppercase border-b-2 border-x-2 border-slate-300" :table="table" name="effective_date">effective date</Th>
        <Th class="px-3 py-2 uppercase border-b-2 border-x-2 border-slate-300" :table="table" name="expire_date">expire date</Th>
        <!-- <Th class="px-1 py-2 uppercase border-b-2 border-r-2 border-slate-300" :sortable="false">action</Th> -->
      </tr>
    </template>
    <template #tfoot>
      <Th class="px-1 py-2 uppercase border-b-2 border-r-2 border-slate-300" :sortable="false">no</Th>
        <Th class="px-3 py-2 uppercase border-b-2 border-x-2 border-slate-300" :sortable="false">cost selling per unit</Th>
        <Th class="px-3 py-2 uppercase border-b-2 border-x-2 border-slate-300" :sortable="false">cost selling per box</Th>
        <Th class="px-3 py-2 uppercase border-b-2 border-x-2 border-slate-300" :sortable="false">cost selling per carton</Th>
        <Th class="px-3 py-2 uppercase border-b-2 border-x-2 border-slate-300" :sortable="false">price per unit</Th>
        <Th class="px-3 py-2 uppercase border-b-2 border-x-2 border-slate-300" :sortable="false">price per box</Th>
        <Th class="px-3 py-2 uppercase border-b-2 border-x-2 border-slate-300" :sortable="false">price per carton</Th>
        <Th class="px-3 py-2 uppercase border-b-2 border-x-2 border-slate-300" :sortable="false">effective date</Th>
        <Th class="px-3 py-2 uppercase border-b-2 border-x-2 border-slate-300" :sortable="false">expire date</Th>
        <!-- <Th class="px-1 py-2 uppercase border-b-2 border-r-2 border-slate-300" :sortable="false">action</Th> -->
    </template>
    <template v-slot:tbody="{ index, item }">
      <tr>
        <td class="border p-2 border-x-2 border-slate-300 text-center">{{ index + 1 }}</td>
        <td class="border p-2 border-x-2 border-slate-300">{{ rupiah(item.cost_selling_per_unit) }}</td>
        <td class="border p-2 border-x-2 border-slate-300">{{ rupiah(item.cost_selling_per_box) }}</td>
        <td class="border p-2 border-x-2 border-slate-300">{{ rupiah(item.cost_selling_per_carton) }}</td>
        <td class="border p-2 border-x-2 border-slate-300">{{ rupiah(item.price_per_unit) }}</td>
        <td class="border p-2 border-x-2 border-slate-300">{{ rupiah(item.price_per_box) }}</td>
        <td class="border p-2 border-x-2 border-slate-300">{{ rupiah(item.price_per_carton) }}</td>
        <td class="border p-2 border-x-2 border-slate-300">{{ new Date(item.effective_date).toLocaleDateString('id') }}</td>
        <td class="border p-2 border-x-2 border-slate-300">{{ new Date(item.expire_date).toLocaleDateString('id') }}</td>
        <!-- <td class="border p-2 border-x-2 border-slate-300">
          <button @click.prevent="edit(item)" class="bg-blue-600 rounded-md px-3 py-1 text-sm text-white">
            <div class="flex items-center space-x-1">
              <i class="bx bx-edit"></i>
              <p class="lowercase first-letter:capitalize">edit</p>
            </div>
          </button>
        </td> -->
      </tr>
    </template>
  </Builder>
</template>