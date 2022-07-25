<script setup>
import { getCurrentInstance, nextTick, onMounted, onUnmounted, ref } from 'vue';
import DataTable from './DataTablePrice.vue'
import { useForm } from '@inertiajs/inertia-vue3';

const { product, close } = defineProps({
  product: Object,
  close: Function,
})

const self = getCurrentInstance()
const render = ref(true)

const reRender = () => {
  render.value = false
  nextTick(() => render.value = true)
}

const form = useForm({
  id: null,
  product_id: product?.id,
  cost_selling_per_unit: '',
  cost_selling_per_box: '',
  cost_selling_per_carton: '',
  price_per_unit: '',
  price_per_box: '',
  price_per_carton: '',
})

const show = ref(true)

const hide = e => {
  if (e.key === 'Escape') {
    show.value = true
    close()
  
    window.addEventListener('keyup', hide)
  }
}

const edit = price => {
  form.id = price.id
  form.product_id = price.product_id
  form.cost_selling_per_unit = price.cost_selling_per_unit
  form.cost_selling_per_box = price.cost_selling_per_box
  form.cost_selling_per_carton = price.cost_selling_per_carton
  form.price_per_unit = price.price_per_unit
  form.price_per_box = price.price_per_box
  form.price_per_carton = price.price_per_carton

  show.value = false

  nextTick(() => {
    Object.keys(form.data()).forEach(key => {
      if (self.refs.hasOwnProperty(key)) {
        reformat(self.refs[key], key, form[key])
      }
    })
  })
}

const reformat = (e, target, initial) => {
  const value = initial || e.value
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

  e.value = `Rp ${result}`
  form[target] = parseFloat(result.replaceAll('.', '').replaceAll(',', '.'))
}

const store = () => {
  return form.post(route('price.store'), {
    onSuccess() {
      show.value = true
      nextTick(reRender)
    },

    onError() {
      show.value = false
    }
  })
}

const update = () => {
  return form.patch(route('price.update', form.id), {
    onSuccess() {
      show.value = true
      nextTick(reRender)
    },

    onError() {
      show.value = false
    },
  })
}

const submit = () => form.id ? update() : store()

onMounted(() => window.addEventListener('keyup', hide))
onUnmounted(() => window.removeEventListener('keyup', hide))
</script>

