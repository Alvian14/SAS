@extends('pages.index')

@section('admin_content')
<style>
  /* Custom Calendar Styling based on Mockup */
  #calendar-mini {
    font-family: 'Poppins', sans-serif;
  }
  #calendar-mini .fc-theme-standard td, 
  #calendar-mini .fc-theme-standard th,
  #calendar-mini .fc-theme-standard .fc-scrollgrid {
    border: none !important;
  }
  #calendar-mini .fc-scrollgrid {
    border: none !important;
  }
  #calendar-mini .fc-today-button {
    display: none !important;
  }
  #calendar-mini .fc-button {
    background: transparent !important;
    border: none !important;
    color: #64748b !important;
    box-shadow: none !important;
    padding: 0 !important;
    font-size: 1.1rem !important;
    width: 32px !important;
    height: 32px !important;
    border-radius: 50% !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    transition: all 0.2s ease !important;
  }
  #calendar-mini .fc-button:hover {
    background: #f1f5f9 !important;
    color: #1e293b !important;
  }
  #calendar-mini .fc-button .fc-icon {
    font-size: 1.1rem !important;
  }
  #calendar-mini .fc-toolbar-title {
    font-size: 1.1rem !important;
    font-weight: 600 !important;
    color: #1e293b !important;
    text-transform: capitalize;
  }
  #calendar-mini .fc-header-toolbar {
    margin-bottom: 20px !important;
  }
  #calendar-mini .fc-col-header-cell-cushion {
    visibility: hidden !important;
    position: relative !important;
    display: block !important;
    font-size: 0 !important;
    padding: 8px 0 !important;
  }
  #calendar-mini .fc-col-header-cell:nth-child(1) .fc-col-header-cell-cushion::after {
    content: "M";
    visibility: visible !important;
    font-size: 0.95rem !important;
    font-weight: 500 !important;
    color: #64748b !important;
    position: absolute !important;
    left: 50% !important;
    transform: translateX(-50%) !important;
  }
  #calendar-mini .fc-col-header-cell:nth-child(2) .fc-col-header-cell-cushion::after {
    content: "S";
    visibility: visible !important;
    font-size: 0.95rem !important;
    font-weight: 500 !important;
    color: #64748b !important;
    position: absolute !important;
    left: 50% !important;
    transform: translateX(-50%) !important;
  }
  #calendar-mini .fc-col-header-cell:nth-child(3) .fc-col-header-cell-cushion::after {
    content: "S";
    visibility: visible !important;
    font-size: 0.95rem !important;
    font-weight: 500 !important;
    color: #64748b !important;
    position: absolute !important;
    left: 50% !important;
    transform: translateX(-50%) !important;
  }
  #calendar-mini .fc-col-header-cell:nth-child(4) .fc-col-header-cell-cushion::after {
    content: "R";
    visibility: visible !important;
    font-size: 0.95rem !important;
    font-weight: 500 !important;
    color: #64748b !important;
    position: absolute !important;
    left: 50% !important;
    transform: translateX(-50%) !important;
  }
  #calendar-mini .fc-col-header-cell:nth-child(5) .fc-col-header-cell-cushion::after {
    content: "K";
    visibility: visible !important;
    font-size: 0.95rem !important;
    font-weight: 500 !important;
    color: #64748b !important;
    position: absolute !important;
    left: 50% !important;
    transform: translateX(-50%) !important;
  }
  #calendar-mini .fc-col-header-cell:nth-child(6) .fc-col-header-cell-cushion::after {
    content: "J";
    visibility: visible !important;
    font-size: 0.95rem !important;
    font-weight: 500 !important;
    color: #64748b !important;
    position: absolute !important;
    left: 50% !important;
    transform: translateX(-50%) !important;
  }
  #calendar-mini .fc-col-header-cell:nth-child(7) .fc-col-header-cell-cushion::after {
    content: "S";
    visibility: visible !important;
    font-size: 0.95rem !important;
    font-weight: 500 !important;
    color: #64748b !important;
    position: absolute !important;
    left: 50% !important;
    transform: translateX(-50%) !important;
  }
  #calendar-mini .fc-daygrid-day-top {
    display: flex !important;
    justify-content: center !important;
    align-items: center !important;
    float: none !important;
  }
  #calendar-mini .fc-daygrid-day-number {
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    width: 36px !important;
    height: 36px !important;
    padding: 0 !important;
    font-size: 0.95rem !important;
    color: #334155 !important;
    text-decoration: none !important;
    transition: all 0.2s ease !important;
  }
  #calendar-mini .fc-daygrid-day-number:hover {
    background-color: #f1f5f9 !important;
    border-radius: 50% !important;
    color: #0f172a !important;
  }
  #calendar-mini .fc-day-today {
    background-color: transparent !important;
  }
  #calendar-mini .fc-day-today .fc-daygrid-day-number,
  #calendar-mini .cal-active-day {
    background-color: #6366f1 !important;
    color: #ffffff !important;
    border-radius: 50% !important;
    font-weight: 600 !important;
    box-shadow: 0 4px 10px rgba(99, 102, 241, 0.3) !important;
  }
  #calendar-mini .fc-daygrid-day-events,
  #calendar-mini .fc-daygrid-day-bottom {
    display: none !important;
  }
  #calendar-mini .fc-daygrid-day {
    height: 48px !important;
  }
