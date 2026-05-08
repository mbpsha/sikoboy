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
            <div class="bg-[#0C505C] text-white p-8 sm:p-10 w-full max-w-2xl shadow-lg rounded-3xl">

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
                    <span class="text-green-400 cursor-pointer" @click="goRegister">
                        Daftar Disini
                    </span>
                </p>

                <form @submit.prevent="submit" class="space-y-4 mr-4 ml-4">

                    <!-- EMAIL / USERNAME -->
                    <div>
                        <p class="text-center mb-2 font-semibold text-xl">Email/Username</p>
                        <input
                            type="text"
                            v-model="form.login"
                            placeholder="email/username"
                            class="w-full pl-3 pr-3 py-2 rounded bg-white text-black text-center placeholder:text-center border border-gray-300 focus:outline-none focus:ring-2 focus:ring-[#0C505C]/40 focus:border-[#0C505C] transition duration-200"
                        />
                        <p v-if="form.errors.login" class="text-red-400 text-sm mt-1 text-center">
                            {{ form.errors.login }}
                        </p>
                    </div>

                    <!-- PASSWORD -->
                    <div class="relative">
                        <p class="text-center mb-2 font-semibold text-xl">Password</p>
                        <input
                            :type="showPassword ? 'text' : 'password'"
                            v-model="form.password"
                            placeholder="********"
                            class="w-full pl-3 pr-3 py-2 rounded bg-white text-black text-center placeholder:text-center border border-gray-300 focus:outline-none focus:ring-2 focus:ring-[#0C505C]/40 focus:border-[#0C505C] transition duration-200"
                        />
                        <span
                            class="absolute right-3 top-11 cursor-pointer"
                            @click="togglePassword"
                        >👁️</span>
                        <p v-if="form.errors.password" class="text-red-400 text-sm mt-1 text-center">
                            {{ form.errors.password }}
                        </p>
                    </div>

                    <!-- RECAPTCHA -->
                    <div class="flex flex-col items-center my-4">
                        <div v-if="hasRecaptcha">
                            <div ref="recaptchaElement" class="g-recaptcha" :data-sitekey="recaptchaSiteKey"></div>
                        </div>
                        <p v-else class="text-sm text-gray-300 italic">
                            reCAPTCHA disabled (development)
                        </p>
                        <p v-if="form.errors.captcha" class="text-red-400 text-sm mt-2 text-center">
                            {{ form.errors.captcha }}
                        </p>
                    </div>

                    <!-- SUBMIT -->
                    <div class="flex justify-center mt-6">
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="w-[220px] mx-auto block bg-white text-[#0C505C] py-2 rounded font-bold hover:bg-[#0C505C] hover:text-white hover:scale-105 hover:shadow-md active:scale-95 transition duration-200 disabled:opacity-60 disabled:cursor-not-allowed"
                        >
                            {{ form.processing ? 'Memproses...' : 'MASUK' }}
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</template>

<script setup>
import { useForm, router, Head, usePage } from "@inertiajs/vue3"
import { ref, computed, onMounted } from "vue"

const page = usePage()

// Ambil site key dari Inertia shared props (di-share via HandleInertiaRequests)
const recaptchaSiteKey = computed(() => page.props.recaptcha_site_key ?? '')
const hasRecaptcha     = computed(() => !!recaptchaSiteKey.value)

const showPassword     = ref(false)
const recaptchaElement = ref(null)

const togglePassword = () => { showPassword.value = !showPassword.value }

const goRegister = () => {
    router.visit(typeof route !== 'undefined' ? route('register') : '/register')
}

const form = useForm({
    login: '',
    password: '',
    'g-recaptcha-response': '',
})

const submit = () => {
    const token = window.grecaptcha?.getResponse?.() ?? ''

    // Jika reCAPTCHA aktif tapi belum dicentang, tolak submit
    if (hasRecaptcha.value && !token) {
        form.setError('captcha', 'Silakan centang verifikasi CAPTCHA terlebih dahulu.')
        return
    }

    form['g-recaptcha-response'] = token

    form.post(typeof route !== 'undefined' ? route('login') : '/login', {
        onError: () => {
            // Reset widget reCAPTCHA jika login gagal
            window.grecaptcha?.reset?.()
        },
    })
}

onMounted(() => {
    if (!hasRecaptcha.value) return

    // Jangan load ulang jika script sudah ada
    if (document.querySelector('script[src*="recaptcha/api.js"]')) return

    const script    = document.createElement('script')
    script.src      = 'https://www.google.com/recaptcha/api.js'
    script.async    = true
    script.defer    = true
    document.head.appendChild(script)
})
</script>