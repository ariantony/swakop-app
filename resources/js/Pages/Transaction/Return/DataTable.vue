<script setup>
import Builder from '@/Components/DataTable/Builder.vue'
import { useForm } from '@inertiajs/inertia-vue3'
import Th from '@/Components/DataTable/Th.vue'
import { Inertia } from '@inertiajs/inertia';
import { nextTick, ref } from 'vue';
import Swal from 'sweetalert2'

const { detail } = defineProps({
  detail: Function,
})

const form = useForm({
  id : '',
})

const a = ref(true)

const print = (item) => form.post(route('transaction.return.print', item.id))

</script>

<template>
  <Builder v-if="a" :href="route('api.transaction.return.paginate')" :colspan="5">
    <template v-slot:thead="{table}">
      <tr>
        <Th class="px-1 py-2 uppercase border-b-2 border-r-2 border-slate-300" :sortable="false">no</Th>
        <Th class="px-3 py-2 uppercase border-b-2 border-x-2 border-slate-300" :table="table" name="id">invoice</Th>
        <Th class="px-3 py-2 uppercase border-b-2 border-x-2 border-slate-300" :table="table" name="user_id">kasir</Th>
        <!-- <Th class="px-3 py-2 uppercase border-b-2 border-x-2 border-slate-300" :table="table" name="payment_method">metode pembayaran</Th> -->
        <Th class="px-3 py-2 uppercase border-b-2 border-x-2 border-slate-300" :table="table" name="total_cost">total belanja</Th>
        <Th class="px-3 py-2 uppercase border-b-2 border-x-2 border-slate-300" :table="table" name="created_at">di retur pada</Th>
        <Th class="px-3 py-2 uppercase border-b-2 border-x-2 border-slate-300" :table="table" name="note">catatan</Th>
        <Th class="px-3 py-2 uppercase border-b-2 border-l-2 border-slate-300" :sortable="false">aksi</Th>
      </tr>
    </template>
    <template #tfoot>
      <Th class="p-2 uppercase border-t-2 border-x-2 border-slate-300" :sortable="false">no</Th>
      <Th class="p-2 uppercase border-t-2 border-x-2 border-slate-300" :sortable="false">invoice</Th>
      <Th class="p-2 uppercase border-t-2 border-x-2 border-slate-300" :sortable="false">kasir</Th>
      <!-- <Th class="p-2 uppercase border-t-2 border-x-2 border-slate-300" :sortable="false">metode pembayaran</Th> -->
      <Th class="p-2 uppercase border-t-2 border-x-2 border-slate-300" :sortable="false">total belanja</Th>
      <Th class="p-2 uppercase border-t-2 border-x-2 border-slate-300" :sortable="false">di retur pada</Th>
      <Th class="p-2 uppercase border-t-2 border-x-2 border-slate-300" :sortable="false">catatan</Th>
      <Th class="p-2 uppercase border-t-2 border-x-2 border-slate-300" :sortable="false">aksi</Th>
    </template>
    <template v-slot:tbody="{ index, item }">
      <tr>
        <td class="border p-2 border-x-2 border-slate-300 text-center">{{ index + 1 }}</td>
        <td class="border p-2 border-x-2 border-slate-300 text-center">{{ item.id.toString().padStart(10, '0') }}</td>
        <td class="border p-2 border-x-2 border-slate-300 capitalize">{{ item.user.name }}</td>
        <!-- <td class="border p-2 border-x-2 border-slate-300 capitalize">{{ item.payment_method }}</td> -->
        <td class="border p-2 border-x-2 border-slate-300 text-right">{{ rupiah(item.total_cost) }}</td>
        <td class="border p-2 border-x-2 border-slate-300 text-right">{{ dateindo(item.updated_at, true) }}</td>
        <td class="border p-2 border-x-2 border-slate-300">{{ item.note }}</td>
        <td class="border p-1 ">
          <div class="flex items-center justify-center space-x-1 text-white">
            <!-- <button @click.prevent="print(item)" class="bg-pink-600 rounded-md px-3 py-1 text-sm font-semibold">
              <div class="flex items-center">
                <i class="bx bx-printer mr-1 text-white text-sm"></i> Print
              </div>
            </button> -->
            <button @click.prevent="detail(item)" class="bg-blue-600 rounded-md px-3 py-1 text-sm font-semibold">
              <div class="flex items-center">
                <i class="bx bx-list-ul mr-1 text-white text-sm"></i> Detail
              </div>
            </button>
          </div>
        </td>
      </tr>
    </template>
  </Builder>
</template>