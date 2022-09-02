
<script setup>
import { getCurrentInstance, nextTick, onMounted, onUpdated, ref } from 'vue'
import { Link, useForm } from '@inertiajs/inertia-vue3'
import { Inertia } from '@inertiajs/inertia'
import AppLayout from '@/Layouts/AppLayout.vue'
import Card from '@/Components/Card.vue'
import Swal from 'sweetalert2'
import Datepicker from '@vuepic/vue-datepicker';
import { id } from 'date-fns/locale';
import '@vuepic/vue-datepicker/dist/main.css'
import Select from '@vueform/multiselect'

const { totalSell, hpp, grossProfit, burden, netProfit, period } = defineProps({
  totalSell: Number,
  grossProfit: Number,
  netProfit: Number,
  hpp: Array,
  burden: Array,
  period: Number,
})

const self = getCurrentInstance()

const print = () => {
  Swal.fire({
    title: 'Menyiapkan file...',
    text: 'Mohon tunggu hingga preview file muncul',
    showConfirmButton: false,
    allowOutsideClick: false,
    allowEscapeKey: false,
    allowEnterKey: false,
    didOpen : () => {
      Swal.showLoading()
    }
  })
  setTimeout(Swal.close, 800)
  setTimeout(window.print, 1000)
}

</script>

<style src="@vueform/multiselect/themes/default.css"></style>

