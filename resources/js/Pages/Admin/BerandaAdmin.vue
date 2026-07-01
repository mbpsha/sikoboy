<template>
    <AdminLayout title="Beranda">

        <!-- WRAPPER -->
        <div class="w-full overflow-x-hidden">

            <!-- METRIC CARDS -->
            <div
                class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4 md:gap-6"
            >

                <!-- CARD -->
                <div class="p-5 md:p-6 rounded-2xl shadow-sm text-white bg-gradient-to-r from-teal-500 to-emerald-400">
                    <div class="flex items-center justify-between">
                        <div class="flex flex-col justify-center">
                            <p class="text-sm md:text-base opacity-90">Total Kerjasama</p>
                            <h2 class="text-3xl md:text-4xl font-bold mt-1 leading-tight">{{ metrics.total_kerjasama ?? 0 }}</h2>
                        </div>

                        <div class="flex items-center">
                            <div class="w-12 h-12 rounded-full bg-white/12 flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-white/90" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6M9 16h6M7 8h10M5 5h14v14H5z" />
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="p-5 md:p-6 rounded-2xl shadow-sm text-white bg-gradient-to-r from-emerald-400 to-green-400">
                    <div class="flex items-center justify-between">
                        <div class="flex flex-col justify-center">
                            <p class="text-sm md:text-base opacity-90">Kerjasama Aktif</p>
                            <h2 class="text-3xl md:text-4xl font-bold mt-1 leading-tight">{{ metrics.aktif ?? 0 }}</h2>
                        </div>

                        <div class="flex items-center">
                            <div class="w-12 h-12 rounded-full bg-white/12 flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-white/90" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="p-5 md:p-6 rounded-2xl shadow-sm text-white bg-gradient-to-r from-yellow-500 to-orange-400 min-h-[92px]">
                    <div class="flex items-center justify-between">
                        <div class="flex flex-col justify-center">
                            <p class="text-sm md:text-base opacity-90 mb-1">Akan Berakhir</p>
                            <h2 class="text-3xl md:text-4xl font-bold mt-0 leading-none">{{ metrics.akan_berakhir ?? 0 }}</h2>
                        </div>

                        <div class="flex items-center">
                            <div class="w-12 h-12 rounded-full bg-white/12 flex items-center justify-center">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="w-5 h-5 shrink-0"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
                            />
                        </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="p-5 md:p-6 rounded-2xl shadow-sm text-white bg-gradient-to-r from-red-500 to-rose-500">
                    <div class="flex items-center justify-between">
                        <div class="flex flex-col justify-center">
                            <p class="text-sm md:text-base opacity-90">Berakhir</p>
                            <h2 class="text-3xl md:text-4xl font-bold mt-1 leading-tight">{{ metrics.berakhir ?? 0 }}</h2>
                        </div>

                        <div class="flex items-center">
                            <div class="w-12 h-12 rounded-full bg-white/12 flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-white/90" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="p-5 md:p-6 rounded-2xl shadow-sm text-white bg-gradient-to-r from-blue-500 to-indigo-500">
                    <div class="flex items-center justify-between">
                        <div class="flex flex-col justify-center">
                            <p class="text-sm md:text-base opacity-90">Total Mitra</p>
                            <h2 class="text-3xl md:text-4xl font-bold mt-1 leading-tight">{{ metrics.total_mitra ?? 0 }}</h2>
                        </div>

                        <div class="flex items-center">
                            <div class="w-12 h-12 rounded-full bg-white/12 flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-white/90" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a4 4 0 00-4-4h-1M9 20H4v-2a4 4 0 014-4h1M12 12a4 4 0 100-8 4 4 0 000 8z" />
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="p-5 md:p-6 rounded-2xl shadow-sm text-white bg-gradient-to-r from-violet-500 to-fuchsia-500">
                    <div class="flex items-center justify-between">
                        <div class="flex flex-col justify-center">
                            <p class="text-sm md:text-base opacity-90">Dibatalkan</p>
                            <h2 class="text-3xl md:text-4xl font-bold mt-1 leading-tight">{{ metrics.dibatalkan ?? 0 }}</h2>
                        </div>

                        <div class="flex items-center">
                            <div class="w-12 h-12 rounded-full bg-white/12 flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-white/90">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m9.75 9.75 4.5 4.5m0-4.5-4.5 4.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- CHARTS -->
            <div
                class="grid grid-cols-1 xl:grid-cols-2 gap-4 md:gap-6 mt-6 items-start"
            >

                <!-- BAR CHART -->
                <div
                    class="bg-white p-4 md:p-6 rounded-2xl shadow-sm overflow-hidden"
                >
                    <h2
                        class="font-semibold text-gray-800 text-base md:text-lg mb-4"
                    >
                        Jumlah Kerjasama per Tahun
                    </h2>

                    <div
                        v-if="kerjasama_per_tahun.length > 0"
                        class="relative h-[260px] sm:h-[300px] md:h-[360px]"
                    >
                        <canvas
                            ref="barChartRef"
                            class="!w-full !h-full"
                        ></canvas>
                    </div>

                    <div
                        v-else
                        class="flex items-center justify-center h-[260px] sm:h-[300px] md:h-[360px] text-gray-400"
                    >
                        <p>Tidak ada data kerjasama</p>
                    </div>
                </div>

                <!-- PIE CHART -->
                <div
                    class="bg-white p-4 md:p-6 rounded-2xl shadow-sm overflow-hidden"
                >
                    <h2
                        class="font-semibold text-gray-800 text-base md:text-lg mb-4"
                    >
                        Kategori Kerjasama
                    </h2>

                    <div
                        v-if="kategori_kerjasama.length > 0"
                    >

                        <!-- PIE -->
                        <div
                            class="h-[300px] md:h-[380px]"
                        >
                            <canvas ref="categoryChartRef"></canvas>
                        </div>

                        <!-- LEGEND LIST -->
                        <div
                            class="mt-5 grid grid-cols-1 gap-3"
                        >
                            <div
                                v-for="(item, index) in kategori_kerjasama"
                                :key="index"
                                class="flex items-start gap-3 p-3 rounded-xl bg-gray-50"
                            >

                                <!-- COLOR -->
                                <div
                                    class="w-4 h-4 rounded-full mt-1 shrink-0"
                                    :style="{
                                        backgroundColor: pieColors[index]
                                    }"
                                ></div>

                                <!-- TEXT -->
                                <div class="min-w-0 flex-1">

                                    <p
                                        class="text-sm font-medium text-gray-800 break-words"
                                    >
                                        {{ item.kategori }}
                                    </p>

                                    <p
                                        class="text-xs text-gray-500 mt-1"
                                    >
                                        {{ item.total }} kerjasama
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div
                        v-else
                        class="flex items-center justify-center h-[320px] md:h-[380px] text-gray-400"
                    >
                        <p>Tidak ada data kategori</p>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import {
    onMounted,
    onUnmounted,
    ref,
} from "vue";

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

