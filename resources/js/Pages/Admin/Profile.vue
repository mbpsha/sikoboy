<template>
  <AdminLayout title="Profil Saya">
    <div class="max-w-2xl mx-auto space-y-6">

      <!-- Header Card -->
      <div class="bg-teal-700 rounded-2xl px-6 py-8 flex items-center gap-5">
        <div class="w-16 h-16 rounded-full bg-white/20 flex items-center justify-center text-white text-3xl font-bold shrink-0">
          {{ (profile.nama || profile.email || '?').charAt(0).toUpperCase() }}
        </div>
        <div>
          <p class="text-white text-xl font-semibold">{{ profile.nama || '-' }}</p>
          <p class="text-teal-200 text-sm">{{ profile.email }}</p>
          <span class="inline-block mt-1.5 px-3 py-0.5 rounded-full bg-white/20 text-white text-xs font-medium">
            {{ profile.divisi || 'Administrator' }}
          </span>
        </div>
      </div>

      <!-- Flash messages -->
      <div v-if="$page.props.flash?.success" class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-xl text-sm">
        ✓ {{ $page.props.flash.success }}
      </div>
      <div v-if="$page.props.flash?.error" class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-xl text-sm">
        ✕ {{ $page.props.flash.error }}
      </div>

      <!-- Form Profil -->
      <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
          <h2 class="text-base font-semibold text-gray-800">Informasi Profil</h2>
          <p class="text-xs text-gray-500 mt-0.5">Perbarui nama, divisi, dan email akun Anda</p>
        </div>
        <form @submit.prevent="submitProfile" class="px-6 py-5 space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
            <input
              v-model="profileForm.nama"
              type="text"
              placeholder="Nama lengkap"
              class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500 transition"
            />
            <p v-if="profileForm.errors.nama" class="text-red-500 text-xs mt-1">{{ profileForm.errors.nama }}</p>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Divisi / Bidang</label>
            <input
              v-model="profileForm.divisi"
              type="text"
              placeholder="Nama divisi atau bidang"
              class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500 transition"
            />
            <p v-if="profileForm.errors.divisi" class="text-red-500 text-xs mt-1">{{ profileForm.errors.divisi }}</p>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
            <input
              v-model="profileForm.email"
              type="email"
              placeholder="email@example.com"
              class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500 transition"
            />
            <p v-if="profileForm.errors.email" class="text-red-500 text-xs mt-1">{{ profileForm.errors.email }}</p>
          </div>

          <div class="flex justify-end pt-2">
            <button
              type="submit"
              :disabled="profileForm.processing"
              class="px-5 py-2.5 bg-teal-600 hover:bg-teal-700 text-white text-sm font-medium rounded-lg disabled:opacity-50 transition"
            >
              {{ profileForm.processing ? 'Menyimpan...' : 'Simpan Perubahan' }}
            </button>
          </div>
        </form>
      </div>

      <!-- Form Password -->
      <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
          <h2 class="text-base font-semibold text-gray-800">Ubah Password</h2>
          <p class="text-xs text-gray-500 mt-0.5">Pastikan password baru minimal 8 karakter</p>
        </div>
        <form @submit.prevent="submitPassword" class="px-6 py-5 space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Password Saat Ini</label>
            <div class="relative">
              <input
                v-model="passwordForm.current_password"
                :type="showCurrent ? 'text' : 'password'"
                placeholder="********"
                class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm pr-10 focus:outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500 transition"
              />
              <button type="button" @click="toggleShowCurrent" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                <svg v-if="showCurrent" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M2 12s4-7 10-7 10 7 10 7-4 7-10 7-10-7-10-7z"/><circle cx="12" cy="12" r="3"/></svg>
                <svg v-else class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M2 12s4-7 10-7 10 7 10 7-4 7-10 7-10-7-10-7z"/><circle cx="12" cy="12" r="3"/><path d="M3 3l18 18"/></svg>
              </button>
            </div>
            <p v-if="passwordForm.errors.current_password" class="text-red-500 text-xs mt-1">{{ passwordForm.errors.current_password }}</p>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Password Baru</label>
            <div class="relative">
              <input
                v-model="passwordForm.new_password"
                :type="showNew ? 'text' : 'password'"
                placeholder="********"
                class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm pr-10 focus:outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500 transition"
              />
              <button type="button" @click="toggleShowNew" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                <svg v-if="showNew" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M2 12s4-7 10-7 10 7 10 7-4 7-10 7-10-7-10-7z"/><circle cx="12" cy="12" r="3"/></svg>
                <svg v-else class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M2 12s4-7 10-7 10 7 10 7-4 7-10 7-10-7-10-7z"/><circle cx="12" cy="12" r="3"/><path d="M3 3l18 18"/></svg>
              </button>
            </div>
            <p v-if="passwordForm.errors.new_password" class="text-red-500 text-xs mt-1">{{ passwordForm.errors.new_password }}</p>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Password Baru</label>
            <div class="relative">
              <input
                v-model="passwordForm.new_password_confirmation"
                :type="showConfirm ? 'text' : 'password'"
                placeholder="********"
                class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm pr-10 focus:outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500 transition"
              />
              <button type="button" @click="toggleShowConfirm" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                <svg v-if="showConfirm" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M2 12s4-7 10-7 10 7 10 7-4 7-10 7-10-7-10-7z"/><circle cx="12" cy="12" r="3"/></svg>
                <svg v-else class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M2 12s4-7 10-7 10 7 10 7-4 7-10 7-10-7-10-7z"/><circle cx="12" cy="12" r="3"/><path d="M3 3l18 18"/></svg>
              </button>
            </div>
            <p v-if="passwordForm.errors.new_password_confirmation" class="text-red-500 text-xs mt-1">{{ passwordForm.errors.new_password_confirmation }}</p>
          </div>

          <div class="flex justify-end pt-2">
            <button
              type="submit"
              :disabled="passwordForm.processing"
              class="px-5 py-2.5 bg-gray-800 hover:bg-gray-900 text-white text-sm font-medium rounded-lg disabled:opacity-50 transition"
            >
              {{ passwordForm.processing ? 'Menyimpan...' : 'Ubah Password' }}
            </button>
          </div>
        </form>
      </div>

    </div>
  </AdminLayout>
