
<script setup>
import { getCurrentInstance, nextTick, onMounted, onUpdated, ref } from 'vue'
import { Link, useForm, usePage } from '@inertiajs/inertia-vue3'
import { Inertia } from '@inertiajs/inertia'
import AppLayout from '@/Layouts/AppLayout.vue'
import Card from '@/Components/Card.vue'
import Swal from 'sweetalert2'
import { id } from 'date-fns/locale';

const { transaction, totalItem } = defineProps({
  transaction: Object,
  totalItem: Number,
})

const self = getCurrentInstance()

const price = value => new Number(value).toLocaleString('in-ID')

window.print()

</script>

<style>
  .s {
    font-family: 'Merchant Copy' !important;
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
      <h1 class="text-xs">Invoice : {{ transaction.id.toString().padStart(6, '0') }}</h1>
      <h1 class="text-xs">Kasir : {{ transaction.user.name.length > 12 ? new String(transaction.user.name).substring(0, 12) : transaction.user.name }}</h1>
    </div>
    <table class="w-full border-collapse text-xs">
      <template v-for="(item, i) in transaction.details" :key="i" :item="item">
        <tr>
          <td colspan="4" class="capitalize">{{ item.product.name }}</td>
        </tr>
        <tr>
          <td></td>
          <td class="text-right whitespace-nowrap">{{ item.qty_unit }}</td>
          <td class="text-right">{{ price(item.cost_unit) }}</td>
          <td class="text-right">{{ price(item.total_cost_all) }}</td>
        </tr>
      </template>
      <tr class="border-t-2 border-solid">
        <td>Total Item</td>
        <td class="text-right">{{ totalItem }}</td>
        <td colspan="2" class="text-right">{{ price(transaction.total_cost) }}</td>
      </tr>
      <tr>
        <td colspan="2">Cash</td>
        <td colspan="2" class="text-right">{{ price(transaction.pay) }}</td>
      </tr>
      <tr>
        <td colspan="2">Change</td>
        <td colspan="2" class="text-right">{{ price(transaction.pay - transaction.total_cost) }}</td>
      </tr>
    </table>
    <div class="flex items-center justify-center space-y-1 mt-2 border-y-2 border-black border-solid text-xs">
      <div>Tgl. {{ new Date().toLocaleString('en-GB').replaceAll(',', '').replaceAll('/', '-') }}</div>
    </div>
    <div class="flex flex-col items-center justify-center space-y-1 mt-2 text-xs font-semibold uppercase">
      <div style="font-size: 0.8rem">Thanks for shop with us</div>
      <p>...</p>
    </div>
  </div>
</template>