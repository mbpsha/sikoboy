<script setup>
import { ref, computed } from 'vue';
import { usePage, Link, router } from '@inertiajs/vue3';
import Header from '@/Components/Header.vue';
import Footer from '@/Components/Footer.vue';
import DetailNotif from '@/Components/Mitra/Profile/DetailNotif.vue';

const page = usePage();
const allNotifications = computed(() => page.props?.allNotifications || []);

const isDetailNotifOpen = ref(false);
const selectedNotification = ref(null);
const searchQuery = ref('');
const filterStatus = ref('all');
const sortBy = ref('newest');

const openDetailNotif = (notification) => {
  selectedNotification.value = notification;
  isDetailNotifOpen.value = true;
};

// ➡️ CTA: arahkan ke card kerjasama di halaman Profile (tab Riwayat) + highlight
const goToKerjasamaCard = (notification, event) => {
  if (event) event.stopPropagation();
  const kerjasamaId = notification?.kerjasama_id;
  if (!kerjasamaId) {
    openDetailNotif(notification);
    return;
  }
  router.visit(route('mitra.profile.index'), {
    data: { focus_kerjasama: kerjasamaId, tab: 'riwayat' },
    preserveScroll: false,
  });
};

const goBack = () => {
  try {
    router.back();
  } catch (e) {
    window.history.back();
  }
};

const filteredNotifications = computed(() => {
  let filtered = [...allNotifications.value];
  
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase();
    filtered = filtered.filter(notif => 
      notif.title.toLowerCase().includes(query) ||
      notif.message.toLowerCase().includes(query) ||
      (notif.nomor_kerjasama && notif.nomor_kerjasama.toLowerCase().includes(query))
    );
  }
  
  if (filterStatus.value !== 'all') {
    if (filterStatus.value === 'active') {
      filtered = filtered.filter(notif => notif.status_type !== 'expired');
    } else if (filterStatus.value === 'expired') {
      filtered = filtered.filter(notif => notif.status_type === 'expired');
    }
  }
  
  if (sortBy.value === 'newest') {
    filtered.sort((a, b) => new Date(b.created_at) - new Date(a.created_at));
  } else if (sortBy.value === 'oldest') {
    filtered.sort((a, b) => new Date(a.created_at) - new Date(b.created_at));
  }
  
  return filtered;
});

const groupedNotifications = computed(() => {
  const groups = {};
  filteredNotifications.value.forEach(notif => {
    const date = new Date(notif.created_at).toLocaleDateString('id-ID', {
      day: 'numeric',
      month: 'long',
      year: 'numeric'
    });
    if (!groups[date]) {
      groups[date] = [];
    }
    groups[date].push(notif);
  });
  return groups;
});

const getStatusBadgeClass = (status) => {
  const classes = {
    'expiring_soon': 'bg-orange-100 text-orange-700 border-orange-200',
    'expired': 'bg-red-100 text-red-700 border-red-200',
    'approved': 'bg-green-100 text-green-700 border-green-200',
    'pending': 'bg-blue-100 text-blue-700 border-blue-200',
  };
  return classes[status] || 'bg-gray-100 text-gray-700 border-gray-200';
};

const getStatusLabel = (status) => {
  const labels = {
    'expiring_soon': 'Aktif',
    'expired': 'Expired',
    'approved': 'Aktif',
    'pending': 'Aktif',
  };
  return labels[status] || 'Aktif';
};

const getNotificationIcon = (notif) => {
  if (notif.status_type === 'expired') {
    return {
      bg: 'bg-red-500',
      icon: '<svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>'
    };
  } else if (notif.status_type === 'expiring_soon') {
    return {
      bg: 'bg-orange-500',
      icon: '<svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>'
    };
  }
  return {
    bg: 'bg-blue-500',
    icon: '<svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" /></svg>'
  };
};
</script>