</template>

<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { useForm } from '@inertiajs/vue3'
import { ref } from 'vue'
import Swal from 'sweetalert2'

const props = defineProps({
  profile: Object,
})

const profileForm = useForm({
  nama:   props.profile.nama   ?? '',
  divisi: props.profile.divisi ?? '',
  email:  props.profile.email  ?? '',
})

const passwordForm = useForm({
  current_password:          '',
  new_password:              '',
  new_password_confirmation: '',
})

const showCurrent = ref(false)
const showNew     = ref(false)
const showConfirm = ref(false)

function toggleShowCurrent() { showCurrent.value = !showCurrent.value }
function toggleShowNew()     { showNew.value     = !showNew.value     }
function toggleShowConfirm() { showConfirm.value = !showConfirm.value }

function submitProfile() {
  profileForm.put(route('admin.profile.update'), {
    preserveScroll: true,
    onSuccess: () => {
      Swal.fire({
        icon: 'success',
        title: 'Berhasil!',
        text: 'Informasi profil berhasil diperbarui.',
        timer: 2000,
        showConfirmButton: false,
        timerProgressBar: true,
      })
    },
    onError: (errors) => {
      const first = Object.values(errors)[0]
      Swal.fire({
        icon: 'error',
        title: 'Gagal menyimpan',
        text: Array.isArray(first) ? first[0] : String(first ?? 'Periksa data yang diisi.'),
      })
    },
  })
}

function submitPassword() {
  passwordForm.put(route('admin.profile.password'), {
    preserveScroll: true,
    onSuccess: () => {
      passwordForm.reset()
      Swal.fire({
        icon: 'success',
        title: 'Password diperbarui!',
        text: 'Password akun Anda berhasil diganti.',
        timer: 2000,
        showConfirmButton: false,
        timerProgressBar: true,
      })
    },
    onError: (errors) => {
      const first = Object.values(errors)[0]
      Swal.fire({
        icon: 'error',
        title: 'Gagal mengubah password',
        text: Array.isArray(first) ? first[0] : String(first ?? 'Periksa data yang diisi.'),
      })
    },
  })
}
</script>