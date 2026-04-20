<script setup>
import { ref, onMounted } from 'vue'

const props = defineProps({
    stats: {
        type: Array,
        default: () => [],
    },
})

// COUNT UP ANIMATION
const animatedStats = ref([])

onMounted(() => {
    animatedStats.value = props.stats.map(item => ({
        ...item,
        displayValue: 0
    }))

    animatedStats.value.forEach((item) => {
        let start = 0
        const end = parseInt(item.value)
        const duration = 1000
        const increment = end / (duration / 16)

        const counter = setInterval(() => {
            start += increment
            if (start >= end) {
                item.displayValue = end
                clearInterval(counter)
            } else {
                item.displayValue = Math.floor(start)
            }
        }, 16)
    })
})
</script>

<template>
    <section class="relative bg-[#f8fafc] pt-32 pb-0 overflow-hidden">

        <!-- 📊 STATS -->
        <div class="relative z-20 mx-auto max-w-4xl px-6">
            <div class="-mt-24">
                <div class="rounded-2xl bg-[#0C505C] p-4 animate-stats">

                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 text-white 
                                divide-y sm:divide-y-0 sm:divide-x divide-white/20">
                        
                        <article 
                            v-for="(item, idx) in animatedStats" 
                            :key="item.label"
                            class="px-4 py-5 text-center transition-all duration-300 
                                   hover:scale-105 hover:bg-white/5"
                        >
                            <!-- angka -->
                            <p class="text-3xl font-extrabold sm:text-4xl">
                                {{ item.displayValue }}
                            </p>

                            <!-- label -->
                            <p class="mt-2 text-xs text-white/100 sm:text-sm">
                                {{ item.label }}
                            </p>
                        </article>

                    </div>
                </div>
            </div>
        </div>

    </section>
</template>

<style scoped>
.animate-stats{
    animation: slideUpFade 700ms cubic-bezier(.2,.9,.3,1) both;
}
@keyframes slideUpFade{
    from{ transform: translateY(28px); opacity: 0; }
    to{ transform: translateY(0); opacity: 1; }
}
@media (prefers-reduced-motion: reduce){
    .animate-stats{ animation: none; }
}
</style>