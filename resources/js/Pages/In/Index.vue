<script setup>
import { getCurrentInstance, ref, nextTick } from 'vue'
import AppLayout from '@/Layouts/AppLayout.vue'
import Select from '@vueform/multiselect'
import { useForm } from '@inertiajs/inertia-vue3'
import Swal from 'sweetalert2'
import Card from '@/Components/Card.vue'

const self = getCurrentInstance()
const { products } = defineProps({
  products: Array,
})

const form = useForm({
  product: null,
  qty: 1,
})

const create = useForm({
  code: '',
  name: '',
  barcode: '',
  qty: 1,
  price: {
    buy: {
      unit: 0,
      box: 0,
      carton: 0,
    },
    sell: {
      unit: 0,
      box: 0,
      carton: 0,
    },
  },
})

const open = ref(false)

const show = () => {
  open.value = true
}

const close = () => {
  open.value = false
}

const option = e => {
  create.barcode = e
  open.value = true
}

const reformat = (e, target, initial) => {
  const value = initial || e.target.value
  var val = new String(value),
      replaced = val.replace(/[^,\d]/g, '').toString(),
      split = replaced.split(','),
      remaining = split[0].length % 3,
      result = split[0].substring(0, remaining),
      thousand = split[0].substring(remaining).match(/\d{3}/gi),
      separator;

  if (thousand) {
    separator = remaining ? '.' : '';
    result += separator + thousand.join('.');
  }

  result = split[1] != undefined ? result + ',' + split[1] : result;

  e.target.value = `Rp ${result}`
  return parseFloat(result.replaceAll('.', '').replaceAll(',', '.'))
}

const add = () => {
  return form.post(route('in.add'), {
    onSuccess: () => form.reset(),
  })
}

const store = () => {
  return create.post(route('in.store'), {
    onSuccess: () => {
      form.reset()
      create.reset()
      close()
    },

    onError: () => nextTick(show),
  })
}
</script>

<style src="@vueform/multiselect/themes/default.css"></style>

