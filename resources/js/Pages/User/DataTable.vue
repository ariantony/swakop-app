<script setup>
import Builder from '@/Components/DataTable/Builder.vue'
import Th from '@/Components/DataTable/Th.vue'

const { edit, destroy } = defineProps({
  edit: Function,
  destroy: Function,
})
</script>

<template>
  <Builder :href="route('api.user.paginate')">
    <template v-slot:thead="table">
      <tr>
        <Th class="px-1 py-2 uppercase" :sortable="false">no</Th>
        <Th class="px-3 py-2 uppercase" :table="table" key="name">name</Th>
        <Th class="px-3 py-2 uppercase" :table="table" key="name">username</Th>
        <Th class="px-3 py-2 uppercase" :table="table" key="name">email</Th>
        <Th class="px-3 py-2 uppercase" :table="table" key="name">roles</Th>
        <Th class="px-3 py-2 uppercase" :sortable="false">action</Th>
      </tr>
    </template>
    <template #tfoot>
      <Th class="p-1 uppercase" :sortable="false">no</Th>
      <Th class="p-1 uppercase" :sortable="false">name</Th>
      <Th class="p-1 uppercase" :sortable="false">username</Th>
      <Th class="p-1 uppercase" :sortable="false">email</Th>
      <Th class="p-1 uppercase" :sortable="false">roles</Th>
      <Th class="p-1 uppercase" :sortable="false">action</Th>
    </template>
    <template v-slot:tbody="{ index, item }">
      <tr>
        <td class="border p-1 text-center">{{ index + 1 }}</td>
        <td class="border p-1 uppercase">{{ item.name }}</td>
        <td class="border p-1 uppercase">{{ item.username }}</td>
        <td class="border p-1 uppercase">{{ item.email }}</td>
        <td class="border p-1">
          <div class="flex-wrap text-white text-xs">
            <button v-for="(role, i) in item.roles" :key="i" class="bg-slate-700 hover:bg-slate-600 rounded-md px-2 py-1 transition-all ease-in-out duration-150 font-semibold uppercase">
              {{ role.name }}
            </button>
          </div>
        </td>
        <td class="border p-1 ">
          <div class="flex items-center justify-center space-x-1 text-white">
            <button @click.prevent="edit(item)" class="bg-blue-600 rounded-md px-3 py-1 text-sm uppercase font-semibold">
              edit
            </button>

            <button @click.prevent="destroy(item)" class="bg-red-600 rounded-md px-3 py-1 text-sm uppercase font-semibold">
              delete
            </button>
          </div>
        </td>
      </tr>
    </template>
  </Builder>
</template>