<template>
  <div v-if="show" class="fixed top-0 left-0 w-full h-screen flex items-center justify-center">
    <div class="flex flex-col bg-slate-50 w-full max-w-7xl max-w rounded-md">
      <div class="flex items-center justify-between space-x-2 bg-slate-200 rounded-t-md py-2 px-4">
        <div class="lowercase first-letter:capitalize text-2xl font-semibold">harga produk <span class="uppercase">"{{ product.name }}"</span></div>

        <div class="flex items-center space-x-4">
          <button @click.prevent="show = false" class="rounded-md bg-green-600 text-white px-3 py-1 flex items-center">
            <i class="bx bx-plus text-lg pr-2"></i> Tambah
          </button>

          <button @click.prevent="close" type="button" class="rounded-md bg-red-500 text-white px-2 py-1 flex items-center">
            <i class="bx bx-x text-xl"></i>
          </button>
        </div>
      </div>

      <div class="p-4 max-h-96 overflow-auto">
        <DataTable v-if="render" :product="product" :edit="edit" />
      </div>
    </div>
  </div>

  <div v-else class="fixed top-0 left-0 w-full h-screen flex items-center justify-center">
    <form @submit.prevent="submit" class="flex flex-col bg-slate-50 w-full max-w-5xl rounded-md">
      <div class="flex items-center justify-between space-x-2 bg-slate-200 rounded-t-md py-2 px-4">
        <div class="lowercase first-letter:capitalize text-2xl font-semibold">tambah harga produk <span class="uppercase">"{{ product.name }}"</span></div>

        <div class="flex items-center space-x-2">
          <button @click.prevent="show = true" type="button" class="rounded-md bg-red-500 text-white px-1">
            <i class="bx bx-x text-xl"></i>
          </button>
        </div>
      </div>

      <div class="p-4 max-h-96 overflow-auto">
        <div class="flex flex-col space-y-2">
          <div class="flex justify-center text-red-500">
            Penambahan harga baru akan memperbarui harga sebelumnya, dan harga tersebut akan digunakan untuk transaksi.
          </div>
          <div class="flex flex-col space-y-2">
            <div class="flex items-center space-x-2">
              <label for="cost_selling_per_unit" class="lowercase first-letter:capitalize w-1/3">Harga pokok per satuan</label>
              <input ref="cost_selling_per_unit" @input.prevent="reformat($event.target, 'cost_selling_per_unit')" type="text" class="bg-transparent border rounded-md w-2/3 text-right" placeholder="Harga pokok per satuan">
            </div>

            <div v-if="form.errors.cost_selling_per_unit" class="text-red-400 text-sm text-right lowercase first-letter:capitalize">{{ form.errors.cost_selling_per_unit }}</div>
          </div>

          <!-- <div class="flex flex-col space-y-2">
            <div class="flex items-center space-x-2">
              <label for="cost_selling_per_box" class="lowercase first-letter:capitalize w-1/3">Harga pokok per box / renceng</label>
              <input ref="cost_selling_per_box" @input.prevent="reformat($event.target, 'cost_selling_per_box')" type="text" class="bg-transparent border rounded-md w-2/3" placeholder="Harga pokok per box / renceng">
            </div>

            <div v-if="form.errors.cost_selling_per_box" class="text-red-400 text-sm text-right lowercase first-letter:capitalize">{{ form.errors.cost_selling_per_box }}</div>
          </div> -->

          <!-- <div class="flex flex-col space-y-2">
            <div class="flex items-center space-x-2">
              <label for="cost_selling_per_carton" class="lowercase first-letter:capitalize w-1/3">Harga pokok per karton</label>
              <input ref="cost_selling_per_carton" @input.prevent="reformat($event.target, 'cost_selling_per_carton')" type="text" class="bg-transparent border rounded-md w-2/3" placeholder="Harga pokok per karton">
            </div>

            <div v-if="form.errors.cost_selling_per_carton" class="text-red-400 text-sm text-right lowercase first-letter:capitalize">{{ form.errors.cost_selling_per_carton }}</div>
          </div> -->
          
          <div class="flex flex-col space-y-2">
            <div class="flex items-center space-x-2">
              <label for="price_per_unit" class="lowercase first-letter:capitalize w-1/3">Harga jual per satuan</label>
              <input ref="price_per_unit" @input.prevent="reformat($event.target, 'price_per_unit')" type="text" class="bg-transparent border rounded-md w-2/3 text-right" placeholder="Harga jual per satuan">
            </div>

            <div v-if="form.errors.price_per_unit" class="text-red-400 text-sm text-right lowercase first-letter:capitalize">{{ form.errors.price_per_unit }}</div>
          </div>

          <!-- <div class="flex flex-col space-y-2">
            <div class="flex items-center space-x-2">
              <label for="price_per_box" class="lowercase first-letter:capitalize w-1/3">Harga jual per box / renceng</label>
              <input ref="price_per_box" @input.prevent="reformat($event.target, 'price_per_box')" type="text" class="bg-transparent border rounded-md w-2/3" placeholder="Harga jual per box / renceng">
            </div>

            <div v-if="form.errors.price_per_box" class="text-red-400 text-sm text-right lowercase first-letter:capitalize">{{ form.errors.price_per_box }}</div>
          </div> -->

          <!-- <div class="flex flex-col space-y-2">
            <div class="flex items-center space-x-2">
              <label for="price_per_carton" class="lowercase first-letter:capitalize w-1/3">Harga jual per karton</label>
              <input ref="price_per_carton" @input.prevent="reformat($event.target, 'price_per_carton')" type="text" class="bg-transparent border rounded-md w-2/3" placeholder="Harga jual per karton">
            </div>

            <div v-if="form.errors.price_per_carton" class="text-red-400 text-sm text-right lowercase first-letter:capitalize">{{ form.errors.price_per_carton }}</div>
          </div> -->
        </div>
      </div>

      <div class="flex items-center justify-end space-x-2 rounded-b-md p-2 bg-slate-200 text-white">
        <button class="flex items-center bg-green-600 rounded-md px-3 py-1 font-semibold">
          <i class="bx bx-save text-xl mr-1"></i> {{ form.id ? 'Perbarui' : 'Tambah' }}
        </button>
      </div>
    </form>
  </div>
</template>