@extends('layouts.app')
@section('title','Report')

@push('styles')
  @vite('resources/css/report.css')
@endpush

@section('content')
<section class="report-page">
  {{-- Hero --}}
  <div class="report-hero card">
    <div>
      <h1 class="title">Report</h1>
      <p class="subtitle">Lihat ringkasan proyek, grafik, dan ekspor data.</p>
    </div>
    <form class="filters" method="GET" action="{{ route('reports.index') }}">
      <label>
        <span>Dari</span>
        <input class="i" type="date" name="start" value="{{ $start }}">
      </label>
      <label>
        <span>Sampai</span>
        <input class="i" type="date" name="end" value="{{ $end }}">
      </label>
      <button class="btn btn-brand" type="submit">Terapkan</button>
      <a class="btn btn-ghost" href="{{ route('reports.index') }}">Reset</a>
      <a class="btn btn-ghost" href="{{ route('reports.index', array_filter(['start'=>$start,'end'=>$end,'export'=>'csv'])) }}">Export CSV</a>
    </form>
  </div>

  {{-- KPI --}}
  <section class="kpi-grid">
    <div class="card kpi">
      <div class="label">Total</div>
      <div class="value">{{ $total }}</div>
    </div>
    <div class="card kpi">
      <div class="label">In Progress</div>
      <div class="value">{{ $statusCounts['in_progress'] }}</div>
    </div>
    <div class="card kpi">
      <div class="label">Review</div>
      <div class="value">{{ $statusCounts['review'] }}</div>
    </div>
    <div class="card kpi">
      <div class="label">Selesai</div>
      <div class="value">{{ $statusCounts['done'] }}</div>
    </div>
    <div class="card kpi">
      <div class="label">Rata-rata Progress</div>
      <div class="value">{{ $avgProgress }}%</div>
    </div>
  </section>

  {{-- Charts --}}
  <section class="charts card">
    <div class="chart">
      <h3 class="chart-title">Proyek Dimulai per Bulan</h3>
      <canvas id="chartMonthly"></canvas>
    </div>
    <div class="chart">
      <h3 class="chart-title">Distribusi Status</h3>
      <canvas id="chartStatus"></canvas>
    </div>
  </section>

  {{-- Tabel --}}
  <section class="card">
    <div class="card-title">Detail Proyek</div>
    <div class="table-wrap">
      <table class="table">
        <thead>
          <tr>
            <th>Nama Proyek</th>
            <th>PIC</th>
            <th>Status</th>
            <th>Mulai</th>
            <th>Selesai</th>
            <th>Progress</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($projects as $p)
            @php $map = ['todo'=>'Belum Mulai','in_progress'=>'In Progress','review'=>'Review','done'=>'Selesai']; @endphp
            <tr>
              <td><a class="link-project" href="{{ route('projects.show', $p->id) }}">{{ $p->title }}</a></td>
              <td>{{ $p->pic ?: '-' }}</td>
              <td>{{ $map[$p->status] ?? $p->status }}</td>
              <td>{{ $p->start_date?->translatedFormat('d M Y') ?: '-' }}</td>
              <td>{{ $p->end_date?->translatedFormat('d M Y')   ?: '-' }}</td>
              <td>{{ (int)($p->progress ?? 0) }}%</td>
            </tr>
          @empty
            <tr><td colspan="6" class="muted">Tidak ada data untuk rentang ini.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </section>
</section>
@endsection

@push('scripts')
<script>
(function(){
  // ----- data dari server -----
  const monthly = {
    labels: @json($monthlyLabels),
    data:   @json($monthlyCounts),
  };
  const statusData = {
    labels: ['Belum Mulai','In Progress','Review','Selesai'],
    data:   [@json($statusCounts['todo']), @json($statusCounts['in_progress']), @json($statusCounts['review']), @json($statusCounts['done'])]
  };

  const brand = '#F69220';
  const CHARTS = window.__ptCharts || (window.__ptCharts = {});
  const CANVAS_HEIGHT = 240; // kunci tinggi via atribut

  function initCharts(){
    const m = document.getElementById('chartMonthly');
    const s = document.getElementById('chartStatus');
    if (!m || !s) return;

    // set tinggi hanya lewat atribut <canvas>
    m.setAttribute('height', CANVAS_HEIGHT);
    s.setAttribute('height', CANVAS_HEIGHT);

    // destroy instansi lama
    CHARTS.monthly?.destroy();
    CHARTS.status ?.destroy();

    // global: nonaktifkan animasi
    Chart.defaults.animation = false;
    if (Chart.defaults.transitions?.active) {
      Chart.defaults.transitions.active.animation = { duration: 0 };
    }

    // ===== Bar: Proyek per Bulan (tanpa sumbu Y) =====
    CHARTS.monthly = new Chart(m.getContext('2d'), {
      type: 'bar',
      data: {
        labels: monthly.labels,
        datasets: [{
          label: 'Jumlah Proyek',
          data: monthly.data,
          backgroundColor: brand,
          borderColor: brand,
          borderWidth: 1,
          borderRadius: 8
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        animation: false,
        layout: { padding: 8 },
        plugins: { legend: { display:false } },
        scales: {
          x: {
            grid:   { display:false },
            border: { display:false }
          },
          y: {
            display:false,              // <— hilangkan angka & garis sumbu Y
            grid:   { display:false },
            border: { display:false }
          }
        }
      }
    });

    // ===== Doughnut: Distribusi Status =====
    CHARTS.status = new Chart(s.getContext('2d'), {
      type: 'doughnut',
      data: {
        labels: statusData.labels,
        datasets: [{
          data: statusData.data,
          backgroundColor: [ '#94a3b8', '#fbbf24', '#3b82f6', '#22c55e' ],
          borderWidth: 0
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        animation: { duration: 200 },
        cutout: '60%',
        plugins: { legend: { position:'bottom' } }
      }
    });
  }

  // gunakan Chart dari Vite jika ada; kalau tidak, load CDN sekali
  if (window.Chart) { initCharts(); return; }

  if (!window.__chartJsLoading) {
    window.__chartJsLoading = [];
    const s = document.createElement('script');
    s.src = 'https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js';
    s.onload = function(){
      window.__chartJsLoading.forEach(fn => fn());
      window.__chartJsLoading = null;
      initCharts();
    };
    document.body.appendChild(s);
  } else {
    window.__chartJsLoading.push(initCharts);
  }

  // bersih saat pindah halaman
  window.addEventListener('beforeunload', () => {
    CHARTS.monthly?.destroy();
    CHARTS.status ?.destroy();
  });
})();
</script>
@endpush
