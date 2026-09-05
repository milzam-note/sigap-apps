<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, usePage } from "@inertiajs/vue3";

// Menerima data dari DashboardController
defineProps({
    stats: Object,
    recent_documents: Array,
    userRole: String,
});

// Mengambil data user yang sedang login dari global props Inertia
const user = usePage().props.auth.user;

// Fungsi format tanggal sederhana
const formatDate = (dateString) => {
    if (!dateString) return "-";
    const date = new Date(dateString);
    return date.toLocaleDateString("id-ID", {
        day: "numeric",
        month: "short",
        year: "numeric",
    });
};
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <div class="py-8">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- GRID UTAMA -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- ================= BAGIAN KIRI ================= -->
                    <div class="lg:col-span-2 space-y-6">
                        <!-- 1. WELCOME BANNER -->
                        <div
                            class="bg-gradient-to-r from-indigo-50 to-blue-50 rounded-2xl p-6 sm:p-8 flex flex-col sm:flex-row items-center justify-between shadow-sm border border-indigo-100"
                        >
                            <div class="mb-4 sm:mb-0">
                                <h2
                                    class="text-2xl font-bold text-indigo-900 mb-2"
                                >
                                    Selamat datang kembali, {{ user.name }}!
                                </h2>
                                <p
                                    class="text-indigo-700 text-sm mb-4 max-w-md"
                                >
                                    Kelola dokumen perencanaan, pantau statistik
                                    arsip, dan pertahankan tingkat keamanan data
                                    administrasi ke level berikutnya.
                                </p>
                                <!-- TOMBOL INI HANYA TAMPIL UNTUK ADMIN -->
                                <Link
                                    v-if="user.role === 'admin'"
                                    :href="route('surat.index')"
                                    class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 transition"
                                >
                                    Buka Ruang Kerja
                                </Link>
                            </div>
                            <!-- Ilustrasi Sederhana -->
                            <div class="hidden sm:block">
                                <svg
                                    class="w-32 h-32 text-indigo-300"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                    xmlns="http://www.w3.org/2000/svg"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="1.5"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                                    ></path>
                                </svg>
                            </div>
                        </div>

                        <!-- 2. TABEL DOKUMEN TERBARU -->
                        <div
                            class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden"
                        >
                            <div class="p-6 border-b border-gray-100">
                                <h3 class="text-lg font-bold text-gray-900">
                                    Dokumen Terbaru
                                </h3>
                            </div>
                            <div class="overflow-x-auto">
                                <table
                                    class="min-w-full divide-y divide-gray-100"
                                >
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider"
                                            >
                                                Nama Dokumen
                                            </th>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider"
                                            >
                                                Dibuat Pada
                                            </th>
                                            <th
                                                class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider"
                                            >
                                                Aksi
                                            </th>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider"
                                            >
                                                Status
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody
                                        class="bg-white divide-y divide-gray-100"
                                    >
                                        <tr
                                            v-for="surat in recent_documents"
                                            :key="surat.id"
                                            class="hover:bg-gray-50"
                                        >
                                            <td
                                                class="px-6 py-4 whitespace-nowrap"
                                            >
                                                <div class="flex items-center">
                                                    <div
                                                        class="flex-shrink-0 h-8 w-8 bg-red-100 text-red-600 rounded flex items-center justify-center font-bold text-xs"
                                                    >
                                                        PDF
                                                    </div>
                                                    <div class="ml-4">
                                                        <div
                                                            class="text-sm font-medium text-gray-900"
                                                        >
                                                            {{
                                                                surat.nama_surat
                                                            }}
                                                        </div>
                                                        <div
                                                            class="text-xs text-gray-500"
                                                        >
                                                            {{
                                                                surat.nomor_surat
                                                            }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td
                                                class="px-6 py-4 whitespace-nowrap"
                                            >
                                                <div
                                                    class="text-sm text-gray-900"
                                                >
                                                    {{
                                                        formatDate(
                                                            surat.created_at,
                                                        )
                                                    }}
                                                </div>
                                                <div
                                                    class="text-xs text-gray-500"
                                                >
                                                    {{
                                                        surat.pembuat
                                                            ? surat.pembuat.name
                                                            : "Sistem"
                                                    }}
                                                </div>
                                            </td>
                                            <td
                                                class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium"
                                            >
                                                <div
                                                    class="flex items-center justify-center space-x-2"
                                                >
                                                    <a
                                                        :href="
                                                            route(
                                                                'surat.lihat',
                                                                surat.id,
                                                            )
                                                        "
                                                        target="_blank"
                                                        class="text-indigo-600 hover:text-indigo-900 bg-indigo-50 px-3 py-1 rounded transition-colors"
                                                    >
                                                        Lihat
                                                    </a>
                                                    <a
                                                        :href="
                                                            route(
                                                                'surat.unduh',
                                                                surat.id,
                                                            )
                                                        "
                                                        class="text-green-600 hover:text-green-900 bg-green-50 px-3 py-1 rounded transition-colors"
                                                    >
                                                        Unduh
                                                    </a>
                                                </div>
                                            </td>
                                            <td
                                                class="px-6 py-4 whitespace-nowrap"
                                            >
                                                <span
                                                    :class="
                                                        surat.sifat_surat ===
                                                        'rahasia'
                                                            ? 'bg-red-100 text-red-700'
                                                            : 'bg-green-100 text-green-700'
                                                    "
                                                    class="px-3 py-1 inline-flex text-xs leading-5 font-bold rounded-full uppercase"
                                                >
                                                    {{ surat.sifat_surat }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr
                                            v-if="recent_documents.length === 0"
                                        >
                                            <td
                                                colspan="4"
                                                class="px-6 py-8 text-center text-gray-500 text-sm"
                                            >
                                                Belum ada dokumen yang diunggah.
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- ================= BAGIAN KANAN ================= -->
                    <div class="space-y-6">
                        <h3 class="text-lg font-bold text-gray-900 pt-2">
                            Statistik Data
                        </h3>

                        <!-- 3. GRID STATISTIK KARTU (2x2) -->
                        <div class="grid grid-cols-2 gap-4">
                            <!-- Total Dokumen -->
                            <div
                                class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex flex-col justify-between h-32 relative overflow-hidden group"
                            >
                                <h4
                                    class="text-sm font-medium text-gray-500 relative z-10"
                                >
                                    Total Dokumen
                                </h4>
                                <p
                                    class="text-4xl font-bold text-gray-900 relative z-10"
                                >
                                    {{ stats.total }}
                                </p>
                                <svg
                                    class="w-16 h-16 absolute -bottom-2 -right-2 text-gray-100 group-hover:scale-110 transition-transform"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"
                                    ></path>
                                </svg>
                            </div>
                            <!-- Total Interaksi -->
                            <div
                                class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex flex-col justify-between h-32 relative overflow-hidden group"
                            >
                                <h4
                                    class="text-sm font-medium text-gray-500 relative z-10"
                                >
                                    Total Interaksi
                                </h4>
                                <p
                                    class="text-4xl font-bold text-gray-900 relative z-10"
                                >
                                    {{ stats.interaksi }}
                                </p>
                                <svg
                                    class="w-16 h-16 absolute -bottom-2 -right-2 text-gray-100 group-hover:scale-110 transition-transform"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"
                                    ></path>
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"
                                    ></path>
                                </svg>
                            </div>
                            <!-- Dokumen Umum -->
                            <div
                                class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex flex-col justify-between h-32 relative overflow-hidden group"
                            >
                                <h4
                                    class="text-sm font-medium text-gray-500 relative z-10"
                                >
                                    Dokumen Umum
                                </h4>
                                <p
                                    class="text-4xl font-bold text-indigo-600 relative z-10"
                                >
                                    {{ stats.umum }}
                                </p>
                                <svg
                                    class="w-16 h-16 absolute -bottom-2 -right-2 text-indigo-50 group-hover:scale-110 transition-transform"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"
                                    ></path>
                                </svg>
                            </div>
                            <!-- Dokumen Rahasia -->
                            <div
                                class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex flex-col justify-between h-32 relative overflow-hidden group"
                            >
                                <h4
                                    class="text-sm font-medium text-gray-500 relative z-10"
                                >
                                    Dokumen Rahasia
                                </h4>
                                <p
                                    class="text-4xl font-bold text-red-600 relative z-10"
                                >
                                    {{ stats.rahasia }}
                                </p>
                                <svg
                                    class="w-16 h-16 absolute -bottom-2 -right-2 text-red-50 group-hover:scale-110 transition-transform"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"
                                    ></path>
                                </svg>
                            </div>
                        </div>

                        <!-- 4. LOG AKTIVITAS TERAKHIR -->
                        <div
                            class="bg-gradient-to-br from-indigo-900 to-blue-800 rounded-2xl p-6 text-white shadow-md relative overflow-hidden"
                        >
                            <!-- Hiasan Background -->
                            <div class="absolute -right-10 -top-10 opacity-10">
                                <svg
                                    width="150"
                                    height="150"
                                    viewBox="0 0 24 24"
                                    fill="currentColor"
                                >
                                    <path
                                        d="M12 22C6.477 22 2 17.523 2 12S6.477 2 12 2s10 4.477 10 10-4.477 10-10 10zm0-2a8 8 0 100-16 8 8 0 000 16z"
                                    ></path>
                                </svg>
                            </div>

                            <h3
                                class="text-lg font-bold mb-4 relative z-10 flex items-center"
                            >
                                <svg
                                    class="w-5 h-5 mr-2"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                    xmlns="http://www.w3.org/2000/svg"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
                                    ></path>
                                </svg>
                                Log Aktivitas Terakhir
                            </h3>

                            <div class="relative z-10 space-y-4">
                                <!-- Menampilkan maksimal 3 log aktivitas terbaru menggunakan loop -->
                                <div
                                    v-for="(
                                        log, index
                                    ) in recent_documents.slice(0, 3)"
                                    :key="'log-' + index"
                                    class="border-b border-indigo-700/50 pb-3 last:border-0 last:pb-0"
                                >
                                    <p
                                        class="text-sm font-semibold text-indigo-50 line-clamp-1"
                                        :title="log.nama_surat"
                                    >
                                        {{ log.nama_surat }}
                                    </p>
                                    <div
                                        class="flex justify-between items-center mt-1"
                                    >
                                        <span class="text-xs text-indigo-300">
                                            Oleh:
                                            {{
                                                log.pembuat
                                                    ? log.pembuat.name
                                                    : "Sistem"
                                            }}
                                        </span>
                                        <span
                                            class="text-xs font-medium text-indigo-200"
                                        >
                                            {{ formatDate(log.created_at) }}
                                        </span>
                                    </div>
                                </div>

                                <div
                                    v-if="recent_documents.length === 0"
                                    class="text-sm text-indigo-300 italic"
                                >
                                    Belum ada log aktivitas tercatat di sistem.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
