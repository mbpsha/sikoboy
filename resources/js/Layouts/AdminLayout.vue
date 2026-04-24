<template>
    <div class="flex h-screen">
        <!-- Sidebar (DIKUNCI) -->
        <div class="w-64 flex-shrink-0">
            <SidebarAdmin />
        </div>

        <!-- Content Area -->
        <div class="flex-1 flex flex-col bg-gray-100 overflow-hidden">
            <!-- Header -->
            <HeaderAdmin :title="title" />

            <!-- Content -->
            <main class="flex-1 overflow-auto p-6">
                <slot />
            </main>
        </div>
    </div>
</template>

<script setup>
import SidebarAdmin from "@/Components/SidebarAdmin.vue";
import HeaderAdmin from "@/Components/HeaderAdmin.vue";

defineProps({
    title: String,
});
</script>
