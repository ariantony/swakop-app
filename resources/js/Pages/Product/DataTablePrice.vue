<script setup>
import Builder from '@/Components/DataTable/Builder.vue'
import Th from '@/Components/DataTable/Th.vue'
import FormatVariableCostsProductPrice from '../../Components/FormatVariableCostsProductPrice.vue';

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
        <Th class="px-3 py-2 uppercase border-2 border-slate-300" :table="table" name="cost_selling_per_unit">Harga Beli Satuan</Th>
        <Th class="px-3 py-2 uppercase border-2 border-slate-300" :table="table" name="price_per_unit">Harga Jual</Th>
        <Th class="px-3 py-2 uppercase border-2 border-slate-300" :table="table" name="effective_date">Tanggal Efektif</Th>
        <Th class="px-3 py-2 uppercase border-2 border-slate-300" :table="table" name="expire_date">Efektif Hingga</Th>
      </tr>
    </template>
    <template #tfoot>
      <Th class="px-1 py-2 uppercase border-2 border-slate-300" :sortable="false">no</Th>
        <Th class="px-3 py-2 uppercase border-2 border-slate-300" :sortable="false">Harga Beli Satuan</Th>
        <Th class="px-3 py-2 uppercase border-2 border-slate-300" :sortable="false">Harga Jual</Th>
        <Th class="px-3 py-2 uppercase border-2 border-slate-300" :sortable="false">Tanggal Efektif</Th>
        <Th class="px-3 py-2 uppercase border-2 border-slate-300" :sortable="false">Efektif Hingga</Th>
    </template>
    <template v-slot:tbody="{ index, item }">
      <tr>
        <td class="border p-2 border-x-2 border-slate-300 text-center">{{ index + 1 }}</td>
        <td class="border p-2 border-x-2 border-slate-300 text-right">{{ rupiah(item.cost_selling_per_unit) }}</td>
        <td class="border p-2 border-x-2 border-slate-300 text-right">
          <FormatVariableCostsProductPrice :price="item" v-if="item?.variable_costs?.length > 0" />
          <div v-else> {{ rupiah(item.price_per_unit) }} </div>
        </td>
        <td class="border p-2 border-x-2 border-slate-300 text-center">{{ dateindo(item.created_at, true) }}</td>
        <td class="border p-2 border-x-2 border-slate-300 text-center">{{ item.created_at === item.updated_at ? 'Masih Berlaku' : dateindo(item.updated_at, true) }}</td>
      </tr>
    </template>
  </Builder>
</template>