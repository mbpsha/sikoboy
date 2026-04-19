<template>
    <Head title="Register" />
    <div
        class="relative min-h-screen flex items-center justify-center overflow-y-auto"
    >
        <!-- Background -->
        <div
            class="fixed inset-0 bg-cover bg-center"
            style="
                background-image: url(&quot;/storage/images/image19.png&quot;);
            "
        ></div>

        <!-- Gradient -->
        <div
            class="fixed inset-0 bg-gradient-to-b from-transparent via-white/50 to-white"
        ></div>

        <!-- CONTENT -->
        <div class="relative z-10 w-full flex justify-center px-4 py-10">
            <div
                class="bg-[#0C505C] text-white p-8 sm:p-10 w-full max-w-2xl shadow-lg rounded-3xl"
            >
                <!-- HEADER (LOGO + TEXT SEJAJAR) -->
                <div class="flex items-center justify-center gap-3 mb-8">
                    <img
                        src="/storage/images/removebackround1.png"
                        class="w-12"
                    />
                    <div class="text-sm leading-tight">
                        <p class="font-bold text-lg">Sekretariat Daerah</p>
                        <p class="text-lg">Kabupaten Boyolali</p>
                    </div>
                </div>

                <h2 class="text-center text-lg sm:text-xl font-bold mb-2">
                    Daftar Akun Sebagai Mitra
                </h2>

                <p class="text-center text-sm mb-10">
                    Sudah punya akun?
                    <span
                        class="text-green-400 cursor-pointer"
                        @click="goLogin"
                    >
                        Masuk Disini
                    </span>
                </p>

                <form @submit.prevent="submit" class="space-y-4 mr-4 ml-4">
                    <div>
                        <p class="text-center mb-2 font-semibold text-xl">
                            Email
                        </p>
                        <input
                            type="email"
                            v-model="form.email"
                            placeholder="nama@gmail.com"
                            class="w-full pl-3 pr-3 py-2 rounded bg-white text-black text-center placeholder:text-center border border-gray-300 focus:outline-none focus:ring-2 focus:ring-[#0C505C]/40 focus:border-[#0C505C] transition duration-200"
                        />
                        <!-- ERROR -->
                        <p
                            v-if="form.errors.email"
                            class="text-red-400 text-sm mt-1 text-left"
                        >
                            {{ form.errors.email }}
                        </p>
                    </div>

                    <div>
                        <p class="text-center mb-2 font-semibold text-xl">
                            Nama Perusahaan
                        </p>
                        <input
                            type="text"
                            v-model="form.nama_perusahaan"
                            placeholder="Nama Perusahaan"
                            class="w-full pl-3 pr-3 py-2 rounded bg-white text-black text-center placeholder:text-center border border-gray-300 focus:outline-none focus:ring-2 focus:ring-[#0C505C]/40 focus:border-[#0C505C] transition duration-200"
                        />
                        <!-- ERROR -->
                        <p
                            v-if="form.errors.nama_perusahaan"
                            class="text-red-400 text-sm mt-1 text-left"
                        >
                            {{ form.errors.nama_perusahaan }}
                        </p>
                    </div>

                    <div>
                        <p class="text-center mb-2 font-semibold text-xl">
                            Nomor Handphone
                        </p>
                        <input
                            type="text"
                            v-model="form.no_handphone"
                            placeholder="Nomor Handphone"
                            class="w-full pl-3 pr-3 py-2 rounded bg-white text-black text-center placeholder:text-center border border-gray-300 focus:outline-none focus:ring-2 focus:ring-[#0C505C]/40 focus:border-[#0C505C] transition duration-200"
                        />
                        <!-- ERROR -->
                        <p
                            v-if="form.errors.no_handphone"
                            class="text-red-400 text-sm mt-1 text-left"
                        >
                            {{ form.errors.no_handphone }}
                        </p>
                    </div>

                    <div>
                        <p class="text-center mb-2 font-semibold text-xl">
                            Penanggung Jawab (PIC)
                        </p>
                        <input
                            type="text"
                            v-model="form.pic"
                            placeholder="Penanggung Jawab (PIC)"
                            class="w-full pl-3 pr-3 py-2 rounded bg-white text-black text-center placeholder:text-center border border-gray-300 focus:outline-none focus:ring-2 focus:ring-[#0C505C]/40 focus:border-[#0C505C] transition duration-200"
                        />
                        <!-- ERROR -->
                        <p
                            v-if="form.errors.pic"
                            class="text-red-400 text-sm mt-1 text-left"
                        >
                            {{ form.errors.pic }}
                        </p>
                    </div>

                    <div>
                        <p class="text-center mb-2 font-semibold text-xl">
                            Alamat
                        </p>
                        <textarea
                            v-model="form.alamat"
                            placeholder="Alamat"
                            class="w-full pl-3 pr-3 py-2 rounded bg-white text-black text-center placeholder:text-center border border-gray-300 focus:outline-none focus:ring-2 focus:ring-[#0C505C]/40 focus:border-[#0C505C] transition duration-200"
                        ></textarea>
                        <!-- ERROR -->
                        <p
                            v-if="form.errors.alamat"
                            class="text-red-400 text-sm mt-1 text-left"
                        >
                            {{ form.errors.alamat }}
                        </p>
                    </div>

                    <!-- Password -->
                    <div class="relative">
                        <p class="text-center mb-2 font-semibold text-xl">
                            Password
                        </p>
                        <input
                            :type="showPassword ? 'text' : 'password'"
                            v-model="form.password"
                            placeholder="Password"
                            class="w-full pl-3 pr-3 py-2 rounded bg-white text-black text-center placeholder:text-center border border-gray-300 focus:outline-none focus:ring-2 focus:ring-[#0C505C]/40 focus:border-[#0C505C] transition duration-200"
                        />
                        <span
                            class="absolute right-3 top-11 cursor-pointer"
                            @click="togglePassword"
                        >
                            👁️
                        </span>
                        <!-- ERROR -->
                        <p
                            v-if="form.errors.password"
                            class="text-red-400 text-sm mt-1 text-left"
                        >
                            {{ form.errors.password }}
                        </p>
                    </div>

                    <!-- Konfirmasi Password -->
                    <div class="relative">
                        <p class="text-center mb-2 font-semibold text-xl">
                            Konfirmasi Password
                        </p>
                        <input
                            :type="showPassword ? 'text' : 'password'"
                            v-model="form.password_confirmation"
                            placeholder="Konfirmasi Password"
                            class="w-full pl-3 pr-3 py-2 rounded bg-white text-black text-center placeholder:text-center border border-gray-300 focus:outline-none focus:ring-2 focus:ring-[#0C505C]/40 focus:border-[#0C505C] transition duration-200"
                        />
                        <span
                            class="absolute right-3 top-11 cursor-pointer"
                            @click="togglePassword"
                        >
                            👁️
                        </span>
                        <!-- ERROR -->
                        <p
                            v-if="form.errors.password_confirmation"
                            class="text-red-400 text-sm mt-1 text-left"
                        >
                            {{ form.errors.password_confirmation }}
                        </p>
                    </div>

                    <div class="flex justify-center mt-16">
                        <button
                            type="submit"
                            class="w-[220px] mx-auto block bg-white text-[#0C505C] py-2 rounded font-bold hover:bg-[#0C505C] hover:text-white hover:scale-105 hover:shadow-md active:scale-95 transition duration-200"
                            :disabled="form.processing"
                        >
                            DAFTAR
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script setup>
import { useForm, router, Head } from "@inertiajs/vue3";
import { ref } from "vue";

const showPassword = ref(false);

const togglePassword = () => {
    showPassword.value = !showPassword.value;
};

const goLogin = () => {
    router.visit("/login/mitra");
};

const form = useForm({
    email: "",
    nama_perusahaan: "",
    no_handphone: "",
    pic: "",
    alamat: "",
    password: "",
    password_confirmation: "",
});

const submit = () => {
    form.post("/register", {
        onSuccess: () => {
            // after successful registration, go to the email verification notice
            router.visit('/email/verify');
        },
    });
};
</script>
