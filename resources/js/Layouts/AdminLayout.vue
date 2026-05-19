<template>
    <div class="min-h-screen bg-gray-100">

        <!-- MOBILE OVERLAY -->
        <div
            v-if="sidebarOpen"
            @click="sidebarOpen = false"
            class="fixed inset-0 bg-black/40 z-40 lg:hidden"
        ></div>

        <!-- SIDEBAR -->
        <SidebarAdmin
            :is-open="sidebarOpen"
            :is-collapsed="sidebarCollapsed"
            @close="sidebarOpen = false"
        />

        <!-- CONTENT -->
        <div
            class="transition-all duration-300 min-h-screen"
            :class="[
                sidebarCollapsed
                    ? 'lg:ml-[88px]'
                    : 'lg:ml-[280px]'
            ]"
        >

            <!-- HEADER -->
            <HeaderAdmin
                :title="title"
                :sidebar-collapsed="sidebarCollapsed"
                @toggle-sidebar="toggleSidebar"
                @toggle-collapse="toggleCollapse"
            />

            <!-- PAGE -->
            <main class="p-4 md:p-6">
                <slot />
            </main>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, watch } from "vue";
import { usePage } from "@inertiajs/vue3";

import SidebarAdmin from "@/Components/SidebarAdmin.vue";
import HeaderAdmin from "@/Components/HeaderAdmin.vue";

defineProps({
    title: String,
});

const sidebarOpen = ref(false);

const sidebarCollapsed = ref(false);

const page = usePage();

const toggleSidebar = () => {
    sidebarOpen.value = !sidebarOpen.value;
};

const toggleCollapse = () => {

    sidebarCollapsed.value = !sidebarCollapsed.value;

    localStorage.setItem(
        "admin_sidebar_collapsed",
        JSON.stringify(sidebarCollapsed.value)
    );
};

// Auto-close sidebar when route changes
watch(
    () => page.url,
    () => {
        sidebarOpen.value = false;
    }
);

onMounted(() => {

    const saved = localStorage.getItem(
        "admin_sidebar_collapsed"
    );

    if (saved !== null) {
        sidebarCollapsed.value = JSON.parse(saved);
    }
});
</script>
