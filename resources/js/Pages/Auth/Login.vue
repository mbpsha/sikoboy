<template>
    <Head title="Login" />

    <div class="relative min-h-screen flex items-center justify-center overflow-y-auto">
        <!-- Background -->
        <div
            class="fixed inset-0 bg-cover bg-center"
            style="background-image: url('/storage/images/image19.png')"
        ></div>

        <!-- Gradient -->
        <div class="fixed inset-0 bg-gradient-to-b from-transparent via-white/50 to-white"></div>

        <!-- CONTENT -->
        <div class="relative z-10 w-full flex justify-center px-4 py-10">
            <div
                class="bg-[#0C505C] text-white p-8 sm:p-10 w-full max-w-2xl shadow-lg rounded-3xl"
            >
                <!-- HEADER -->
                <div class="flex items-center justify-center gap-3 mb-8">
                    <img src="/storage/images/removebackround1.png" class="w-12" />
                    <div class="text-sm leading-tight">
                        <p class="font-bold text-lg">Sekretariat Daerah</p>
                        <p class="text-lg">Kabupaten Boyolali</p>
                    </div>
                </div>

                <!-- TITLE -->
                <h2 class="text-center text-lg sm:text-xl font-bold mb-2">
                    Masuk ke Akun Anda
                </h2>

                <p class="text-center text-sm mb-10">
                    Belum punya akun?
                    <span
                        class="text-green-400 cursor-pointer"
                        @click="goRegister"
                    >
                        Daftar Disini
                    </span>
                </p>

                <form @submit.prevent="submit" class="space-y-4 mr-4 ml-4">

                    <!-- EMAIL -->
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
                        <p v-if="form.errors.email" class="text-red-400 text-sm mt-1 text-left">
                            {{ form.errors.email }}
                        </p>
                    </div>

                    <!-- PASSWORD -->
                    <div class="relative">
                        <p class="text-center mb-2 font-semibold text-xl">
                            Password
                        </p>
                        <input
                            :type="showPassword ? 'text' : 'password'"
                            v-model="form.password"
                            placeholder="********"
                            class="w-full pl-3 pr-3 py-2 rounded bg-white text-black text-center placeholder:text-center border border-gray-300 focus:outline-none focus:ring-2 focus:ring-[#0C505C]/40 focus:border-[#0C505C] transition duration-200"
                        />

                        <!-- TOGGLE -->
                        <span
                            class="absolute right-3 top-11 cursor-pointer"
                            @click="togglePassword"
                        >
                            👁️
                        </span>

                        <p v-if="form.errors.password" class="text-red-400 text-sm mt-1 text-left">
                            {{ form.errors.password }}
                        </p>
                    </div>

                    <!-- BUTTON -->
                    <div class="flex justify-center mt-16">
                        <button
                            type="submit"
                            class="w-[220px] mx-auto block bg-white text-[#0C505C] py-2 rounded font-bold
                             hover:bg-[#0C505C] hover:text-white hover:scale-105 hover:shadow-md active:scale-95 transition duration-200"
                            :disabled="form.processing"
                        >
                            MASUK
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

const goRegister = () => {
    router.visit("/register");
};

const form = useForm({
    email: "",
    password: "",
});

const submit = () => {
    form.post("/login");
};
</script>
