<style>
.fade-enter-active, .fade-leave-active {
    transition: all 300ms linear;
    opacity: 1;
}

.fade-enter-from, .fade-leave-to {
    opacity: 0;
}

.slide-fade-enter-active {
    transition: all 0.3s ease-out;
}

.slide-fade-leave-active {
    transition: all 0.3s cubic-bezier(1, 0.5, 0.8, 1);
}

.slide-fade-enter-from,
.slide-fade-leave-to {
    transform: translateY(-20px);
    opacity: 0;
}
</style>

<script setup>
import { getCurrentInstance, onMounted, ref } from 'vue';
import { Inertia } from '@inertiajs/inertia';
import { Head, Link, usePage } from '@inertiajs/inertia-vue3';
import JetApplicationMark from '@/Jetstream/ApplicationMark.vue';
import JetBanner from '@/Jetstream/Banner.vue';
import JetDropdown from '@/Jetstream/Dropdown.vue';
import JetDropdownLink from '@/Jetstream/DropdownLink.vue';
import JetNavLink from '@/Jetstream/NavLink.vue';
import JetResponsiveNavLink from '@/Jetstream/ResponsiveNavLink.vue';
import DropdownLink from '../../../vendor/laravel/jetstream/stubs/inertia/resources/js/Jetstream/DropdownLink.vue';
import Dropdown from '@/Components/Dropdown.vue';
import Button from '@/Components/Button.vue';
import SidebarLinks from '../Components/SidebarLinks.vue';
import axios from 'axios';

defineProps({
    title: String,
});

const sopen = ref(true);

const { $token } = usePage().props.value
axios.defaults.headers.common['Authorization'] = `Bearer ${$token}`

const showingNavigationDropdown = ref(false);

const logout = () => {
    Inertia.post(route('logout'));
};

const self = getCurrentInstance()

const resizeSidebar = () => {
    const { topbar, w } = self.refs

    if (!topbar && !w) return

    const sidebarHeight = window.innerHeight - (topbar.clientHeight);

    w.style.height = `${sidebarHeight}px`;
}

onMounted(() => {
    resizeSidebar();
    window.addEventListener('resize', resizeSidebar);
    window.addEventListener('beforeprint', () => {
        const sidebar = document.getElementById('sidebar')
        if (sidebar) {
            sidebar.style.height = '100%';
        }
    });

    const { $token } = usePage().props.value
    $token && (axios.defaults.headers.common['Authorization'] = `Bearer ${$token}`)
});
</script>

<style>
    .slide-enter-active {
        transition: all 0.3s ease-in-out;
    }

    .slide-leave-active {
        transition: all 0.3s ease-in;
    }

    .slide-enter-from,
    .slide-leave-to {
        transform: translateX(-100px);
        opacity: 0;
    }
</style>

