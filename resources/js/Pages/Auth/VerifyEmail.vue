<template>
  <Head title="Verifikasi Email" />

  <div class="relative min-h-screen flex items-center justify-center overflow-y-auto">

    <!-- BACKGROUND (lebih dominan teal) -->
    <div
      class="fixed inset-0"
      style="background: linear-gradient(to bottom, #045C6B 0%, #0C505C 40%, rgba(255,255,255,0.15) 75%, #ffffff 100%);"
    ></div>

    <!-- ALERT -->
    <transition name="slide-down">
      <div
        v-if="showAlert"
        class="fixed top-5 left-1/2 -translate-x-1/2 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50"
      >
        Berhasil Mengirimkan Ulang Verifikasi Email
      </div>
    </transition>

    <!-- CONTENT -->
    <div class="relative z-10 w-full flex justify-center px-4 py-12">
      <div
        class="bg-[#0C505C] text-white p-10 sm:p-12 w-full max-w-2xl shadow-xl rounded-3xl transition duration-300 hover:shadow-2xl hover:shadow-[#0C505C]/40"
      >

        <!-- HEADER -->
        <div class="flex items-center justify-center gap-3 mb-8">
          <img src="/images/remove backround 1.png" class="w-12" />
          <div class="text-sm leading-tight text-center">
            <p class="font-bold text-lg">Sekretariat Daerah</p>
            <p class="text-lg">Kabupaten Boyolali</p>
          </div>
        </div>

        <!-- ICON + TITLE (SEJAJAR) -->
        <div class="flex items-center justify-center gap-4 mb-6">
          <div class="w-14 h-14 rounded-full bg-white/20 flex items-center justify-center text-xl">
            📧
          </div>
          <h2 class="text-xl sm:text-2xl font-bold">
            Verifikasi Email Anda
          </h2>
        </div>

        <!-- TEXT -->
        <p class="text-center text-sm leading-relaxed px-6 mb-4 text-gray-100">
          Terima kasih telah mendaftar! Sebelum melanjutkan, mohon verifikasi alamat email Anda dengan mengklik link yang baru saja kami kirimkan ke email Anda.
        </p>

        <p class="text-center text-sm mb-10 text-gray-300">
          Jika Anda tidak menerima email, kami dapat mengirimkan ulang link verifikasi.
        </p>

        <!-- BUTTONS -->
        <div class="flex flex-col items-center gap-5">

          <!-- RESEND -->
          <button
            @click="resendVerification"
            :disabled="form.processing"
            class="w-[260px] bg-green-500 text-white py-2.5 rounded-xl font-bold
            hover:bg-green-600 hover:scale-105 hover:shadow-lg hover:shadow-green-400/40
            active:scale-95 transition duration-200"
          >
            Kirim Ulang Verifikasi
          </button>

          <!-- DASHBOARD -->
          <a
            href="/"
            class="w-[260px] bg-gray-500 text-white py-2.5 rounded-xl font-bold text-center
            hover:bg-gray-600 hover:scale-105 hover:shadow-lg hover:shadow-gray-400/30
            active:scale-95 transition duration-200"
          >
            Kembali ke Dashboard
          </a>

          <!-- LOGOUT -->
          <button
            @click="logout"
            class="w-[260px] bg-red-500 text-white py-2.5 rounded-xl font-bold
            hover:bg-red-600 hover:scale-105 hover:shadow-lg hover:shadow-red-400/40
            active:scale-95 transition duration-200"
          >
            Logout
          </button>

        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { Head, useForm, usePage, router } from '@inertiajs/vue3'
import { computed, ref, watch } from 'vue'

const page = usePage()
const form = useForm({})
const showAlert = ref(false)

const status = computed(() => page.props.flash?.status)

watch(status, (val) => {
  if (val) {
    showAlert.value = true
    setTimeout(() => (showAlert.value = false), 3000)
  }
})

const resendVerification = () => {
  form.post(route('verification.send'), {
    preserveScroll: true,
  })
}

const logout = () => {
  router.post(route('logout'))
}
</script>

<style>
.slide-down-enter-active,
.slide-down-leave-active {
  transition: all 0.4s ease;
}
.slide-down-enter-from {
  opacity: 0;
  transform: translateY(-20px);
}
.slide-down-enter-to {
  opacity: 1;
  transform: translateY(0);
}
.slide-down-leave-from {
  opacity: 1;
}
.slide-down-leave-to {
  opacity: 0;
  transform: translateY(-20px);
}
</style>