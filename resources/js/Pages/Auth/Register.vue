<script setup>
import GuestLayout from "@/Layouts/GuestLayout.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";

const form = useForm({
    name: "",
    nrp_nip: "",
    jabatan: "",
    pangkat: "",
    password: "",
    password_confirmation: "",
});

const opsiPangkat = [
    "KOMBES POL",
    "AKBP",
    "KOMPOL",
    "AKP",
    "IPTU",
    "IPDA",
    "PEMBINA TK I",
    "PEMBINA",
    "PENATA TK I",
    "PENATA",
    "PENDA TK I",
    "PENDA",
];

const submit = () => {
    form.post(route("register"), {
        onFinish: () => form.reset("password", "password_confirmation"),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Register" />

        <form @submit.prevent="submit">
            <div>
                <InputLabel for="name" value="Nama Lengkap" />
                <TextInput
                    id="name"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.name"
                    required
                    autofocus
                />
                <InputError class="mt-2" :message="form.errors.name" />
            </div>

            <div class="mt-4">
                <InputLabel for="nrp_nip" value="NRP / NIP" />
                <TextInput
                    id="nrp_nip"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.nrp_nip"
                    required
                />
                <InputError class="mt-2" :message="form.errors.nrp_nip" />
            </div>

            <div class="mt-4">
                <InputLabel for="jabatan" value="Jabatan" />
                <TextInput
                    id="jabatan"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.jabatan"
                    required
                />
                <InputError class="mt-2" :message="form.errors.jabatan" />
            </div>

            <div class="mt-4">
                <InputLabel for="pangkat" value="Pangkat" />
                <select
                    id="pangkat"
                    v-model="form.pangkat"
                    required
                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                >
                    <option value="" disabled>-- Pilih Pangkat --</option>
                    <option
                        v-for="pangkat in opsiPangkat"
                        :key="pangkat"
                        :value="pangkat"
                    >
                        {{ pangkat }}
                    </option>
                </select>
                <InputError class="mt-2" :message="form.errors.pangkat" />
            </div>

            <div class="mt-4">
                <InputLabel for="password" value="Password" />
                <TextInput
                    id="password"
                    type="password"
                    class="mt-1 block w-full"
                    v-model="form.password"
                    required
                />
                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div class="mt-4">
                <InputLabel
                    for="password_confirmation"
                    value="Konfirmasi Password"
                />
                <TextInput
                    id="password_confirmation"
                    type="password"
                    class="mt-1 block w-full"
                    v-model="form.password_confirmation"
                    required
                />
                <InputError
                    class="mt-2"
                    :message="form.errors.password_confirmation"
                />
            </div>

            <div class="flex items-center justify-end mt-4">
                <Link
                    :href="route('login')"
                    class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                >
                    Sudah punya akun?
                </Link>

                <PrimaryButton
                    class="ms-4"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    Daftar
                </PrimaryButton>
            </div>
        </form>
    </GuestLayout>
</template>
