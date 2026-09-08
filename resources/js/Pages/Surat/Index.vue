<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, useForm, router } from "@inertiajs/vue3";
import { ref } from "vue";

const props = defineProps({
    surats: Array,
    jenis_surats: Array,
    userRole: String,
    filters: Object,
});

const filterParams = ref({
    search: props.filters?.search || "",
    tanggal: props.filters?.tanggal || "",
    tahun: props.filters?.tahun || "",
});

const currentYear = new Date().getFullYear();
const years = Array.from({ length: 11 }, (_, i) => currentYear - i);

const applyFilter = () => {
    router.get(route("surat.index"), filterParams.value, {
        preserveState: true,
        preserveScroll: true,
    });
};
const resetFilter = () => {
    filterParams.value = { search: "", tanggal: "", tahun: "" };
    applyFilter();
};

const form = useForm({
    nama_surat: "",
    deskripsi: "",
    tanggal_surat: "",
    tanggal_berlaku: "",
    nomor_surat: "",
    jenis_surat_id: "",
    sifat_surat: "umum",
    file_pdf: null,
});

const editForm = useForm({
    id: null,
    nama_surat: "",
    deskripsi: "",
    tanggal_surat: "",
    tanggal_berlaku: "",
    nomor_surat: "",
    jenis_surat_id: "",
    sifat_surat: "umum",
    file_pdf: null,
    _method: "PUT",
});

const isEditModalOpen = ref(false);
const openEditModal = (surat) => {
    editForm.id = surat.id;
    editForm.nama_surat = surat.nama_surat;
    editForm.deskripsi = surat.deskripsi;
    editForm.tanggal_surat = surat.tanggal_surat;
    editForm.tanggal_berlaku = surat.tanggal_berlaku;
    editForm.nomor_surat = surat.nomor_surat;
    editForm.jenis_surat_id = surat.jenis_surat_id;
    editForm.sifat_surat = surat.sifat_surat;
    editForm.file_pdf = null;
    isEditModalOpen.value = true;
};
const closeEditModal = () => {
    isEditModalOpen.value = false;
    editForm.reset();
};

const isHistoryModalOpen = ref(false);
const selectedSurat = ref(null);
const openHistoryModal = (surat) => {
    selectedSurat.value = surat;
    isHistoryModalOpen.value = true;
};
const closeHistoryModal = () => {
    isHistoryModalOpen.value = false;
    selectedSurat.value = null;
};

const submitForm = () => {
    form.post(route("surat.store"), {
        onSuccess: () => {
            form.reset();
            const fileInput = document.getElementById("file_input");
            if (fileInput) fileInput.value = "";
        },
    });
};

const submitEditForm = () => {
    editForm.post(route("surat.update", editForm.id), {
        onSuccess: () => closeEditModal(),
    });
};

