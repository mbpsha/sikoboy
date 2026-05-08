<script setup>
import { ref, onMounted, onUnmounted } from "vue";
const heroTentang = "/storage/images/kontak.jpeg";

// PARALLAX (biar konsisten sama landing)
const offset = ref(0);

const handleScroll = () => {
    offset.value = window.scrollY * 0.3;
};

onMounted(() => {
    window.addEventListener("scroll", handleScroll);
});

onUnmounted(() => {
    window.removeEventListener("scroll", handleScroll);
});
</script>

<template>
    <section
        class="relative overflow-hidden text-white min-h-[100vh] flex flex-col"
    >
        <!-- BACKGROUND -->
        <div class="absolute inset-0 pointer-events-none">
            <!-- PARALLAX IMAGE -->
            <div
                class="w-full h-[120%] bg-cover bg-[center_30%]"
                :style="{
                    backgroundImage: `url(${heroTentang})`,
                    transform: `translateY(${offset}px)`,
                }"
            ></div>

            <!-- gradient bawah -->
            <div
                class="absolute inset-0"
                style="
                    background: linear-gradient(
                        to bottom,
                        rgba(255, 255, 255, 0.05) 20%,
                        rgba(255, 255, 255, 0.6) 70%,
                        rgba(255, 255, 255, 1) 100%
                    );
                "
            ></div>
        </div>

        <!-- CONTENT UTAMA (CENTERED DI TENGAH HERO) -->
        <div class="relative z-10 flex-1 flex items-center justify-center">
            <div class="mx-auto max-w-6xl px-6 text-center animate-fadeUp">
                <h1 class="text-4xl md:text-5xl font-bold mb-3 tracking-widest">
                    KONTAK
                </h1>

                <div class="mx-auto mb-4 w-40 h-px bg-white/50"></div>

                <!-- DESKRIPSI PANJANG -->
                <p class="mt-4 max-w-2xl mx-auto text-sm md:text-lg text-white/90 leading-relaxed">
                    Kami siap membantu menjawab pertanyaan dan memfasilitasi kerjasama Anda
                </p>
            </div>
        </div>

        <!-- OVERLAPPING CONTACT CARDS + CTA -->
        <div class="relative z-20 -mt-12">
            <div class="max-w-6xl mx-auto px-6">
                <div class="flex flex-col md:flex-row items-end justify-center gap-8">
                    <!-- TELEPON -->
                    <div class="bg-white rounded-2xl p-8 shadow-2xl w-full md:w-1/4 text-center">
                        <div class="inline-flex items-center justify-center w-12 h-12 bg-[#E6F0F0] rounded-full mx-auto mb-4 text-[#0C505C]">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M22 16.92V21a1 1 0 0 1-1.11 1A19 19 0 0 1 3 5.11 1 1 0 0 1 4 4h4.09a1 1 0 0 1 1 .75 11 11 0 0 0 .7 2.81 1 1 0 0 1-.24 1L8.91 11.2a16 16 0 0 0 5.9 5.9l1.6-1.6a1 1 0 0 1 1-.24 11 11 0 0 0 2.8.7 1 1 0 0 1 .75 1V21z"></path></svg>
                        </div>
                        <h3 class="text-xl font-semibold text-[#0C505C]">Telepon</h3>
                        <p class="mt-3 text-sm text-slate-600">(0276) 321021</p>
                    </div>

                    <!-- ALAMAT (center elevated) -->
                    <div class="bg-white rounded-2xl p-8 shadow-2xl w-full md:w-1/4 text-center transform -translate-y-6">
                        <div class="inline-flex items-center justify-center w-12 h-12 bg-[#E6F0F0] rounded-full mx-auto mb-4 text-[#0C505C]">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 6-9 12-9 12s-9-6-9-12a9 9 0 1 1 18 0z"></path><circle cx="12" cy="10" r="2"/></svg>
                        </div>
                        <h3 class="text-xl font-semibold text-[#0C505C]">Alamat</h3>
                        <p class="mt-3 text-sm text-slate-600">Biro Kerja Sama Daerah<br/>Jl. merdeka Barat, Kemiri, Boyolali</p>
                    </div>

                    <!-- EMAIL -->
                    <div class="bg-white rounded-2xl p-8 shadow-2xl w-full md:w-1/4 text-center">
                        <div class="inline-flex items-center justify-center w-12 h-12 bg-[#E6F0F0] rounded-full mx-auto mb-4 text-[#0C505C]">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 8l7.89 5.26a2 2 0 0 0 2.22 0L21 8"></path><rect x="3" y="5" width="18" height="14" rx="2"></rect></svg>
                        </div>
                        <h3 class="text-xl font-semibold text-[#0C505C]">Email</h3>
                        <p class="mt-3 text-sm text-slate-600">setda@boyolali.go.id</p>
                    </div>
                </div>

                <div class="mt-8 flex justify-center">
                    <button class="bg-[#0C505C] text-white px-12 py-3 rounded-full shadow-2xl text-lg">Hubungi Kami</button>
                </div>
            </div>
        </div>
    </section>
</template>

<style>
@keyframes fadeUp {
    from {
        opacity: 0;
        transform: translateY(60px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-fadeUp {
    animation: fadeUp 1s ease-out forwards;
}
</style>