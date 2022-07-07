<script setup>
    import { Link } from '@inertiajs/inertia-vue3';
    import 'boxicons';

    defineProps({
        active: {
            type: Boolean,
            default: false
        },
        withIcon: {
            type: Boolean,
            default: true
        },
        iconType: {
            type: String,
            default: 'regular'
        },
        iconName: {
            type: String,
            default: 'menu'
        },
        iconSize: {
            type: String,
            default: 'md'
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
    <Link v-if="href" :href="href" type="button" class="rounded-lg px-4 py-1 pt-4" :class="active ? 'bg-[#6c5dd3] text-white' : 'bg-slate-50 text-black'">
        <div class="flex">
            <box-icon v-if="withIcon" :type="iconType" :name="iconName" :size="iconSize" :color="active ? 'white' : 'black'"></box-icon>
            {{ text }}
        </div>
    </Link>

    <button v-else type="button" class="rounded-lg px-4 py-1 pt-4" :class="active ? 'bg-[#6c5dd3] text-white' : 'bg-slate-50 text-black'">
        <div class="flex">
            <box-icon v-if="withIcon" :type="iconType" :name="iconName" :size="iconSize" :color="active ? 'white' : 'black'"></box-icon>
            {{ text }}
        </div>
    </button>
</template>