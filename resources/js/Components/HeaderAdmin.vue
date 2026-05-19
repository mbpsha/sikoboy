<template>
    <header
        class="bg-white shadow px-3 sm:px-6 py-3 sm:py-4 flex justify-between items-center sticky top-0 z-30"
    >
        <div class="flex items-center gap-2 sm:gap-3 min-w-0">
            <button
                type="button"
                class="p-2 rounded-lg hover:bg-gray-100 transition-colors"
                :aria-label="isMobile ? 'Buka menu' : 'Toggle sidebar'"
                @click="$emit('toggle-sidebar')"
            >
                <svg
                    v-if="isMobile"
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-6 w-6 text-gray-700"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M4 6h16M4 12h16M4 18h16"
                    />
                </svg>
                <svg
                    v-else
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-6 w-6 text-gray-700"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                >
                    <path
                        v-if="isSidebarCollapsed"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M13 5l7 7-7 7M4 5h6v14H4z"
                    />
                    <path
                        v-else
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M11 5l-7 7 7 7M14 5h6v14h-6z"
                    />
                </svg>
            </button>

            <div v-if="isMobile" class="min-w-0 leading-tight">
                <p class="text-sm font-extrabold tracking-wider text-gray-800 truncate">
                    SIKOBOY
                </p>
                <p class="text-[11px] text-gray-500 truncate">Admin Dashboard</p>
            </div>
        </div>

        <div class="flex items-center gap-2 sm:gap-3">
            <div class="relative">
                <button
                    @click="toggleNotifications"
                    class="relative p-2 rounded-full hover:bg-gray-100 transition-colors"
                    aria-label="Notifikasi"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-6 w-6 text-gray-600"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"
                        />
                    </svg>

                    <span
                        v-if="totalNotifications > 0"
                        class="absolute -top-1 -right-1 bg-red-500 text-white text-xs font-bold rounded-full w-5 h-5 flex items-center justify-center"
                    >
                        {{ totalNotifications > 9 ? "9+" : totalNotifications }}
                    </span>
                </button>

                <div
                    v-if="showNotifications"
                    class="absolute right-0 mt-2 w-[calc(100vw-2rem)] sm:w-96 bg-white rounded-lg shadow-xl border border-gray-200 z-50"
                    role="dialog"
                    aria-label="Panel notifikasi admin"
                >
                    <div class="px-4 py-3 border-b border-gray-200 flex justify-between items-center">
                        <h3 class="font-semibold text-gray-800">Notifikasi</h3>
                        <button
                            @click="markAllAsRead"
                            class="text-sm text-teal-600 hover:text-teal-700"
                        >
                            Tandai semua dibaca
                        </button>
                    </div>

                    <div class="max-h-96 overflow-y-auto">
                        <div
                            v-for="notif in notifications"
                            :key="notif.id"
                            class="px-4 py-3 border-b border-gray-100 hover:bg-gray-50 transition-colors cursor-pointer"
                            @click="handleNotificationClick(notif)"
                        >
                            <div class="flex gap-3">
                                <div class="flex-shrink-0">
                                    <div
                                        :class="[
                                            'w-10 h-10 rounded-full flex items-center justify-center',
                                            notif.type === 'MITRA' ? 'bg-blue-100' : 'bg-green-100',
                                        ]"
                                    >
                                        <svg
                                            v-if="notif.type === 'MITRA'"
                                            xmlns="http://www.w3.org/2000/svg"
                                            class="h-5 w-5 text-blue-600"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke="currentColor"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"
                                            />
                                        </svg>
                                        <svg
                                            v-else
                                            xmlns="http://www.w3.org/2000/svg"
                                            class="h-5 w-5 text-green-600"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke="currentColor"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                                            />
                                        </svg>
                                    </div>
                                </div>

                                <div class="flex-1 min-w-0">
                                    <div class="flex items-start justify-between gap-2">
                                        <span
                                            :class="[
                                                'text-xs font-bold px-2 py-0.5 rounded',
                                                notif.type === 'MITRA'
                                                    ? 'bg-blue-100 text-blue-700'
                                                    : 'bg-green-100 text-green-700',
                                            ]"
                                        >
                                            {{ notif.type }}
                                        </span>
                                        <span
                                            class="w-2 h-2 bg-red-500 rounded-full flex-shrink-0 mt-1.5"
                                        ></span>
                                    </div>

                                    <p class="font-semibold text-gray-800 text-sm mt-1 truncate">
                                        {{ notif.title }}
                                    </p>
                                    <p class="text-gray-600 text-xs mt-1 leading-relaxed line-clamp-2">
                                        {{ notif.description }}
                                    </p>

                                    <div class="flex items-center gap-2 mt-2">
                                        <svg
                                            v-if="notif.status === 'expired'"
                                            xmlns="http://www.w3.org/2000/svg"
                                            class="h-4 w-4 text-red-500"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke="currentColor"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"
                                            />
                                        </svg>

                                        <svg
                                            v-else
                                            xmlns="http://www.w3.org/2000/svg"
                                            class="h-4 w-4 text-yellow-500"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke="currentColor"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
                                            />
                                        </svg>

                                        <span
                                            :class="[
                                                'text-xs font-semibold',
                                                notif.status === 'expired'
                                                    ? 'text-red-600'
                                                    : 'text-yellow-600',
                                            ]"
                                        >
                                            {{ notif.countdown }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div v-if="notifications.length === 0" class="px-4 py-8 text-center">
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-12 w-12 text-gray-300 mx-auto mb-2"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                                />
                            </svg>
                            <p class="text-gray-500 text-sm">Tidak ada notifikasi</p>
                        </div>
                    </div>

                    <div class="px-4 py-3 border-t border-gray-200 text-center">
                        <Link
                            href="/admin/notifikasi"
                            class="text-teal-600 hover:text-teal-700 text-sm font-semibold"
                        >
                            Lihat Semua →
                        </Link>
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-2 sm:gap-3">
                <div
                    class="bg-teal-600 text-white rounded-full w-9 h-9 sm:w-10 sm:h-10 flex items-center justify-center font-semibold"
                >
                    {{ initial }}
                </div>
                <div class="hidden sm:block">
                    <p class="font-semibold leading-tight">{{ displayName }}</p>
                    <p class="text-sm text-gray-500 leading-tight">{{ roleLabel }}</p>
                </div>
            </div>
        </div>
    </header>
</template>

<script setup>
import { computed, ref, onMounted, onUnmounted } from 'vue'
import { usePage, Link, router } from '@inertiajs/vue3'

defineProps({
    isMobile: {
        type: Boolean,
        default: false,
    },
    isSidebarCollapsed: {
        type: Boolean,
        default: false,
    },
});

defineEmits(["toggle-sidebar"]);

const page = usePage();
const showNotifications = ref(false);

const rawAdminNotifications = computed(() => page.props.admin_notifications ?? [])

// local state: closed notifications (persisted in localStorage) to hide from header popup
const closedAdminNotifications = ref([])

const notifications = computed(() => {
  return rawAdminNotifications.value.filter(n => !closedAdminNotifications.value.includes(n.id))
})
const authUser = computed(() => page.props.auth?.user ?? null);

const displayName = computed(() => {
    if (!authUser.value) return "";
    return authUser.value.username || authUser.value.email?.split("@")[0] || "";
});

const roleLabel = computed(() => {
  // Show only the division for admins/ users that have it
  const divisi = authUser.value?.admin?.divisi ?? authUser.value?.divisi ?? ''
  return divisi || ''
})

const initial = computed(() => displayName.value?.charAt(0).toUpperCase() || '')
// show count for visible (not-closed) notifications in header
const totalNotifications = computed(() => notifications.value.length)

const toggleNotifications = () => {
    showNotifications.value = !showNotifications.value;
};

const handleNotificationClick = (notif) => {
  // Navigasi ke halaman notifikasi dengan highlight notifikasi yang diklik
  // hide this notification from header popup (but keep in full list)
  if (!closedAdminNotifications.value.includes(notif.id)) {
    closedAdminNotifications.value.push(notif.id)
    try { localStorage.setItem('closed_admin_notifications', JSON.stringify(closedAdminNotifications.value)) } catch (e) {}
  }
  router.visit(`/admin/notifikasi?highlight=${notif.id}`)
}

const markAllAsRead = () => {
  // add all current raw admin notification ids to closed list
  const ids = rawAdminNotifications.value.map(n => n.id)
  closedAdminNotifications.value = Array.from(new Set([...closedAdminNotifications.value, ...ids]))
  try { localStorage.setItem('closed_admin_notifications', JSON.stringify(closedAdminNotifications.value)) } catch (e) {}
  showNotifications.value = false
}

const handleClickOutside = (event) => {
    if (!event.target.closest(".relative")) {
        showNotifications.value = false;
    }
};

onMounted(() => {
  document.addEventListener('click', handleClickOutside)
  // load closed admin notifications
  try {
    const stored = localStorage.getItem('closed_admin_notifications')
    if (stored) closedAdminNotifications.value = JSON.parse(stored)
  } catch (e) {
    closedAdminNotifications.value = []
  }
})

onUnmounted(() => {
    document.removeEventListener("click", handleClickOutside);
});
</script>

<style scoped>
.max-h-96::-webkit-scrollbar {
    width: 6px;
}
.max-h-96::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 3px;
}
.max-h-96::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 3px;
}
.max-h-96::-webkit-scrollbar-thumb:hover {
    background: #555;
}
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