</style>
<div class="container-fluid px-4 py-4">

  {{-- ===== Header ===== --}}
  <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
      <h4 class="fw-bold mb-1" style="color:#1e293b;">Dashboard</h4>
      <span class="text-muted" style="font-size:0.85rem;">
        <i class="lni lni-calendar me-1"></i>{{ now()->translatedFormat('l, d F Y') }}
      </span>
    </div>
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb mb-0" style="font-size:0.85rem;">
        <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}" class="text-decoration-none">Dashboard</a></li>
        <li class="breadcrumb-item active">Dashboard</li>
      </ol>
    </nav>
  </div>

  {{-- ===== Statistik Cards ===== --}}
  <div class="row g-4 mb-4">

    {{-- Total Siswa --}}
    <div class="col-xl-3 col-md-6">
      <div class="card border-0 rounded-4 shadow-sm overflow-hidden h-100">
        <div class="card-body p-0">
          <div class="d-flex align-items-stretch h-100">
            <div class="d-flex align-items-center justify-content-center px-4" style="background:#6366f1;min-width:80px;">
              <i class="lni lni-users text-white" style="font-size:1.8rem;"></i>
            </div>
            <div class="p-4 flex-grow-1">
              <p class="text-muted small mb-1 fw-semibold text-uppercase" style="letter-spacing:.05em;font-size:.72rem;">Total Siswa</p>
              <h3 class="fw-bold mb-0" style="color:#1e293b;">{{ $totalSiswa ?? 0 }}</h3>
              <span class="badge mt-2" style="background:#ede9fe;color:#6366f1;font-size:.75rem;">Semua Kelas</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    {{-- Kelas 10 --}}
    <div class="col-xl-3 col-md-6">
      <div class="card border-0 rounded-4 shadow-sm overflow-hidden h-100">
        <div class="card-body p-0">
          <div class="d-flex align-items-stretch h-100">
            <div class="d-flex align-items-center justify-content-center px-4" style="background:#10b981;min-width:80px;">
              <i class="lni lni-graduation text-white" style="font-size:1.8rem;"></i>
            </div>
            <div class="p-4 flex-grow-1">
              <p class="text-muted small mb-1 fw-semibold text-uppercase" style="letter-spacing:.05em;font-size:.72rem;">Siswa Kelas 10</p>
              <h3 class="fw-bold mb-0" style="color:#1e293b;">{{ $siswaKelas10 ?? 0 }}</h3>
              <span class="badge mt-2" style="background:#d1fae5;color:#10b981;font-size:.75rem;">Kelas X</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    {{-- Kelas 11 --}}
    <div class="col-xl-3 col-md-6">
      <div class="card border-0 rounded-4 shadow-sm overflow-hidden h-100">
        <div class="card-body p-0">
          <div class="d-flex align-items-stretch h-100">
            <div class="d-flex align-items-center justify-content-center px-4" style="background:#3b82f6;min-width:80px;">
              <i class="lni lni-graduation text-white" style="font-size:1.8rem;"></i>
            </div>
            <div class="p-4 flex-grow-1">
              <p class="text-muted small mb-1 fw-semibold text-uppercase" style="letter-spacing:.05em;font-size:.72rem;">Siswa Kelas 11</p>
              <h3 class="fw-bold mb-0" style="color:#1e293b;">{{ $siswaKelas11 ?? 0 }}</h3>
              <span class="badge mt-2" style="background:#dbeafe;color:#3b82f6;font-size:.75rem;">Kelas XI</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    {{-- Kelas 12 --}}
    <div class="col-xl-3 col-md-6">
      <div class="card border-0 rounded-4 shadow-sm overflow-hidden h-100">
        <div class="card-body p-0">
          <div class="d-flex align-items-stretch h-100">
            <div class="d-flex align-items-center justify-content-center px-4" style="background:#f59e0b;min-width:80px;">
              <i class="lni lni-graduation text-white" style="font-size:1.8rem;"></i>
            </div>
            <div class="p-4 flex-grow-1">
              <p class="text-muted small mb-1 fw-semibold text-uppercase" style="letter-spacing:.05em;font-size:.72rem;">Siswa Kelas 12</p>
              <h3 class="fw-bold mb-0" style="color:#1e293b;">{{ $siswaKelas12 ?? 0 }}</h3>
              <span class="badge mt-2" style="background:#fef3c7;color:#f59e0b;font-size:.75rem;">Kelas XII</span>
            </div>
          </div>
        </div>
      </div>
    </div>

  </div>

  {{-- ===== Grafik Absensi + Chart2 ===== --}}
  <div class="row g-4 mb-4">

    {{-- Grafik Absensi Harian --}}
    <div class="col-lg-8">
      <div class="card border-0 rounded-4 shadow-sm h-100">
        <div class="card-header bg-white border-0 px-4 pt-4 pb-0 rounded-top-4">
          <div class="d-flex justify-content-between align-items-center">
            <div>
              <h6 class="fw-bold mb-0" style="color:#1e293b;">Grafik Absensi Harian</h6>
              <small class="text-muted" id="chart-subtitle">{{ now()->translatedFormat('F Y') }}</small>
            </div>
            <div class="d-flex align-items-center gap-3">
              <div class="d-flex align-items-center gap-2">
                <span class="d-inline-block rounded-circle" style="width:10px;height:10px;background:#6366f1;"></span>
                <small class="text-muted">Persentase Kehadiran</small>
              </div>
              <select id="month-picker" class="form-select form-select-sm" style="width:auto;font-size:0.82rem;">
                <option value="1" {{ now()->month == 1 ? 'selected' : '' }}>Januari</option>
                <option value="2" {{ now()->month == 2 ? 'selected' : '' }}>Februari</option>
                <option value="3" {{ now()->month == 3 ? 'selected' : '' }}>Maret</option>
                <option value="4" {{ now()->month == 4 ? 'selected' : '' }}>April</option>
                <option value="5" {{ now()->month == 5 ? 'selected' : '' }}>Mei</option>
                <option value="6" {{ now()->month == 6 ? 'selected' : '' }}>Juni</option>
                <option value="7" {{ now()->month == 7 ? 'selected' : '' }}>Juli</option>
                <option value="8" {{ now()->month == 8 ? 'selected' : '' }}>Agustus</option>
                <option value="9" {{ now()->month == 9 ? 'selected' : '' }}>September</option>
                <option value="10" {{ now()->month == 10 ? 'selected' : '' }}>Oktober</option>
                <option value="11" {{ now()->month == 11 ? 'selected' : '' }}>November</option>
                <option value="12" {{ now()->month == 12 ? 'selected' : '' }}>Desember</option>
              </select>
            </div>
          </div>
        </div>
        <div class="card-body px-4 pb-4 pt-3">
          <div style="position:relative;height:300px;">
            <canvas id="Chart1"></canvas>
          </div>
        </div>
      </div>
    </div>

    {{-- Kalender --}}
    <div class="col-lg-4">
      <div class="card border-0 rounded-4 shadow-sm h-100">
        <div class="card-header bg-white border-0 px-4 pt-4 pb-0 rounded-top-4">
          <h6 class="fw-bold mb-0" style="color:#1e293b;">
            <i class="lni lni-calendar me-2 text-primary"></i>Kalender
          </h6>
        </div>
        <div class="card-body p-4">
          <div id="calendar-mini"></div>
        </div>
      </div>
    </div>

  </div>

