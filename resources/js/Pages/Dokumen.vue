<script setup>
import LandingLayout from '@/Layouts/LandingLayout.vue'
import DokumenHero from '@/Components/Dokumen/HeroSection.vue'
import DokumenContent from '@/Components/Dokumen/Content.vue'

const props = defineProps({
  kategoris: { type: Array, default: () => [] },
})
</script>

<template>
  <LandingLayout>
    <DokumenHero />
    <DokumenContent :kategoris="props.kategoris" />
  </LandingLayout>
</template>