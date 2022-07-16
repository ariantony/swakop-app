<script setup>
import Builder from '@/Components/DataTable/Builder.vue'
import Th from '@/Components/DataTable/Th.vue'

const { edit, destroy, detail } = defineProps({
  edit: Function,
  destroy: Function,
  detail: Function,
  price: Function,
})
</script>

<template>
  <Builder :href="route('api.product.paginate')" :colspan="8">
    <template v-slot:thead="{table}">
      <tr>
        <Th class="px-1 py-2 uppercase border-b-2 border-r-2 border-slate-300" :sortable="false" rowspan="2">no</Th>
        <Th class="px-3 py-2 uppercase border-b-2 border-x-2 border-slate-300" :table="table" name="code" rowspan="2">kode</Th>
        <Th class="px-3 py-2 uppercase border-b-2 border-x-2 border-slate-300" :table="table" name="name" rowspan="2">nama</Th>
        <Th class="px-3 py-2 uppercase border-b-2 border-x-2 border-slate-300" :table="table" name="barcode" rowspan="2">barcode</Th>
        <Th class="px-3 py-2 uppercase border-b-2 border-x-2 border-slate-300" :sortable="false" colspan="3">harga</Th>
        <Th class="px-3 py-2 uppercase border-b-2 border-x-2 border-slate-300" :sortable="false" colspan="3">stok</Th>
        <Th class="px-3 py-2 uppercase border-b-2 border-l-2 border-slate-300" :sortable="false" rowspan="2">aksi</Th>
      </tr>

      <tr>
        <Th class="px-3 py-2 uppercase border-b-2 border-x-2 border-slate-300" :sortable="false">unit</Th>
        <Th class="px-3 py-2 uppercase border-b-2 border-x-2 border-slate-300" :sortable="false">box</Th>
        <Th class="px-3 py-2 uppercase border-b-2 border-x-2 border-slate-300" :sortable="false">carton</Th>
        
        <Th class="px-3 py-2 uppercase border-b-2 border-x-2 border-slate-300" :sortable="false">unit</Th>
        <Th class="px-3 py-2 uppercase border-b-2 border-x-2 border-slate-300" :sortable="false">box</Th>
        <Th class="px-3 py-2 uppercase border-b-2 border-x-2 border-slate-300" :sortable="false">carton</Th>
      </tr>
    </template>
    <template #tfoot>
      <Th class="p-2 uppercase border-t-2 border-x-2 border-slate-300" :sortable="false">no</Th>
      <Th class="p-2 uppercase border-t-2 border-x-2 border-slate-300" :sortable="false">kode</Th>
      <Th class="p-2 uppercase border-t-2 border-x-2 border-slate-300" :sortable="false">nama</Th>
      <Th class="p-2 uppercase border-t-2 border-x-2 border-slate-300" :sortable="false">barcode</Th>
      <Th class="p-2 uppercase border-t-2 border-x-2 border-slate-300" :sortable="false" colspan="3">harga</Th>
      <Th class="p-2 uppercase border-t-2 border-x-2 border-slate-300" :sortable="false" colspan="3">stok</Th>
      <Th class="p-2 uppercase border-t-2 border-x-2 border-slate-300" :sortable="false">aksi</Th>
    </template>
    <template v-slot:tbody="{ index, item }">
      <tr>
        <td class="border p-2 border-x-2 border-slate-300 text-center">{{ index + 1 }}</td>
        <td class="border p-2 border-x-2 border-slate-300">{{ item.code }}</td>
        <td class="border p-2 border-x-2 border-slate-300">{{ item.name }}</td>
        <td class="border p-2 border-x-2 border-slate-300">{{ item.barcode }}</td>
        <td class="border p-2 border-x-2 border-slate-300">{{ item.price?.cost_selling_per_unit ? rupiah(item.price?.cost_selling_per_unit) : '-' }}</td>
        <td class="border p-2 border-x-2 border-slate-300">{{ item.price?.cost_selling_per_box ? rupiah(item.price?.cost_selling_per_box) : '-' }}</td>
        <td class="border p-2 border-x-2 border-slate-300">{{ item.price?.cost_selling_per_carton ? rupiah(item.price?.cost_selling_per_carton) : '-' }}</td>
        <td class="border p-2 border-x-2 border-slate-300 text-center">{{ item.stock_unit }}</td>
        <td class="border p-2 border-x-2 border-slate-300 text-center">{{ item.stock_box }}</td>
        <td class="border p-2 border-x-2 border-slate-300 text-center">{{ item.stock_carton }}</td>
        <td class="border p-1 ">
          <div class="flex items-center justify-center space-x-1 text-white">
            <button @click.prevent="detail(item)" class="bg-cyan-600 rounded-md px-3 py-1 text-sm font-semibold">
              <div class="flex items-center">
                <i class="bx bx-list-ul mr-1 text-white text-sm"></i> Transaksi
              </div>
            </button>

            <button @click.prevent="price(item)" class="bg-pink-500 rounded-md px-3 py-1 text-sm font-semibold">
              <div class="flex items-center">
                <i class="bx bx-dollar mr-1 text-white text-sm"></i> Harga
              </div>
            </button>

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