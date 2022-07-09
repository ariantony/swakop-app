<script setup>
import { getCurrentInstance, onMounted, onUnmounted, ref } from 'vue'
import { Link, useForm } from '@inertiajs/inertia-vue3'
import Swal from 'sweetalert2'
import axios from 'axios'

const { href } = defineProps({
  href: {
    type: String,
    required: true,
  },
  colspan: {
    type: Number,
    default: 1,
  },
})

const form = useForm({
  search: '',
  per_page: 10,
  order: {
    key: null,
    dir: null,
  },
})

const paginator = ref({})
const latest = ref(href)

const refresh = (page) => {
  return axios.post(page || href, form.data())
              .then(response => response.data)
              .then(response => paginator.value = response)
              .then(() => latest.value = page || href)
              .catch(e => Swal.fire({ text: `${e}`, icon: 'error' }))
}

const table = {
  refresh, paginator, form,
}

const interval = ref(null)

onMounted(() => refresh().then(() => interval.value = setInterval(() => refresh(latest.value), 5000)))
onUnmounted(() => interval.value && clearInterval(interval.value))
</script>

<template>
  <div class="flex flex-col space-y-2">
    <div class="flex flex-col sm:flex-row sm:justify-between space-y-1 sm:space-y-0 sm:space-x-2">
      <div class="w-1/3 flex items-center space-x-1">
        <template v-if="paginator.total > form.per_page">
          <label for="per_page" class="lowercase first-letter:capitalize">Tampilkan data</label>
          <select name="per_page" v-model="form.per_page" @change.prevent="refresh()" class="bg-transparent border-2 border-slate-300 rounded-md text-sm">
            <option value="10">10</option>
            <option value="15">15</option>
            <option value="25">25</option>
            <option value="50">50</option>
            <option value="100">100</option>
          </select>
        </template>
      </div>

      <div class="w-2/3 flex items-center justify-end space-x-1">
        <label for="search" class="lowecase first-letter:capitalize">Cari</label>
        <input type="search" v-model="form.search" @input.prevent="refresh()" class="bg-transparent w-full max-w-xs border-2 border-slate-300 rounded-md placeholder:capitalize text-sm" placeholder="Cari sesuatu ...">
      </div>
    </div>

    <div class="w-full overflow-x-auto rounded-md">
      <table class="w-full border-collapse border-2 border-slate-300">
        <thead class="bg-slate-100">
          <slot name="thead" :table="table" />
        </thead>

        <tbody>
            <slot v-for="(item, i) in paginator.data" :key="i" :index="i" :item="item" :refresh="refresh" name="tbody" />

          <tr v-if="!paginator.data?.length">
            <td :colspan="colspan" class="py-4 text-xl text-center font-semibold first-letter:capitalize">
              Tidak ada data ditemukan.
            </td>
          </tr>
        </tbody>

        <tfoot class="bg-slate-100">
          <slot name="tfoot" :table="table" />
        </tfoot>
      </table>
    </div>

    <div v-if="paginator.total > form.per_page" class="flex items-center space-x-2">
      <div class="flex-none w-1/3">
        <p class="lowercase first-letter:capitalize">menampilkan {{ paginator.per_page }} data dari data ke {{ paginator.from }} sampai {{ paginator.to }}. total semua data {{ paginator.total }}</p>
      </div>

      <div class="flex items-center justify-end overflow-x-auto w-2/3">
        <div class="w-full flex items-center">
          <button
            v-for="(link, i) in paginator.links"
            :key="i"
            @click.prevent="link.url && refresh(link.url)"
            class="text-sm uppercase font-semibold px-3 py-1 border flex-none"
            :class="`${i === 0 ? 'rounded-l-md' : ''} ${paginator.links?.length - 1 === i ? 'rounded-r-md' : ''} ${link.active ? 'bg-blue-600 text-white border-blue-600' : (link.url ? 'bg-slate-50' : 'bg-slate-200')} ${link.url ? 'cursor-pointer' : 'cursor-default'}`"
            v-html="link.label"></button>
        </div>
      </div>
    </div>
  </div>
</template>