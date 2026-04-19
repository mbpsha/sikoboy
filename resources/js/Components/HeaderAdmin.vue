<template>
  <header class="bg-white shadow px-6 py-4 flex justify-between items-center">
    
    <div>
      <p class="text-sm text-gray-500">Dashboard / {{ title }}</p>
      <h1 class="text-2xl font-semibold text-gray-700">
        {{ title }}
      </h1>
    </div>

    <div class="flex items-center gap-3">
      <div class="bg-teal-600 text-white rounded-full w-10 h-10 flex items-center justify-center">
        👤
      </div>
      <div>
        <p class="font-semibold">Boyolali</p>
        <p class="text-sm text-gray-500">Administrator</p>
      </div>
    </div>
  </header>
</template>

<script setup>
defineProps({
  title: String
})
</script>