<template>
  <AppLayout title="Stock masuk">
    <Card>
      <template #body>
        <form @submit.prevent="add" v-if="open === false" class="flex flex-col space-y-2">
          <div class="flex items-center space-x-4">
            <div class="flex items-center space-x-4 w-full">
              <div class="flex flex-col space-y-2 w-full">
                <div class="flex items-center space-x-4 w-full">
                  <label for="product" class="lowercase first-letter:capitalize flex-none">kode produk / nama produk / barcode</label>
                  <Select 
                    v-model="form.product"
                    :options="products.map(p => ({
                      label: `${p.code} - ${p.name} - ${p.barcode}`,
                      value: p.id,
                    }))"
                    :searchable="true"
                    :createOption="true"
                    @option="option" />
                </div>

                <div class="text-red-500 text-right text-sm" v-html="form.errors.product || '&nbsp;'"></div>
              </div>
            </div>

            <div class="flex flex-col space-y-2">
              <div class="flex items-center space-x-4">
                <label for="qty" class="uppercase flex-none">qty</label>
                <input type="number" name="qty" v-model="form.qty" min="1" placeholder="QTY" class="rounded border border-slate-300" required>
              </div>

              <div class="text-red-500 text-right text-sm" v-html="form.errors.qty || '&nbsp;'"></div>
            </div>
          </div>

          <div class="flex items-center space-x-2 justify-end">
            <button type="submit" class="bg-green-600 hover:bg-green-700 rounded-md px-3 py-1 text-white text-sm transition-all">
              <div class="flex items-center space-x-1">
                <i class="bx bx-plus"></i>
                <p class="uppercase font-semibold">tambah</p>
              </div>
            </button>
          </div>
        </form>
      </template>
    </Card>
  </AppLayout>

  <transition name="fade">
    <div v-if="open" class="fixed top-0 left-0 w-full h-screen bg-black bg-opacity-40 flex items-center justify-center">
      <form @submit.prevent="store" class="w-full max-w-xl bg-slate-50 rounded-md">
        <div class="flex flex-col rounded-md">
          <div class="flex items-center justify-end space-x-2 bg-slate-200 rounded-t-md p-2">
            <button @click.prevent="close" type="button" class="rounded-md bg-red-500 text-white px-1">
              <i class="bx bx-x text-xl"></i>
            </button>
          </div>

          <div class="flex flex-col space-y-4 p-4">
            <div class="flex flex-col space-y-2">
              <div class="flex items-center space-x-2">
                <label for="code" class="lowercase first-letter:capitalize w-1/3">kode produk</label>
                <input ref="code" type="text" name="code" v-model="create.code" class="bg-transparent rounded-md px-3 py-1 w-full" placeholder="kode produk">
              </div>

              <div v-if="create.errors.code" class="text-right text-sm text-red-500">{{ create.errors.code }}</div>
            </div>

            <div class="flex flex-col space-y-2">
              <div class="flex items-center space-x-2">
                <label for="name" class="lowercase first-letter:capitalize w-1/3">nama produk</label>
                <input ref="name" type="text" name="name" v-model="create.name" class="bg-transparent rounded-md px-3 py-1 w-full" placeholder="nama produk" required>
              </div>

              <div v-if="create.errors.name" class="text-right text-sm text-red-500">{{ create.errors.name }}</div>
            </div>

            <div class="flex flex-col space-y-2">
              <div class="flex items-center space-x-2">
                <label for="barcode" class="lowercase first-letter:capitalize w-1/3">barcode</label>
                <input ref="barcode" type="text" name="barcode" v-model="create.barcode" class="bg-slate-100 rounded-md px-3 py-1 w-full" placeholder="barcode" disabled required>
              </div>

              <div v-if="create.errors.barcode" class="text-right text-sm text-red-500">{{ create.errors.barcode }}</div>
            </div>

            <div class="flex flex-col space-y-2">
              <div class="flex items-center space-x-2">
                <label for="price_buy_unit" class="lowercase first-letter:capitalize w-1/3">harga beli per unit</label>
                <input ref="price_buy_unit" type="text" name="price_buy_unit" @input.prevent="create.price.buy.unit = reformat($event)" class="bg-transparent rounded-md px-3 py-1 w-full" placeholder="harga beli per unit" required>
              </div>

              <div v-if="create.errors.price?.buy?.unit" class="text-right text-sm text-red-500">{{ create.errors.price?.buy?.unit }}</div>
            </div>

            <div class="flex flex-col space-y-2">
              <div class="flex items-center space-x-2">
                <label for="price_sell_unit" class="lowercase first-letter:capitalize w-1/3">harga jual per unit</label>
                <input ref="price_sell_unit" type="text" name="price_sell_unit" @input.prevent="create.price.sell.unit = reformat($event)" class="bg-transparent rounded-md px-3 py-1 w-full" placeholder="harga jual per unit" required>
              </div>

              <div v-if="create.errors.price?.sell?.unit" class="text-right text-sm text-red-500">{{ create.errors.price?.sell?.unit }}</div>
            </div>

            <div class="flex flex-col space-y-2">
              <div class="flex items-center space-x-2">
                <label for="qty" class="lowercase first-letter:capitalize w-1/3">qty</label>
                <input ref="qty" type="number" name="qty" v-model="create.qty" class="bg-transparent rounded-md px-3 py-1 w-full" placeholder="qty" min="1" required>
              </div>

              <div v-if="create.errors.qty" class="text-right text-sm text-red-500">{{ create.errors.qty }}</div>
            </div>
          </div>

          <div class="flex items-center justify-end space-x-2 px-2 py-1 bg-slate-200 rounded-b-md">
            <button class="bg-green-600 hover:bg-green-700 rounded-md px-3 py-1 text-white transition-all text-sm">
              <div class="flex items-center space-x-1">
                <i class="bx bx-check"></i>
                <p class="uppercase font-semibold">buat</p>
              </div>
            </button>
          </div>
        </div>
      </form>
    </div>
  </transition>
</template>