const deleteSurat = (id) => {
    if (
        confirm(
            "Apakah Anda yakin ingin menghapus dokumen surat beserta seluruh riwayatnya?",
        )
    ) {
        router.delete(route("surat.destroy", id));
    }
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
const formatDateTime = (dateString) => {
    if (!dateString) return "-";
    const date = new Date(dateString);
    return (
        date.toLocaleDateString("id-ID", {
            year: "numeric",
            month: "long",
            day: "numeric",
            hour: "2-digit",
            minute: "2-digit",
        }) + " WIB"
    );
};
</script>

<template>
    <Head title="Kelola Surat Perencanaan" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-bold text-xl text-gray-800 leading-tight">
                Dokumen Surat Perencanaan
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
                <!-- ================= TAMBAH SURAT ================= -->
                <div
                    v-if="userRole === 'admin'"
                    class="bg-white shadow-sm border border-gray-200 sm:rounded-2xl overflow-hidden relative"
                >
                    <div
                        class="h-1 bg-indigo-600 w-full absolute top-0 left-0"
                    ></div>
                    <div class="p-6 sm:p-8">
                        <div class="flex items-center mb-6">
                            <div
                                class="w-12 h-12 bg-indigo-50 rounded-xl flex items-center justify-center text-indigo-600 mr-4 border border-indigo-100"
                            >
                                <svg
                                    class="w-6 h-6"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                                    ></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-gray-900">
                                    Registrasi Dokumen Baru
                                </h3>
                                <p class="text-sm text-gray-500 font-medium">
                                    Unggah dan arsipkan dokumen perencanaan
                                    dengan aman.
                                </p>
                            </div>
                        </div>

                        <form
                            @submit.prevent="submitForm"
                            class="grid grid-cols-1 md:grid-cols-12 gap-6"
                        >
                            <div
                                class="md:col-span-8 grid grid-cols-1 sm:grid-cols-2 gap-5"
                            >
                                <div class="sm:col-span-2">
                                    <label
                                        class="block text-sm font-bold text-gray-700 mb-1"
                                        >Nama / Judul Dokumen</label
                                    >
                                    <input
                                        v-model="form.nama_surat"
                                        type="text"
                                        required
                                        class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm transition"
                                        placeholder="Masukkan judul resmi dokumen..."
                                    />
                                </div>
                                <div class="sm:col-span-2">
                                    <label
                                        class="block text-sm font-bold text-gray-700 mb-1"
                                        >Deskripsi / Perihal</label
                                    >
                                    <textarea
                                        v-model="form.deskripsi"
                                        rows="2"
                                        required
                                        class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm transition"
                                        placeholder="Jelaskan secara singkat isi atau perihal dari dokumen ini..."
                                    ></textarea>
                                </div>
                                <div>
                                    <label
                                        class="block text-sm font-bold text-gray-700 mb-1"
                                        >Nomor Surat</label
                                    >
                                    <input
                                        v-model="form.nomor_surat"
                                        type="text"
                                        required
                                        class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm transition"
                                        placeholder="Contoh: KEP/123/XII/2026"
                                    />
                                </div>
                                <div>
                                    <label
                                        class="block text-sm font-bold text-gray-700 mb-1"
                                        >Kategori Dokumen</label
                                    >
                                    <select
                                        v-model="form.jenis_surat_id"
                                        required
                                        class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm transition"
                                    >
                                        <option value="" disabled>
                                            -- Pilih Kategori --
                                        </option>
                                        <option
                                            v-for="jenis in jenis_surats"
                                            :key="jenis.id"
                                            :value="jenis.id"
                                        >
                                            {{ jenis.nama_jenis }}
                                        </option>
                                    </select>
                                </div>
                                <div>
                                    <label
                                        class="block text-sm font-bold text-gray-700 mb-1"
                                        >Tanggal Ditetapkan</label
                                    >
                                    <input
                                        v-model="form.tanggal_surat"
                                        type="date"
                                        required
                                        class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm transition"
                                    />
                                </div>
                                <div>
                                    <label
                                        class="block text-sm font-bold text-gray-700 mb-1"
                                        >Tanggal Berlaku</label
                                    >
                                    <input
                                        v-model="form.tanggal_berlaku"
                                        type="date"
                                        required
                                        class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm transition"
                                    />
                                </div>
                                <div class="sm:col-span-2">
                                    <label
                                        class="block text-sm font-bold text-gray-700 mb-1"
                                        >Sifat & Keamanan</label
                                    >
                                    <select
                                        v-model="form.sifat_surat"
                                        required
                                        class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm transition font-bold"
                                        :class="
                                            form.sifat_surat === 'rahasia'
                                                ? 'text-red-700 bg-red-50'
                                                : 'text-emerald-700 bg-emerald-50'
                                        "
                                    >
                                        <option value="umum">
                                            Umum (Dapat dilihat publik)
                                        </option>
                                        <option value="rahasia">
                                            Rahasia (Hanya internal)
                                        </option>
                                    </select>
                                </div>
                            </div>

                            <div
                                class="md:col-span-4 flex flex-col justify-between"
                            >
                                <div>
                                    <label
                                        class="block text-sm font-bold text-gray-700 mb-1"
                                        >Berkas Digital (PDF)</label
                                    >
                                    <div
                                        class="mt-1 flex justify-center px-6 pt-10 pb-10 border-2 border-gray-300 border-dashed rounded-xl hover:bg-indigo-50 hover:border-indigo-300 transition-colors cursor-pointer group h-full items-center"
                                        onclick="
                                            document
                                                .getElementById('file_input')
                                                .click()
                                        "
                                    >
                                        <div class="space-y-2 text-center">
                                            <svg
                                                class="mx-auto h-12 w-12 text-gray-400 group-hover:text-indigo-500 transition-colors"
                                                stroke="currentColor"
                                                fill="none"
                                                viewBox="0 0 48 48"
                                            >
                                                <path
                                                    d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02"
                                                    stroke-width="2"
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                />
                                            </svg>
                                            <div
                                                class="flex text-sm text-gray-600 justify-center"
                                            >
                                                <span
                                                    class="relative cursor-pointer bg-transparent rounded-md font-bold text-indigo-600 hover:text-indigo-500"
                                                    >Pilih File PDF</span
                                                >
                                            </div>
                                            <p
                                                class="text-xs font-medium"
                                                :class="
                                                    form.file_pdf
                                                        ? 'text-indigo-700 font-bold'
                                                        : 'text-gray-500'
                                                "
                                            >
                                                {{
                                                    form.file_pdf
                                                        ? form.file_pdf.name
                                                        : "Maksimal 10MB"
                                                }}
                                            </p>
                                        </div>
                                        <input
                                            id="file_input"
                                            type="file"
                                            accept=".pdf"
                                            class="sr-only"
                                            required
                                            @change="
                                                (e) => {
                                                    form.file_pdf = e.target
                                                        .files[0]
                                                        ? e.target.files[0]
                                                        : null;
                                                }
                                            "
                                        />
                                    </div>
                                </div>
                                <button
                                    type="submit"
                                    :disabled="form.processing"
                                    class="w-full mt-4 flex justify-center py-2.5 px-4 border border-transparent rounded-lg shadow-sm text-sm font-bold text-white bg-indigo-600 hover:bg-indigo-700 disabled:opacity-50 transition"
                                >
                                    Simpan Dokumen
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- ================= FILTER ================= -->
                <div
                    class="bg-white shadow-sm border border-gray-200 sm:rounded-2xl p-6"
                >
                    <h3
                        class="text-xs font-bold text-gray-500 mb-4 uppercase tracking-wider"
                    >
                        Pencarian & Filter Dokumen
                    </h3>
                    <div
                        class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end"
                    >
                        <div class="md:col-span-2">
                            <label
                                class="block text-xs font-bold text-gray-700 mb-1.5"
                                >Kata Kunci (Nama, Perihal, atau Nomor)</label
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
                                class="block text-xs font-bold text-gray-700 mb-1.5"
                                >Tanggal Ditetapkan</label
                            >
                            <input
                                v-model="filterParams.tanggal"
                                type="date"
                                class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition"
                            />
                        </div>
                        <div>
                            <label
                                class="block text-xs font-bold text-gray-700 mb-1.5"
                                >Tahun</label
                            >
                            <select
                                v-model="filterParams.tahun"
                                class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition"
                            >
                                <option value="">Semua Tahun</option>
                                <option
                                    v-for="year in years"
                                    :key="year"
                                    :value="year"
                                >
                                    {{ year }}
                                </option>
                            </select>
                        </div>
                        <div
                            class="md:col-span-4 flex justify-end space-x-3 mt-2"
                        >
                            <button
                                @click="resetFilter"
                                class="px-5 py-2.5 bg-gray-100 border border-transparent text-gray-700 text-sm font-bold rounded-lg hover:bg-gray-200 transition"
                            >
                                Reset
                            </button>
                            <button
                                @click="applyFilter"
                                class="px-6 py-2.5 bg-indigo-600 text-white text-sm font-bold rounded-lg hover:bg-indigo-700 shadow-sm transition"
                            >
                                Terapkan Filter
                            </button>
                        </div>
                    </div>
                </div>

                <!-- ================= CARD LIST ARSIP ================= -->
                <div class="space-y-5">
                    <div
                        v-for="surat in surats"
                        :key="surat.id"
                        class="bg-white rounded-2xl border border-gray-200 shadow-sm p-4 sm:p-6 flex flex-col sm:flex-row gap-5 sm:gap-6 hover:shadow-md hover:border-indigo-200 transition-all relative group"
                    >
                        <div
                            class="flex-shrink-0 flex justify-center sm:justify-start"
                        >
                            <div
                                class="w-24 h-24 sm:w-32 sm:h-32 bg-gradient-to-br from-indigo-50 to-blue-50 rounded-2xl flex items-center justify-center shadow-inner border border-indigo-100/60 group-hover:scale-105 transition-transform"
                            >
                                <svg
                                    class="w-10 h-10 sm:w-14 sm:h-14 text-indigo-600 drop-shadow-sm"
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

                        <div class="flex-grow flex flex-col justify-between">
                            <div>
                                <div
                                    class="flex justify-between items-start mb-3"
                                >
                                    <span
                                        class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800 uppercase tracking-wider border border-amber-200"
                                    >
                                        {{
                                            surat.jenis_surat
                                                ? surat.jenis_surat.nama_jenis
                                                : "TIDAK ADA KATEGORI"
                                        }}
                                    </span>
                                    <div
                                        v-if="userRole === 'admin'"
                                        class="flex space-x-2 opacity-100 sm:opacity-0 sm:group-hover:opacity-100 transition-opacity"
                                    >
                                        <button
                                            @click="openHistoryModal(surat)"
                                            class="px-3 py-1.5 text-xs font-bold rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white border border-transparent hover:border-blue-700 transition"
                                        >
                                            Riwayat
                                        </button>
                                        <button
                                            @click="openEditModal(surat)"
                                            class="px-3 py-1.5 text-xs font-bold rounded-lg bg-amber-50 text-amber-600 hover:bg-amber-500 hover:text-white border border-transparent hover:border-amber-600 transition"
                                        >
                                            Edit
                                        </button>
                                        <button
                                            @click="deleteSurat(surat.id)"
                                            class="px-3 py-1.5 text-xs font-bold rounded-lg bg-red-50 text-red-600 hover:bg-red-600 hover:text-white border border-transparent hover:border-red-700 transition"
                                        >
                                            Hapus
                                        </button>
                                    </div>
                                </div>
                                <h3
                                    class="text-xl font-bold text-gray-900 leading-tight mb-1 group-hover:text-indigo-700 transition-colors"
                                >
                                    {{ surat.nama_surat }}
                                </h3>
                                <p
                                    class="text-sm font-bold text-gray-500 uppercase tracking-widest mb-2"
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
                                    class="flex items-center bg-white px-3 py-2 rounded-lg border border-gray-200 shadow-sm text-sm"
                                >
                                    <span
                                        class="text-gray-500 font-medium text-xs uppercase tracking-wide mr-2"
                                        >Status</span
                                    >
                                    <span
                                        :class="
                                            surat.sifat_surat === 'rahasia'
                                                ? 'text-red-700 bg-red-50'
                                                : 'text-emerald-700 bg-emerald-50'
                                        "
                                        class="px-2 py-0.5 rounded flex items-center font-bold capitalize text-xs"
                                    >
                                        <div
                                            :class="
                                                surat.sifat_surat === 'rahasia'
                                                    ? 'bg-red-500'
                                                    : 'bg-emerald-500'
                                            "
                                            class="w-1.5 h-1.5 rounded-full mr-1.5"
                                        ></div>
                                        {{ surat.sifat_surat }}
                                    </span>
                                </div>
                                <div
                                    class="flex items-center bg-white px-3 py-2 rounded-lg border border-gray-200 shadow-sm text-sm"
                                >
                                    <span
                                        class="text-gray-500 font-medium text-xs uppercase tracking-wide mr-2"
                                        >Ditetapkan</span
                                    >
                                    <span class="font-bold text-gray-900">{{
                                        formatDateShort(surat.tanggal_surat)
                                    }}</span>
                                </div>
                                <div
                                    class="flex items-center bg-white px-3 py-2 rounded-lg border border-gray-200 shadow-sm text-sm"
                                >
                                    <span
                                        class="text-gray-500 font-medium text-xs uppercase tracking-wide mr-2"
                                        >Berlaku</span
                                    >
                                    <span class="font-bold text-indigo-700">{{
                                        formatDateShort(surat.tanggal_berlaku)
                                    }}</span>
                                </div>

                                <div class="hidden sm:block flex-grow"></div>
                                <div
                                    class="flex w-full sm:w-auto gap-2 mt-2 sm:mt-0"
                                >
                                    <a
                                        :href="route('surat.lihat', surat.id)"
                                        target="_blank"
                                        class="flex-1 sm:flex-none text-center px-6 py-2 bg-white border border-indigo-600 text-indigo-700 hover:bg-indigo-50 font-bold text-sm rounded-lg transition-colors"
                                        >Lihat</a
                                    >
                                    <a
                                        :href="route('surat.unduh', surat.id)"
                                        class="flex-1 sm:flex-none text-center px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-sm rounded-lg shadow-sm transition-colors"
                                        >Unduh</a
                                    >
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ================= MODAL EDIT ================= -->
        <div
            v-if="isEditModalOpen"
            class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center bg-gray-900/60 backdrop-blur-sm p-4"
        >
            <div
                class="bg-white rounded-2xl shadow-xl max-w-2xl w-full p-6 sm:p-8"
            >
                <div
                    class="flex justify-between items-center border-b pb-4 mb-6"
                >
                    <div>
                        <h3 class="text-xl font-bold text-gray-900">
                            Revisi Dokumen
                        </h3>
                        <p class="text-xs font-medium text-gray-500 mt-1">
                            Versi lama akan otomatis disimpan ke dalam Riwayat.
                        </p>
                    </div>
                    <button
                        @click="closeEditModal"
                        class="text-gray-400 hover:text-gray-600 text-2xl font-bold transition-colors"
                    >
                        &times;
                    </button>
                </div>

                <form
                    @submit.prevent="submitEditForm"
                    class="grid grid-cols-1 md:grid-cols-2 gap-5"
                >
                    <div class="md:col-span-2">
                        <label
                            class="block text-sm font-bold text-gray-700 mb-1"
                            >Nama Surat</label
                        >
                        <input
                            v-model="editForm.nama_surat"
                            type="text"
                            required
                            class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm transition"
                        />
                    </div>
                    <div class="md:col-span-2">
                        <label
                            class="block text-sm font-bold text-gray-700 mb-1"
                            >Deskripsi / Perihal</label
                        >
                        <textarea
                            v-model="editForm.deskripsi"
                            rows="2"
                            required
                            class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm transition"
                        ></textarea>
                    </div>
                    <div class="md:col-span-2">
                        <label
                            class="block text-sm font-bold text-gray-700 mb-1"
                            >Nomor Surat</label
                        >
                        <input
                            v-model="editForm.nomor_surat"
                            type="text"
                            required
                            class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm transition"
                        />
                    </div>
                    <div>
                        <label
                            class="block text-sm font-bold text-gray-700 mb-1"
                            >Jenis Surat</label
                        >
                        <select
                            v-model="editForm.jenis_surat_id"
                            required
                            class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm transition"
                        >
                            <option
                                v-for="jenis in jenis_surats"
                                :key="jenis.id"
                                :value="jenis.id"
                            >
                                {{ jenis.nama_jenis }}
                            </option>
                        </select>
                    </div>
                    <div>
                        <label
                            class="block text-sm font-bold text-gray-700 mb-1"
                            >Sifat Surat</label
                        >
                        <select
                            v-model="editForm.sifat_surat"
                            required
                            class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm transition font-bold"
                            :class="
                                editForm.sifat_surat === 'rahasia'
                                    ? 'text-red-700'
                                    : 'text-emerald-700'
                            "
                        >
                            <option value="umum">Umum (Publik)</option>
                            <option value="rahasia">Rahasia (Internal)</option>
                        </select>
                    </div>
                    <div>
                        <label
                            class="block text-sm font-bold text-gray-700 mb-1"
                            >Tanggal Ditetapkan</label
                        >
                        <input
                            v-model="editForm.tanggal_surat"
                            type="date"
                            required
                            class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm transition"
                        />
                    </div>
                    <div>
                        <label
                            class="block text-sm font-bold text-gray-700 mb-1"
                            >Tanggal Berlaku</label
                        >
                        <input
                            v-model="editForm.tanggal_berlaku"
                            type="date"
                            required
                            class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm transition"
                        />
                    </div>

                    <div
                        class="md:col-span-2 mt-2 bg-blue-50/50 p-4 rounded-xl border border-blue-100"
                    >
                        <label
                            class="block text-sm font-bold text-blue-900 mb-1"
                            >Unggah PDF Baru (Opsional)</label
                        >
                        <p class="text-xs font-medium text-blue-600 mb-3">
                            Kosongkan jika hanya merevisi teks.
                        </p>
                        <input
                            type="file"
                            accept=".pdf"
                            @change="
                                (e) => (editForm.file_pdf = e.target.files[0])
                            "
                            class="block w-full text-sm text-gray-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-bold file:bg-white file:text-indigo-700 file:shadow-sm hover:file:bg-indigo-50 transition-colors cursor-pointer"
                        />
                    </div>

                    <div
                        class="md:col-span-2 flex justify-end space-x-3 mt-4 pt-5 border-t border-gray-100"
                    >
                        <button
                            type="button"
                            @click="closeEditModal"
                            class="px-5 py-2.5 bg-gray-100 text-gray-700 font-bold text-sm rounded-lg hover:bg-gray-200 transition"
                        >
                            Batal
                        </button>
                        <button
                            type="submit"
                            :disabled="editForm.processing"
                            class="px-6 py-2.5 bg-indigo-600 text-white font-bold text-sm rounded-lg hover:bg-indigo-700 shadow-sm disabled:opacity-50 transition"
                        >
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- ================= MODAL RIWAYAT ================= -->
        <div
            v-if="isHistoryModalOpen"
            class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center bg-gray-900/60 backdrop-blur-sm p-4"
        >
            <div
                class="bg-white rounded-2xl shadow-xl max-w-4xl w-full p-6 sm:p-8 max-h-[90vh] flex flex-col"
            >
                <div
                    class="flex justify-between items-start border-b pb-4 mb-6"
                >
                    <div>
                        <h3 class="text-xl font-bold text-gray-900">
                            Log Aktivitas & Riwayat Dokumen
                        </h3>
                        <p class="text-sm text-gray-500 font-medium mt-1">
                            Dokumen:
                            <span class="font-bold text-indigo-700">{{
                                selectedSurat?.nama_surat
                            }}</span>
                        </p>
                    </div>
                    <button
                        @click="closeHistoryModal"
                        class="text-gray-400 hover:text-gray-600 text-2xl font-bold transition-colors"
                    >
                        &times;
                    </button>
                </div>

                <div class="overflow-y-auto flex-grow pr-2 custom-scrollbar">
                    <div
                        v-if="
                            selectedSurat?.riwayat &&
                            selectedSurat.riwayat.length > 0
                        "
                        class="space-y-6 relative before:absolute before:inset-0 before:ml-5 before:-translate-x-px md:before:mx-auto md:before:translate-x-0 before:h-full before:w-0.5 before:bg-gradient-to-b before:from-indigo-100 before:via-gray-200 before:to-transparent"
                    >
                        <div
                            v-for="(log, index) in selectedSurat.riwayat"
                            :key="log.id"
                            class="relative flex items-center justify-between md:justify-normal md:odd:flex-row-reverse group"
                        >
                            <div
                                class="flex items-center justify-center w-10 h-10 rounded-full border-4 border-white bg-indigo-100 text-indigo-700 shrink-0 md:order-1 md:group-odd:-translate-x-1/2 md:group-even:translate-x-1/2 shadow-sm relative z-10 font-black text-sm"
                            >
                                V{{ selectedSurat.riwayat.length - index }}
                            </div>

                            <div
                                class="w-[calc(100%-4rem)] md:w-[calc(50%-2.5rem)] bg-white border border-gray-200 p-5 rounded-2xl shadow-sm hover:shadow-md hover:border-indigo-200 transition-all"
                            >
                                <div
                                    class="flex justify-between items-start mb-3"
                                >
                                    <span
                                        :class="
                                            log.sifat_surat_lama === 'rahasia'
                                                ? 'bg-red-50 text-red-700 border-red-100'
                                                : 'bg-emerald-50 text-emerald-700 border-emerald-100'
                                        "
                                        class="px-2.5 py-1 border font-bold text-xs rounded-md uppercase"
                                        >{{ log.sifat_surat_lama }}</span
                                    >
                                    <span
                                        class="text-xs font-bold text-gray-500 bg-gray-50 px-2 py-1 rounded"
                                        >{{
                                            formatDateTime(log.created_at)
                                        }}</span
                                    >
                                </div>
                                <h4
                                    class="font-bold text-gray-900 text-base mb-1"
                                    :title="log.nama_surat_lama"
                                >
                                    {{ log.nama_surat_lama }}
                                </h4>
                                <p
                                    class="text-xs font-semibold text-gray-500 mb-1"
                                >
                                    No: {{ log.nomor_surat_lama }}
                                </p>
                                <p
                                    class="text-xs font-semibold text-gray-500 mb-3 pb-3 border-b border-gray-100"
                                >
                                    Berlaku:
                                    {{
                                        formatDateShort(
                                            log.tanggal_berlaku_lama,
                                        )
                                    }}
                                </p>

                                <p
                                    class="text-xs text-gray-600 mb-4 line-clamp-2 italic"
                                >
                                    "{{ log.deskripsi_lama }}"
                                </p>

                                <div
                                    class="flex justify-between items-end mt-2"
                                >
                                    <div class="text-xs">
                                        <span class="block text-gray-400 mb-0.5"
                                            >Diubah oleh:</span
                                        >
                                        <span
                                            class="font-bold text-indigo-700 flex items-center"
                                        >
                                            <svg
                                                class="w-3 h-3 mr-1"
                                                fill="none"
                                                stroke="currentColor"
                                                viewBox="0 0 24 24"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"
                                                ></path>
                                            </svg>
                                            {{
                                                log.pengubah
                                                    ? log.pengubah.name
                                                    : "Sistem"
                                            }}
                                        </span>
                                    </div>
                                    <div class="flex space-x-2">
                                        <a
                                            :href="
                                                route('riwayat.lihat', log.id)
                                            "
                                            target="_blank"
                                            class="px-3.5 py-1.5 bg-white border border-indigo-600 text-indigo-700 text-xs font-bold rounded-lg hover:bg-indigo-50 transition"
                                            >Lihat</a
                                        >
                                        <a
                                            :href="
                                                route('riwayat.unduh', log.id)
                                            "
                                            class="px-3.5 py-1.5 bg-indigo-600 text-white text-xs font-bold rounded-lg hover:bg-indigo-700 transition shadow-sm"
                                            >Unduh</a
                                        >
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div v-else class="text-center py-12">
                        <p
                            class="text-sm font-medium text-gray-500 max-w-xs mx-auto"
                        >
                            Dokumen ini belum pernah mengalami perubahan sejak
                            pertama kali diunggah.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
    width: 6px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: #f1f5f9;
    border-radius: 4px;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 4px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}
</style>
