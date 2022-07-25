<script setup>
import { getCurrentInstance, nextTick, onMounted, ref } from 'vue'
import { Link, useForm } from '@inertiajs/inertia-vue3'
import { Inertia } from '@inertiajs/inertia'
import AppLayout from '@/Layouts/AppLayout.vue'
import Card from '@/Components/Card.vue'
import DataTable from './DataTable.vue'
import Swal from 'sweetalert2'
import Datepicker from '@vuepic/vue-datepicker';
import { id } from 'date-fns/locale';
import '@vuepic/vue-datepicker/dist/main.css'

const self = getCurrentInstance()
const render = ref(true)
const open = ref(false)
const form = useForm({
  id: null,
  name: '',
  cost: '',
  period: '',
})
const burdens = ref([])

const show = () => {
  open.value = true

  nextTick(() => {
    if(form.errors.name) self.refs.first.focus()
    else if(form.errors.cost) self.refs.second.focus()
    else if(form.errors.period) self.refs.third.focus()
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
  return form.post(route('burden.store'), {
    onSuccess: () => reset(),
    onError: () => nextTick(show),
  })
}

const edit = burden => {
  form.id = burden.id
  form.name = burden.name
  form.cost = burden.cost
  form.period = {
    month: burden.period.substring(4, 6) - 1,
    year: burden.period.substring(0, 4),
  }

  reformat(form.cost)
  show()
}

const update = () => {
  return form.patch(route('burden.update', form.id), {
    onSuccess: () => reset(),
    onError: () => nextTick(show),
  })
}

const destroy = burden => {
  return Swal.fire({
    title: 'Konfirmasi!',
    html: 'Anda yakin akan menghapus beban pengeluaran ini?',
    icon: 'question',
    showCancelButton: true,
  }).then(response => {
    if (response.isConfirmed) {
      return Inertia.delete(route('burden.destroy', burden.id), {
        onSuccess: () => reset(),
      })
    }
  })
}

const submit = () => form.id ? update() : store()

const reformat = (value) => {
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

  nextTick(() => {
    self.refs.second.value = `Rp ${result}`
    form.cost = parseInt(result.replaceAll('.', ''))
  })
}

const format = date => {
  const month = id.localize.month(date.month) 
  return `${month} ${date.year}`
}

onMounted(() => {
  window.addEventListener('keyup', e => {
    if (e.key === 'Escape' && open.value) {
      close()
      form.reset()
    }
  })
})
</script>

<style src="@vueform/multiselect/themes/default.css"></style>

<template>
  <AppLayout title="Burden">
    <Card>
      <template #header>
        <div class="flex items-center justify-between space-x-2 text-white p-2">
          <h1 class="ml-4 text-black text-2xl font-semibold">Beban Pengeluaran</h1>
          <button @click.prevent="show" class="bg-green-600 rounded-md px-3 py-1 font-semibold">
            <div class="flex items-center">
              <i class="bx bx-plus mr-1 text-xl"></i> Tambah
            </div>
          </button>
        </div>
      </template>
      <template #body>
        <DataTable v-if="render" :edit="edit" :destroy="destroy" :toggle="toggle" />
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
              <h1 class="ml-2 text-black text-2xl font-semibold">{{ form.id ? 'Edit' : 'Tambah' }} Beban Pengeluaran</h1>
              <button @click.prevent="close" type="button" class="border rounded-md bg-red-500 text-white px-1">
                <i class="bx bx-x text-xl"></i>
              </button>
            </div>
          </template>

          <template #body>
            <div class="flex flex-col space-y-2">
              <div class="flex flex-col space-y-2">
                <div class="flex items-center space-x-2">
                  <label for="name" class="lowercase first-letter:capitalize w-1/4">nama beban</label>
                  <input ref="first" type="text" name="name" v-model="form.name" class="w-3/4 bg-transparent border border-slate-200 rounded-md placeholder:capitalize" autocomplete="off" placeholder="nama">
                </div>
                <div v-if="form.errors.name" class="text-right text-red-400 text-sm lowercase first-letter:capitalize">{{ form.errors.name }}</div>
              </div>

              <div class="flex flex-col space-y-2">
                <div class="flex items-center space-x-2">
                  <label for="cost" class="lowercase first-letter:capitalize w-1/4">biaya</label>
                  <input @input.prevent="reformat(this.$refs.second.value)" ref="second" type="text" name="cost" class="w-3/4 bg-transparent border border-slate-200 rounded-md placeholder:capitalize text-right" autocomplete="off" placeholder="biaya">
                </div>
                <div v-if="form.errors.cost" class="text-right text-red-400 text-sm lowercase first-letter:capitalize">{{ form.errors.cost }}</div>
              </div>

              <div class="flex flex-col space-y-2">
                <div class="flex items-center space-x-2">
                  <label for="cost" class="lowercase first-letter:capitalize w-1/4">Periode</label>
                  <Datepicker 
                      v-model="form.period"
                      ref="third"
                      class="w-full flex-1"
                      locale="id"
                      :format="format"
                      autoApply
                      position="right"
                      monthPicker
                    />
                </div>
                <div v-if="form.errors.period" class="text-right text-red-400 text-sm lowercase first-letter:capitalize">{{ form.errors.period }}</div>
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