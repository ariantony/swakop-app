
<script setup>
import { getCurrentInstance, nextTick, onMounted, onUpdated, ref } from 'vue'
import { Link, useForm, usePage } from '@inertiajs/inertia-vue3'
import { Inertia } from '@inertiajs/inertia'
import AppLayout from '@/Layouts/AppLayout.vue'
import Card from '@/Components/Card.vue'
import Swal from 'sweetalert2'
import Datepicker from '@vuepic/vue-datepicker';
import { id } from 'date-fns/locale';
import '@vuepic/vue-datepicker/dist/main.css'
import Select from '@vueform/multiselect'

const { groups } = defineProps({
  groups: Array,
})

const self = getCurrentInstance()
const products = ref([])
const fgroup = useForm({
  group_id: '',
})
const fprice = useForm({
  products: [],
})

const generateGroup = () => fgroup.post(route('product.print.group'))
const generatePrice = () => fprice.post(route('product.print.price'))

const change = () => {
  nextTick(() => {
    if (fprice.products.includes(0)) {
      fprice.products = [0, ...products.value.map(p => p.id)]
    }
  })
}

const fetch = async () => {
  try {
    const response = await axios.get(route('api.product.where.has.price'))
    products.value = response.data
    Swal.close()
  } catch (e) {
    const response = await Swal.fire({
      title: 'Pengambilan data produk gagal',
      text: 'Apakah anda ingin mencoba lagi?',
      icon: 'question',
      showCancelButton: true,
      showCloseButton: true,
    })

    response.isConfirmed && fetch()
  }
}
onMounted(fetch)

</script>

<style src="@vueform/multiselect/themes/default.css"></style>

<template>
  <AppLayout title="Print Produk">
    <Card>
      <template #header>
        <div class="flex items-center justify-between space-x-2 text-white p-2">
          <h1 class="ml-4 text-black text-2xl font-semibold">Print Produk Perkelompok Barang</h1>
          <Link :href="route('product.index')" class="bg-slate-600 rounded-md px-3 py-1 font-semibold">
            <div class="flex items-center">
              <i class="bx bx-arrow-back mr-1 text-xl"></i> Kembali
            </div>
          </Link>
        </div>
      </template>
      <template #body>
        <form @submit.prevent="generateGroup">
          <div class="flex items-center space-x-4">
            <div class="flex flex-col items-center w-full">
              <div class="flex flex-col space-y-2 mb-2 w-full">
                <div class="flex items-center space-x-4 w-full">
                  <label for="group_id" class="w-1/4">Kelompok Barang</label>
                  <Select
                    v-model="fgroup.group_id"
                    :options="groups.map(g => ({
                      label: `${g.code} - ${new String(g.name).toUpperCase()}`,
                      value: g.id,
                    }))"
                    :searchable="true"
                    class="w-3/4"
                  />
                </div>
                <div class="text-red-500 text-right text-sm first-letter:capitalize" v-html="fgroup.errors.group_id || '&nbsp;'"></div>
              </div>
            </div>
          </div>
          <div class="flex items-center space-x-2 mt-6 justify-end">
            <button type="submit" class="bg-green-600 hover:bg-green-700 rounded-md px-3 py-1 text-white text-sm transition-all">
              <div class="flex items-center space-x-1">
                <i class="bx bx-search-alt"></i>
                <p class="font-semibold">Tampilkan</p>
              </div>
            </button>
          </div>
        </form>
      </template>
    </Card>
    <Card>
      <template #header>
        <div class="flex items-center justify-between space-x-2 text-white p-2">
          <h1 class="ml-4 text-black text-2xl font-semibold">Print Harga Produk</h1>
        </div>
      </template>
      <template #body>
        <form @submit.prevent="generatePrice">
          <div class="flex items-center space-x-4">
            <div class="flex flex-col items-center w-full">
              <div class="flex flex-col space-y-2 mb-2 w-full">
                <div class="flex items-center space-x-4 w-full">
                  <label for="products" class="w-1/4">Pilih Produk</label>
                  <Select  
                    v-if="fprice.products.includes(0)"
                    v-model="fprice.products"
                    mode="tags"
                    :options="[{
                      label: 'Pilih Semua',
                      value: 0,
                    }]"
                    :searchable="true"
                    :hideSelected="false"
                    :closeOnSelect="false"
                    noOptionsText="Mohon tunggu..."
                    class="w-3/4"
                    ref="multiselect"
                    @change="change"
                  />
                  <Select  
                    v-if="!fprice.products.includes(0)"
                    v-model="fprice.products"
                    mode="tags"
                    :options="[{
                      label: 'Pilih Semua',
                      value: 0,
                    }].concat(products.map(p => ({
                      label: `${p.name} ${p.barcode != null ? '- ' + p.barcode : ''}`,
                      value: p.id,
                    })))"
                    :searchable="true"
                    :hideSelected="false"
                    :closeOnSelect="false"
                    noOptionsText="Mohon tunggu..."
                    class="w-3/4"
                    ref="multiselect"
                    @change="change"
                  />
                </div>
                <div class="text-red-500 text-right text-sm first-letter:capitalize" v-html="fprice.errors.products || '&nbsp;'"></div>
              </div>
            </div>
          </div>
          <div class="flex items-center space-x-2 mt-6 justify-end">
            <button type="submit" class="bg-green-600 hover:bg-green-700 rounded-md px-3 py-1 text-white text-sm transition-all">
              <div class="flex items-center space-x-1">
                <i class="bx bx-search-alt"></i>
                <p class="font-semibold">Tampilkan</p>
              </div>
            </button>
          </div>
        </form>
      </template>
    </Card>
  </AppLayout>
</template>