<template>
    <div>
        <Head :title="title" />

        <JetBanner />

        <div class="min-h-screen bg-gray-100 print:bg-white">
            <nav ref="topbar" class="bg-white border-b border-gray-100 print:hidden">
                <!-- Primary Navigation Menu -->
                <div class="max-w-8xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between h-16">
                        <div class="flex justify-between items-center w-52">
                            <!-- Logo -->
                            <div class="shrink-0 flex items-center">
                                <Link :href="route('dashboard')">
                                    <img :src="url('assets/images/logo-swakop.png')" class="w-32" alt="Logo Swakop">
                                </Link>
                            </div>
                            <button @click="sopen = !sopen" type="button" class="text-2xl w-12 h-12 rounded-md"><i class="bx bx-menu mt-2"></i></button>
                        </div>

                        <div class="hidden sm:flex sm:items-center sm:ml-6">
                            <!-- Settings Dropdown -->
                            <div class="ml-3 relative">
                                <JetDropdown align="right" width="48">
                                    <template #trigger>
                                        <button v-if="$page.props.jetstream.managesProfilePhotos" class="flex text-sm border-2 border-transparent rounded-full focus:outline-none focus:border-gray-300 transition">
                                            <img class="h-8 w-8 rounded-full object-cover" :src="$page.props.user.profile_photo_url" :alt="$page.props.user.name">
                                        </button>

                                        <span v-else class="inline-flex rounded-md">
                                            <button type="button" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition">
                                                {{ $page.props.user.name }}

                                                <svg
                                                    class="ml-2 -mr-0.5 h-4 w-4"
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    viewBox="0 0 20 20"
                                                    fill="currentColor"
                                                >
                                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                                </svg>
                                            </button>
                                        </span>
                                    </template>

                                    <template #content>
                                        <!-- Account Management -->
                                        <div class="block px-4 py-2 text-xs text-gray-400">
                                            Kelola Akun
                                        </div>

                                        <JetDropdownLink :href="route('profile.show')">
                                            <div class="flex items-center">
                                                <i class="bx bx-user-circle mr-2 text-xl"></i> Profil
                                            </div>
                                        </JetDropdownLink>

                                        <div class="border-t border-gray-100" />

                                        <!-- Authentication -->
                                        <form @submit.prevent="logout" class="w-full">
                                            <JetDropdownLink as="button">
                                                <div class="flex items-center">
                                                    <i class="bx bx-log-out mr-2 text-xl"></i> Log Out
                                                </div>
                                            </JetDropdownLink>
                                        </form>
                                    </template>
                                </JetDropdown>
                            </div>
                        </div>

                        <!-- Hamburger -->
                        <div class="-mr-2 flex items-center sm:hidden">
                            <button class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition" @click="showingNavigationDropdown = ! showingNavigationDropdown">
                                <svg
                                    class="h-6 w-6"
                                    stroke="currentColor"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        :class="{'hidden': showingNavigationDropdown, 'inline-flex': ! showingNavigationDropdown }"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M4 6h16M4 12h16M4 18h16"
                                    />
                                    <path
                                        :class="{'hidden': ! showingNavigationDropdown, 'inline-flex': showingNavigationDropdown }"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"
                                    />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Responsive Navigation Menu -->
                <div :class="{'block': showingNavigationDropdown, 'hidden': ! showingNavigationDropdown}" class="sm:hidden">
                    <div class="pt-2 pb-3 space-y-1">
                        <JetResponsiveNavLink :href="route('dashboard')" :active="route().current('dashboard')">
                            Dashboard
                        </JetResponsiveNavLink>
                    </div>

                    <!-- Responsive Settings Options -->
                    <div class="pt-4 pb-1 border-t border-gray-200">
                        <div class="flex items-center px-4">
                            <div v-if="$page.props.jetstream.managesProfilePhotos" class="shrink-0 mr-3">
                                <img class="h-10 w-10 rounded-full object-cover" :src="$page.props.user.profile_photo_url" :alt="$page.props.user.name">
                            </div>

                            <div>
                                <div class="font-medium text-base text-gray-800">
                                    {{ $page.props.user.name }}
                                </div>
                                <div class="font-medium text-sm text-gray-500">
                                    {{ $page.props.user.email }}
                                </div>
                            </div>
                        </div>

                        <div class="mt-3 space-y-1">
                            <JetResponsiveNavLink :href="route('profile.show')" :active="route().current('profile.show')">
                                Profil
                            </JetResponsiveNavLink>

                            <!-- Authentication -->
                            <form method="POST" @submit.prevent="logout">
                                <JetResponsiveNavLink as="button">
                                    Log Out
                                </JetResponsiveNavLink>
                            </form>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Sidebar -->
            <div ref="w" id="sidebar" class="flex print:h-full print:overflow-hidden">
                <transition name="slide" mode="in-out">
                    <div v-if="sopen" class="flex flex-col space-y-1 bg-white w-72 rounded-md p-2 sidebar-height overflow-y-auto print:hidden">
                        <Button iconClass="bxs-dashboard" text="Dashboard" :href="route('dashboard')" :active="route().current('dashboard')"/>
                        <Button v-if="!isAdmin()" iconClass="bx-package" text="Produk" :href="route('product.index')" :active="route().current('product.*')"/>
                        <SidebarLinks v-if="isAdmin()" :active="route().current('masterdata.*') || route().current('user.*') || route().current('product.*') || route().current('burden.*') || route().current('conversion.*') || route().current('group.*') || route().current('pricetag-group.*')" text="Master Data" icon="bx-data">
                            <Button iconClass="bx-package" text="Produk" :href="route('product.index')" :active="route().current('product.*')"/>
                            <Button iconClass="bx-transfer" text="Konversi Stok" :href="route('conversion.index')" :active="route().current('conversion.*')"/>
                            <Button iconClass="bx-user" text="User" :href="route('user.index')" :active="route().current('user.*')"/>
                            <Button iconClass="bx-bookmark" text="Beban" :href="route('burden.index')" :active="route().current('burden.*')"/>
                            <Button iconClass="bx-box" text="Kelompok Barang" :href="route('group.index')" :active="route().current('group.*')"/>
                            <Button iconClass="bx-category" text="Pricetag Group" :href="route('pricetag-group.index')" :active="route().current('pricetag-group.*')"/>
                        </SidebarLinks>
                        <SidebarLinks :active="route().current('transaction.*') || route().current('in.*') || route().current('return-stock.*')" text="Transaksi" icon="bx-dollar-circle">
                            <Button iconClass="bx-cart-add" text="Penjualan" :href="route('transaction.index')" :active="route().current('transaction.index')"/>
                            <Button v-if="isAdmin()" iconClass="bxs-inbox" text="Stok Masuk" :href="route('in.index')" :active="route().current('in.*')"/>
                            <Button v-if="isAdmin()" iconClass="bx-undo" text="Retur Pembelian" :href="route('return-stock.index')" :active="route().current('return-stock.*')"/>
                            <Button v-if="isAdmin()" iconClass="bx-layer-minus" text="Void Penjualan" :href="route('transaction.return.index')" :active="route().current('transaction.return.index')"/>
                            <SidebarLinks :active="route().current('transaction.history') || route().current('transaction.return.history')" text="Riwayat" icon="bx-history">
                                <Button iconClass="bx-money-withdraw" text="Penjualan" :href="route('transaction.history')" :active="route().current('transaction.history')"/>
                                <Button v-if="isAdmin()" iconClass="bx-bookmark-alt-minus" text="Void Penjualan" :href="route('transaction.return.history')" :active="route().current('transaction.return.history')"/>
                            </SidebarLinks>
                        </SidebarLinks>
                        <SidebarLinks :active="route().current('presence.*') || route().current('income.statement.*') || route().current('daily.report.*') || route().current('return.report.*') || route().current('income-statement.*') || route().current('daily-report.*') || route().current('goods-return.*')" text="Laporan" icon="bxs-report">
                            <Button v-if="isAdmin()" iconClass="bxs-file" text="Laba Rugi" :href="route('income.statement.index')" :active="route().current('income.statement.*') || route().current('income-statement.*')"/>
                            <Button iconClass="bx-receipt" text="Penjualan Harian" :href="route('daily.report.index')" :active="route().current('daily.report.*') || route().current('daily-report.*')"/>
                            <Button v-if="isAdmin()" iconClass="bxs-user-detail" text="Absensi" :href="route('presence.index')" :active="route().current('presence.*')"/>
                            <Button v-if="isAdmin()" iconClass="bx-reset" text="Retur Pembelian Barang" :href="route('return.report.index')" :active="route().current('return.report.*') || route().current('goods-return.*')"/>
                        </SidebarLinks>
                        <Button v-if="isAdmin()" iconClass="bx-cog" text="Pengaturan" :href="route('setting.index')" :active="route().current('setting.*')"/>
                    </div>
                </transition>
                <!-- Page Content -->
                <main class="flex flex-col w-full space-y-2 py-10 px-8 overflow-y-auto transition-all">
                    <slot />
                </main>
            </div>

        </div>
    </div>
</template>
