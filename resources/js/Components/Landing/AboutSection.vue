<script setup>
import { ref, onMounted } from 'vue'

defineProps({
    heading: {
        type: String,
        default: 'Ayo Berkenalan dengan SIKOBOY',
    },
    content: {
        type: String,
        default:
            'SIKOBOY adalah sistem informasi kolaborasi Boyolali untuk mempertemukan potensi daerah dengan mitra strategis.',
    },
})

const show = ref(false)

onMounted(() => {
    setTimeout(() => {
        show.value = true
    }, 200)
})
</script>

<template>
    <!-- SPACING SUDAH -->
    <section id="about" class="px-6 pt-32 pb-6 bg-white about-section overflow-hidden relative">

        <div class="relative mx-auto max-w-4xl text-center">

            <!-- TITLE -->
            <h2 class="text-2xl font-normal text-slate-700">
                Ayo Berkenalan dengan
                <span class="text-[#0C505C] font-bold">SIKOBOY</span>
            </h2>

            <!-- DIVIDER -->
            <div class="mx-auto mt-6 w-full max-w-xl h-px bg-slate-200"></div>

            <!-- CARD -->
            <div
                class="mx-auto mt-15 rounded-2xl bg-[#f1f4f8] p-10 text-center text-base leading-relaxed text-slate-700
                       shadow-[0_8px_30px_rgba(0,0,0,0.07)]
                       transition-all duration-700"
                :class="show ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-10'"
            >
                <p>
                    <span class="font-bold text-slate-700">SIKOBOY</span> (Sistem Kolaboratif Boyolali) adalah sistem informasi berbasis web yang dikembangkan untuk membantu
                    Sekretariat Daerah Kabupaten Boyolali dalam mengelola arsip serta melakukan monitoring kerja sama secara
                    terpusat. Sistem ini dibangun karena pengelolaan dokumen kerja sama sebelumnya belum terintegrasi dalam satu
                    platform, sehingga proses pencarian arsip dan pemantauan masa berlaku kontrak sering memerlukan waktu lama
                    serta berisiko menimbulkan keterlambatan administratif. Melalui SIKOBOY, seluruh data kerja sama dapat disimpan
                    secara terstruktur dalam satu sistem, dicari berdasarkan tahun maupun mitra, dipantau masa berlaku kontraknya,
                    serta diajukan dan dimonitor secara daring. Dengan demikian, pengelolaan kerja sama diharapkan menjadi lebih
                    tertib, efisien, transparan, serta mendukung transformasi digital di Kabupaten Boyolali.
                </p>
            </div>

        </div>
    </section>
</template>

<style scoped>
.about-section {
  font-family: 'Poppins', system-ui, -apple-system, 'Segoe UI', Roboto;
}
</style>
