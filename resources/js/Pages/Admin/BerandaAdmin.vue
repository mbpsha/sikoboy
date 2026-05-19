<template>
    <AdminLayout title="Beranda">
        <!-- Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-teal-500 text-white p-6 rounded-xl">
                <p>Total Kerjasama</p>
                <h2 class="text-3xl font-bold mt-2">{{ metrics.total_kerjasama ?? 0 }}</h2>
            </div>

            <div class="bg-green-500 text-white p-6 rounded-xl">
                <p>Kerjasama Aktif</p>
                <h2 class="text-3xl font-bold mt-2">{{ metrics.aktif ?? 0 }}</h2>
            </div>

            <div class="bg-yellow-500 text-white p-6 rounded-xl">
                <p>Akan Berakhir</p>
                <h2 class="text-3xl font-bold mt-2">{{ metrics.akan_berakhir ?? 0 }}</h2>
            </div>

            <div class="bg-red-500 text-white p-6 rounded-xl">
                <p>Berakhir</p>
                <h2 class="text-3xl font-bold mt-2">{{ metrics.berakhir ?? 0 }}</h2>
            </div>

            <div class="bg-blue-500 text-white p-6 rounded-xl">
                <p>Total Mitra</p>
                <h2 class="text-3xl font-bold mt-2">{{ metrics.total_mitra ?? 0 }}</h2>
            </div>

            <div class="bg-purple-500 text-white p-6 rounded-xl">
                <p>Total Dokumen</p>
                <h2 class="text-3xl font-bold mt-2">{{ metrics.total_dokumen ?? 0 }}</h2>
            </div>
        </div>

        <!-- Charts -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
            <div class="bg-white p-6 rounded-xl shadow">
                <h2 class="font-semibold mb-4">Jumlah Kerjasama per Tahun</h2>
                <div v-if="kerjasama_per_tahun.length > 0" class="h-72 sm:h-80">
                    <canvas ref="barChartRef"></canvas>
                </div>
                <div v-else class="flex items-center justify-center h-72 sm:h-80 text-gray-400">
                    <p>Tidak ada data kerjasama</p>
                </div>
            </div>

            <div class="bg-white p-6 rounded-xl shadow">
                <h2 class="font-semibold mb-4">Kategori Kerjasama</h2>
                <div v-if="kategori_kerjasama.length > 0" class="h-72 sm:h-80">
                    <canvas ref="categoryChartRef"></canvas>
                </div>
                <div v-else class="flex items-center justify-center h-72 sm:h-80 text-gray-400">
                    <p>Tidak ada data kategori</p>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import { onMounted, ref } from "vue";
import Chart from "chart.js/auto";
import AdminLayout from "@/Layouts/AdminLayout.vue";

const MOBILE_BREAKPOINT = 768;

const props = defineProps({
    metrics: {
        type: Object,
        default: () => ({}),
    },
    kerjasama_per_tahun: {
        type: Array,
        default: () => [],
    },
    kategori_kerjasama: {
        type: Array,
        default: () => [],
    },
});

const metrics = props.metrics ?? {};
const barChartRef = ref(null);
const categoryChartRef = ref(null);

