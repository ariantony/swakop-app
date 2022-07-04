<script setup>
    import { getCurrentInstance, nextTick, ref } from 'vue'
    
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
    <div class="flex relative h-full" ref="parent">
        <slot name="trigger" :toggle="toggle" />
        <div class="rounded-md ring-1 ring-black ring-opacity-5 py-2 bg-white absolute shadow-md top-0 left-0" v-if="open" ref="child">
            <slot name="body"/>
        </div>
    </div>
</template>