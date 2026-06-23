<script setup>
import { defineProps } from 'vue'
import LandingLayout from '@/Layouts/LandingLayout.vue'
import HeroSection from '@/Components/Landing/HeroSection.vue'
import StatsSection from '@/Components/Landing/StatsSection.vue'
import AboutSection from '@/Components/Landing/AboutSection.vue'
import PotentialSection from '@/Components/Landing/PotentialSection.vue'

defineProps({
    // ✅ Stats dari controller
    stats: {
        type: Array,
        required: true,
        default: () => [
            { label: 'Jumlah Kerja Sama', value: '0' },
            { label: 'Masa Berlaku <6 Bulan', value: '0' },
            { label: 'Masa Berlaku <3 Bulan', value: '0' },
            { label: 'Masa Berlaku Habis', value: '0' },
        ]
    },
    // ✅ Potensi dari controller
    potensiData: {
        type: Object,
        default: () => ({})
    },
})
</script>

<template>
    <LandingLayout>
        <HeroSection
            title="S I K A R S A"
            subtitle="Mewujudkan pengelolaan kerja sama daerah yang efisien, transparan, dan berdampak untuk Kabupaten Boyolali."
        />

        <!-- ✅ Stats dari controller -->
        <StatsSection :stats="stats" />
        
        <AboutSection />
        
        <!-- ✅ Potensi dari controller -->
        <PotentialSection :potensiData="potensiData" />
    </LandingLayout>
</template>