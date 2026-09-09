<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, usePage, router } from "@inertiajs/vue3";
import { ref } from "vue";

const props = defineProps({
    stats: Object,
    recent_documents: Array,
    userRole: String,
    filters: Object,
});

const user = usePage().props.auth.user;

const filterParams = ref({
    search: props.filters?.search || "",
    tahun: props.filters?.tahun || "",
});

const currentYear = new Date().getFullYear();
const years = Array.from({ length: 11 }, (_, i) =>
    (currentYear - i).toString(),
);

const applyFilter = () => {
    router.get(route("dashboard"), filterParams.value, {
        preserveState: true,
        preserveScroll: true,
    });
};

const resetFilter = () => {
    filterParams.value = { search: "", tahun: "" };
    applyFilter();
};

const formatDateShort = (dateString) => {
    if (!dateString) return "-";
    const date = new Date(dateString);
    return date.toLocaleDateString("id-ID", {
        day: "2-digit",
        month: "short",
        year: "numeric",
    });
};
</script>

<template>
    <Head title="Dashboard Internal" />

    <AuthenticatedLayout>
        <div class="py-8">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <div class="lg:col-span-2 space-y-6">
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
                                <Link
                                    v-if="user.role === 'admin'"
                                    :href="route('surat.index')"
                                    class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 transition"
                                >
                                    Buka Ruang Kerja
                                </Link>
                            </div>
                            <div class="hidden sm:block">
                                <svg
                                    class="w-32 h-32 text-indigo-300"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
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

                        <!-- <div
                            class="bg-white p-6 rounded-2xl shadow-sm border border-gray-200"
                        >
                            <h3
                                class="text-xs font-bold text-gray-500 mb-4 uppercase tracking-wider"
                            >
                                Pencarian Arsip Dokumen
                            </h3>
                            <div
                                class="grid grid-cols-1 md:grid-cols-2 gap-4 items-end"
                            >
                                <div>
                                    <label
                                        class="block text-xs font-bold text-gray-700 mb-1.5"
                                        >Nama / Nomor Dokumen</label
                                    >
                                    <input
                                        v-model="filterParams.search"
                                        type="text"
                                        placeholder="Cari nama atau nomor..."
                                        class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition"
                                        @keyup.enter="applyFilter"
                                    />
                                </div>
                                <div>
                                    <label
                                        class="block text-xs font-bold text-gray-700 mb-1.5"
                                        >Tahun Dokumen</label
                                    >
                                    <select
                                        v-model="filterParams.tahun"
                                        class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition"
                                    >
                                        <option value="">Semua Tahun</option>
                                        <option
                                            v-for="y in years"
                                            :key="y"
                                            :value="y"
                                        >
                                            {{ y }}
                                        </option>
                                    </select>
                                </div>
                                <div
                                    class="md:col-span-2 flex justify-end space-x-3 mt-2"
                                >
                                    <button
                                        @click="resetFilter"
                                        class="px-5 py-2 bg-gray-100 text-gray-700 text-sm font-bold rounded-lg hover:bg-gray-200 transition"
                                    >
                                        Reset
                                    </button>
                                    <button
                                        @click="applyFilter"
                                        class="px-6 py-2 bg-indigo-600 text-white text-sm font-bold rounded-lg hover:bg-indigo-700 transition shadow-sm"
                                    >
                                        Cari
                                    </button>
                                </div>
                            </div>
                        </div> -->

                        <div class="space-y-4">
                            <h3 class="text-lg font-bold text-gray-900 px-1">
                                Dokumen Terbaru
                            </h3>

                            <div
                                v-for="surat in recent_documents"
                                :key="surat.id"
                                class="bg-white rounded-2xl border border-gray-200 shadow-sm p-4 sm:p-5 flex flex-col sm:flex-row gap-4 items-center justify-between hover:shadow-md transition-all group"
                            >
                                <div
                                    class="flex items-center gap-4 w-full sm:w-auto"
                                >
                                    <div
                                        class="w-16 h-16 bg-indigo-50 rounded-xl flex items-center justify-center text-indigo-600 border border-indigo-100 shrink-0 relative"
                                    >
                                        <div
                                            class="absolute -top-2 -right-2 bg-indigo-600 text-white text-[10px] font-black px-1.5 py-0.5 rounded shadow-sm"
                                        >
                                            {{ surat.tahun_dokumen }}
                                        </div>
                                        <svg
                                            class="w-8 h-8"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="1.5"
                                                d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"
                                            ></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <span
                                            class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-800 uppercase tracking-wider mb-1"
                                        >
                                            {{
                                                surat.jenis_surat
                                                    ? surat.jenis_surat
                                                          .nama_jenis
                                                    : "KATEGORI"
                                            }}
                                        </span>
                                        <h4
                                            class="text-base font-bold text-gray-900 group-hover:text-indigo-600 transition-colors leading-tight"
                                        >
                                            {{ surat.nama_surat }}
                                        </h4>
                                        <p
                                            class="text-xs font-semibold text-gray-500 uppercase tracking-wider mt-0.5"
                                        >
                                            NOMOR: {{ surat.nomor_surat }}
                                        </p>
                                    </div>
                                </div>

                                <div
                                    class="flex flex-wrap sm:flex-nowrap items-center gap-2 w-full sm:w-auto justify-end pt-3 sm:pt-0 border-t sm:border-0 border-gray-100"
                                >
                                    <div
                                        class="text-xs bg-gray-50 px-2.5 py-1.5 rounded-lg border border-gray-100"
                                    >
                                        <span
                                            class="text-gray-400 block text-[10px] uppercase font-bold"
                                            >Status</span
                                        >
                                        <span
                                            :class="
                                                surat.sifat_surat === 'rahasia'
                                                    ? 'text-red-600 font-bold'
                                                    : 'text-emerald-600 font-bold'
                                            "
                                            class="capitalize"
                                            >{{ surat.sifat_surat }}</span
                                        >
                                    </div>
                                    <div
                                        class="text-xs bg-gray-50 px-2.5 py-1.5 rounded-lg border border-gray-100"
                                    >
                                        <span
                                            class="text-gray-400 block text-[10px] uppercase font-bold"
                                            >Ditetapkan</span
                                        >
                                        <span
                                            class="font-semibold text-gray-800"
                                            >{{
                                                formatDateShort(
                                                    surat.tanggal_surat,
                                                )
                                            }}</span
                                        >
                                    </div>
                                    <div class="flex gap-1.5 ml-2">
                                        <a
                                            :href="
                                                route('surat.lihat', surat.id)
                                            "
                                            target="_blank"
                                            class="px-3.5 py-2 bg-white border border-indigo-600 text-indigo-600 hover:bg-indigo-50 font-bold text-xs rounded-lg transition"
                                            >Lihat</a
                                        >
                                        <a
                                            :href="
                                                route('surat.unduh', surat.id)
                                            "
                                            class="px-3.5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-lg shadow-sm transition"
                                            >Unduh</a
                                        >
                                    </div>
                                </div>
                            </div>

                            <div
                                v-if="recent_documents.length === 0"
                                class="bg-white rounded-2xl border border-gray-200 p-12 text-center text-gray-500 text-sm"
                            >
                                Belum ada dokumen yang ditemukan.
                            </div>
                        </div>
                    </div>

                    <div class="space-y-6">
                        <h3 class="text-lg font-bold text-gray-900 pt-2">
                            Statistik Data
                        </h3>

                        <div class="grid grid-cols-2 gap-4">
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

                        <div
                            class="bg-gradient-to-br from-indigo-900 to-blue-800 rounded-2xl p-6 text-white shadow-md relative overflow-hidden"
                        >
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
                                        <span class="text-xs text-indigo-300"
                                            >Oleh:
                                            {{
                                                log.pembuat
                                                    ? log.pembuat.name
                                                    : "Sistem"
                                            }}</span
                                        >
                                        <span
                                            class="text-xs font-medium text-indigo-200"
                                            >{{
                                                formatDateShort(log.created_at)
                                            }}</span
                                        >
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