<template>
  <AppLayout title="Laporan Laba Rugi Bulan">
    <Card>
      <template #header>
        <div class="flex items-center justify-between space-x-2 text-white p-2">
          <h1 class="ml-4 text-black text-2xl font-semibold">Laporan Laba Rugi Bulan "{{ periodindo(period) }}"</h1>
          <div class="flex flex-none space-x-2">
            <button @click.prevent="print" type="button" class="bg-pink-600 rounded-md px-3 py-1 font-semibold">
              <div class="flex items-center">
                <i class="bx bx-download mr-1 text-xl"></i> Download
              </div>
            </button>
            <Link :href="route('income.statement.index')" class="bg-slate-600 rounded-md px-3 py-1 font-semibold">
              <div class="flex items-center">
                <i class="bx bx-arrow-back mr-1 text-xl"></i> Kembali
              </div>
            </Link>
          </div>
        </div>
      </template>
      <template #body>
        <table class="w-full border-collapse border-2 border-slate-300">
          <thead class="bg-slate-100">
            <th class="px-1 py-2 uppercase border-b-2 border-r-2 border-slate-300" colspan="2">Keterangan</th>
            <th class="px-1 py-2 uppercase border-b-2 border-r-2 border-slate-300" colspan="2">Nilai</th>
          </thead>
          <tbody>
            <!-- Pendapatan Bersih -->
            <tr>
              <td class="border p-2 border-x-2 border-slate-300 font-bold text-lg" colspan="2">Pendapatan / Penjualan Bersih</td>
              <td class="border p-2 border-x-2 border-slate-300"></td>
              <td class="border p-2 border-x-2 border-slate-300 text-right text-lg" colspan="2">{{ rupiah(totalSell) }}</td>
            </tr>
            <!-- HPP -->
            <tr>
              <td class="border p-2 border-x-2 border-slate-300 font-bold text-lg" colspan="2">HPP</td>
              <td class="border p-2 border-x-2 border-slate-300" colspan="2"></td>
            </tr>
            <tr>
              <td class="border p-2 border-x-2 border-slate-300 text-center">1</td>
              <td class="border p-2 border-x-2 border-slate-300">Pembelian Bersih</td>
              <td class="border p-2 border-x-2 border-slate-300 text-right">{{ rupiah(hpp.totalBuy) }}</td>
              <td class="border p-2 border-x-2 border-slate-300"></td>
            </tr>
            <tr>
              <td class="border p-2 border-x-2 border-slate-300 text-center">2</td>
              <td class="border p-2 border-x-2 border-slate-300">Retur Pembelian Barang</td>
              <td class="border p-2 border-x-2 border-slate-300 text-right">{{ rupiah(hpp.totalReturn) }}</td>
              <td class="border p-2 border-x-2 border-slate-300"></td>
            </tr>
            <tr>
              <td class="border p-2 border-x-2 border-slate-300"></td>
              <td class="border p-2 border-x-2 border-slate-300">Total HPP</td>
              <td class="border p-2 border-x-2 border-slate-300"></td>
              <td class="border p-2 border-x-2 border-slate-300 text-right" colspan="2">{{ rupiah(hpp.total) }}</td>
            </tr>
            <!-- Laba Kotor -->
            <tr>
              <td class="border p-2 border-x-2 border-slate-300 font-bold text-lg" colspan="2">Laba Kotor</td>
              <td class="border p-2 border-x-2 border-slate-300 text-right text-lg" colspan="2">{{ rupiah(grossProfit) }}</td>
            </tr>
            <!-- Beban -->
            <tr>
              <td class="border p-2 border-x-2 border-slate-300 font-bold text-lg" colspan="2">Beban</td>
              <td class="border p-2 border-x-2 border-slate-300" colspan="2"></td>
            </tr>
            <tr v-for="(item, i) in burden.list" :key="i" :index="i" :item="item">
              <td class="border p-2 border-x-2 border-slate-300 text-center">{{ i + 1 }}</td>
              <td class="border p-2 border-x-2 border-slate-300 capitalize">{{ item.name }}</td>
              <td class="border p-2 border-x-2 border-slate-300 text-right">{{ rupiah(item.cost) }}</td>
              <td class="border p-2 border-x-2 border-slate-300"></td>
            </tr>
            <tr>
              <td class="border p-2 border-x-2 border-slate-300"></td>
              <td class="border p-2 border-x-2 border-slate-300">Total Beban</td>
              <td class="border p-2 border-x-2 border-slate-300"></td>
              <td class="border p-2 border-x-2 border-slate-300 text-right" colspan="2">{{ rupiah(burden.total) }}</td>
            </tr>
            <!-- Laba Bersih -->
            <tr :class="netProfit > 0 ? 'text-green-500' : 'text-red-500'">
              <td class="border p-2 border-x-2 border-slate-300 font-bold text-xl" colspan="2">Laba Bersih</td>
              <td class="border p-2 border-x-2 border-slate-300 text-right font-bold text-xl" colspan="2">{{ rupiah(netProfit) }}</td>
            </tr>
          </tbody>
          <tfoot class="bg-slate-100">
            <th class="px-1 py-2 uppercase border-t-2 border-r-2 border-slate-300" colspan="2">Keterangan</th>
            <th class="px-1 py-2 uppercase border-t-2 border-r-2 border-slate-300" colspan="2">Nilai</th>
          </tfoot>
        </table>
      </template>

      <template #print>
        <div class="flex items-center justify-center font-semibold mb-10">
          <h1 class="text-black text-2xl">Laporan Laba Rugi "{{ periodindo(period) }}"</h1>
        </div>
        <table class="w-full border-collapse border-2 border-slate-300">
          <thead class="bg-slate-100">
            <th class="px-1 py-2 uppercase border-b-2 border-r-2 border-slate-300" colspan="2">Keterangan</th>
            <th class="px-1 py-2 uppercase border-b-2 border-r-2 border-slate-300" colspan="2">Nilai</th>
          </thead>
          <tbody>
            <!-- Pendapatan Bersih -->
            <tr>
              <td class="border p-2 border-x-2 border-slate-300 font-bold text-lg" colspan="2">Pendapatan / Penjualan Bersih</td>
              <td class="border p-2 border-x-2 border-slate-300"></td>
              <td class="border p-2 border-x-2 border-slate-300 text-right text-lg" colspan="2">{{ rupiah(totalSell) }}</td>
            </tr>
            <!-- HPP -->
            <tr>
              <td class="border p-2 border-x-2 border-slate-300 font-bold text-lg" colspan="2">HPP</td>
              <td class="border p-2 border-x-2 border-slate-300" colspan="2"></td>
            </tr>
            <tr>
              <td class="border p-2 border-x-2 border-slate-300 text-center">1</td>
              <td class="border p-2 border-x-2 border-slate-300">Pembelian Bersih</td>
              <td class="border p-2 border-x-2 border-slate-300 text-right">{{ rupiah(hpp.totalBuy) }}</td>
              <td class="border p-2 border-x-2 border-slate-300"></td>
            </tr>
            <tr>
              <td class="border p-2 border-x-2 border-slate-300 text-center">2</td>
              <td class="border p-2 border-x-2 border-slate-300">Retur Pembelian Barang</td>
              <td class="border p-2 border-x-2 border-slate-300 text-right">{{ rupiah(hpp.totalReturn) }}</td>
              <td class="border p-2 border-x-2 border-slate-300"></td>
            </tr>
            <tr>
              <td class="border p-2 border-x-2 border-slate-300"></td>
              <td class="border p-2 border-x-2 border-slate-300">Total HPP</td>
              <td class="border p-2 border-x-2 border-slate-300"></td>
              <td class="border p-2 border-x-2 border-slate-300 text-right" colspan="2">{{ rupiah(hpp.total) }}</td>
            </tr>
            <!-- Laba Kotor -->
            <tr>
              <td class="border p-2 border-x-2 border-slate-300 font-bold text-lg" colspan="2">Laba Kotor</td>
              <td class="border p-2 border-x-2 border-slate-300 text-right text-lg" colspan="2">{{ rupiah(grossProfit) }}</td>
            </tr>
            <!-- Beban -->
            <tr>
              <td class="border p-2 border-x-2 border-slate-300 font-bold text-lg" colspan="2">Beban</td>
              <td class="border p-2 border-x-2 border-slate-300" colspan="2"></td>
            </tr>
            <tr v-for="(item, i) in burden.list" :key="i" :index="i" :item="item">
              <td class="border p-2 border-x-2 border-slate-300 text-center">{{ i + 1 }}</td>
              <td class="border p-2 border-x-2 border-slate-300 capitalize">{{ item.name }}</td>
              <td class="border p-2 border-x-2 border-slate-300 text-right">{{ rupiah(item.cost) }}</td>
              <td class="border p-2 border-x-2 border-slate-300"></td>
            </tr>
            <tr>
              <td class="border p-2 border-x-2 border-slate-300"></td>
              <td class="border p-2 border-x-2 border-slate-300">Total Beban</td>
              <td class="border p-2 border-x-2 border-slate-300"></td>
              <td class="border p-2 border-x-2 border-slate-300 text-right" colspan="2">{{ rupiah(burden.total) }}</td>
            </tr>
            <!-- Laba Bersih -->
            <tr :class="netProfit > 0 ? 'text-green-500' : 'text-red-500'">
              <td class="border p-2 border-x-2 border-slate-300 font-bold text-lg" colspan="2">Laba Bersih</td>
              <td class="border p-2 border-x-2 border-slate-300 text-right font-bold text-lg" colspan="2">{{ rupiah(netProfit) }}</td>
            </tr>
          </tbody>
        </table>
        <div class="flex items-center justify-start text-black space-y-2 mt-2">
          <div>Laporan laba rugi dicetak pada {{ dateindo(new Date(), true) }}</div>
        </div>
      </template>
    </Card>
  </AppLayout>
</template>