let barChartInstance = null;
let categoryChartInstance = null;

let resizeHandler = null;
let resizeRafId = null;

const pieColors = [
    "rgba(20, 184, 166, 0.9)",
    "rgba(45, 212, 191, 0.9)",
    "rgba(59, 130, 246, 0.9)",
    "rgba(96, 165, 250, 0.9)",
    "rgba(168, 85, 247, 0.9)",
    "rgba(236, 72, 153, 0.9)",
];

onMounted(() => {

    const kerjasamaTahun =
        props.kerjasama_per_tahun || [];

    const kategoriKerjasama =
        props.kategori_kerjasama || [];

    const isMobileViewport = () =>
        typeof window !== "undefined" &&
        window.innerWidth < MOBILE_BREAKPOINT;

    // BAR CHART
    if (
        kerjasamaTahun.length > 0 &&
        barChartRef.value
    ) {

        barChartInstance = new Chart(
            barChartRef.value,
            {
                type: "bar",

                data: {
                    labels: kerjasamaTahun.map(
                        row => row.tahun
                    ),

                    datasets: [
                        {
                            label: "Jumlah Kerjasama",

                            data: kerjasamaTahun.map(
                                row => row.total
                            ),

                            backgroundColor:
                                "rgba(54, 162, 235, 0.7)",

                            borderColor:
                                "rgba(54, 162, 235, 1)",

                            borderWidth: 1,

                            borderRadius: 8,
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
                                maxRotation: isMobileViewport()
                                    ? 40
                                    : 0,

                                minRotation: isMobileViewport()
                                    ? 30
                                    : 0,

                                autoSkip: true,

                                color: "#6B7280",
                            },

                            grid: {
                                display: false,
                            },
                        },

                        y: {
                            beginAtZero: true,

                            ticks: {
                                stepSize: 1,
                                precision: 0,
                                color: "#6B7280",
                            },

                            grid: {
                                color: "rgba(0,0,0,0.05)",
                            },
                        },
                    },
                },
            }
        );
    }

    // PIE CHART
    if (
        kategoriKerjasama.length > 0 &&
        categoryChartRef.value
    ) {

        categoryChartInstance = new Chart(
            categoryChartRef.value,
            {
                type: "pie",

                data: {
                    labels: kategoriKerjasama.map(
                        row => row.kategori
                    ),

                    datasets: [
                        {
                            data: kategoriKerjasama.map(
                                row => row.total
                            ),

                            backgroundColor: pieColors,

                            borderWidth: 2,

                            borderColor: "#ffffff",
                        },
                    ],
                },

                options: {
                    responsive: true,

                    maintainAspectRatio: false,

                    radius: isMobileViewport()
                        ? "80%"
                        : "88%",

                    layout: {
                        padding: isMobileViewport()
                            ? 8
                            : 16,
                    },

                    plugins: {

                        // HIDE DEFAULT LEGEND
                        legend: {
                            display: false,
                        },

                        tooltip: {
                            callbacks: {
                                label: function (context) {

                                    const total =
                                        context.dataset.data.reduce(
                                            (a, b) => a + b,
                                            0
                                        );

                                    const percentage =
                                        Math.round(
                                            (
                                                context.parsed /
                                                total
                                            ) * 100
                                        );

                                    return ` ${context.label}: ${context.parsed} (${percentage}%)`;
                                },
                            },
                        },
                    },
                },
            }
        );
    }

    // RESIZE
    resizeHandler = () => {

        if (resizeRafId) {
            cancelAnimationFrame(
                resizeRafId
            );
        }

        resizeRafId =
            requestAnimationFrame(() => {

                if (barChartInstance) {
                    barChartInstance.resize();
                }

                if (categoryChartInstance) {
                    categoryChartInstance.resize();
                }

                resizeRafId = null;
            });
    };

    window.addEventListener(
        "resize",
        resizeHandler
    );
});

onUnmounted(() => {

    if (resizeHandler) {

        window.removeEventListener(
            "resize",
            resizeHandler
        );
    }

    if (resizeRafId) {

        cancelAnimationFrame(
            resizeRafId
        );

        resizeRafId = null;
    }

    if (barChartInstance) {

        barChartInstance.destroy();

        barChartInstance = null;
    }

    if (categoryChartInstance) {

        categoryChartInstance.destroy();

        categoryChartInstance = null;
    }
});
</script>

<style scoped>
canvas {
    max-width: 100% !important;
}

* {
    word-break: break-word;
}
</style>