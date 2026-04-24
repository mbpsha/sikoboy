<script setup>
import { ref, onMounted } from 'vue';
import { usePage, useForm, Link } from '@inertiajs/vue3';
import Header from '@/Components/Header.vue';
import Footer from '@/Components/Footer.vue';

const page = usePage();
const mitra = page.props.value?.mitra;

const form = useForm({
  nama_perusahaan: mitra?.nama_perusahaan || '',
  pic: mitra?.pic || '',
  no_handphone: mitra?.no_handphone || '',
  alamat: mitra?.alamat || '',
});

const showPasswordForm = ref(false);
const passwordForm = useForm({
  current_password: '',
  new_password: '',
  new_password_confirmation: '',
});

const updateProfile = () => {
  form.put(route('mitra.profile.update'), {
    onFinish: () => form.reset(),
  });
};

const updatePassword = () => {
  passwordForm.put(route('mitra.profile.password'), {
    onFinish: () => {
      passwordForm.reset();
      showPasswordForm.value = false;
    },
  });
};

onMounted(() => {
  console.log('[Edit.vue] mounted — edit component mounted')
})
</script>

<template>
  <div class="min-h-screen bg-gradient-to-br from-[#8AB4BB] to-[#6B9BA5] flex flex-col">
    <!-- Header -->
    <Header />

    <!-- Main Content -->
    <main class="flex-1 pt-24 pb-8 px-6">
      <div class="max-w-4xl mx-auto">
        <!-- Title Section -->
        <div class="mb-8">
          <Link :href="route('mitra.profile.index')" class="flex items-center gap-2 text-[#17464E] hover:text-[#0f3238] mb-4">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 111.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
            </svg>
            Kembali ke Profil
          </Link>
          <h1 class="text-4xl font-bold text-[#17464E] mb-2">Edit Profil Mitra</h1>
          <p class="text-gray-700">Perbarui informasi perusahaan Anda</p>
        </div>

        <!-- Edit Form Card -->
        <div class="bg-white rounded-lg shadow-lg p-8 mb-6">
          <form @submit.prevent="updateProfile" class="space-y-6">
            <!-- Nama Perusahaan -->
            <div>
              <label for="nama_perusahaan" class="block text-sm font-semibold text-[#17464E] mb-2">
                Nama Perusahaan
              </label>
              <input
                v-model="form.nama_perusahaan"
                id="nama_perusahaan"
                type="text"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#17464E] focus:border-transparent"
                placeholder="Masukkan nama perusahaan"
              />
              <p v-if="form.errors.nama_perusahaan" class="text-red-500 text-sm mt-1">
                {{ form.errors.nama_perusahaan }}
              </p>
            </div>

            <!-- PIC (Penanggung Jawab) -->
            <div>
              <label for="pic" class="block text-sm font-semibold text-[#17464E] mb-2">
                Penanggung Jawab (PIC)
              </label>
              <input
                v-model="form.pic"
                id="pic"
                type="text"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#17464E] focus:border-transparent"
                placeholder="Nama penanggung jawab"
              />
              <p v-if="form.errors.pic" class="text-red-500 text-sm mt-1">
                {{ form.errors.pic }}
              </p>
            </div>

            <!-- No. Handphone -->
            <div>
              <label for="no_handphone" class="block text-sm font-semibold text-[#17464E] mb-2">
                No. Handphone
              </label>
              <input
                v-model="form.no_handphone"
                id="no_handphone"
                type="tel"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#17464E] focus:border-transparent"
                placeholder="Contoh: 08123456789"
              />
              <p v-if="form.errors.no_handphone" class="text-red-500 text-sm mt-1">
                {{ form.errors.no_handphone }}
              </p>
            </div>

            <!-- Alamat -->
            <div>
              <label for="alamat" class="block text-sm font-semibold text-[#17464E] mb-2">
                Alamat
              </label>
              <textarea
                v-model="form.alamat"
                id="alamat"
                rows="4"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#17464E] focus:border-transparent"
                placeholder="Masukkan alamat lengkap perusahaan"
              ></textarea>
              <p v-if="form.errors.alamat" class="text-red-500 text-sm mt-1">
                {{ form.errors.alamat }}
              </p>
            </div>

            <!-- Action Buttons -->
            <div class="flex gap-3 pt-4">
              <button
                type="submit"
                :disabled="form.processing"
                class="flex-1 px-6 py-3 bg-[#17464E] text-white rounded-lg font-semibold hover:bg-[#0f3238] transition disabled:opacity-50 disabled:cursor-not-allowed"
              >
                <span v-if="form.processing" class="inline-block">Menyimpan...</span>
                <span v-else>Simpan Perubahan</span>
              </button>
              <Link
                :href="route('mitra.profile.index')"
                class="flex-1 px-6 py-3 bg-gray-300 text-[#17464E] rounded-lg font-semibold hover:bg-gray-400 transition text-center"
              >
                Batal
              </Link>
            </div>
          </form>
        </div>

        <!-- Password Change Card -->
        <div class="bg-white rounded-lg shadow-lg p-8">
          <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-bold text-[#17464E]">Ubah Password</h2>
            <button
              @click="showPasswordForm = !showPasswordForm"
              type="button"
              class="px-4 py-2 bg-gray-200 text-[#17464E] rounded-lg font-semibold hover:bg-gray-300 transition"
            >
              {{ showPasswordForm ? 'Tutup' : 'Ubah Password' }}
            </button>
          </div>

          <form v-show="showPasswordForm" @submit.prevent="updatePassword" class="space-y-4 border-t pt-6">
            <!-- Current Password -->
            <div>
              <label for="current_password" class="block text-sm font-semibold text-[#17464E] mb-2">
                Password Saat Ini
              </label>
              <input
                v-model="passwordForm.current_password"
                id="current_password"
                type="password"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#17464E] focus:border-transparent"
                placeholder="Masukkan password saat ini"
              />
              <p v-if="passwordForm.errors.current_password" class="text-red-500 text-sm mt-1">
                {{ passwordForm.errors.current_password }}
              </p>
            </div>

            <!-- New Password -->
            <div>
              <label for="new_password" class="block text-sm font-semibold text-[#17464E] mb-2">
                Password Baru
              </label>
              <input
                v-model="passwordForm.new_password"
                id="new_password"
                type="password"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#17464E] focus:border-transparent"
                placeholder="Masukkan password baru (minimal 8 karakter)"
              />
              <p v-if="passwordForm.errors.new_password" class="text-red-500 text-sm mt-1">
                {{ passwordForm.errors.new_password }}
              </p>
            </div>

            <!-- Confirm Password -->
            <div>
              <label for="new_password_confirmation" class="block text-sm font-semibold text-[#17464E] mb-2">
                Konfirmasi Password Baru
              </label>
              <input
                v-model="passwordForm.new_password_confirmation"
                id="new_password_confirmation"
                type="password"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#17464E] focus:border-transparent"
                placeholder="Konfirmasi password baru"
              />
              <p v-if="passwordForm.errors.new_password_confirmation" class="text-red-500 text-sm mt-1">
                {{ passwordForm.errors.new_password_confirmation }}
              </p>
            </div>

            <!-- Action Buttons -->
            <div class="flex gap-3 pt-4">
              <button
                type="submit"
                :disabled="passwordForm.processing"
                class="flex-1 px-6 py-3 bg-[#17464E] text-white rounded-lg font-semibold hover:bg-[#0f3238] transition disabled:opacity-50 disabled:cursor-not-allowed"
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