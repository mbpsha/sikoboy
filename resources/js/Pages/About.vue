<script setup>
import LandingLayout from "@/Layouts/LandingLayout.vue";

import HeroTentang from "@/Components/Tentang/HeroTentang.vue";
import AboutSikarsa from "@/Components/Tentang/AboutSikarsa.vue";
import VisiSection from "@/Components/Tentang/VisiSection.vue";
import MisiSection from "@/Components/Tentang/MisiSection.vue";
</script>

<template>
    <LandingLayout>
        <HeroTentang />
        <AboutSikarsa />
        <VisiSection />
        <MisiSection />
    </LandingLayout>
</template>
