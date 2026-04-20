<template>
    <AdminLayout title="Beranda">
        <!-- Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-teal-500 text-white p-6 rounded-xl">
                <p>Total Kerjasama</p>
                <h2 class="text-3xl font-bold mt-2">47</h2>
            </div>

            <div class="bg-green-500 text-white p-6 rounded-xl">
                <p>Kerjasama Aktif</p>
                <h2 class="text-3xl font-bold mt-2">32</h2>
            </div>

            <div class="bg-yellow-500 text-white p-6 rounded-xl">
                <p>Akan Berakhir</p>
                <h2 class="text-3xl font-bold mt-2">8</h2>
            </div>

            <div class="bg-red-500 text-white p-6 rounded-xl">
                <p>Berakhir</p>
                <h2 class="text-3xl font-bold mt-2">7</h2>
            </div>

            <div class="bg-blue-500 text-white p-6 rounded-xl">
                <p>Total Mitra</p>
                <h2 class="text-3xl font-bold mt-2">38</h2>
            </div>

            <div class="bg-purple-500 text-white p-6 rounded-xl">
                <p>Total Dokumen</p>
                <h2 class="text-3xl font-bold mt-2">124</h2>
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

onMounted(() => {
    new Chart(document.getElementById("barChart"), {
        type: "bar",
        data: {
            labels: ["2020", "2021", "2022", "2023", "2024", "2025"],
            datasets: [{ data: [5, 9, 11, 13, 15, 16] }],
        },
    });

    new Chart(document.getElementById("pieChart"), {
        type: "pie",
        data: {
            labels: ["PKS", "MoU", "Nota", "Perjanjian"],
            datasets: [{ data: [32, 26, 18, 25] }],
        },
    });
});
</script>
