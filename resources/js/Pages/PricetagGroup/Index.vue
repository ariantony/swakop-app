<script setup>
import { getCurrentInstance, nextTick, onMounted, ref } from 'vue'
import { useForm } from '@inertiajs/inertia-vue3'
import { Inertia } from '@inertiajs/inertia'
import AppLayout from '@/Layouts/AppLayout.vue'
import Card from '@/Components/Card.vue'
import DataTable from './DataTable.vue'
import Swal from 'sweetalert2'
import Select from '@vueform/multiselect'

const self = getCurrentInstance()
const render = ref(true)
const open = ref(false)
const products = ref([])
const form = useForm({
  id: null,
  name: '',
  products: [],
})

const show = () => {
  open.value = true

  nextTick(() => {
    if(form.errors.name) self.refs.first.focus()
    else if(form.errors.products) self.refs.second.focus()
  })
}

const close = () => {
  open.value = false
  form.reset()
}

const reset = () => {
  render.value = false
  nextTick(() => render.value = true)
  form.reset()
  close()
}

const store = () => {
  return form.post(route('pricetag-group.store'), {
    onSuccess: () => reset(),
    onError: () => nextTick(show),
  })
}

const edit = group => {
  const products = [...group.products.map(p => p.id)]
  form.id = group.id
  form.name = group.name
  form.products = products

  show()
}

const update = () => {
  return form.patch(route('pricetag-group.update', form.id), {
    onSuccess: () => reset(),
    onError: () => nextTick(show),
  })
}

const destroy = group => {
  return Swal.fire({
    title: 'Konfirmasi!',
    html: 'Anda yakin akan menghapus kelompok pricetag ini?',
    icon: 'question',
    showCancelButton: true,
  }).then(response => {
    if (response.isConfirmed) {
      return Inertia.delete(route('pricetag-group.destroy', group.id), {
        onSuccess: () => reset(),
      })
    }
  })
}

const remove = (group, product) => {
  return Swal.fire({
    title: 'Konfirmasi!',
    html: `Anda yakin akan menghapus <span class="font-bold">${product.name}</span> dari kelompok pricetag <span class="font-bold">${new String(group.name).toUpperCase()}</span>?`,
    icon: 'question',
    showCancelButton: true,
  }).then(response => {
    if (response.isConfirmed) {
      return Inertia.delete(route('pricetag-group.remove', [group.id, product.id]), {
        onSuccess: () => reset(),
      })
    }
  })
}

const submit = () => form.id ? update() : store()

const fetch = async () => {
  try {
    const response = await axios.get(route('api.product.only.name'))
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

onMounted(() => {
  window.addEventListener('keyup', e => {
    if (e.key === 'Escape' && open.value) {
      close()
      form.reset()
    }
  })

  fetch()
})
</script>

<style src="@vueform/multiselect/themes/default.css"></style>

<template>
  <AppLayout title="Pricetag Group">
    <Card>
      <template #header>
        <div class="flex items-center justify-between space-x-2 text-white p-2">
          <h1 class="ml-4 text-black text-2xl font-semibold">Pricetag Group</h1>
          <button @click.prevent="show" class="bg-green-600 rounded-md px-3 py-1 font-semibold">
            <div class="flex items-center">
              <i class="bx bx-plus mr-1 text-xl"></i> Tambah
            </div>
          </button>
        </div>
      </template>
      <template #body>
        <DataTable v-if="render" :edit="edit" :destroy="destroy" :remove="remove" />
      </template>
    </Card>
  </AppLayout>

  <transition name="fade">
    <div  v-if="open" class="fixed top-0 left-0 w-full h-screen bg-slate-600 bg-opacity-70"></div>
  </transition>
  <transition name="slide-fade">
    <div v-if="open" class="fixed top-0 left-0 w-full h-screen flex items-center justify-center">
      <form @submit.prevent="submit" class="relative w-full max-w-3xl bg-slate-100 rounded-md z-10">
        <Card>
          <template #header>
            <div class="flex items-center justify-between space-x-2 p-2">
              <h1 class="ml-2 text-black text-2xl font-semibold">{{ form.id ? 'Edit' : 'Tambah' }} Pricetag Group</h1>
              <button @click.prevent="close" type="button" class="border rounded-md bg-red-500 text-white px-1">
                <i class="bx bx-x text-xl"></i>
              </button>
            </div>
          </template>

          <template #body>
            <div class="flex flex-col space-y-2">
              <div class="flex flex-col space-y-2">
                <div class="flex items-center space-x-2">
                  <label for="name" class="lowercase first-letter:capitalize w-1/4">nama</label>
                  <input ref="first" type="text" name="name" v-model="form.name" class="w-3/4 bg-transparent border border-slate-200 rounded-md placeholder:capitalize uppercase" autocomplete="off" placeholder="nama">
                </div>
                <div v-if="form.errors.name" class="text-right text-red-400 text-sm lowercase first-letter:capitalize">{{ form.errors.name }}</div>
              </div>

              <div class="flex flex-col space-y-2">
                <div class="flex items-center space-x-2">
                  <label for="cost" class="lowercase first-letter:capitalize w-1/4">produk</label>
                  <div class="w-3/4">
                    <Select  
                      ref="second"
                      v-model="form.products"
                      mode="tags"
                      :options="products.map(p => ({
                        label: `${p.name} ${p.barcode != null ? '- ' + p.barcode : ''}`,
                        value: p.id,
                      }))"
                      :searchable="true"
                      :hideSelected="false"
                      :closeOnSelect="false"
                      noOptionsText="Mohon tunggu..."
                    />
                  </div>
                </div>
                <div v-if="form.errors.products" class="text-right text-red-400 text-sm lowercase first-letter:capitalize">{{ form.errors.products }}</div>
              </div>
            </div>
          </template>

          <template #footer>
            <div class="flex items-center justify-end space-x-2 p-2 text-white">
              <button class="flex items-center bg-green-600 rounded-md px-3 py-1 font-semibold">
                <i class="bx bx-save text-xl mr-1"></i>
                {{ form.id ? 'Perbarui' : 'Tambah' }}
              </button>
            </div>
          </template>
        </Card>
      </form>
    </div>
  </transition>
</template>