</div>
@endsection

@push('scripts')
<script>
    const allMonthlyStats = @json($allMonthlyStats);
    const monthNames = {
        1:'Januari',2:'Februari',3:'Maret',4:'April',5:'Mei',6:'Juni',
        7:'Juli',8:'Agustus',9:'September',10:'Oktober',11:'November',12:'Desember'
    };

    function getLabelsAndData(month) {
        const stats = allMonthlyStats[month] ?? {};
        const days = Object.keys(stats).map(Number).sort((a,b) => a-b);
        const labels = days.map(day => 'Tgl ' + day);
        const data = days.map(day => stats[day] ?? 0);
        return { labels, data };
    }

    let chart1;
    function renderChart(month) {
        const ctx = document.getElementById("Chart1").getContext("2d");
        const { labels, data } = getLabelsAndData(month);
        const total = data.reduce((s, v) => s + v, 0);
        const percentData = data.map(v => total > 0 ? parseFloat(((v / total) * 100).toFixed(2)) : 0);

        // Update subtitle
        document.getElementById('chart-subtitle').textContent = monthNames[month] + ' {{ now()->year }}';

        if (chart1) chart1.destroy();

        const gradient = ctx.createLinearGradient(0, 0, 0, 300);
        gradient.addColorStop(0, 'rgba(99,102,241,0.25)');
        gradient.addColorStop(1, 'rgba(99,102,241,0)');

        chart1 = new Chart(ctx, {
            type: "line",
            data: {
                labels,
                datasets: [{
                    label: "Absensi (%)",
                    backgroundColor: gradient,
                    borderColor: "#6366f1",
                    data: percentData,
                    fill: true,
                    pointBackgroundColor: "#fff",
                    pointBorderColor: "#6366f1",
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    borderWidth: 2.5,
                    tension: 0.4,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: "#fff",
                        titleColor: "#64748b",
                        bodyColor: "#1e293b",
                        borderColor: "#e2e8f0",
                        borderWidth: 1,
                        padding: 12,
                        callbacks: {
                            label: c => `Kehadiran: ${c.parsed.y}%`
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100,
                        grid: { color: "rgba(0,0,0,0.04)", drawBorder: false },
                        ticks: {
                            callback: v => v + '%',
                            padding: 8,
                            color: '#94a3b8',
                            font: { size: 11 }
                        }
                    },
                    x: {
                        grid: { display: false, drawBorder: false },
                        ticks: { padding: 8, color: '#94a3b8', font: { size: 11 } }
                    }
                }
            }
        });
    }

    // Render bulan berjalan saat load
    renderChart({{ now()->month }});

    document.getElementById('month-picker').addEventListener('change', function() {
        renderChart(parseInt(this.value));
    });

    // Handle day selection for custom calendar
    document.addEventListener('DOMContentLoaded', function() {
        const container = document.getElementById('calendar-mini');
        if (container) {
            container.addEventListener('click', function(e) {
                const dayCell = e.target.closest('.fc-daygrid-day-number');
                if (dayCell) {
                    // Remove active class from previous
                    const activeCell = container.querySelector('.cal-active-day');
                    if (activeCell) {
                        activeCell.classList.remove('cal-active-day');
                    }
                    // If the clicked day is today, it already highlights, but we can standardise the class
                    const todayCell = container.querySelector('.fc-day-today .fc-daygrid-day-number');
                    if (todayCell && todayCell !== dayCell) {
                        // Temporarily override today's color back to normal
                        todayCell.style.setProperty('background-color', 'transparent', 'important');
                        todayCell.style.setProperty('color', '#334155', 'important');
                        todayCell.style.setProperty('font-weight', '400', 'important');
                        todayCell.style.setProperty('box-shadow', 'none', 'important');
                    } else if (todayCell === dayCell) {
                        todayCell.style.removeProperty('background-color');
                        todayCell.style.removeProperty('color');
                        todayCell.style.removeProperty('font-weight');
                        todayCell.style.removeProperty('box-shadow');
                    }
                    dayCell.classList.add('cal-active-day');
                }
            });
        }
    });
</script>
@endpush