onMounted(() => {
    // Gunakan data real dari backend, tidak ada dummy fallback
    const kerjasamaTahun = props.kerjasama_per_tahun || [];
    const kategoriKerjasama = props.kategori_kerjasama || [];
    const isMobileViewport = () =>
        typeof window !== "undefined" && window.innerWidth < MOBILE_BREAKPOINT;

    const applyBarResponsiveOptions = (chart) => {
        const isMobile = isMobileViewport();
        chart.options.scales.x.ticks.maxRotation = isMobile ? 40 : 0;
        chart.options.scales.x.ticks.minRotation = isMobile ? 30 : 0;
        chart.options.scales.x.ticks.autoSkip = !isMobile;
        chart.options.scales.x.ticks.maxTicksLimit = isMobile ? 6 : undefined;
    };

    const applyPieResponsiveOptions = (chart) => {
        const isMobile = isMobileViewport();
        chart.options.radius = isMobile ? "80%" : "88%";
        chart.options.layout.padding = isMobile ? 8 : 16;
        chart.options.plugins.legend.position = isMobile ? "bottom" : "right";
        chart.options.plugins.legend.labels.boxWidth = isMobile ? 8 : 12;
        chart.options.plugins.legend.labels.padding = isMobile ? 10 : 14;
        chart.options.plugins.legend.labels.font.size = isMobile ? 10 : 12;
    };

    // Hanya render chart jika ada data
    if (kerjasamaTahun.length > 0 && barChartRef.value) {
        // Chart 1: Kerjasama per Tahun (Bar Chart)
        const barChart = new Chart(barChartRef.value, {
            type: "bar",
            data: {
                labels: kerjasamaTahun.map((row) => row.tahun),
                datasets: [
                    {
                        label: "Jumlah Kerjasama",
                        data: kerjasamaTahun.map((row) => row.total),
                        backgroundColor: "rgba(54, 162, 235, 0.7)",
                        borderColor: "rgba(54, 162, 235, 1)",
                        borderWidth: 1,
                    },
                ],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false,
                    },
                },
                scales: {
                    x: {
                        ticks: {
                            maxRotation: 0,
                            minRotation: 0,
                            autoSkip: true,
                        },
                    },
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 1, precision: 0 },
                    },
                },
            },
        });

        applyBarResponsiveOptions(barChart);
        barChart.options.onResize = (chart) => {
            applyBarResponsiveOptions(chart);
            chart.update();
        };
    }

    // Hanya render chart jika ada data
    if (kategoriKerjasama.length > 0 && categoryChartRef.value) {
        // Hitung total untuk persentase
        const totalKategori = kategoriKerjasama.reduce((sum, row) => sum + row.total, 0) || 1;

        // Helper untuk format persentase agar konsisten
        const formatPercentage = (value) => Math.round((value / totalKategori) * 100) + "%";

        // Chart 2: Kategori Kerjasama (Pie Chart)
        const categoryChart = new Chart(categoryChartRef.value, {
            type: "pie",
            data: {
                labels: kategoriKerjasama.map((row) => row.kategori),
                datasets: [
                    {
                        data: kategoriKerjasama.map((row) => row.total),
                        backgroundColor: [
                            "rgba(20, 184, 166, 0.8)",   // KSDD
                            "rgba(45, 212, 191, 0.8)",   // KSDPK
                            "rgba(59, 130, 246, 0.8)",   // NK/RK
                            "rgba(96, 165, 250, 0.8)",   // PERTEK
                            "rgba(168, 85, 247, 0.8)",   // KSDPL
                            "rgba(236, 72, 153, 0.8)",   // KSDLL
                        ],
                        borderColor: [
                            "rgba(20, 184, 166, 1)",
                            "rgba(45, 212, 191, 1)",
                            "rgba(59, 130, 246, 1)",
                            "rgba(96, 165, 250, 1)",
                            "rgba(168, 85, 247, 1)",
                            "rgba(236, 72, 153, 1)",
                        ],
                        borderWidth: 2,
                    },
                ],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                radius: "88%",
                layout: {
                    padding: 16,
                },
                plugins: {
                    legend: {
                        display: true,
                        position: "right",
                        labels: {
                            usePointStyle: true,
                            boxWidth: 12,
                            padding: 14,
                            font: {
                                size: 12,
                            },
                        },
                    },
                    tooltip: {
                        callbacks: {
                            label: function (context) {
                                const percentage = formatPercentage(context.parsed);
                                return ` ${context.label}: ${context.parsed} (${percentage})`;
                            },
                        },
                    },
                },
            },
        });

        applyPieResponsiveOptions(categoryChart);
        categoryChart.options.onResize = (chart) => {
            applyPieResponsiveOptions(chart);
            chart.update();
        };
    }
});
</script>
