<script setup>
import { ref, computed } from 'vue';
import { usePage, Link, router } from '@inertiajs/vue3';
import Header from '@/Components/Header.vue';
import Footer from '@/Components/Footer.vue';
import DetailNotif from '@/Components/Mitra/Profile/DetailNotif.vue';

const page = usePage();

// Get notifications from props
const allNotifications = computed(() => page.props?.allNotifications || []);

// Modal state
const isDetailNotifOpen = ref(false);
const selectedNotification = ref(null);

// Filter & Search
const searchQuery = ref('');
const filterStatus = ref('all');
const sortBy = ref('newest');

// Open detail modal
const openDetailNotif = (notification) => {
  selectedNotification.value = notification;
  isDetailNotifOpen.value = true;
};

const goBack = () => {
  try {
    router.back()
  } catch (e) {
    window.history.back()
  }
}

// Filter notifications
const filteredNotifications = computed(() => {
  let filtered = [...allNotifications.value];
  
  // Search filter
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase();
    filtered = filtered.filter(notif => 
      notif.title.toLowerCase().includes(query) ||
      notif.message.toLowerCase().includes(query) ||
      (notif.nomor_kerjasama && notif.nomor_kerjasama.toLowerCase().includes(query))
    );
  }
  
  // Status filter
 if (filterStatus.value !== 'all') {
  if (filterStatus.value === 'active') {
    // "Aktif" = tampilkan semua KECUALI expired
    filtered = filtered.filter(notif => notif.status_type !== 'expired');
  } else if (filterStatus.value === 'expired') {
    // "Expired" = tampilkan hanya yang expired
    filtered = filtered.filter(notif => notif.status_type === 'expired');
  }
}
  
  // Sort
  if (sortBy.value === 'newest') {
    filtered.sort((a, b) => new Date(b.created_at) - new Date(a.created_at));
  } else if (sortBy.value === 'oldest') {
    filtered.sort((a, b) => new Date(a.created_at) - new Date(b.created_at));
  } else if (sortBy.value === 'days_left') {
    filtered.sort((a, b) => (a.days_left || 999) - (b.days_left || 999));
  }
  
  return filtered;
});

// Group notifications by date
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

// Get status badge class
const getStatusBadgeClass = (status) => {
  const classes = {
    'expiring_soon': 'bg-orange-100 text-orange-700 border-orange-200',
    'expired': 'bg-red-100 text-red-700 border-red-200',
    'approved': 'bg-green-100 text-green-700 border-green-200',
    'pending': 'bg-blue-100 text-blue-700 border-blue-200',
  };
  return classes[status] || 'bg-gray-100 text-gray-700 border-gray-200';
};

// Get status label
const getStatusLabel = (status) => {
  const labels = {
    'expiring_soon': 'Aktif',
    'expired': 'Expired',
    'approved': 'Aktif',
    'pending': 'Aktif',
  };
  return labels[status] || 'Aktif';
};

// Get icon for notification type
const getNotificationIcon = (notif) => {
  if (notif.status_type === 'expired') {
    return {
      bg: 'bg-red-500',
      icon: `<svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
      </svg>`
    };
  } else if (notif.status_type === 'expiring_soon') {
    return {
      bg: 'bg-orange-500',
      icon: `<svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
      </svg>`
    };
  } else {
    return {
      bg: 'bg-blue-500',
      icon: `<svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
      </svg>`
    };
  }
};
</script>

<template>
  <div class="min-h-screen bg-gradient-to-br from-[#8AB4BB] to-[#6B9BA5] flex flex-col">
    <Header />

    <main class="flex-1 pt-32 pb-10 px-8">
      <div class="max-w-6xl mx-auto">
        
        <!-- Header -->
        <div class="mb-8">
          <div class="flex items-center justify-between">
            <h1 class="text-3xl font-bold text-[#17464E]">Semua Notifikasi</h1>
            <button
              @click.prevent="goBack"
              class="ml-4 px-3 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-sm focus:outline-none focus:ring-2 focus:ring-blue-300 transition"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 inline-block mr-2" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M9.707 14.707a1 1 0 01-1.414 0l-5-5a1 1 0 010-1.414l5-5a1 1 0 011.414 1.414L6.414 9H17a1 1 0 110 2H6.414l3.293 3.293a1 1 0 010 1.414z" clip-rule="evenodd" />
              </svg>
              Kembali
            </button>
          </div>
          <p class="text-sm text-[#2f5e66] mt-1">
            Pantau seluruh pengingat status kerjasama Anda dengan SETDA Boyolali
          </p>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-2xl shadow-md p-6 mb-6">
          <div class="flex flex-col md:flex-row gap-4">
            <!-- Search -->
            <div class="flex-1">
              <div class="relative">
                <svg xmlns="http://www.w3.org/2000/svg" class="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <input 
                  v-model="searchQuery"
                  type="text" 
                  placeholder="Cari Notifikasi..."
                  class="w-full pl-10 pr-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#2f6f73] focus:border-transparent"
                />
              </div>
            </div>

            <!-- Filter Status -->
            <div class="flex gap-2">
             <select v-model="filterStatus"  class="px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#2f6f73] bg-white">
  <option value="all">Semua Status</option>
  <option value="active">Aktif</option>  <!-- ✅ Ubah dari 'approved' jadi 'active' -->
  <option value="expired">Expired</option>
