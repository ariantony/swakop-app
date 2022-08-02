
<script setup>
import { getCurrentInstance, nextTick, onMounted, onUpdated, ref } from 'vue'
import { Link, useForm, usePage } from '@inertiajs/inertia-vue3'
import { Inertia } from '@inertiajs/inertia'
import AppLayout from '@/Layouts/AppLayout.vue'
import Card from '@/Components/Card.vue'
import Swal from 'sweetalert2'
import DataTable from './DataTable.vue'
import '@vuepic/vue-datepicker/dist/main.css'
import Select from '@vueform/multiselect'

const { users } = defineProps({
  users: Array,
})

const self = getCurrentInstance()
const products = ref([])
const table = ref(null)
const a = ref(true)

const form = useForm({
  from_id: '',
  to_id: '',
  large: 0,
  small_per_large: 0,
})

const setFrom = () => products.value.find(p => p.id === form.from_id)
const setTo = () => products.value.find(p => p.id === form.to_id)

const submit = () => {
  if (form.large > setFrom()?.stock_unit) {
    return Swal.fire({
      title: 'Peringatan',
      text: 'Qty besar melebihi stok tersedia',
      icon: 'warning',
    }).then(response => {
      self.refs?.large?.focus()
    })
  }

  return Swal.fire({
    title: 'Konfirmasi konversi stok?',
    html: '<span class="text-red-600"> Harap berhati-hati, konversi stok tidak bisa dikembalikan! </span>',
    icon: 'question',
    showCancelButton: true,
  }).then(response => response.isConfirmed && (
    form.post(route('conversion.store'), {
      onSuccess: () => {
        form.reset()
        a.value = false
        nextTick(() => a.value = true)
      }
    })
  ))
}

const fetch = async () => {
  try {
    const response = await axios.get(route('api.product.where.has.stock'))
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
  <AppLayout title="Konversi Stok">
    <Card>
      <template #header>
        <div class="flex items-center justify-between space-x-2 text-white p-2">
          <h1 class="ml-4 text-black text-2xl font-semibold">Konversi Stok</h1>
        </div>
      </template>
      <template #body>
        <form @submit.prevent="submit">
          <div class="flex items-center space-x-4">
            <div class="flex flex-col items-center w-full">
              <div class="flex flex-col space-y-1 w-full">
                <div class="flex items-center space-x-4 w-full">
                  <div class="w-1/4">
                    <label for="from_id">Produk qty besar</label>
                    <div class="text-xs text-slate-500">Qty besar = box / renceng / pak / karton</div>
                  </div>
                  <div class="w-3/4">
                    <Select
                      v-model="form.from_id"
                      :options="products.map(p => ({
                        label: `${p.code ? p.code + '-' : ''} ${p.barcode} - ${p.name}`,
                        value: p.id,
                      }))"
                      :searchable="true"
                      ref="from"
                      noOptionsText="Mohon tunggu..."
                      @change="setFrom"
                    />
                  </div>
                </div>
              </div>
              <div class="flex space-x-3 w-full">
                <div class="w-1/4">&nbsp;</div>
                <div class="w-3/4">
                  <div class="flex items-center justify-between space-x-1 my-1 ml-1 text-sm first-letter:capitalize">
                    <div v-if="form.from_id" class="text-blue-600" >Sisa stok qty besar saat ini : {{ setFrom()?.stock_unit }} </div>
                    <div class="text-red-500" v-html="form.errors.from_id || '&nbsp;'"></div>
                  </div>
                </div>
              </div>

              <div class="flex flex-col space-y-1 w-full">
                <div class="flex items-center space-x-4 w-full">
                  <div class="w-1/4">
                    <label for="to_id" class="w-1/3">Produk qty kecil</label>
                    <div class="text-xs text-slate-500">Qty kecil = satuan / pcs / sachet</div>
                  </div>
                  <div class="w-3/4">
                    <Select
                      v-model="form.to_id"
                      :options="products.map(p => ({
                        label: `${p.code ? p.code + '-' : ''} ${p.barcode} - ${p.name}`,
                        value: p.id,
                      }))"
                      :searchable="true"
                      class="w-3/4"
                      ref="to"
                      noOptionsText="Mohon tunggu..."
                    />
                  </div>
                </div>
              </div>  
              <div class="flex space-x-3 w-full">
                <div class="w-1/4">&nbsp;</div>
                <div class="w-3/4">
                  <div class="flex items-center justify-between space-x-1 my-1 ml-1 text-sm first-letter:capitalize">
                    <div v-if="form.to_id" class="text-blue-600" >Sisa stok qty kecil saat ini : {{ setTo()?.stock_unit }} </div>
                    <div class="text-red-500" v-html="form.errors.to_id || '&nbsp;'"></div>
                  </div>
                </div>
              </div>

              <div class="flex flex-col space-y-1 w-full">
                <div class="flex items-center space-x-4 w-full">
                  <label for="date" class="w-1/3">Jumlah qty besar yang akan dikonversi</label>
                  <input type="number" name="large" ref="large" v-model="form.large" min="1" placeholder="Jumlah qty besar yang akan dikonversi" class="rounded border border-slate-300 w-full" :class="setFrom()?.stock_unit < form.large ? 'focus:border-red-600 border-red-600 border-2' : ''">
                </div>
                <div class="text-red-500 text-right text-sm first-letter:capitalize" v-html="form.errors.large || '&nbsp;'"></div>
              </div>
              <div class="flex flex-col space-y-1 w-full">
                <div class="flex items-center space-x-4 w-full">
                  <label for="date" class="w-1/3">Jumlah qty kecil per satu qty besar</label>
                  <input type="number" name="small_per_large" ref="small_per_large" v-model="form.small_per_large" min="1" placeholder="Jumlah qty kecil per satu qty besar" class="rounded border border-slate-300 w-full">
                </div>
                <div class="text-red-500 text-right text-sm first-letter:capitalize" v-html="form.errors.small_per_large || '&nbsp;'"></div>
              </div>
            </div>
          </div>

          <div v-if="form.from_id && form.to_id && form.large > 0 && form.small_per_large > 0" class="flex flex-col items-center space-y-1 border-slate-500 border-2 rounded p-2 mb-2">
            <h1 class="text-lg w-full">Setelah Konversi stok :</h1>
            <div class="flex flex-col space-y-1 w-full">
              <div class="flex items-center space-x-4 w-full">
                <div class="flex flex-col w-full">
                  <div>
                    Stok Produk <span class="font-bold"> {{ setFrom()?.name }} </span> akan menjadi <span class="font-bold" :class="setFrom()?.stock_unit < form.large ? 'text-red-600' : 'text-green-700'"> {{ setFrom()?.stock_unit - form.large}} </span> dan
                    Stok Produk <span class="font-bold"> {{ setTo()?.name }} </span> akan menjadi <span class="font-bold text-green-700"> {{ setTo()?.stock_unit + (form.large * form.small_per_large)}} </span>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="flex items-center space-x-2 justify-end">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 rounded-md px-3 py-1 text-white text-sm transition-all">
              <div class="flex items-center space-x-1">
                <i class="bx bx-transfer-alt"></i>
                <p class="font-semibold">Konversi</p>
              </div>
            </button>
          </div>
        </form>
      </template>
    </Card>
    <Card>
      <template #header>
        <div class="flex items-center justify-start space-x-2 text-white p-2">
          <h1 class="ml-4 text-black text-2xl font-semibold">Riwayat Konversi Stok</h1>
        </div>
      </template>
      <template #body>
        <DataTable v-if="a" ref="table" />
      </template>
    </Card>
  </AppLayout>
</template>