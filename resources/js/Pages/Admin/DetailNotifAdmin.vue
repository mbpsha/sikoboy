<template>
  <AdminLayout title="Detail Notifikasi Kerjasama">

    <!-- ================= MAIN CONTENT ================= -->
    <main
      v-if="notificationType === 'status'"
      class="flex-1"
    >
      <div class="p-6 space-y-6">

        <!-- Alert -->
        <div
          class="rounded-lg p-4 flex items-start gap-3"
          :class="
            kerjasama?.status === 'expired'
              ? 'bg-red-50 border border-red-200'
              : 'bg-orange-50 border border-orange-200'
          "
        >
          <div class="flex-1">
            <h3
              class="font-semibold"
              :class="
                kerjasama?.status === 'expired'
                  ? 'text-red-800'
                  : 'text-orange-800'
              "
            >
              {{
                kerjasama?.status === 'expired'
                  ? 'Kerjasama Telah Berakhir'
                  : 'Kerjasama Akan Segera Berakhir'
              }}
            </h3>

            <p
              class="text-sm mt-1"
              :class="
                kerjasama?.status === 'expired'
                  ? 'text-red-700'
                  : 'text-orange-700'
              "
            >
              {{
                kerjasama?.status === 'expired'
                  ? `Kerjasama ini telah berakhir pada ${formatDate(
                      kerjasama.tanggalBerakhir
                    )}.`
                  : `Kerjasama ini akan berakhir dalam ${kerjasama.daysLeft} hari lagi pada ${formatDate(
                      kerjasama.tanggalBerakhir
                    )}.`
              }}
            </p>
          </div>
        </div>

        <!-- Info -->
        <div class="bg-white rounded-lg shadow-sm p-6">

          <div class="flex items-start gap-3 mb-6">
            <span
              class="px-3 py-1 text-sm font-bold rounded-full"
              :class="
                kerjasama?.type === 'MITRA'
                  ? 'bg-blue-100 text-blue-700'
                  : 'bg-green-100 text-green-700'
              "
            >
              {{ kerjasama?.type }}
            </span>
          </div>

          <h2 class="text-2xl font-bold text-gray-800 mb-6">
            {{ kerjasama?.judul }}
          </h2>

          <div class="grid grid-cols-2 gap-6">

            <div>
              <p class="text-sm text-gray-500">
                Nomor Kerjasama
              </p>
              <p class="font-semibold">
                {{ kerjasama?.nomor }}
              </p>
            </div>

            <div>
              <p class="text-sm text-gray-500">
                Durasi
              </p>
              <p class="font-semibold">
                {{ kerjasama?.durasi }}
              </p>
            </div>

            <div>
              <p class="text-sm text-gray-500">
                Tanggal Mulai
              </p>
              <p class="font-semibold">
                {{ formatDate(kerjasama?.tanggalMulai) }}
              </p>
            </div>

            <div>
              <p class="text-sm text-gray-500">
                Tanggal Berakhir
              </p>
              <p class="font-semibold">
                {{ formatDate(kerjasama?.tanggalBerakhir) }}
              </p>
            </div>

          </div>
        </div>

        <!-- Pihak -->
        <div class="grid grid-cols-2 gap-6">

          <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="font-semibold mb-4">
              Pihak Pertama (SETDA)
            </h3>

            <p class="font-semibold">
              {{ kerjasama?.pihak1?.nama_instansi }}
            </p>

            <p class="text-gray-600">
              {{ kerjasama?.pihak1?.alamat }}
            </p>
          </div>

          <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="font-semibold mb-4">
              Pihak Kedua ({{ kerjasama?.type }})
            </h3>

            <p class="font-semibold">
              {{ kerjasama?.pihak2?.nama_instansi }}
            </p>

            <p class="text-gray-600">
              {{ kerjasama?.pihak2?.alamat }}
            </p>
          </div>

        </div>

        <div class="flex justify-end">
          <button
            @click="goBack"
            class="px-6 py-2 bg-teal-600 text-white rounded-lg"
          >
            Tutup
          </button>
        </div>

      </div>
    </main>

    <!-- ================= UPLOAD DOKUMEN ================= -->
    <main
      v-else-if="notificationType === 'upload'"
      class="flex-1"
    >
      <div class="p-6">

        <div class="bg-white rounded-lg shadow-sm p-6">

          <h2 class="text-2xl font-bold text-gray-800 mb-6">
            {{ notification.title }}
          </h2>

          <div class="grid grid-cols-2 gap-6">

            <div>
              <p class="text-sm text-gray-500">
                Nama File
              </p>
              <p class="font-semibold">
                {{ notification.nama_file }}
              </p>
            </div>

            <div>
              <p class="text-sm text-gray-500">
                Versi Dokumen
              </p>
              <p class="font-semibold">
                {{ notification.versi_dokumen }}
              </p>
            </div>

            <div>
              <p class="text-sm text-gray-500">
                Nama Mitra
              </p>
              <p class="font-semibold">
                {{ notification.mitra }}
              </p>
            </div>

            <div>
              <p class="text-sm text-gray-500">
                Nomor Kerjasama
              </p>
              <p class="font-semibold">
                {{ notification.kerjasama?.nomor }}
              </p>
            </div>

            <div class="col-span-2">
              <p class="text-sm text-gray-500">
                Judul Kerjasama
              </p>
              <p class="font-semibold">
                {{ notification.kerjasama?.judul }}
              </p>
            </div>

          </div>

          <div class="flex justify-end mt-8">
            <button
              @click="goBack"
              class="px-6 py-2 bg-teal-600 text-white rounded-lg"
            >
              Tutup
            </button>
          </div>

        </div>

      </div>
    </main>
    <!-- ================= END MAIN CONTENT ================= -->

  </AdminLayout>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import { router, Link, usePage } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

// --- Detail Page Logic ---
const props = defineProps({
  notificationType: String,
  notification: Object
})

const kerjasama = computed(() => props.notification)

const goBack = () => {
  router.visit('/admin/notifikasi')
}

const formatDate = (dateString) => {
  if (!dateString) return '-'

  return new Date(dateString).toLocaleDateString(
    'id-ID',
    {
      year: 'numeric',
      month: 'long',
      day: 'numeric',
    }
  )
}
</script>
