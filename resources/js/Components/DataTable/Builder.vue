<script setup>
import { getCurrentInstance, onMounted, ref } from 'vue'
import { Link, useForm } from '@inertiajs/inertia-vue3'
import axios from 'axios'

const { href } = defineProps({
  href: {
    type: String,
    required: true,
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

const refresh = () => {
  return axios.post(href, form.data())
              .then(response => response.data)
              .then(response => paginator.value = response)
              // .catch(e => Swal.fire({ text: `${e}`, icon: 'error' }))
}

const table = {
  refresh, paginator, form,
}

onMounted(() => refresh())
</script>

<template>
  <div class="flex flex-col space-y-2">
    <div class="flex flex-col sm:flex-row sm:justify-between space-y-1 sm:space-y-0 sm:space-x-2">
      <div class="w-1/3 flex items-center space-x-1">
        <label for="per_page" class="lowercase first-letter:capitalize">per page</label>
        <select name="per_page" v-model="form.per_page" @change.prevent="refresh" class="bg-transparent border border-slate-200 rounded-md text-sm">
          <option value="10">10</option>
          <option value="15">15</option>
          <option value="25">25</option>
          <option value="50">50</option>
          <option value="100">100</option>
        </select>
      </div>

      <div class="w-2/3 flex items-center justify-end space-x-1">
        <label for="search" class="lowecase first-letter:capitalize">search</label>
        <input type="search" v-model="form.search" @input.prevent="refresh" class="bg-transparent w-full max-w-xs border border-slate-200 rounded-md placeholder:capitalize text-sm" placeholder="search something">
      </div>
    </div>

    <div class="w-full overflow-x-auto rounded-md">
      <table class="w-full border-collapse border">
        <thead class="bg-slate-100 sticky top-0 left-0">
          <slot name="thead" :table="table" />
        </thead>

        <tfoot class="bg-slate-100">
          <slot name="tfoot" :table="table" />
        </tfoot>

        <tbody>
          <transition-group name="fade">
            <slot v-for="(item, i) in paginator.data" :key="i" :index="i" :item="item" :refresh="refresh" name="tbody" />
          </transition-group>

          <tr v-if="!paginator.data?.length">
            <td colspan="1000" class="py-4 text-xl text-center font-semibold lowercase first-letter:capitalize">
              there are no data available :'(
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>