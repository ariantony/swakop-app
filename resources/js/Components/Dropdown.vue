<script setup>
    import { getCurrentInstance, nextTick, ref } from 'vue'
    import 'boxicons';

const open = ref(false)
const self = getCurrentInstance()

const toggle = () => {
    open.value = !open.value
    
    nextTick(() => {
        const { parent, child } = self.refs
        if (open.value) {
            child.style.top = `${parent.offsetHeight}px`   
        }
    })
}
</script>

<template>
    <div @focusout="open = false" class="flex relative h-full" ref="parent">
        <slot name="trigger" :toggle="toggle" />
        <transition
            enter-active-class="transition ease-out duration-200"
            enter-from-class="transform opacity-0 scale-95"
            enter-to-class="transform opacity-100 scale-100"
            leave-active-class="transition ease-in duration-75"
            leave-from-class="transform opacity-100 scale-100"
            leave-to-class="transform opacity-0 scale-95"
        >
            <div class="rounded-md ring-1 ring-black ring-opacity-5 py-2 bg-white absolute shadow-md top-0 left-0" v-if="open" ref="child">
                <slot name="body"/>
            </div>
        </transition>
        <div class="flex items-center h-full">
            <box-icon :class="!open ? 'rotate-0' : '-rotate-180'" class="transition-all duration-150" type="regular" size="sm" name="chevron-down"></box-icon>
        </div>
    </div>
</template>