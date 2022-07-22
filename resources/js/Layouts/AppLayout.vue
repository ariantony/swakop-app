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

});
</script>

<template>
    <div>
        <Head :title="title" />

        <JetBanner />

        <div class="min-h-screen max-h-screen bg-gray-100 overflow-auto">
            <nav ref="topbar" class="bg-white border-b border-gray-100">
                <!-- Primary Navigation Menu -->
                <div class="max-w-8xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between h-16">
                        <div class="flex">
                            <!-- Logo -->
                            <div class="shrink-0 flex items-center">
                                <Link :href="route('dashboard')">
                                    <img :src="url('assets/images/logo-swakop.png')" class="w-32" alt="Logo Swakop">
                                </Link>
                            </div>

                            <!-- Navigation Links -->
                            <!-- <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                                <JetNavLink :href="route('dashboard')" :active="route().current('dashboard')">
                                    Dashboard
                                </JetNavLink>
                            </div>
                            <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                                <Dropdown>
                                    <template v-slot:trigger="{toggle}">
                                        <button @click.prevent="toggle" class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5 text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition" :class="route().current('masterdata.goods') ? 'border-indigo-400' : 'border-transparent' ">Masterdata</button>
                                    </template>
                                    <template #body>
                                        <transition
                                            enter-active-class="transition ease-out duration-200"
                                            enter-from-class="transform opacity-0 scale-95"
                                            enter-to-class="transform opacity-100 scale-100"
                                            leave-active-class="transition ease-in duration-75"
                                            leave-from-class="transform opacity-100 scale-100"
                                            leave-to-class="transform opacity-0 scale-95"
                                        >
                                            <JetDropdownLink :href="route('masterdata.goods')" :active="route().current('masterdata/goods')">
                                                <div class="flex items-center">
                                                    <div class="flex pr-4 text-sm leading-5 text-gray-700 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 transition"><i class="bx bxr-package mr-2"></i>Barang</div>
                                                </div>
                                            </JetDropdownLink>
                                        </transition>
                                    </template>
                                </Dropdown>
                            </div> -->
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
            <div ref="w" class="flex overflow-auto max-h-screen">
                <div class="flex flex-col space-y-1 bg-white w-72 rounded-md p-2 sidebar-height overflow-y-auto">
                    <Button iconClass="bxs-dashboard" text="Dashboard" :href="route('dashboard')" :active="route().current('dashboard')"/>
                    <SidebarLinks v-if="isAdmin()" :active="route().current('masterdata.*') || route().current('user.*') || route().current('product.*') || route().current('burden.*')" text="Master Data" icon="bx-data">
                        <Button iconClass="bx-package" text="Produk" :href="route('product.index')" :active="route().current('product.*')"/>
                        <Button iconClass="bx-user" text="User" :href="route('user.index')" :active="route().current('user.*')"/>
                        <Button iconClass="bx-bookmark" text="Beban" :href="route('burden.index')" :active="route().current('burden.*')"/>
                    </SidebarLinks>
                    <SidebarLinks :active="route().current('transaction.*') || route().current('in.*')" text="Transaksi" icon="bx-dollar-circle">
                        <Button iconClass="bx-cart-add" text="Penjualan" :href="route('transaction.index')" :active="route().current('transaction.index')"/>
                        <Button v-if="isAdmin()" iconClass="bxs-inbox" text="Stok Masuk" :href="route('in.index')" :active="route().current('in.*')"/>
                        <Button iconClass="bx-history" text="Riwayat Transaksi" :href="route('transaction.history')" :active="route().current('transaction.history')"/>
                    </SidebarLinks>
                    <SidebarLinks :active="route().current('presence.*')" text="Laporan" icon="bxs-report">
                        <Button v-if="isAdmin()" iconClass="bxs-file" text="Laba Rugi" :active="false"/>
                        <Button iconClass="bx-receipt" text="Harian" :active="false"/>
                        <Button v-if="isAdmin()" iconClass="bxs-user-detail" text="Absensi" :href="route('presence.index')" :active="route().current('presence.*')"/>
                    </SidebarLinks>
                    <Button v-if="isAdmin()" iconClass="bx-cog" text="Pengaturan" :href="route('setting.index')" :active="route().current('setting.*')"/>
                </div>
                <!-- Page Content -->
                <main class="flex flex-col w-full space-y-2 py-10 px-8 overflow-y-auto">
                    <slot />
                </main>
            </div>

            <!-- Page Heading -->
            <!-- <header v-if="$slots.header" class="bg-white shadow">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    <slot name="header" />
                </div>
            </header> -->


        </div>
    </div>
</template>