<template>
  <div class="min-h-screen bg-gradient-to-br from-[#8AB4BB] to-[#6B9BA5] flex flex-col">
    <Header />

    <main class="flex-1 pt-24 sm:pt-28 md:pt-32 pb-8 sm:pb-10 px-4 sm:px-6 md:px-8">
      <div class="max-w-6xl mx-auto">
        
        <!-- Header -->
        <div class="mb-6 sm:mb-8">
          <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 sm:gap-4">
            <div>
              <h1 class="text-2xl sm:text-3xl font-bold text-[#17464E]">Semua Notifikasi</h1>
              <p class="text-xs sm:text-sm text-[#2f5e66] mt-1">Pantau seluruh pengingat status kerjasama Anda</p>
            </div>
            <button @click.prevent="goBack" class="px-3 sm:px-4 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-blue-300 transition whitespace-nowrap">
              <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 inline-block mr-2" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M9.707 14.707a1 1 0 01-1.414 0l-5-5a1 1 0 010-1.414l5-5a1 1 0 011.414 1.414L6.414 9H17a1 1 0 110 2H6.414l3.293 3.293a1 1 0 010 1.414z" clip-rule="evenodd" />
              </svg>
              Kembali
            </button>
          </div>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-lg sm:rounded-2xl shadow-md p-4 sm:p-6 mb-6">
          <div class="flex flex-col sm:flex-row gap-3 sm:gap-4">
            <div class="flex-1">
              <div class="relative">
                <svg xmlns="http://www.w3.org/2000/svg" class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 sm:w-5 h-4 sm:h-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <input v-model="searchQuery" type="text" placeholder="Cari Notifikasi..." class="w-full pl-10 pr-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#2f6f73] focus:border-transparent text-xs sm:text-sm" />
              </div>
            </div>
            <div class="flex gap-2 flex-1 sm:flex-initial">
              <select v-model="filterStatus" class="flex-1 px-3 sm:px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#2f6f73] bg-white text-xs sm:text-sm">
                <option value="all">Semua Status</option>
                <option value="active">Aktif</option>
                <option value="expired">Expired</option>
              </select>
              <select v-model="sortBy" class="flex-1 px-3 sm:px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#2f6f73] bg-white text-xs sm:text-sm">
                <option value="newest">Terbaru</option>
                <option value="oldest">Terlama</option>
              </select>
            </div>
          </div>
        </div>

        <!-- Notifications List -->
        <div class="space-y-3 sm:space-y-4">
          <div v-if="Object.keys(groupedNotifications).length === 0" class="text-center py-8 sm:py-12 bg-white rounded-lg sm:rounded-2xl shadow-md">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-12 sm:w-16 h-12 sm:h-16 text-gray-300 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
            </svg>
            <p class="text-gray-500 text-xs sm:text-sm">Tidak ada notifikasi</p>
          </div>

          <div v-for="(notifications, date) in groupedNotifications" :key="date">
            <h3 class="text-xs sm:text-sm font-semibold text-gray-600 mb-2 sm:mb-3">{{ date }}</h3>
            <div class="space-y-2 sm:space-y-3">
              <div v-for="notif in notifications" :key="notif.id" class="bg-white rounded-lg sm:rounded-2xl shadow-md p-4 sm:p-6 hover:shadow-lg transition-shadow cursor-pointer" @click="openDetailNotif(notif)">
                <div class="flex flex-col sm:flex-row items-start sm:items-start gap-3 sm:gap-4">
                  <div v-html="getNotificationIcon(notif).icon" :class="'w-12 sm:w-16 h-12 sm:h-16 rounded-lg sm:rounded-xl flex items-center justify-center flex-shrink-0 ' + getNotificationIcon(notif).bg"></div>
                  
                  <div class="flex-1 min-w-0">
                    <h4 :class="'text-sm sm:text-base font-semibold mb-1 ' + (notif.status_type === 'expired' ? 'text-red-600' : (notif.status_type === 'expiring_soon' ? 'text-orange-600' : 'text-[#2f6f73]'))">
                      {{ notif.title }}
                    </h4>
                    <p class="text-xs sm:text-sm text-gray-600 mb-2 sm:mb-3 break-words">{{ notif.message }}</p>
                    <div class="flex flex-wrap items-center gap-2 sm:gap-4 text-xs">
                      <div class="flex items-center gap-1 text-gray-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3 sm:w-4 h-3 sm:h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <span>{{ notif.nomor_kerjasama || '-' }}</span>
                      </div>
                      <span :class="'px-2 py-1 rounded-full border text-xs font-medium ' + getStatusBadgeClass(notif.status_type)">
                        {{ getStatusLabel(notif.status_type) }}
                      </span>
                      <div v-if="notif.tanggal_berakhir" class="flex items-center gap-1 text-gray-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3 sm:w-4 h-3 sm:h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <span>{{ new Date(notif.tanggal_berakhir).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' }) }}</span>
                      </div>
                    </div>
                  </div>

                  <div class="flex flex-row sm:flex-col items-center sm:items-end gap-2 mt-3 sm:mt-0 w-full sm:w-auto flex-shrink-0">
                    <div v-if="notif.days_left !== undefined && notif.days_left !== null && notif.status_type !== 'expired'" :class="'w-12 sm:w-16 h-12 sm:h-16 rounded-lg sm:rounded-xl flex items-center justify-center text-white font-bold text-xs sm:text-base ' + (notif.days_left <= 30 ? 'bg-red-500' : (notif.days_left <= 90 ? 'bg-orange-500' : 'bg-green-500'))">
                      <div class="text-center">
                        <div class="leading-none">{{ notif.days_left }}</div>
                        <div class="text-xs">Hari</div>
                      </div>
                    </div>
                    <!-- CTA: langsung ke card kerjasama terkait (scroll + highlight) -->
                    <button
                      v-if="notif.kerjasama_id"
                      class="px-3 sm:px-4 py-1 sm:py-2 bg-[#2f6f73] text-white rounded-lg text-xs sm:text-sm font-medium hover:bg-[#1e565a] transition-colors flex-1 sm:flex-initial flex items-center justify-center gap-1"
                      @click.stop="goToKerjasamaCard(notif, $event)"
                      title="Lihat kerjasama terkait"
                    >
                      Lihat Kerjasama
                      <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                      </svg>
                    </button>
                    <button class="px-3 sm:px-4 py-1 sm:py-2 border border-[#2f6f73] text-[#2f6f73] rounded-lg text-xs sm:text-sm font-medium hover:bg-[#2f6f73] hover:text-white transition-colors flex-1 sm:flex-initial" @click.stop="openDetailNotif(notif)">
                      Lihat Detail →
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </main>

    <DetailNotif :is-open="isDetailNotifOpen" :notification="selectedNotification" @close="isDetailNotifOpen = false" />
    <Footer />
  </div>
</template>

<style scoped>
::-webkit-scrollbar {
  width: 8px;
}
::-webkit-scrollbar-track {
  background: #f1f1f1;
}
::-webkit-scrollbar-thumb {
  background: #c1ced0;
  border-radius: 4px;
}
::-webkit-scrollbar-thumb:hover {
  background: #8FA4A7;
}
</style>
