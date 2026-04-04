@extends('adminlte::page')

@section('title', 'Dashboard Admin')

@section('content_header')
    <h1>Dashboard Sistem Madrasah</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ $totalPutra }}</h3>
                    <p>Total Santri Putra</p>
                </div>
                <div class="icon"><i class="fas fa-male"></i></div>
            </div>
        </div>
        
        <div class="col-lg-3 col-6">
            <div class="small-box bg-pink">
                <div class="inner">
                    <h3>{{ $totalPutri }}</h3>
                    <p>Total Santri Putri</p>
                </div>
                <div class="icon"><i class="fas fa-female"></i></div>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>Rp {{ number_format($pembayaranBulanIni, 0, ',', '.') }}</h3>
                    <p>Pembayaran Bulan Ini</p>
                </div>
                <div class="icon"><i class="fas fa-wallet"></i></div>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>Rp {{ number_format($tagihanBelumBayar, 0, ',', '.') }}</h3>
                    <p>Total Tagihan Belum Bayar</p>
                </div>
                <div class="icon"><i class="fas fa-file-invoice-dollar"></i></div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">Tren Pertumbuhan Santri (Per Tahun Ajaran)</h3>
                </div>
                <div class="card-body">
                    <canvas id="lineChartSantri" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card card-info">
                <div class="card-header">
                    <h3 class="card-title">Feedback Orang Tua</h3>
                </div>
                <div class="card-body">
                    <canvas id="pieChartFeedback" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card card-success">
                <div class="card-header">
                    <h3 class="card-title">Perbandingan Keuangan Keseluruhan</h3>
                </div>
                <div class="card-body">
                    <canvas id="barChartKeuangan" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                </div>
            </div>
        </div>
    </div>
@stop

@section('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    // --- 1. Line Chart (Tren Santri) ---
    const ctxLine = document.getElementById('lineChartSantri').getContext('2d');
    new Chart(ctxLine, {
        type: 'line',
        data: {
            labels: {!! json_encode($chart1Labels) !!},
            datasets: [{
                label: 'Jumlah Santri',
                data: {!! json_encode($chart1Data) !!},
                backgroundColor: 'rgba(60,141,188,0.9)',
                borderColor: 'rgba(60,141,188,0.8)',
                pointRadius: 5,
                pointColor: '#3b8bba',
                pointStrokeColor: 'rgba(60,141,188,1)',
                pointHighlightFill: '#fff',
                pointHighlightStroke: 'rgba(60,141,188,1)',
                fill: true,
                tension: 0.3 // Membuat garis agak melengkung
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: { beginAtZero: true, ticks: { stepSize: 1 } }
            }
        }
    });

    // --- 2. Bar Chart (Keuangan) ---
    const ctxBar = document.getElementById('barChartKeuangan').getContext('2d');
    new Chart(ctxBar, {
        type: 'bar',
        data: {
            labels: ['Pembayaran Masuk', 'Tagihan Belum Bayar'],
            datasets: [{
                label: 'Nominal (Rp)',
                data: {!! json_encode($chart2Data) !!},
                backgroundColor: ['#28a745', '#dc3545'], // Hijau dan Merah
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: { beginAtZero: true }
            },
            plugins: {
                legend: { display: false } // Sembunyikan legend karena label sudah jelas di bawah
            }
        }
    });

    // --- 3. Pie Chart (Feedback) ---
    const ctxPie = document.getElementById('pieChartFeedback').getContext('2d');
    new Chart(ctxPie, {
        type: 'pie',
        data: {
            labels: ['Puas', 'Tidak Puas', 'Belum Isi'],
            datasets: [{
                data: {!! json_encode($chart3Data) !!},
                backgroundColor: ['#00a65a', '#f56954', '#d2d6de'], // Hijau, Merah, Abu-abu
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
        }
    });
</script>
@stop