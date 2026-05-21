<script setup>
import { ref, onMounted, computed, watch } from 'vue';
import { usePage, useForm, Link, router } from '@inertiajs/vue3';
import Swal from 'sweetalert2';
import 'sweetalert2/dist/sweetalert2.min.css';

const page = usePage();
const mitra = computed(() => page.props.value?.mitra ?? page.props.mitra ?? null);
const mode = page.props.value?.mode || 'edit';

const form = useForm({
  nama_perusahaan: '',
  pic: '',
  no_handphone: '',
  alamat: '',
});

const showPasswordForm = ref(false);
const passwordForm = useForm({
  current_password: '',
  new_password: '',
  new_password_confirmation: '',
});

// show/hide toggles for password fields
const showCurrent = ref(false)
const showNew = ref(false)
const showConfirm = ref(false)

function toggleShowCurrent() {
  showCurrent.value = !showCurrent.value
  console.log('toggleShowCurrent ->', showCurrent.value)
}

function toggleShowNew() {
  showNew.value = !showNew.value
  console.log('toggleShowNew ->', showNew.value)
}

function toggleShowConfirm() {
  showConfirm.value = !showConfirm.value
  console.log('toggleShowConfirm ->', showConfirm.value)
}

const updateProfile = () => {
  // If we're completing profile for the first time, use store route
  if (mode === 'complete') {
    form.post(route('mitra.profile.store'), {
      onSuccess: () => {
        try {
          Swal.fire({
            icon: 'success',
            title: 'Profil lengkap',
            text: 'Profil berhasil dilengkapi',
            timer: 2000,
            showConfirmButton: false,
          });
        } catch (e) {}
        try { router.visit(route('mitra.profile.index')) } catch (e) { window.location.href = '/mitra/profile' }
      },
      onFinish: () => form.reset(),
    });
    return;
  }

  // Otherwise update existing mitra
  form.put(route('mitra.profile.update'), {
    onSuccess: () => {
      try {
        Swal.fire({
          icon: 'success',
          title: 'Tersimpan',
          text: 'Perubahan informasi akun disimpan',
          timer: 2000,
          showConfirmButton: false,
        });
      } catch (e) {}
      try { router.visit(route('mitra.profile.index')) } catch (e) { window.location.href = '/mitra/profile' }
    },
    onFinish: () => form.reset(),
  });
};

const updatePassword = () => {
  passwordForm.put(route('mitra.profile.password'), {
    onSuccess: () => {
      try {
        Swal.fire({
          icon: 'success',
          title: 'Berhasil',
          text: 'Password berhasil diubah',
          timer: 2000,
          showConfirmButton: false,
        });
      } catch (e) {}
      try { router.visit(route('mitra.profile.index')) } catch (e) { window.location.href = '/mitra/profile' }
    },
    onFinish: () => {
      passwordForm.reset();
      showPasswordForm.value = false;
    },
  });
};

onMounted(() => {
  console.log('[Edit.vue] mounted — edit component mounted')
  // Ensure form fields are prefilled from server-provided `mitra` data
  if (mitra.value) {
    form.nama_perusahaan = mitra.value.nama_perusahaan || '';
    form.pic = mitra.value.pic || '';
    form.no_handphone = mitra.value.no_handphone || '';
    form.alamat = mitra.value.alamat || '';
  }
});

// react to prop changes (in case Inertia updates props after mount)
watch(mitra, (v) => {
  if (v) {
    form.nama_perusahaan = v.nama_perusahaan || '';
    form.pic = v.pic || '';
    form.no_handphone = v.no_handphone || '';
    form.alamat = v.alamat || '';
  }
})
</script>

