<script setup>
import Checkbox from "@/Components/Checkbox.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    nrp_nip: "",
    password: "",
    remember: false,
});

const submit = () => {
    form.post(route("login"), {
        onFinish: () => form.reset("password"),
    });
};
</script>

<template>
    <Head title="Log in Aplikasi" />

    <div class="min-h-screen flex flex-col lg:flex-row bg-gray-50">
        <!-- BAGIAN KIRI: BRANDING & INFORMASI (Disembunyikan di layar kecil) -->
        <div
            class="hidden lg:flex lg:w-1/2 bg-gradient-to-br from-indigo-900 to-blue-900 text-white min-h-screen flex-col items-center justify-center p-12 relative overflow-hidden"
        >
            <!-- Hiasan Background (Opsional) -->
            <div class="absolute -right-20 -top-20 opacity-10">
                <svg
                    width="400"
                    height="400"
                    viewBox="0 0 24 24"
                    fill="currentColor"
                >
                    <path
                        d="M12 22C6.477 22 2 17.523 2 12S6.477 2 12 2s10 4.477 10 10-4.477 10-10 10zm0-2a8 8 0 100-16 8 8 0 000 16z"
                    ></path>
                </svg>
            </div>
            <div class="absolute left-10 bottom-10 opacity-10">
                <svg
                    width="200"
                    height="200"
                    viewBox="0 0 24 24"
                    fill="currentColor"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="1.5"
                        d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"
                    ></path>
                </svg>
            </div>

            <div class="relative z-10 text-center max-w-lg">
                <img
                    src="/logo.png"
                    alt="Logo Utama SIGAP"
                    class="w-48 h-auto mx-auto mb-8 drop-shadow-xl"
                />
                <h1
                    class="text-5xl font-black mb-4 tracking-tight text-white drop-shadow-md"
                >
                    SIGAP
                </h1>
                <p class="text-lg text-indigo-200 font-medium leading-relaxed">
                    Sistem Informasi Digitalisasi Arsip Perencanaan dan Regulasi
                </p>
                <div class="mt-10 pt-10 border-t border-indigo-700/50">
                    <p class="text-sm text-indigo-300">
                        Bagian Perencanaan dan Administrasi<br />SSDM Polri
                    </p>
                </div>
            </div>
        </div>

        <!-- BAGIAN KANAN: FORM LOGIN -->
        <div
            class="w-full lg:w-1/2 min-h-screen flex items-center justify-center px-4 sm:px-6 lg:px-16 py-12"
        >
            <div class="max-w-md w-full">
                <!-- Logo untuk Mobile & Judul Form -->
                <div class="text-center mb-10">
                    <img
                        src="/logo_horizontal.png"
                        alt="Logo SIGAP Form"
                        class="h-20 lg:h-24 w-auto mx-auto mb-6 drop-shadow-sm transition-all"
                    />
                    <h2 class="text-3xl font-extrabold text-gray-900">
                        Masuk Akun
                    </h2>
                    <p class="text-sm text-gray-500 mt-2 font-medium">
                        Gunakan identitas NRP/NIP Anda untuk masuk ke sistem.
                    </p>
                </div>

                <!-- Notifikasi Status -->
                <div
                    v-if="status"
                    class="mb-6 font-bold text-sm text-emerald-700 bg-emerald-50 p-4 rounded-xl border border-emerald-200 text-center"
                >
                    {{ status }}
                </div>

                <!-- Kotak Form Utama -->
                <div
                    class="bg-white rounded-3xl shadow-xl border border-gray-100 p-8 sm:p-10"
                >
                    <form @submit.prevent="submit" class="space-y-6">
                        <!-- Input NRP / NIP -->
                        <div>
                            <InputLabel
                                for="nrp_nip"
                                value="NRP / NIP"
                                class="font-bold text-gray-700"
                            />
                            <TextInput
                                id="nrp_nip"
                                type="text"
                                class="mt-1.5 block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition"
                                v-model="form.nrp_nip"
                                required
                                autofocus
                                placeholder="Masukkan NRP atau NIP"
                            />
                            <InputError
                                class="mt-2"
                                :message="form.errors.nrp_nip"
                            />
                        </div>

                        <!-- Input Password -->
                        <div>
                            <InputLabel
                                for="password"
                                value="Password"
                                class="font-bold text-gray-700"
                            />
                            <TextInput
                                id="password"
                                type="password"
                                class="mt-1.5 block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition"
                                v-model="form.password"
                                required
                                placeholder="••••••••"
                            />
                            <InputError
                                class="mt-2"
                                :message="form.errors.password"
                            />
                        </div>

                        <!-- Checkbox Remember Me -->
                        <div class="flex items-center justify-between">
                            <label
                                class="flex items-center cursor-pointer group"
                            >
                                <Checkbox
                                    name="remember"
                                    v-model:checked="form.remember"
                                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                                />
                                <span
                                    class="ms-2 text-sm text-gray-600 font-medium group-hover:text-indigo-600 transition-colors"
                                    >Ingat Saya</span
                                >
                            </label>
                        </div>

                        <!-- Tombol Submit -->
                        <div class="pt-2">
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="w-full flex justify-center py-3.5 px-4 border border-transparent rounded-xl shadow-sm text-sm font-bold text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 transition-all"
                            >
                                Log In Sistem
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Tombol Kembali ke Portal -->
                <div class="text-center mt-8">
                    <Link
                        :href="route('home')"
                        class="inline-flex items-center justify-center text-sm font-bold text-indigo-600 hover:text-indigo-500 transition-colors bg-indigo-50 hover:bg-indigo-100 px-6 py-2.5 rounded-full"
                    >
                        <svg
                            class="w-4 h-4 mr-2"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M10 19l-7-7m0 0l7-7m-7 7h18"
                            ></path>
                        </svg>
                        Kembali ke Portal Publik
                    </Link>
                </div>
            </div>
        </div>
    </div>
</template>
