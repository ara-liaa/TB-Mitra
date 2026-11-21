/**
 * Admin Charts - Toko Bangunan Mitra (TB Mitra)
 * Menginisialisasi grafik Tren Penjualan & Distribusi Kategori Produk
 * menggunakan Chart.js dengan palet biru, putih, dan abu-abu.
 */
document.addEventListener('DOMContentLoaded', function () {
    initSalesTrendChart();
    initCategoryDistributionChart();
    initPaymentBreakdownChart();
});

/**
 * Format angka ke format Rupiah ringkas untuk sumbu Y dan tooltip grafik
 */
function formatRupiahChart(value) {
    const num = Number(value) || 0;
    return 'Rp ' + num.toLocaleString('id-ID');
}

function formatRupiahCompact(value) {
    const num = Number(value) || 0;
    if (num >= 1_000_000_000) {
        return 'Rp ' + (num / 1_000_000_000).toFixed(1).replace('.', ',') + ' M';
    }
    if (num >= 1_000_000) {
        return 'Rp ' + (num / 1_000_000).toFixed(1).replace('.', ',') + ' jt';
    }
    if (num >= 1_000) {
        return 'Rp ' + Math.round(num / 1_000) + ' rb';
    }
    return 'Rp ' + num;
}

/**
 * 1. Grafik Tren Penjualan (Line Chart)
 * Membaca data dari atribut data-labels & data-values pada <canvas id="salesTrendChart">
 */
function initSalesTrendChart() {
    const canvas = document.getElementById('salesTrendChart');
    if (!canvas || typeof window.Chart === 'undefined') return;

    let labels = ['1 Jan', '5 Jan', '10 Jan', '15 Jan', '20 Jan', '25 Jan', '31 Jan'];
    let values = [0, 0, 0, 0, 0, 0, 0];

    try {
        if (canvas.dataset.labels) {
            const parsedLabels = JSON.parse(canvas.dataset.labels);
            if (Array.isArray(parsedLabels) && parsedLabels.length) labels = parsedLabels;
        }
        if (canvas.dataset.values) {
            const parsedValues = JSON.parse(canvas.dataset.values);
            if (Array.isArray(parsedValues) && parsedValues.length) values = parsedValues;
        }
    } catch (e) {
        console.warn('Format data grafik tren penjualan tidak valid:', e);
    }

    const ctx = canvas.getContext('2d');
    const gradient = ctx.createLinearGradient(0, 0, 0, 260);
    gradient.addColorStop(0, 'rgba(29, 78, 216, 0.22)');
    gradient.addColorStop(1, 'rgba(29, 78, 216, 0.01)');

    new window.Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Omzet Penjualan',
                    data: values,
                    borderColor: '#1d4ed8',
                    backgroundColor: gradient,
                    borderWidth: 2.5,
                    tension: 0.35,
                    fill: true,
                    pointBackgroundColor: '#ffffff',
                    pointBorderColor: '#1d4ed8',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                mode: 'index',
                intersect: false,
            },
            plugins: {
                legend: {
                    display: false,
                },
                tooltip: {
                    backgroundColor: '#0f172a',
                    titleColor: '#f8fafc',
                    bodyColor: '#ffffff',
                    padding: 10,
                    cornerRadius: 8,
                    callbacks: {
                        label: function (context) {
                            return ' Total: ' + formatRupiahChart(context.parsed.y);
                        },
                    },
                },
            },
            scales: {
                x: {
                    grid: {
                        display: false,
                    },
                    ticks: {
                        color: '#64748b',
                        font: { size: 11 },
                    },
                },
                y: {
                    beginAtZero: true,
                    border: {
                        display: false,
                    },
                    grid: {
                        color: '#e2e8f0',
                        borderDash: [4, 4],
                    },
                    ticks: {
                        color: '#64748b',
                        font: { size: 11 },
                        callback: function (value) {
                            return formatRupiahCompact(value);
                        },
                    },
                },
            },
        },
    });
}

/**
 * 2. Grafik Distribusi Kategori Produk (Doughnut Chart)
 * Membaca data dari atribut data-labels & data-values pada <canvas id="categoryChart">
 */
function initCategoryDistributionChart() {
    const canvas = document.getElementById('categoryChart');
    if (!canvas || typeof window.Chart === 'undefined') return;

    let labels = [];
    let values = [];

    try {
        if (canvas.dataset.labels) labels = JSON.parse(canvas.dataset.labels);
        if (canvas.dataset.values) values = JSON.parse(canvas.dataset.values);
    } catch (e) {
        console.warn('Format data grafik kategori tidak valid:', e);
    }

    if (!labels.length || !values.length) return;

    const palette = [
        '#1e3a8a',
        '#1d4ed8',
        '#3b82f6',
        '#60a5fa',
        '#93c5fd',
        '#475569',
        '#64748b',
        '#94a3b8',
    ];

    new window.Chart(canvas.getContext('2d'), {
        type: 'doughnut',
        data: {
            labels: labels,
            datasets: [
                {
                    data: values,
                    backgroundColor: labels.map((_, idx) => palette[idx % palette.length]),
                    borderColor: '#ffffff',
                    borderWidth: 2,
                    hoverOffset: 4,
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '66%',
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        usePointStyle: true,
                        pointStyle: 'circle',
                        padding: 14,
                        color: '#334155',
                        font: { size: 11 },
                    },
                },
                tooltip: {
                    backgroundColor: '#0f172a',
                    padding: 10,
                    cornerRadius: 8,
                    callbacks: {
                        label: function (context) {
                            const total = context.dataset.data.reduce((acc, v) => acc + Number(v || 0), 0);
                            const current = Number(context.parsed || 0);
                            const pct = total > 0 ? ((current / total) * 100).toFixed(1) : 0;
                            return ` ${context.label}: ${current} produk (${pct}%)`;
                        },
                    },
                },
            },
        },
    });
}

/**
 * 3. Grafik Rekap Metode Pembayaran (Bar Chart untuk Laporan)
 * Membaca data dari atribut data-labels & data-values pada <canvas id="paymentMethodChart">
 */
function initPaymentBreakdownChart() {
    const canvas = document.getElementById('paymentMethodChart');
    if (!canvas || typeof window.Chart === 'undefined') return;

    let labels = [];
    let values = [];

    try {
        if (canvas.dataset.labels) labels = JSON.parse(canvas.dataset.labels);
        if (canvas.dataset.values) values = JSON.parse(canvas.dataset.values);
    } catch (e) {
        console.warn('Format data grafik pembayaran tidak valid:', e);
    }

    if (!labels.length || !values.length) return;

    new window.Chart(canvas.getContext('2d'), {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Pendapatan',
                    data: values,
                    backgroundColor: ['#1e3a8a', '#2563eb', '#64748b'],
                    borderRadius: 8,
                    maxBarThickness: 48,
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#0f172a',
                    padding: 10,
                    cornerRadius: 8,
                    callbacks: {
                        label: function (context) {
                            return ' Pendapatan: ' + formatRupiahChart(context.parsed.y);
                        },
                    },
                },
            },
            scales: {
                x: {
                    grid: { display: false },
                    ticks: { color: '#475569', font: { size: 12, weight: '600' } },
                },
                y: {
                    beginAtZero: true,
                    border: { display: false },
                    grid: { color: '#e2e8f0' },
                    ticks: {
                        color: '#64748b',
                        font: { size: 11 },
                        callback: function (val) {
                            return formatRupiahCompact(val);
                        },
                    },
                },
            },
        },
    });
}