<template>
  <div class="min-h-screen bg-gradient-to-br from-[#8AB4BB] to-[#6B9BA5] flex flex-col">
    <!-- Header -->
    <Header />

    <!-- Main Content -->
    <main class="flex-1 pt-20 sm:pt-24 pb-8 px-4 sm:px-6 md:px-8">
      <div class="max-w-4xl mx-auto">
        <!-- Title Section -->
        <div class="mb-6 sm:mb-8 gap-3">
          <Link :href="route('mitra.profile.index')" class="flex items-center gap-2 text-[#17464E] hover:text-[#0f3238] mb-3 sm:mb-4 text-xs sm:text-sm">
            <svg class="w-4 sm:w-5 h-4 sm:h-5" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 111.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
            </svg>
            Kembali ke Profil
          </Link>
          <h1 class="text-2xl sm:text-3xl md:text-4xl font-bold text-[#17464E] mb-2">Edit Profil Mitra</h1>
          <p class="text-xs sm:text-sm text-gray-700">Perbarui informasi perusahaan Anda</p>
        </div>

        <!-- Edit Form Card -->
        <div class="bg-white rounded-lg sm:rounded-xl shadow-lg p-5 sm:p-8 mb-6">
          <form @submit.prevent="updateProfile" class="space-y-5 sm:space-y-6">
            <!-- Nama Perusahaan -->
            <div>
              <label for="nama_perusahaan" class="block text-xs sm:text-sm font-semibold text-[#17464E] mb-2">
                Nama Perusahaan
              </label>
              <input
                v-model="form.nama_perusahaan"
                id="nama_perusahaan"
                type="text"
                class="w-full px-4 sm:px-5 py-2 sm:py-3 border border-gray-300 rounded-lg sm:rounded-xl focus:outline-none focus:ring-2 focus:ring-[#17464E] focus:border-transparent text-xs sm:text-sm"
                placeholder="Masukkan nama perusahaan"
              />
              <p v-if="form.errors.nama_perusahaan" class="text-red-500 text-xs mt-1">
                {{ form.errors.nama_perusahaan }}
              </p>
            </div>

            <!-- PIC (Penanggung Jawab) -->
            <div>
              <label for="pic" class="block text-xs sm:text-sm font-semibold text-[#17464E] mb-2">
                Penanggung Jawab (PIC)
              </label>
              <input
                v-model="form.pic"
                id="pic"
                type="text"
                class="w-full px-4 sm:px-5 py-2 sm:py-3 border border-gray-300 rounded-lg sm:rounded-xl focus:outline-none focus:ring-2 focus:ring-[#17464E] focus:border-transparent text-xs sm:text-sm"
                placeholder="Nama penanggung jawab"
              />
              <p v-if="form.errors.pic" class="text-red-500 text-xs mt-1">
                {{ form.errors.pic }}
              </p>
            </div>

            <!-- No. Handphone -->
            <div>
              <label for="no_handphone" class="block text-xs sm:text-sm font-semibold text-[#17464E] mb-2">
                No. Handphone
              </label>
              <input
                v-model="form.no_handphone"
                id="no_handphone"
                type="tel"
                class="w-full px-4 sm:px-5 py-2 sm:py-3 border border-gray-300 rounded-lg sm:rounded-xl focus:outline-none focus:ring-2 focus:ring-[#17464E] focus:border-transparent text-xs sm:text-sm"
                placeholder="Contoh: 08123456789"
              />
              <p v-if="form.errors.no_handphone" class="text-red-500 text-xs mt-1">
                {{ form.errors.no_handphone }}
              </p>
            </div>

            <!-- Alamat -->
            <div>
              <label for="alamat" class="block text-xs sm:text-sm font-semibold text-[#17464E] mb-2">
                Alamat
              </label>
              <textarea
                v-model="form.alamat"
                id="alamat"
                rows="4"
                class="w-full px-4 sm:px-5 py-2 sm:py-3 border border-gray-300 rounded-lg sm:rounded-xl focus:outline-none focus:ring-2 focus:ring-[#17464E] focus:border-transparent text-xs sm:text-sm"
                placeholder="Masukkan alamat lengkap perusahaan"
              ></textarea>
              <p v-if="form.errors.alamat" class="text-red-500 text-xs mt-1">
                {{ form.errors.alamat }}
              </p>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row gap-3 sm:gap-4 pt-4 sm:pt-6">
              <button
                type="submit"
                :disabled="form.processing"
                class="flex-1 px-4 sm:px-6 py-2 sm:py-3 bg-[#17464E] text-white rounded-lg sm:rounded-xl font-semibold hover:bg-[#0f3238] transition disabled:opacity-50 disabled:cursor-not-allowed text-xs sm:text-sm"
              >
                <span v-if="form.processing" class="inline-block">Menyimpan...</span>
                <span v-else>Simpan Perubahan</span>
              </button>
              <Link
                :href="route('mitra.profile.index')"
                class="flex-1 px-4 sm:px-6 py-2 sm:py-3 bg-gray-300 text-[#17464E] rounded-lg sm:rounded-xl font-semibold hover:bg-gray-400 transition text-center text-xs sm:text-sm"
              >
                Batal
              </Link>
            </div>
          </form>
        </div>

        <!-- Password Change Card -->
        <div class="bg-white rounded-lg sm:rounded-xl shadow-lg p-5 sm:p-8">
          <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
            <h2 class="text-xl sm:text-2xl font-bold text-[#17464E]">Ubah Password</h2>
            <button
              @click="showPasswordForm = !showPasswordForm"
              type="button"
              class="px-3 sm:px-4 py-2 bg-gray-200 text-[#17464E] rounded-lg font-semibold hover:bg-gray-300 transition text-xs sm:text-sm"
            >
              {{ showPasswordForm ? 'Tutup' : 'Ubah Password' }}
            </button>
          </div>

          <form v-show="showPasswordForm" @submit.prevent="updatePassword" class="space-y-4 sm:space-y-5 border-t pt-6">
            <!-- Current Password -->
            <div>
              <label for="current_password" class="block text-xs sm:text-sm font-semibold text-[#17464E] mb-2">
                Password Saat Ini <span class="text-red-500">*</span>
              </label>
              <div class="relative">
                <input
                  v-model="passwordForm.current_password"
                  id="current_password"
                  :type="showCurrent ? 'text' : 'password'"
                  required
                  autocomplete="current-password"
                  class="w-full px-4 sm:px-5 py-2 sm:py-3 border border-gray-300 rounded-lg sm:rounded-xl focus:outline-none focus:ring-2 focus:ring-[#17464E] focus:border-transparent text-xs sm:text-sm"
                  placeholder="Masukkan password saat ini"
                />
              <button type="button" @click="toggleShowCurrent" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500">
                  <svg v-if="showCurrent" class="w-4 sm:w-5 h-4 sm:h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M2 12s4-7 10-7 10 7 10 7-4 7-10 7-10-7-10-7z"/>
                      <circle cx="12" cy="12" r="3"/>
                  </svg>
                  <svg v-else class="w-4 sm:w-5 h-4 sm:h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M2 12s4-7 10-7 10 7 10 7-4 7-10 7-10-7-10-7z"/>
                      <circle cx="12" cy="12" r="3"/>
                      <path stroke-linecap="round" stroke-linejoin="round" d="M3 3l18 18"/>
                  </svg>
              </button>
              </div>
              <p v-if="passwordForm.errors.current_password" class="text-red-500 text-xs mt-1">
                {{ passwordForm.errors.current_password }}
              </p>
            </div>

            <!-- New Password -->
            <div>
              <label for="new_password" class="block text-xs sm:text-sm font-semibold text-[#17464E] mb-2">
                Password Baru
              </label>
              <div class="relative">
                <input
                  v-model="passwordForm.new_password"
                  id="new_password"
                  :type="showNew ? 'text' : 'password'"
                  class="w-full px-4 sm:px-5 py-2 sm:py-3 border border-gray-300 rounded-lg sm:rounded-xl focus:outline-none focus:ring-2 focus:ring-[#17464E] focus:border-transparent text-xs sm:text-sm"
                  placeholder="Masukkan password baru (minimal 8 karakter)"
                />
                <button type="button" @click="toggleShowNew" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500">
                  <svg v-if="showNew" class="w-4 sm:w-5 h-4 sm:h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M2 12s4-7 10-7 10 7 10 7-4 7-10 7-10-7-10-7z"/>
                      <circle cx="12" cy="12" r="3"/>
                  </svg>
                  <svg v-else class="w-4 sm:w-5 h-4 sm:h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M2 12s4-7 10-7 10 7 10 7-4 7-10 7-10-7-10-7z"/>
                      <circle cx="12" cy="12" r="3"/>
                      <path stroke-linecap="round" stroke-linejoin="round" d="M3 3l18 18"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.477 0-8.268-2.943-9.542-7a10.05 10.05 0 012.042-3.182" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6.2 6.2L17.8 17.8" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.88 9.88a3 3 0 104.24 4.24" />
                  </svg>
                </button>
              </div>
              <p v-if="passwordForm.errors.new_password" class="text-red-500 text-sm mt-1">
                {{ passwordForm.errors.new_password }}
              </p>
            </div>

            <!-- Confirm Password -->
            <div>
              <label for="new_password_confirmation" class="block text-xs sm:text-sm font-semibold text-[#17464E] mb-2">
                Konfirmasi Password Baru
              </label>
              <div class="relative">
                <input
                  v-model="passwordForm.new_password_confirmation"
                  id="new_password_confirmation"
                  :type="showConfirm ? 'text' : 'password'"
                  class="w-full px-4 sm:px-5 py-2 sm:py-3 border border-gray-300 rounded-lg sm:rounded-xl focus:outline-none focus:ring-2 focus:ring-[#17464E] focus:border-transparent text-xs sm:text-sm"
                  placeholder="Konfirmasi password baru"
                />
              <button type="button" @click="toggleShowConfirm" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500">
                  <svg v-if="showConfirm" class="w-4 sm:w-5 h-4 sm:h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M2 12s4-7 10-7 10 7 10 7-4 7-10 7-10-7-10-7z"/>
                      <circle cx="12" cy="12" r="3"/>
                  </svg>
                  <svg v-else class="w-4 sm:w-5 h-4 sm:h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M2 12s4-7 10-7 10 7 10 7-4 7-10 7-10-7-10-7z"/>
                      <circle cx="12" cy="12" r="3"/>
                      <path stroke-linecap="round" stroke-linejoin="round" d="M3 3l18 18"/>
                  </svg>
              </button>
              </div>
              <p v-if="passwordForm.errors.new_password_confirmation" class="text-red-500 text-xs mt-1">
                {{ passwordForm.errors.new_password_confirmation }}
              </p>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row gap-3 sm:gap-4 pt-4 sm:pt-6">
              <button
                type="submit"
                :disabled="passwordForm.processing"
                class="flex-1 px-4 sm:px-6 py-2 sm:py-3 bg-[#17464E] text-white rounded-lg sm:rounded-xl font-semibold hover:bg-[#0f3238] transition disabled:opacity-50 disabled:cursor-not-allowed text-xs sm:text-sm"
              >
                <span v-if="passwordForm.processing">Menyimpan...</span>
                <span v-else>Simpan Password Baru</span>
              </button>
            </div>
          </form>
        </div>
      </div>
    </main>

    <!-- Footer -->
    <Footer />
  </div>
</template>