</select>

              <!-- Sort -->
              <select 
                v-model="sortBy"
                class="px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#2f6f73] bg-white"
              >
                <option value="newest">Terbaru</option>
                <option value="oldest">Terlama</option>
              </select>
            </div>
          </div>
        </div>

        <!-- Notifications List -->
        <div class="space-y-4">
          <div v-if="Object.keys(groupedNotifications).length === 0" class="text-center py-12 bg-white rounded-2xl shadow-md">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
            </svg>
            <p class="text-gray-500">Tidak ada notifikasi</p>
          </div>

          <div 
            v-for="(notifications, date) in groupedNotifications" 
            :key="date"
          >
            <!-- Date Group Header -->
            <h3 class="text-sm font-semibold text-gray-600 mb-3">{{ date }}</h3>
            
            <!-- Notification Cards -->
            <div class="space-y-3">
              <div 
                v-for="notif in notifications" 
                :key="notif.id"
                class="bg-white rounded-2xl shadow-md p-6 hover:shadow-lg transition-shadow cursor-pointer"
                @click="openDetailNotif(notif)"
              >
                <div class="flex items-start gap-4">
                  <!-- Icon -->
                  <div 
                    v-html="getNotificationIcon(notif).icon"
                    :class="`w-16 h-16 rounded-xl flex items-center justify-center flex-shrink-0 ${getNotificationIcon(notif).bg}`"
                  ></div>

                  <!-- Content -->
                  <div class="flex-1">
                    <h4 
                      class="text-base font-semibold mb-1"
                      :class="notif.status_type === 'expired' ? 'text-red-600' : (notif.status_type === 'expiring_soon' ? 'text-orange-600' : 'text-[#2f6f73]')"
                    >
                      {{ notif.title }}
                    </h4>
                    <p class="text-sm text-gray-600 mb-3">{{ notif.message }}</p>

                    <!-- Meta Info -->
                    <div class="flex flex-wrap items-center gap-4 text-xs">
                      <div class="flex items-center gap-1 text-gray-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <span>{{ notif.nomor_kerjasama || '-' }}</span>
                      </div>

                      <div class="flex items-center gap-1">
                        <span 
                          class="px-2 py-1 rounded-full border text-xs font-medium"
                          :class="getStatusBadgeClass(notif.status_type)"
                        >
                          {{ getStatusLabel(notif.status_type) }}
                        </span>
                      </div>

                      <div v-if="notif.tanggal_berakhir" class="flex items-center gap-1 text-gray-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <span>{{ new Date(notif.tanggal_berakhir).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' }) }}</span>
                      </div>
                    </div>
                  </div>

                 <!-- Days Left Badge & Button -->
<div class="flex flex-col items-end gap-2">
  <!-- Days Left Badge - HANYA tampil jika BELUM expired -->
  <div 
    v-if="notif.days_left !== undefined && notif.days_left !== null && notif.status_type !== 'expired'"
    class="w-16 h-16 rounded-xl flex items-center justify-center text-white font-bold"
    :class="notif.days_left <= 30 ? 'bg-red-500' : (notif.days_left <= 90 ? 'bg-orange-500' : 'bg-green-500')"
  >
    <div class="text-center">
      <div class="text-xl leading-none">{{ notif.days_left }}</div>
      <div class="text-xs">Hari Lagi</div>
    </div>
  </div>

  <button 
    class="px-4 py-2 border border-[#2f6f73] text-[#2f6f73] rounded-lg text-sm font-medium hover:bg-[#2f6f73] hover:text-white transition-colors"
    @click.stop="openDetailNotif(notif)"
  >
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

    <!-- Detail Notifikasi Modal -->
    <DetailNotif 
      :is-open="isDetailNotifOpen"
      :notification="selectedNotification"
      @close="isDetailNotifOpen = false"
    />

    <Footer />
  </div>
</template>

<style scoped>
/* Custom scrollbar */
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
