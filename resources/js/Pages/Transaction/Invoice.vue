<script setup>
import SinglePrice from './SinglePrice.vue'
import VariablePrice from './VariablePrice.vue'

const { transaction, totalItem } = defineProps({
  transaction: Object,
  totalItem: Number,
})

const price = value => new Number(value).toLocaleString('in-ID')

setTimeout(() => window.print(), 1000)

</script>

<style>
  .s {
    font-family: 'Merchant Copy' !important;
  }
  .text-md {
    font-size: 0.925rem; 
    line-height: 1.15rem;
  }
</style>

<template>
  <div class="pb-12 pt-0 px-1 font-light border-b-2 border-slate-900 s">
    <div class="flex flex-col items-center justify-center mb-2">
      <h1 class="text-2xl font-semibold">Swakop</h1>
      <br>
      <h1 class="text-sm">Jl Raya Karangnunggal</h1>
      <h1 class="text-sm">BATUCURI</h1>
      <h1 class="text-sm">Hp : 081221673528</h1>
    </div>
    <div class="flex items-center justify-between border-y-2 border-black border-solid space-x-1">
      <h1 class="text-sm">Invoice : {{ transaction.id.toString().padStart(6, '0') }}</h1>
      <h1 class="text-sm capitalize">Kasir : {{ transaction.user.name.length > 12 ? new String(transaction.user.name).substring(0, 12) : transaction.user.name }}</h1>
    </div>
    <table class="w-full border-collapse text-md">
      <template v-for="(item, i) in transaction.details" :key="i" :item="item">
        <tr>
          <td colspan="4" class="capitalize px-1">{{ new String(item.product.name).toUpperCase() }}</td>
        </tr>
        <VariablePrice v-if="item.price !== null && item.price?.variable_costs?.length > 0" :detail="item" :price="price" />
        <SinglePrice v-else :detail="item" :price="price" />
      </template>
      <tr class="border-t-2 border-black border-dotted">
        <td class="px-1">Total Item</td>
        <td class="text-right">{{ totalItem }}</td>
        <td colspan="2" class="px-1 text-right">{{ price(transaction.total_cost) }}</td>
      </tr>
      <tr>
        <td class="px-1" colspan="2">Cash</td>
        <td colspan="2" class="px-1 text-right">{{ price(transaction.pay) }}</td>
      </tr>
      <tr>
        <td class="px-1" colspan="2">Change</td>
        <td colspan="2" class="px-1 text-right">{{ price(transaction.pay - transaction.total_cost) }}</td>
      </tr>
    </table>
    <div class="flex items-center justify-center space-y-1 mt-2 border-y-2 border-black border-solid text-sm">
      <div>Tgl. {{ new Date().toLocaleString('en-GB').replaceAll(',', '').replaceAll('/', '-') }}</div>
    </div>
    <div class="flex flex-col items-center justify-center space-y-1 mt-2 text-sm font-semibold uppercase">
      <div style="font-size: 0.9rem">Thanks for shop with us</div>
      <p>...</p>
    </div>
  </div>
</template>