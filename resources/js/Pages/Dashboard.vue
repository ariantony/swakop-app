<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import Welcome from '@/Jetstream/Welcome.vue';
import DatePicker from '@vuepic/vue-datepicker';
import { id } from 'date-fns/locale';
import '@vuepic/vue-datepicker/dist/main.css'
import { useForm, Link } from '@inertiajs/inertia-vue3';
import { nextTick, onMounted, ref } from 'vue';
import axios from 'axios';
import Swal from 'sweetalert2';
import SellingThisMonth from '@/Charts/SellingThisMonth.vue';
import TransactionThisYear from '@/Charts/TransactionThisYear.vue';

const date = ref({
    month: new Date().getMonth(),
    year: new Date().getFullYear(),
})

const format = date => {
    const month = id.localize.month(date.month) 

    return `${month} ${date.year}`
}

const data = ref({
    month: 0,
    today: 0,
    customer: 0,
})

const a = ref(true)

const fetch = async () => {
    a.value = false
    nextTick(() => a.value = true)
    try {
        const response = await axios.post(route('api.dashboard'), date.value)
        data.value.month = response.data.month
        data.value.today = response.data.today
        data.value.customer = response.data.customer
        
        Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 2000,
            timerProgressBar: true,
            onOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        }).fire({
            icon: 'success',
            title: 'Menampilkan data pada periode ' + id.localize.month(date.value.month) + ' - ' + date.value.year
        })

    } catch (e) {
        const response = await Swal.fire({
            title: 'Terjadi kesalahan',
            text: 'Apakah anda ingin mencoba kembali?',
            icon: 'question',
            showCancelButton: true,
            showCloseButton: true,
        })

        response.isConfirmed && fetch()
    }
}

const now = () => {
    const date = new Date().getDate()
    const month = id.localize.month(new Date().getMonth())
    const year = new Date().getFullYear()

    return `${date} ${month} ${year}`
}

onMounted(fetch)
</script>

<template>
    <AppLayout title="Dashboard">
        <div class="flex items-center justify-between">
            <p class="text-3xl font-bold capitalize">dashboard</p>

            <div class="flex items-center space-x-2">
                <DatePicker
                    v-model="date"
                    locale="id"
                    :format="format"
                    @update:modelValue="fetch"
                    monthPicker
                    autoApply />
            </div>
        </div>

        <div class="grid grid-cols-4 gap-4">
            <Link :href="route('transaction.index')" class="bg-primary text-white rounded-xl px-4 py-8 flex flex-col justify-center space-y-1">
                <h1 class="text-3xl capitalize text-center">mulai transaksi</h1>
            </Link>

            <div class="bg-white rounded-xl px-4 py-8 flex items-center space-x-4 justify-between">
                <div class="flex flex-col space-y-1">
                    <p class="text-slate-600 text-md capitalize">penjualan per bulan</p>
                    <p class="text-gray-900 text-2xl capitalize">{{ rupiah(data.month) }}</p>
                </div>

                <i class="bx bx-money-withdraw text-5xl"></i>
            </div>

            <div class="bg-white rounded-xl px-4 py-8 flex items-center space-x-4 justify-between">
                <div class="flex flex-col space-y-1">
                    <p class="text-slate-600 text-md capitalize">Total customer</p>
                    <p class="text-gray-900 text-2xl capitalize">{{ data.customer }}</p>
                </div>

                <i class="bx bx-user-circle text-5xl"></i>
            </div>

            <div class="bg-white rounded-xl px-4 py-8 flex items-center space-x-4 justify-between">
                <div class="flex flex-col space-y-1">
                    <p class="text-slate-600 text-md capitalize">penjualan hari ini</p>
                    <p class="text-gray-900 text-2xl capitalize">{{ rupiah(data.today) }}</p>
                    <p class="text-sm">{{ now() }} <i class="bx bx-calendar text-md"></i></p>
                </div>

                <i class="bx bx-money text-5xl"></i>
            </div>
        </div>

        <div class="flex flex-col space-y-4 pt-2">
            <SellingThisMonth v-if="a && date" :date="date" />
            <TransactionThisYear v-if="a && date" :date="date" />
        </div>
    </AppLayout>
</template>
