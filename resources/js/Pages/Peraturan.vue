<script setup>
import LandingLayout from "@/Layouts/LandingLayout.vue"

import HeroPeraturan from '@/Components/Peraturan/HeroPeraturan.vue'
import CardPeraturan from '@/Components/Peraturan/CardPeraturan.vue'

defineProps({
    peraturans: {
        type: Array,
        default: () => []
    }
})
</script>

<template>
    <LandingLayout>
        <HeroPeraturan />

        <section class="bg-white py-16">
            <div class="mx-auto max-w-6xl px-6">

                <div
                    v-if="peraturans.length"
                    class="grid gap-6 sm:grid-cols-2"
                >
                    <CardPeraturan
                        v-for="item in peraturans"
                        :key="item.id"
                        :item="item"
                    />
                </div>

                <div
                    v-else
                    class="rounded-2xl border border-dashed border-slate-300 bg-slate-50 px-6 py-14 text-center text-slate-500"
                >
                    Belum ada dokumen peraturan yang ditambahkan.
                </div>
            </div>
        </section>
    </LandingLayout>
</template>
