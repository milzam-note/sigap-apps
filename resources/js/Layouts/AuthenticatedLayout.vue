<script setup>
import { ref } from "vue";
import ApplicationLogo from "@/Components/ApplicationLogo.vue";
import Dropdown from "@/Components/Dropdown.vue";
import DropdownLink from "@/Components/DropdownLink.vue";
import NavLink from "@/Components/NavLink.vue";
import ResponsiveNavLink from "@/Components/ResponsiveNavLink.vue";
import { Link } from "@inertiajs/vue3";

const showingNavigationDropdown = ref(false);
</script>

<template>
    <div class="min-h-screen bg-gray-50 flex flex-col relative z-0">
        <!-- ================= WATERMARK GLOBAL ================= -->
        <div
            class="fixed inset-0 z-[-1] flex items-center justify-center opacity-[0.5] pointer-events-none"
        >
            <img
                src="/watermark.png"
                alt="Watermark"
                class="w-2/3 md:w-1/2 object-contain"
            />
        </div>

        <!-- PERBAIKAN DI SINI: z-10 diubah menjadi z-50 agar dropdown tidak tertutup konten main -->
        <nav class="border-b border-gray-200 bg-white shadow-sm relative z-50">
            <!-- Primary Navigation Menu -->
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex h-16 justify-between">
                    <div class="flex">
                        <!-- Logo -->
                        <div class="flex shrink-0 items-center mt-2">
                            <Link :href="route('dashboard')">
                                <ApplicationLogo
                                    class="block h-12 w-auto fill-current text-gray-800 transition-transform hover:scale-105"
                                />
                            </Link>
                        </div>

                        <!-- Navigation Links -->
                        <div
                            class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex"
                        >
                            <NavLink
                                :href="route('dashboard')"
                                :active="route().current('dashboard')"
                            >
                                Dashboard
                            </NavLink>
                            <NavLink
                                v-if="$page.props.auth.user.role === 'admin'"
                                :href="route('surat.index')"
                                :active="route().current('surat.*')"
                            >
                                Kelola Dokumen
                            </NavLink>
                            <NavLink
                                v-if="$page.props.auth.user.role === 'admin'"
                                :href="route('users.index')"
                                :active="route().current('users.*')"
                            >
                                Manajemen Pengguna
                            </NavLink>
                        </div>
                    </div>

                    <div class="hidden sm:ms-6 sm:flex sm:items-center">
                        <div class="relative ms-3">
                            <Dropdown align="right" width="48">
                                <template #trigger>
                                    <span class="inline-flex rounded-md">
                                        <button
                                            type="button"
                                            class="inline-flex items-center rounded-md border border-transparent bg-white px-3 py-2 text-sm font-medium text-gray-500 transition duration-150 ease-in-out hover:text-gray-700 hover:bg-gray-50 focus:outline-none"
                                        >
                                            <div
                                                class="flex flex-col items-end mr-2 text-right"
                                            >
                                                <span
                                                    class="text-gray-900 font-bold leading-tight"
                                                    >{{
                                                        $page.props.auth.user
                                                            .name
                                                    }}</span
                                                >
                                                <span
                                                    class="text-xs text-indigo-600 font-semibold mt-0.5"
                                                    >{{
                                                        $page.props.auth.user
                                                            .jabatan
                                                    }}</span
                                                >
                                            </div>
                                            <svg
                                                class="-me-0.5 ms-1 h-4 w-4 text-gray-400"
                                                xmlns="http://www.w3.org/2000/svg"
                                                viewBox="0 0 20 20"
                                                fill="currentColor"
                                            >
                                                <path
                                                    fill-rule="evenodd"
                                                    d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                    clip-rule="evenodd"
                                                />
                                            </svg>
                                        </button>
                                    </span>
                                </template>
                                <template #content>
                                    <DropdownLink
                                        :href="route('logout')"
                                        method="post"
                                        as="button"
                                        class="text-red-600 font-medium hover:bg-red-50"
                                    >
                                        Keluar
                                    </DropdownLink>
                                </template>
                            </Dropdown>
                        </div>
                    </div>

                    <!-- Hamburger -->
                    <div class="-me-2 flex items-center sm:hidden">
                        <button
                            @click="
                                showingNavigationDropdown =
                                    !showingNavigationDropdown
                            "
                            class="inline-flex items-center justify-center rounded-md p-2 text-gray-400 transition duration-150 ease-in-out hover:bg-gray-100 hover:text-gray-500 focus:bg-gray-100 focus:text-gray-500 focus:outline-none"
                        >
                            <svg
                                class="h-6 w-6"
                                stroke="currentColor"
                                fill="none"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    :class="{
                                        hidden: showingNavigationDropdown,
                                        'inline-flex':
                                            !showingNavigationDropdown,
                                    }"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M4 6h16M4 12h16M4 18h16"
                                />
                                <path
                                    :class="{
                                        hidden: !showingNavigationDropdown,
                                        'inline-flex':
                                            showingNavigationDropdown,
                                    }"
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
            <div
                :class="{
                    block: showingNavigationDropdown,
                    hidden: !showingNavigationDropdown,
                }"
                class="sm:hidden"
            >
                <div class="space-y-1 pb-3 pt-2 border-t border-gray-100">
                    <ResponsiveNavLink
                        :href="route('dashboard')"
                        :active="route().current('dashboard')"
                        >Dashboard</ResponsiveNavLink
                    >
                    <ResponsiveNavLink
                        v-if="$page.props.auth.user.role === 'admin'"
                        :href="route('surat.index')"
                        :active="route().current('surat.*')"
                        >Kelola Dokumen</ResponsiveNavLink
                    >
                    <ResponsiveNavLink
                        v-if="$page.props.auth.user.role === 'admin'"
                        :href="route('users.index')"
                        :active="route().current('users.*')"
                        >Manajemen Pengguna</ResponsiveNavLink
                    >
                </div>
                <div class="border-t border-gray-200 bg-gray-50 pb-3 pt-4">
                    <div class="px-4">
                        <div class="text-base font-bold text-indigo-900">
                            {{ $page.props.auth.user.name }}
                        </div>
                        <div class="text-sm font-medium text-gray-500">
                            {{ $page.props.auth.user.jabatan }}
                        </div>
                    </div>
                    <div class="mt-3 space-y-1">
                        <ResponsiveNavLink
                            :href="route('logout')"
                            method="post"
                            as="button"
                            class="text-red-600 font-bold"
                            >Keluar</ResponsiveNavLink
                        >
                    </div>
                </div>
            </div>
        </nav>

        <!-- Page Heading -->
        <header
            class="bg-white/80 backdrop-blur-md shadow-sm relative z-10 border-b border-gray-200"
            v-if="$slots.header"
        >
            <div class="mx-auto max-w-7xl px-4 py-5 sm:px-6 lg:px-8">
                <slot name="header" />
            </div>
        </header>

        <!-- Page Content -->
        <main class="flex-grow relative z-10">
            <slot />
        </main>

        <!-- ================= FOOTER GLOBAL ================= -->
        <footer class="bg-white border-t border-gray-200 mt-auto relative z-10">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
                <div class="md:flex md:items-center md:justify-between">
                    <div
                        class="flex justify-center md:justify-start mb-4 md:mb-0"
                    >
                        <img
                            src="/logo.png"
                            alt="Logo"
                            class="h-8 w-auto grayscale opacity-50"
                        />
                    </div>
                    <div class="text-center md:text-right">
                        <p class="text-sm text-gray-500 font-medium">
                            &copy; 2026
                            <span class="text-indigo-600 font-bold">SIGAP</span>
                            - Sistem Informasi Digitalisasi Arsip Perencanaan
                            dan Regulasi.
                        </p>
                        <p class="text-xs text-gray-400 mt-1">
                            Dikembangkan oleh Subbagren Bagrenmin SSDM Polri.
                        </p>
                    </div>
                </div>
            </div>
        </footer>
    </div>
</template>
