<script setup>
    import { Link } from '@inertiajs/inertia-vue3';

    defineProps({
        active: {
            type: Boolean,
            default: false
        },
        withIcon: {
            type: Boolean,
            default: true
        },
        iconClass: {
            type: String,
            default: ''
        },
        text: {
            type: String,
            default: ''
        },
        href: {
            type: String,
        },
    })
</script>

<template>
    <Link v-if="href" :href="href" type="button" class="rounded-lg px-4 py-2" :class="active ? 'bg-[#6c5dd3] text-white' : 'bg-slate-50 text-black'">
        <div class="flex items-center">
            <i v-if="withIcon" class="mr-2 text-lg bx" :class="iconClass + (active ? ' text-white' : ' text-black')"></i>
            {{ text }}
        </div>
    </Link>

    <button v-else type="button" class="rounded-lg px-4 py-2" :class="active ? 'bg-[#6c5dd3] text-white' : 'bg-slate-50 text-black'">
        <div class="flex items-center">
            <i v-if="withIcon" class="mr-2 text-lg bx" :class="iconClass + (active ? ' text-white' : ' text-black')"></i>
            {{ text }}
        </div>
    </button>
</template>