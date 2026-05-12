<script setup>
import { computed } from 'vue';

const props = defineProps({
  isOpen: {
    type: Boolean,
    default: false,
  },
  notification: {
    type: Object,
    default: null,
  },
});

const emit = defineEmits(['close']);

const closeModal = () => {
  emit('close');
};

// Format tanggal ke format Indonesia
const formatDate = (dateString) => {
  if (!dateString) return '-';
  const date = new Date(dateString);
  const options = { day: 'numeric', month: 'long', year: 'numeric' };
  return date.toLocaleDateString('id-ID', options);
};

// Get icon dan color berdasarkan status
const getNotificationStyle = computed(() => {
  if (props.notification?.status_type === 'expired') {
    return {
      iconBg: 'bg-red-100',
      iconColor: 'text-red-500',
      icon: `<svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
      </svg>`,
      titleColor: 'text-red-600',
      daysLeftColor: 'text-red-600',
      statusBg: 'bg-red-100',
      statusText: 'text-red-800',
    };
  } else if (props.notification?.status_type === 'expiring_soon') {
    return {
      iconBg: 'bg-orange-100',
      iconColor: 'text-orange-500',
      icon: `<svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
      </svg>`,
      titleColor: 'text-[#2f6f73]',
      daysLeftColor: 'text-orange-600',
      statusBg: 'bg-orange-100',
      statusText: 'text-orange-800',
    };
  } else {
    return {
      iconBg: 'bg-blue-100',
      iconColor: 'text-blue-500',
      icon: `<svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
      </svg>`,
      titleColor: 'text-[#2f6f73]',
      daysLeftColor: 'text-[#2f6f73]',
      statusBg: 'bg-green-100',
      statusText: 'text-green-800',
    };
  }
});
</script>

<template>
  <Teleport to="body">
    <Transition name="modal">
      <div v-if="isOpen" class="fixed inset-0 z-[100] overflow-y-auto">
        <!-- Backdrop -->
        <div 
          class="fixed inset-0 bg-black/50 transition-opacity" 
          @click="closeModal"
        ></div>

        <!-- Modal Container -->
        <div class="flex min-h-full items-center justify-center p-4">
          <!-- Modal Panel -->
          <div class="relative bg-white rounded-2xl shadow-2xl max-w-2xl w-full overflow-hidden">
            
            <!-- Close Button -->
            <button
              @click="closeModal"
              class="absolute top-4 right-4 w-8 h-8 flex items-center justify-center rounded-full bg-gray-100 hover:bg-gray-200 transition-colors z-10"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>

            <!-- Header -->
            <div class="px-8 pt-8 pb-4">
              <h2 class="text-2xl font-bold text-[#17464E]">Detail Notifikasi</h2>
            </div>

            <!-- Content -->
            <div class="px-8 pb-8">
              <!-- Alert Header -->
              <div class="flex items-start gap-4 mb-6">
                <div class="flex-shrink-0">
                  <div 
                    class="w-16 h-16 rounded-full flex items-center justify-center"
                    :class="getNotificationStyle.iconBg"
                  >
                    <div 
                      v-html="getNotificationStyle.icon"
                      :class="getNotificationStyle.iconColor"
                    ></div>
                  </div>
                </div>
                <div class="flex-1">
                  <h3 
                    class="text-lg font-semibold mb-1"
                    :class="getNotificationStyle.titleColor"
                  >
                    {{ notification?.title || 'Kerjasama Anda akan berakhir dalam' }}
                  </h3>
                  <!-- Tampilkan "Hari Lagi" hanya jika BELUM expired -->
                  <p 
                    v-if="notification?.status_type !== 'expired' && notification?.days_left"
                    class="text-2xl font-bold"
                    :class="getNotificationStyle.daysLeftColor"
                  >
                    {{ notification.days_left }} Hari Lagi
                  </p>
                  <!-- Tampilkan pesan expired jika sudah expired -->
                 
                </div>
              </div>

              <!-- Informasi Kerjasama Card -->
              <div class="bg-gray-50 rounded-xl p-6 border border-gray-200">
                <h4 class="text-base font-semibold text-[#2f6f73] mb-4">Informasi Kerjasama</h4>
                
                <div class="space-y-3">
                  <!-- Judul Kerjasama -->
                  <div class="flex gap-4">
                    <div class="w-40 flex-shrink-0">
                      <span class="text-sm text-gray-600">Judul Kerjasama</span>
                    </div>
                    <div class="flex-1">
                      <span class="text-sm text-gray-600 mx-2">:</span>
                      <span class="text-sm font-medium text-gray-800">
                        {{ notification?.kerjasama_judul || '"Perjanjian Kerja Sama antara Dinas Pemberdayaan Masyarakat dan Desa dan PT BPR Bank Boyolali (Perseroda) tentang Pengelolaan Atas Rekening Kas Desa Melalui PT BPR Bank Boyolali (Perseroda)" akan berakhir dalam 90 hari' }}
                      </span>
                    </div>
                  </div>

                  <!-- Nomor Kerjasama -->
                  <div class="flex gap-4">
                    <div class="w-40 flex-shrink-0">
                      <span class="text-sm text-gray-600">Nomor Kerjasama</span>
                    </div>
                    <div class="flex-1">
                      <span class="text-sm text-gray-600 mx-2">:</span>
                      <span class="text-sm font-medium text-gray-800">
                        {{ notification?.nomor_kerjasama || '012/SP-KS/PT-ABC/V/2026' }}
                      </span>
                    </div>
                  </div>

                  <!-- Tanggal Mulai -->
                  <div class="flex gap-4">
                    <div class="w-40 flex-shrink-0">
                      <span class="text-sm text-gray-600">Tanggal Mulai</span>
                    </div>
                    <div class="flex-1">
                      <span class="text-sm text-gray-600 mx-2">:</span>
                      <span class="text-sm font-medium text-gray-800">
                        {{ formatDate(notification?.tanggal_mulai) || '2 Januari 2026' }}
                      </span>
                    </div>
                  </div>

                  <!-- Tanggal Berakhir -->
                  <div class="flex gap-4">
                    <div class="w-40 flex-shrink-0">
                      <span class="text-sm text-gray-600">Tanggal Berakhir</span>
                    </div>
                    <div class="flex-1">
                      <span class="text-sm text-gray-600 mx-2">:</span>
                      <span class="text-sm font-medium text-gray-800">
                        {{ formatDate(notification?.tanggal_berakhir) || '2 Januari 2027' }}
                      </span>
                    </div>
                  </div>

                  <!-- Status -->
                  <div class="flex gap-4">
                    <div class="w-40 flex-shrink-0">
                      <span class="text-sm text-gray-600">Status</span>
                    </div>
                    <div class="flex-1">
                      <span class="text-sm text-gray-600 mx-2">:</span>
                      <span 
                        class="inline-block px-3 py-1 text-xs font-medium rounded-full"
                        :class="getNotificationStyle.statusBg + ' ' + getNotificationStyle.statusText"
                      >
                        {{ notification?.status || 'Aktif' }}
                      </span>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Action Buttons -->
              <div class="mt-6 flex justify-end gap-3">
                
                
              </div>
            </div>
          </div>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<style scoped>
/* Modal Transition */
.modal-enter-active,
.modal-leave-active {
  transition: opacity 0.3s ease;
}

.modal-enter-active .modal-panel,
.modal-leave-active .modal-panel {
  transition: transform 0.3s ease;
}

.modal-enter-from,
.modal-leave-to {
  opacity: 0;
}

.modal-enter-from .modal-panel,
.modal-leave-to .modal-panel {
  transform: scale(0.95);
}
</style>