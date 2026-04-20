<template>
  <div class="flex">
    
    <!-- Sidebar -->
    <SidebarAdmin />

    <!-- Content Area -->
    <div class="flex-1 bg-gray-100 min-h-screen">
      
      <!-- Header -->
      <HeaderAdmin :title="title" />

      <!-- Slot Content -->
      <div class="p-6">
        <slot />
      </div>

    </div>
  </div>
</template>

<script setup>
import SidebarAdmin from '@/Components/SidebarAdmin.vue'
import HeaderAdmin from '@/Components/HeaderAdmin.vue'

defineProps({
  title: String
})
</script>