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
                <canvas id="barChart"></canvas>
            </div>

            <div class="bg-white p-6 rounded-xl shadow">
                <h2 class="font-semibold mb-4">Jenis Dokumen</h2>
                <canvas id="pieChart"></canvas>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import { onMounted } from "vue";
import Chart from "chart.js/auto";
import AdminLayout from "@/Layouts/AdminLayout.vue";

const props = defineProps({
    metrics: {
        type: Object,
        default: () => ({}),
    },
    kerjasama_per_tahun: {
        type: Array,
        default: () => [],
    },
    jenis_dokumen: {
        type: Array,
        default: () => [],
    },
});

const metrics = props.metrics ?? {};

onMounted(() => {
    new Chart(document.getElementById("barChart"), {
        type: "bar",
        data: {
            labels: (props.kerjasama_per_tahun ?? []).map((row) => row.tahun),
            datasets: [
                {
                    label: "Jumlah Kerjasama",
                    data: (props.kerjasama_per_tahun ?? []).map((row) => row.total),
                },
            ],
        },
    });

    new Chart(document.getElementById("pieChart"), {
        type: "pie",
        data: {
            labels: (props.jenis_dokumen ?? []).map((row) => row.jenis_dokumen),
            datasets: [
                {
                    data: (props.jenis_dokumen ?? []).map((row) => row.total),
                },
            ],
        },
    });
});
</script>
