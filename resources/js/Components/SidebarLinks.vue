<script setup>
import { getCurrentInstance, ref } from 'vue';
import Button from '@/Components/Button.vue';

const { active, text, icon } = defineProps({
  active: Boolean,
  text: String,
  icon: String,
})

const open = ref(active)
</script>

<template>
  <div class="relative flex flex-col space-y-2 .a">
    <div class="flex relative cursor-pointer">
      <Button class="w-full" :iconClass="`bx ${icon}`" :text="text" :active="open" :isDropdown="true" @click.prevent="open = ! open" />
      <div class="flex absolute right-0 top-0 p-2">
          <i class="bx bx-chevron-down transition-all duration-250 text-xl" :class="!open ? 'rotate-0 text-black' : '-rotate-180 text-white'"></i>
      </div>
    </div>

    <transition name="slide-fade">
      <div v-if="open" class="flex flex-col space-y-2 ml-8 transition-all duration-300">
        <slot />
      </div>
    </transition>
  </div>
</template>