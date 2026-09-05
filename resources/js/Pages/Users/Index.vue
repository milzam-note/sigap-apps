<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, useForm, router } from "@inertiajs/vue3";
import { ref } from "vue";

const props = defineProps({
    users: Array,
});

// State Form
const form = useForm({
    id: null,
    name: "",
    nrp_nip: "",
    jabatan: "",
    pangkat: "",
    email: "",
    role: "internal",
    password: "",
});

const isModalOpen = ref(false);
const isEditing = ref(false);

const openModal = (user = null) => {
    isEditing.value = !!user;
    if (user) {
        form.id = user.id;
        form.name = user.name;
        form.nrp_nip = user.nrp_nip;
        form.jabatan = user.jabatan;
        form.pangkat = user.pangkat;
        form.email = user.email || "";
        form.role = user.role;
        form.password = ""; // Dikosongkan saat edit
    } else {
        form.reset();
    }
    isModalOpen.value = true;
};

const closeModal = () => {
    isModalOpen.value = false;
    form.reset();
    form.clearErrors();
};

const submitForm = () => {
    if (isEditing.value) {
        form.put(route("users.update", form.id), {
            onSuccess: () => closeModal(),
        });
    } else {
        form.post(route("users.store"), {
            onSuccess: () => closeModal(),
        });
    }
};

const toggleStatus = (id) => {
    if (confirm("Ubah status akses pengguna ini?")) {
        router.put(route("users.toggle", id));
    }
};

const resetPassword = (id) => {
    if (confirm("Reset password pengguna ini ke default (NRP/NIP)?")) {
        router.put(route("users.reset-password", id));
    }
};
</script>

