<script setup>
import Builder from '@/Components/DataTable/Builder.vue'
import Th from '@/Components/DataTable/Th.vue'

const { product } = defineProps({
  product: Object,
  edit: Function,
})
</script>

<template>
  <Builder :href="route('api.product.price.paginate', product.id)" :colspan="9">
    <template v-slot:thead="{table}">
      <tr>
        <Th class="px-1 py-2 uppercase border-2 border-slate-300" :sortable="false">no</Th>
        <Th class="px-3 py-2 uppercase border-2 border-slate-300" :table="table" name="cost_selling_per_unit">HPP Satuan</Th>
        <!-- <Th class="px-3 py-2 uppercase border-2 border-slate-300" :table="table" name="cost_selling_per_box">HPP Box / Renceng</Th>
        <Th class="px-3 py-2 uppercase border-2 border-slate-300" :table="table" name="cost_selling_per_carton">HPP Karton</Th> -->
        <Th class="px-3 py-2 uppercase border-2 border-slate-300" :table="table" name="price_per_unit">Harga Jual Satuan</Th>
        <!-- <Th class="px-3 py-2 uppercase border-2 border-slate-300" :table="table" name="price_per_box">Harga Jual Box / Renceng</Th>
        <Th class="px-3 py-2 uppercase border-2 border-slate-300" :table="table" name="price_per_carton">Harga Jual Karton</Th> -->
        <Th class="px-3 py-2 uppercase border-2 border-slate-300" :table="table" name="effective_date">Tanggal Efektif</Th>
        <Th class="px-3 py-2 uppercase border-2 border-slate-300" :table="table" name="expire_date">Efektif Hingga</Th>
        <!-- <Th class="px-1 py-2 uppercase border-2 border-slate-300" :sortable="false">action</Th> -->
      </tr>
    </template>
    <template #tfoot>
      <Th class="px-1 py-2 uppercase border-2 border-slate-300" :sortable="false">no</Th>
        <Th class="px-3 py-2 uppercase border-2 border-slate-300" :sortable="false">HPP Satuan</Th>
        <!-- <Th class="px-3 py-2 uppercase border-2 border-slate-300" :sortable="false">HPP Box / Renceng</Th>
        <Th class="px-3 py-2 uppercase border-2 border-slate-300" :sortable="false">HPP Karton</Th> -->
        <Th class="px-3 py-2 uppercase border-2 border-slate-300" :sortable="false">Harga Jual Satuan</Th>
        <!-- <Th class="px-3 py-2 uppercase border-2 border-slate-300" :sortable="false">Harga Jual Box / Renceng</Th>
        <Th class="px-3 py-2 uppercase border-2 border-slate-300" :sortable="false">Harga Jual Karton</Th> -->
        <Th class="px-3 py-2 uppercase border-2 border-slate-300" :sortable="false">Tanggal Efektif</Th>
        <Th class="px-3 py-2 uppercase border-2 border-slate-300" :sortable="false">Efektif Hingga</Th>
        <!-- <Th class="px-1 py-2 uppercase border-2 border-slate-300" :sortable="false">action</Th> -->
    </template>
    <template v-slot:tbody="{ index, item }">
      <tr>
        <td class="border p-2 border-x-2 border-slate-300 text-center">{{ index + 1 }}</td>
        <td class="border p-2 border-x-2 border-slate-300 text-right">{{ rupiah(item.cost_selling_per_unit) }}</td>
        <!-- <td class="border p-2 border-x-2 border-slate-300 text-right">{{ rupiah(item.cost_selling_per_box) }}</td>
        <td class="border p-2 border-x-2 border-slate-300 text-right">{{ rupiah(item.cost_selling_per_carton) }}</td> -->
        <td class="border p-2 border-x-2 border-slate-300 text-right">{{ rupiah(item.price_per_unit) }}</td>
        <!-- <td class="border p-2 border-x-2 border-slate-300 text-right">{{ rupiah(item.price_per_box) }}</td>
        <td class="border p-2 border-x-2 border-slate-300 text-right">{{ rupiah(item.price_per_carton) }}</td> -->
        <td class="border p-2 border-x-2 border-slate-300 text-center">{{ dateindo(item.effective_date) }}</td>
        <td class="border p-2 border-x-2 border-slate-300 text-center">{{ item.created_at === item.updated_at ? 'Masih Berlaku' : dateindo(item.expire_date) }}</td>
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