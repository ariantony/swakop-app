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
    <Button :iconClass="`bx bx-${icon}`" :text="text" :active="open" @click.prevent="open = ! open" />

    <transition name="fade">
      <div v-if="open" class="flex flex-col space-y-2 ml-8 transition-all duration-300">
        <slot />
      </div>
    </transition>
  </div>
</template>