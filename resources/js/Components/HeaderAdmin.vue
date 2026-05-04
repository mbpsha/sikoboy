<template>
  <header class="bg-white shadow px-6 py-4 flex justify-between items-center sticky top-0 z-30">
    
    <div>
      <p class="text-sm text-gray-500">Dashboard / {{ title }}</p>
      <h1 class="text-2xl font-semibold text-gray-700">
        {{ title }}
      </h1>
    </div>

    <div class="flex items-center gap-3">
      <div class="bg-teal-600 text-white rounded-full w-10 h-10 flex items-center justify-center font-semibold">
        {{ initial }}
      </div>
      <div>
        <p class="font-semibold">{{ displayName }}</p>
        <p class="text-sm text-gray-500">{{ roleLabel }}</p>
      </div>
    </div>
  </header>
</template>

<script setup>
import { computed } from 'vue'
import { usePage } from '@inertiajs/vue3'

const props = defineProps({ title: String })

const page = usePage()

// page.props langsung, tanpa .value
const authUser = computed(() => page.props.auth?.user ?? null)

const displayName = computed(() => {
  if (!authUser.value) return ''
  return authUser.value.username || authUser.value.email?.split('@')[0] || ''
})

const roleLabel = computed(() => {
  const r = authUser.value?.role ?? ''
  if (r === 'admin') return 'Administrator'
  if (r === 'mitra') return 'Mitra'
  return r
})

const initial = computed(() => displayName.value?.charAt(0).toUpperCase() || '')
</script>