<template>
    <Head title="Manajemen Pengguna" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Manajemen Akses Pengguna
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <div
                    class="bg-white p-6 shadow sm:rounded-lg flex justify-between items-center"
                >
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">
                            Daftar Personel & Hak Akses
                        </h3>
                        <p class="text-sm text-gray-500">
                            Kelola akun admin dan pengguna internal sistem.
                        </p>
                    </div>
                    <button
                        @click="openModal()"
                        class="px-5 py-2 bg-indigo-600 text-white font-bold text-sm rounded-lg hover:bg-indigo-700 transition"
                    >
                        + Tambah Personel
                    </button>
                </div>

                <!-- TABEL PENGGUNA -->
                <div class="bg-white shadow sm:rounded-lg overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider"
                                    >
                                        Nama & NRP/NIP
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider"
                                    >
                                        Jabatan & Pangkat
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider"
                                    >
                                        Role
                                    </th>
                                    <th
                                        class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider"
                                    >
                                        Status Akses
                                    </th>
                                    <th
                                        class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider"
                                    >
                                        Aksi
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr
                                    v-for="user in users"
                                    :key="user.id"
                                    class="hover:bg-gray-50"
                                >
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div
                                            class="text-sm font-bold text-gray-900"
                                        >
                                            {{ user.name }}
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            {{ user.nrp_nip }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">
                                            {{ user.jabatan }}
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            {{ user.pangkat }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span
                                            :class="
                                                user.role === 'admin'
                                                    ? 'bg-purple-100 text-purple-800'
                                                    : 'bg-blue-100 text-blue-800'
                                            "
                                            class="px-3 py-1 inline-flex text-xs leading-5 font-bold rounded-full uppercase"
                                        >
                                            {{ user.role }}
                                        </span>
                                    </td>
                                    <td
                                        class="px-6 py-4 whitespace-nowrap text-center"
                                    >
                                        <button
                                            @click="toggleStatus(user.id)"
                                            :class="
                                                user.is_active
                                                    ? 'bg-emerald-100 text-emerald-700 hover:bg-emerald-200'
                                                    : 'bg-red-100 text-red-700 hover:bg-red-200'
                                            "
                                            class="px-3 py-1 rounded-full text-xs font-bold transition-colors"
                                        >
                                            {{
                                                user.is_active
                                                    ? "Aktif"
                                                    : "Nonaktif"
                                            }}
                                        </button>
                                    </td>
                                    <td
                                        class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium"
                                    >
                                        <div
                                            class="flex items-center justify-center space-x-2"
                                        >
                                            <button
                                                @click="openModal(user)"
                                                class="text-amber-600 hover:text-amber-900 bg-amber-50 hover:bg-amber-100 px-3 py-1 rounded transition-colors"
                                            >
                                                Edit
                                            </button>
                                            <button
                                                @click="resetPassword(user.id)"
                                                class="text-gray-600 hover:text-gray-900 bg-gray-100 hover:bg-gray-200 px-3 py-1 rounded transition-colors"
                                                title="Reset ke NRP/NIP"
                                            >
                                                Reset Sandi
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <tr v-if="users.length === 0">
                                    <td
                                        colspan="5"
                                        class="px-6 py-8 text-center text-gray-500 text-sm"
                                    >
                                        Belum ada data pengguna selain Anda.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- MODAL TAMBAH/EDIT -->
        <div
            v-if="isModalOpen"
            class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center bg-gray-900 bg-opacity-50 p-4"
        >
            <div class="bg-white rounded-xl shadow-xl max-w-2xl w-full p-6">
                <div
                    class="flex justify-between items-center border-b pb-3 mb-4"
                >
                    <h3 class="text-lg font-bold text-gray-900">
                        {{
                            isEditing
                                ? "Edit Data Personel"
                                : "Registrasi Personel Baru"
                        }}
                    </h3>
                    <button
                        @click="closeModal"
                        class="text-gray-400 hover:text-gray-600 text-xl font-bold"
                    >
                        &times;
                    </button>
                </div>

                <form
                    @submit.prevent="submitForm"
                    class="grid grid-cols-1 md:grid-cols-2 gap-4"
                >
                    <div class="md:col-span-2">
                        <label class="block text-sm font-bold text-gray-700"
                            >Nama Lengkap</label
                        >
                        <input
                            v-model="form.name"
                            type="text"
                            required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                        />
                        <div
                            v-if="form.errors.name"
                            class="text-red-500 text-xs mt-1"
                        >
                            {{ form.errors.name }}
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700"
                            >NRP / NIP</label
                        >
                        <input
                            v-model="form.nrp_nip"
                            type="text"
                            required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                            placeholder="Sebagai ID Login"
                        />
                        <div
                            v-if="form.errors.nrp_nip"
                            class="text-red-500 text-xs mt-1"
                        >
                            {{ form.errors.nrp_nip }}
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700"
                            >Email (Opsional)</label
                        >
                        <input
                            v-model="form.email"
                            type="email"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                        />
                        <div
                            v-if="form.errors.email"
                            class="text-red-500 text-xs mt-1"
                        >
                            {{ form.errors.email }}
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700"
                            >Pangkat</label
                        >
                        <input
                            v-model="form.pangkat"
                            type="text"
                            required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                            placeholder="Contoh: Penata Tk. I (III/d)"
                        />
                        <div
                            v-if="form.errors.pangkat"
                            class="text-red-500 text-xs mt-1"
                        >
                            {{ form.errors.pangkat }}
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700"
                            >Jabatan</label
                        >
                        <input
                            v-model="form.jabatan"
                            type="text"
                            required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                            placeholder="Contoh: Staf Administrasi"
                        />
                        <div
                            v-if="form.errors.jabatan"
                            class="text-red-500 text-xs mt-1"
                        >
                            {{ form.errors.jabatan }}
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700"
                            >Hak Akses (Role)</label
                        >
                        <select
                            v-model="form.role"
                            required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                        >
                            <option value="internal">
                                Internal (Membaca & Mengunduh)
                            </option>
                            <option value="admin">
                                Administrator (Akses Penuh)
                            </option>
                        </select>
                        <div
                            v-if="form.errors.role"
                            class="text-red-500 text-xs mt-1"
                        >
                            {{ form.errors.role }}
                        </div>
                    </div>

                    <div v-if="!isEditing">
                        <label class="block text-sm font-bold text-gray-700"
                            >Password Awal</label
                        >
                        <input
                            v-model="form.password"
                            type="password"
                            required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                        />
                        <div
                            v-if="form.errors.password"
                            class="text-red-500 text-xs mt-1"
                        >
                            {{ form.errors.password }}
                        </div>
                    </div>

                    <div
                        class="md:col-span-2 flex justify-end space-x-3 mt-4 pt-4 border-t"
                    >
                        <button
                            type="button"
                            @click="closeModal"
                            class="px-4 py-2 bg-gray-200 text-gray-800 font-bold text-sm rounded-lg hover:bg-gray-300 transition"
                        >
                            Batal
                        </button>
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="px-6 py-2 bg-indigo-600 text-white font-bold text-sm rounded-lg hover:bg-indigo-700 disabled:opacity-50 transition"
                        >
                            Simpan Data
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
