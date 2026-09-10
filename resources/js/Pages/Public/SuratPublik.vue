<script setup>
import { Head, Link, router } from "@inertiajs/vue3";
import { ref } from "vue";

const props = defineProps({
    surats: Array,
    years: Array,
    filters: Object,
});

const filterParams = ref({
    search: props.filters?.search || "",
    tanggal: props.filters?.tanggal || "",
    tahun: props.filters?.tahun || "",
});

const applyFilter = () => {
    router.get(route("home"), filterParams.value, {
        preserveState: true,
        preserveScroll: true,
    });
};

const resetFilter = () => {
    filterParams.value = { search: "", tanggal: "", tahun: "" };
    applyFilter();
};

const setFilterTahun = (year) => {
    filterParams.value.tahun = year;
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
    <Head title="Portal Informasi Dokumen Perencanaan" />

    <div class="min-h-screen bg-gray-50 flex flex-col relative z-0">
        <div
            class="fixed inset-0 z-[-1] flex items-center justify-center opacity-[0.5] pointer-events-none"
        >
            <img
                src="/watermark.png"
                alt="Watermark"
                class="w-2/3 md:w-1/2 object-contain"
            />
        </div>

        <nav
            class="bg-white border-b border-gray-200 shadow-sm sticky top-0 z-20"
        >
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-20">
                    <div class="flex items-center">
                        <Link
                            :href="route('home')"
                            class="flex items-center gap-3 hover:opacity-80 transition-opacity"
                        >
                            <img
                                src="/logo_horizontal.png"
                                alt="Logo Portal"
                                class="h-12 w-auto drop-shadow-sm"
                            />
                            <span
                                class="font-black text-xl text-indigo-900 tracking-tight hidden sm:block"
                            >
                                Sistem Informasi Digitalisasi Arsip Perencanaan
                                dan Regulasi
                            </span>
                        </Link>
                    </div>
                    <div class="flex items-center space-x-4">
                        <Link
                            :href="route('login')"
                            class="px-6 py-2.5 bg-indigo-600 text-white text-sm font-bold rounded-full hover:bg-indigo-700 shadow-md transition-all"
                        >
                            Masuk
                        </Link>
                    </div>
                </div>
            </div>
        </nav>

        <main class="flex-grow py-10 relative z-10">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <div class="text-center max-w-2xl mx-auto mb-10 mt-6 px-4">
                    <h1
                        class="text-3xl font-extrabold text-gray-900 sm:text-4xl"
                    >
                        Selamat Datang di SIGAP
                    </h1>
                    <p class="mt-4 text-lg text-gray-500 font-medium">
                        Cari, baca, dan unduh dokumen perencanaan yang telah
                        ditetapkan dan disahkan untuk publik.
                    </p>
                </div>

                <div class="flex flex-col lg:flex-row gap-8 items-start">
                    <div class="w-full lg:w-1/4 sticky top-24 space-y-6">
                        <div
                            class="bg-white rounded-2xl shadow-sm border border-gray-200 p-5"
                        >
                            <h3
                                class="text-sm font-bold text-gray-900 uppercase tracking-wider mb-4 border-b pb-3"
                            >
                                Daftar Pustaka
                            </h3>
                            <div class="flex flex-col space-y-1">
                                <button
                                    @click="setFilterTahun('')"
                                    class="text-left px-4 py-2.5 rounded-lg text-sm font-bold transition-colors flex items-center justify-between"
                                    :class="
                                        filterParams.tahun === ''
                                            ? 'bg-indigo-600 text-white shadow-md'
                                            : 'text-gray-600 hover:bg-indigo-50'
                                    "
                                >
                                    Semua Tahun
                                    <svg
                                        v-if="filterParams.tahun === ''"
                                        class="w-4 h-4"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M9 5l7 7-7 7"
                                        ></path>
                                    </svg>
                                </button>
                                <button
                                    v-for="year in years"
                                    :key="year"
                                    @click="setFilterTahun(year)"
                                    class="text-left px-4 py-2.5 rounded-lg text-sm font-bold transition-colors flex items-center justify-between"
                                    :class="
                                        filterParams.tahun === year
                                            ? 'bg-indigo-600 text-white shadow-md'
                                            : 'text-gray-600 hover:bg-indigo-50'
                                    "
                                >
                                    Tahun {{ year }}
                                    <svg
                                        v-if="filterParams.tahun === year"
                                        class="w-4 h-4"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M9 5l7 7-7 7"
                                        ></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="w-full lg:w-3/4 space-y-6">
                        <div
                            class="bg-white p-6 rounded-2xl shadow-sm border border-gray-200"
                        >
                            <h3
                                class="text-xs font-bold text-gray-500 mb-4 uppercase tracking-wider"
                            >
                                Pencarian Spesifik
                            </h3>
                            <div
                                class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end"
                            >
                                <div class="md:col-span-2">
                                    <label
                                        class="block text-xs font-bold text-gray-600 mb-1.5"
                                        >Kata Kunci (Nama, Perihal, atau
                                        Nomor)</label
                                    >
                                    <input
                                        v-model="filterParams.search"
                                        type="text"
                                        placeholder="Masukkan kata kunci pencarian..."
                                        class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition"
                                        @keyup.enter="applyFilter"
                                    />
                                </div>
                                <div>
                                    <label
                                        class="block text-xs font-bold text-gray-600 mb-1.5"
                                        >Tanggal Ditetapkan</label
                                    >
                                    <input
                                        v-model="filterParams.tanggal"
                                        type="date"
                                        class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition"
                                    />
                                </div>
                                <div
                                    class="md:col-span-3 flex justify-end space-x-3 mt-2"
                                >
                                    <button
                                        @click="resetFilter"
                                        class="px-5 py-2.5 bg-gray-100 border border-transparent text-gray-700 text-sm font-bold rounded-lg hover:bg-gray-200 transition"
                                    >
                                        Reset Pencarian
                                    </button>
                                    <button
                                        @click="applyFilter"
                                        class="px-6 py-2.5 bg-indigo-600 text-white text-sm font-bold rounded-lg shadow-sm hover:bg-indigo-700 transition"
                                    >
                                        Cari Dokumen
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div
                            class="mb-2 px-1 flex justify-between items-center"
                        >
                            <h3 class="text-sm font-bold text-gray-600">
                                Menampilkan
                                <span class="text-indigo-600">{{
                                    surats.length
                                }}</span>
                                Dokumen
                                <span v-if="filterParams.tahun"
                                    >Tahun {{ filterParams.tahun }}</span
                                >
                            </h3>
                        </div>

                        <div class="space-y-5">
                            <div
                                v-for="surat in surats"
                                :key="surat.id"
                                class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 sm:p-6 flex flex-col sm:flex-row gap-5 sm:gap-8 hover:shadow-md hover:border-indigo-200 transition-all group"
                            >
                                <div
                                    class="flex-shrink-0 flex justify-center sm:justify-start"
                                >
                                    <div
                                        class="w-24 h-24 sm:w-32 sm:h-32 bg-gradient-to-br from-indigo-50 to-blue-100 rounded-2xl flex items-center justify-center shadow-inner border border-indigo-100/50 group-hover:scale-105 transition-transform relative"
                                    >
                                        <div
                                            class="absolute -top-3 -right-3 bg-indigo-600 text-white text-xs font-black px-2 py-1 rounded-lg shadow-sm border-2 border-white"
                                        >
                                            {{ surat.tahun_dokumen }}
                                        </div>
                                        <svg
                                            class="w-12 h-12 sm:w-16 sm:h-16 text-indigo-600 drop-shadow-sm"
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
                                </div>

                                <div
                                    class="flex-grow flex flex-col justify-between"
                                >
                                    <div>
                                        <div
                                            class="flex justify-between items-start mb-3"
                                        >
                                            <span
                                                class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800 uppercase tracking-wider"
                                            >
                                                {{
                                                    surat.jenis_surat
                                                        ? surat.jenis_surat
                                                              .nama_jenis
                                                        : "TIDAK ADA KATEGORI"
                                                }}
                                            </span>

                                            <!-- BADGE KETERANGAN KANAN ATAS -->
                                            <span
                                                :class="
                                                    surat.keterangan ===
                                                    'berlaku'
                                                        ? 'bg-emerald-100 text-emerald-700 border-emerald-200'
                                                        : 'bg-red-100 text-red-700 border-red-200'
                                                "
                                                class="inline-flex items-center px-3 py-1 rounded-full text-xs font-black uppercase tracking-wider border shadow-sm"
                                            >
                                                {{ surat.keterangan }}
                                            </span>
                                        </div>
                                        <h3
                                            class="text-xl sm:text-2xl font-bold text-gray-900 leading-tight mb-2 group-hover:text-indigo-700 transition-colors"
                                        >
                                            {{ surat.nama_surat }}
                                        </h3>
                                        <p
                                            class="text-sm font-semibold text-gray-500 uppercase tracking-widest mb-2"
                                        >
                                            NOMOR: {{ surat.nomor_surat }}
                                        </p>
                                        <p
                                            class="text-sm text-gray-600 mb-4 line-clamp-2 border-l-2 border-indigo-200 pl-3 italic"
                                        >
                                            {{ surat.deskripsi }}
                                        </p>
                                    </div>

                                    <div
                                        class="bg-gray-50 rounded-xl p-3 flex flex-col sm:flex-row flex-wrap items-center gap-3 border border-gray-100 mt-2"
                                    >
                                        <div
                                            class="flex items-center bg-white px-3 py-1.5 rounded-lg border border-gray-200 shadow-sm text-sm"
                                        >
                                            <span
                                                class="text-gray-600 font-medium"
                                                >Ditetapkan:</span
                                            >
                                            <span
                                                class="ml-2 font-bold text-gray-900"
                                                >{{
                                                    formatDateShort(
                                                        surat.tanggal_surat,
                                                    )
                                                }}</span
                                            >
                                        </div>

                                        <div
                                            class="flex items-center bg-white px-3 py-1.5 rounded-lg border border-gray-200 shadow-sm text-sm font-bold"
                                        >
                                            <span
                                                class="flex items-center text-blue-600"
                                                title="Total Dilihat"
                                            >
                                                <svg
                                                    class="w-4 h-4 mr-1.5"
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
                                                {{ surat.jumlah_dilihat }}
                                            </span>
                                            <span class="mx-3 text-gray-300"
                                                >|</span
                                            >
                                            <span
                                                class="flex items-center text-emerald-600"
                                                title="Total Diunduh"
                                            >
                                                <svg
                                                    class="w-4 h-4 mr-1.5"
                                                    fill="none"
                                                    stroke="currentColor"
                                                    viewBox="0 0 24 24"
                                                >
                                                    <path
                                                        stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"
                                                    ></path>
                                                </svg>
                                                {{ surat.jumlah_unduh }}
                                            </span>
                                        </div>

                                        <div
                                            class="hidden sm:block flex-grow"
                                        ></div>
                                        <div
                                            class="flex w-full sm:w-auto gap-2 mt-2 sm:mt-0"
                                        >
                                            <a
                                                :href="
                                                    route(
                                                        'surat.lihat',
                                                        surat.id,
                                                    )
                                                "
                                                target="_blank"
                                                class="flex-1 sm:flex-none text-center px-6 py-2 bg-white border border-indigo-600 text-indigo-600 hover:bg-indigo-50 font-bold text-sm rounded-lg transition-colors"
                                                >Lihat</a
                                            >
                                            <a
                                                :href="
                                                    route(
                                                        'surat.unduh',
                                                        surat.id,
                                                    )
                                                "
                                                class="flex-1 sm:flex-none text-center px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-sm rounded-lg shadow-sm transition-colors"
                                                >Unduh</a
                                            >
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div
                                v-if="surats.length === 0"
                                class="bg-white border border-gray-200 rounded-2xl p-16 text-center shadow-sm"
                            >
                                <svg
                                    class="mx-auto h-12 w-12 text-gray-400 mb-4"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="1.5"
                                        d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                                    ></path>
                                </svg>
                                <h3
                                    class="text-lg font-bold text-gray-900 mb-1"
                                >
                                    Tidak Ada Dokumen
                                </h3>
                                <p class="text-gray-500 font-medium">
                                    Belum ada dokumen terbuka yang sesuai dengan
                                    pencarian Anda.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <footer
            class="bg-[#0f172a] text-gray-300 relative z-10 pt-12 pb-6 border-t-[6px] border-indigo-600"
        >
            <!-- Isi footer tetap -->
        </footer>
    </div>
</template>
