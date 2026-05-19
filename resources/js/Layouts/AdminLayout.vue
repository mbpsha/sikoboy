<template>
    <div class="bg-gray-100 h-dvh overflow-hidden">
        <SidebarAdmin
            v-model:collapsed="isSidebarCollapsed"
            :is-mobile="isMobile"
            :mobile-open="isMobileSidebarOpen"
            @close-mobile="isMobileSidebarOpen = false"
        />

        <div
            class="h-dvh overflow-y-auto transition-all duration-300"
            :class="contentWrapperClass"
        >
            <HeaderAdmin
                :title="title"
                :show-menu-button="isMobile || isSidebarCollapsed"
                @toggle-menu="toggleMenu"
            />

            <div class="p-4 sm:p-6 pt-20">
                <slot />
            </div>
        </div>

        <div
            v-if="isMobileSidebarOpen"
            class="fixed inset-0 bg-black/40 z-30 lg:hidden"
            @click="isMobileSidebarOpen = false"
        ></div>
    </div>
</template>

<script setup>
import { computed, onMounted, onUnmounted, ref, watch } from "vue";
import SidebarAdmin from "@/Components/SidebarAdmin.vue";
import HeaderAdmin from "@/Components/HeaderAdmin.vue";

defineProps({
    title: String,
});

const isSidebarCollapsed = ref(false);
const isMobile = ref(false);
const isMobileSidebarOpen = ref(false);
const SIDEBAR_STORAGE_KEY = "admin_sidebar_collapsed";
const SIDEBAR_COLLAPSED_VALUE = "1";
const MOBILE_BREAKPOINT = 1024;

const updateViewportState = () => {
    isMobile.value = window.innerWidth < MOBILE_BREAKPOINT;
    if (!isMobile.value) {
        isMobileSidebarOpen.value = false;
    }
};

const contentWrapperClass = computed(() => {
    if (isMobile.value) {
        return "ml-0";
    }
    return isSidebarCollapsed.value ? "lg:ml-20" : "lg:ml-64";
});

const toggleMenu = () => {
    if (isMobile.value) {
        isMobileSidebarOpen.value = !isMobileSidebarOpen.value;
        return;
    }
    isSidebarCollapsed.value = !isSidebarCollapsed.value;
};

onMounted(() => {
    try {
        isSidebarCollapsed.value = localStorage.getItem(SIDEBAR_STORAGE_KEY) === SIDEBAR_COLLAPSED_VALUE;
    } catch (error) {
        isSidebarCollapsed.value = false;
        if (import.meta.env.DEV) {
            console.error("Failed to load sidebar state:", error);
        }
    }

    updateViewportState();
    window.addEventListener("resize", updateViewportState);
});

watch(isSidebarCollapsed, (collapsed) => {
    try {
        localStorage.setItem(SIDEBAR_STORAGE_KEY, collapsed ? SIDEBAR_COLLAPSED_VALUE : "0");
    } catch (error) {
        if (import.meta.env.DEV) {
            console.error("Failed to store sidebar state:", error);
        }
    }
});

onUnmounted(() => {
    window.removeEventListener("resize", updateViewportState);
});
</script>
