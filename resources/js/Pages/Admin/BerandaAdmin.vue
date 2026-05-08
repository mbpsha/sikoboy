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
                <h2 class="font-semibold mb-4">Kategori Kerjasama</h2>
                <canvas id="categoryChart"></canvas>
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
    kategori_kerjasama: {
        type: Array,
        default: () => [],
    },
});

const metrics = props.metrics ?? {};

onMounted(() => {
    // Dummy data untuk Kerjasama per Tahun
    const dummyKerjasamaTahun = props.kerjasama_per_tahun.length > 0 
        ? props.kerjasama_per_tahun 
        : [
            { tahun: '2020', total: 12 },
            { tahun: '2021', total: 18 },
            { tahun: '2022', total: 25 },
            { tahun: '2023', total: 30 },
            { tahun: '2024', total: 28 },
        ];

    // Dummy data untuk Kategori Kerjasama
    const dummyKategoriKerjasama = props.kategori_kerjasama.length > 0 
        ? props.kategori_kerjasama 
        : [
            { kategori: 'Kerjasama Daerah Antar Daerah (KSDD)', total: 25 },
            { kategori: 'Kerjasama Dengan Pihak Ketiga (KSDPK)', total: 32 },
            { kategori: 'Sinergi Dengan Pemerintah Pusat / Lembaga (NK/RK)', total: 20 },
            { kategori: 'Perjanjian Teknis (PERTEK)', total: 18 },
            { kategori: 'Kerjasama Daerah Dengan Pemerintah Daerah Di Luar Negeri (KSDPL)', total: 12 },
            { kategori: 'Kerjasama Daerah Dengan Lembaga Di Luar Negeri (KSDLL)', total: 8 },
        ];

    // Hitung total untuk persentase
    const totalKategori = dummyKategoriKerjasama.reduce((sum, row) => sum + row.total, 0) || 1;

    // Helper untuk format persentase agar konsisten
    const formatPercentage = (value) => Math.round((value / totalKategori) * 100) + '%';

    // Chart 1: Kerjasama per Tahun (Bar Chart)
    new Chart(document.getElementById("barChart"), {
        type: "bar",
        data: {  // ✅ PENTING: Keyword "data:" harus ada di sini
            labels: dummyKerjasamaTahun.map((row) => row.tahun),
            datasets: [
                {
                    label: "Jumlah Kerjasama",
                    data: dummyKerjasamaTahun.map((row) => row.total),
                    backgroundColor: "rgba(54, 162, 235, 0.7)",
                    borderColor: "rgba(54, 162, 235, 1)",
                    borderWidth: 1,
                },
            ],
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { stepSize: 1 },
                },
            },
        },
    });

    // Chart 2: Kategori Kerjasama (Pie Chart) - LEGEND DIHAPUS
    new Chart(document.getElementById("categoryChart"), {
        type: "pie",
        data: {  // ✅ PENTING: Keyword "data:" harus ada di sini
            labels: dummyKategoriKerjasama.map((row) => row.kategori),
            datasets: [
                {
                    data: dummyKategoriKerjasama.map((row) => row.total),
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
            radius: '65%',
            layout: {
                padding: {
                    top: 50,
                    right: 40,
                    bottom: 40,
                    left: 40
                }
            },
            plugins: {
                // ✅ LEGEND DIHAPUS agar teks panjang tidak muncul di bawah
                legend: {
                    display: false,
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return ` ${context.label}: ${formatPercentage(context.parsed)}`;
                        }
                    }
                }
            },
        },
        plugins: [{
            id: 'externalLabels',
            afterDraw: (chart) => {
                const ctx = chart.ctx;
                const chartArea = chart.chartArea;
                const centerX = (chartArea.left + chartArea.right) / 2;
                const centerY = (chartArea.top + chartArea.bottom) / 2;
                const radius = Math.min(chartArea.right - chartArea.left, chartArea.bottom - chartArea.top) / 2.5;
                
                const abbreviations = ['KSDD', 'KSDPK', 'NK/RK', 'PERTEK', 'KSDPL', 'KSDLL'];
                
                chart.data.datasets[0].data.forEach((value, index) => {
                    const percentage = formatPercentage(value);
                    const abbr = abbreviations[index] || '';
                    const color = chart.data.datasets[0].backgroundColor[index];
                    
                    const meta = chart._metasets[0];
                    const startAngle = meta.data[index].startAngle;
                    const endAngle = meta.data[index].endAngle;
                    const midAngle = (startAngle + endAngle) / 2;
                    
                    const labelRadius = radius + 25;
                    const x = centerX + Math.cos(midAngle) * labelRadius;
                    const y = centerY + Math.sin(midAngle) * labelRadius;
                    
                    ctx.save();
                    
                    // Kotak warna
                    ctx.fillStyle = color;
                    ctx.fillRect(x - 45, y - 6, 10, 10);
                    
                    // Teks
                    ctx.font = 'bold 11px Arial';
                    ctx.fillStyle = '#333';
                    ctx.textAlign = 'left';
                    ctx.textBaseline = 'middle';
                    ctx.fillText(`${abbr} ${percentage}`, x - 30, y);
                    
                    ctx.restore();
                });
            },
        }],
    });
});
</script>