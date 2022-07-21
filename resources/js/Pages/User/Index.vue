<script setup>
import { getCurrentInstance, nextTick, onMounted, ref } from 'vue'
import { Link, useForm } from '@inertiajs/inertia-vue3'
import { Inertia } from '@inertiajs/inertia'
import AppLayout from '@/Layouts/AppLayout.vue'
import Card from '@/Components/Card.vue'
import DataTable from './DataTable.vue'
import Swal from 'sweetalert2'
import { rupiah } from '../../common'

const self = getCurrentInstance()
const render = ref(true)
const open = ref(false)
const form = useForm({
  id: null,
  name: '',
  username: '',
  email: '',
  password: '',
  password_confirmation: '',
  basic_salary: 0,
})
const users = ref([])

const show = () => {
  open.value = true

  nextTick(() => {
    if(form.errors.name) self.refs.first.focus()
    else if(form.errors.username) self.refs.second.focus()
    else if(form.errors.email) self.refs.third.focus()
    else if(form.errors.password) self.refs.fourth.focus()
    else if (form.errors.password_confirmation) self.refs.fifth.focus()
    else if (form.errors.basic_salary) self.refs.sixth.focus()
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
  return form.post(route('user.store'), {
    onSuccess: () => reset(),
    onError: () => nextTick(show),
  })
}

const edit = user => {
  form.id = user.id
  form.name = user.name
  form.username = user.username
  form.email = user.email
  form.password = ''
  form.password_confirmation = ''
  form.basic_salary = user.basic_salary

  nextTick(() => {
    self.refs.sixth.value = rupiah(form.basic_salary)
  })
  show()
}

const update = () => {
  return form.patch(route('user.update', form.id), {
    onSuccess: () => reset(),
    onError: () => nextTick(show),
  })
}

const destroy = user => {
  return Swal.fire({
    title: 'Konfirmasi!',
    html: 'Anda yakin akan menghapus akun ini? Semua pencatatan data yang berhubungan akun ini akan ikut terhapus.',
    icon: 'question',
    showCancelButton: true,
  }).then(response => {
    if (response.isConfirmed) {
      return Inertia.delete(route('user.destroy', user.id), {
        onSuccess: () => reset(),
      })
    }
  })
}

const submit = () => form.id ? update() : store()

const reformat = () => {
  var val = new String(self.refs.sixth.value),
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
    self.refs.sixth.value = `Rp ${result}`
    form.basic_salary = parseInt(result.replaceAll('.', ''))
  })
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

<template>
  <AppLayout title="User">
    <Card>
      <template #header>
        <div class="flex items-center justify-between space-x-2 text-white p-2">
          <h1 class="ml-4 text-black text-2xl font-semibold">User</h1>
          <button @click.prevent="show" class="bg-green-600 rounded-md px-3 py-1 font-semibold">
            <div class="flex items-center">
              <i class="bx bx-plus mr-1 text-xl"></i> Tambah
            </div>
          </button>
        </div>
      </template>
      <template #body>
        <DataTable v-if="render" :edit="edit" :destroy="destroy" />
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
              <h1 class="ml-2 text-black text-2xl font-semibold">{{ form.id ? 'Edit' : 'Tambah' }} User</h1>
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
                  <input ref="first" type="text" name="name" v-model="form.name" class="w-3/4 bg-transparent border border-slate-200  rounded-md placeholder:capitalize" autocomplete="off" placeholder="nama">
                </div>
                <div v-if="form.errors.name" class="text-right text-red-400 text-sm lowercase first-letter:capitalize">{{ form.errors.name }}</div>
              </div>

              <div class="flex flex-col space-y-2">
                <div class="flex items-center space-x-2">
                  <label for="username" class="lowercase first-letter:capitalize w-1/4">username</label>
                  <input ref="second" type="text" name="username" v-model="form.username" class="w-3/4 bg-transparent border border-slate-200 rounded-md lowercase placeholder:capitalize" autocomplete="off" placeholder="username">
                </div>
                <div v-if="form.errors.username" class="text-right text-red-400 text-sm lowercase first-letter:capitalize">{{ form.errors.username }}</div>
              </div>

              <div class="flex flex-col space-y-2">
                <div class="flex items-center space-x-2">
                  <label for="email" class="lowercase first-letter:capitalize w-1/4">email</label>
                  <input ref="third" type="email" name="email" v-model="form.email" class="w-3/4 bg-transparent border border-slate-200 rounded-md placeholder:capitalize" autocomplete="off" placeholder="email">
                </div>
                <div v-if="form.errors.email" class="text-right text-red-400 text-sm lowercase first-letter:capitalize">{{ form.errors.email }}</div>
              </div>

              <div class="flex flex-col space-y-2">
                <div class="flex items-center space-x-2">
                  <label for="password" class="lowercase first-letter:capitalize w-1/4">password</label>
                  <input ref="fourth" type="password" name="password" v-model="form.password" class="w-3/4 bg-transparent border border-slate-200 rounded-md uppercase placeholder:capitalize" autocomplete="off" placeholder="password">
                </div>
                <div v-if="form.errors.password" class="text-right text-red-400 text-sm lowercase first-letter:capitalize">{{ form.errors.password }}</div>
              </div>

              <div class="flex flex-col space-y-2">
                <div class="flex items-center space-x-2">
                  <label for="password_confirmation" class="lowercase first-letter:capitalize w-1/4">konfirmasi password</label>
                  <input ref="fifth" type="password" name="password_confirmation" v-model="form.password_confirmation" class="w-3/4 bg-transparent border border-slate-200 rounded-md uppercase placeholder:capitalize" autocomplete="off" placeholder="konfirmasi Password">
                </div>
                <div v-if="form.errors.password_confirmation" class="text-right text-red-400 text-sm lowercase first-letter:capitalize">{{ form.errors.password_confirmation }}</div>
              </div>

              <div class="flex flex-col space-y-2">
                <div class="flex items-center space-x-2">
                  <label for="cost" class="lowercase first-letter:capitalize w-1/4">gaji pokok</label>
                  <input ref="sixth" @input.prevent="reformat()" type="text" name="basic_salary" class="w-3/4 bg-transparent border border-slate-200 rounded-md placeholder:capitalize" autocomplete="off" placeholder="gaji pokok">
                </div>
                <div v-if="form.errors.basic_salary" class="text-right text-red-400 text-sm lowercase first-letter:capitalize">{{ form.errors.basic_salary }}</div>
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