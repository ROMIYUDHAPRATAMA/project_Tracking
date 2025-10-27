@extends('layouts.app')
@section('title','Project')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/projects.css') }}">
@endpush

@section('content')
  @php
    $list       = $projects ?? collect();
    $total      = $list->count();
    $inProgress = $list->where('status','in_progress')->count();
    $review     = $list->where('status','review')->count();
    $done       = $list->where('status','done')->count();
  @endphp

  {{-- Alerts --}}
  @if (session('success'))
    <div class="panel alert success">
      <div class="alert-title">Berhasil</div>
      <div>{{ session('success') }}</div>
    </div>
  @endif
  @if ($errors->any())
    <div class="panel alert danger">
      <div class="alert-title">Gagal menyimpan</div>
      <ul>
        @foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach
      </ul>
    </div>
  @endif

  {{-- Hero --}}
  <section class="project-hero">
    <div class="left">
      <h1 class="title">Project</h1>
      <p class="subtitle">Pantau progres pekerjaanmu: status, timeline, dan dokumen kontrak.</p>
    </div>
    <div class="right">
      <button class="btn btn-brand" type="button" id="btnOpenCreate">Tambah Project</button>
    </div>
  </section>

  {{-- KPI --}}
  <section class="kpi-cards">
    <div class="kpi-card"><div class="kpi-label">Total Project</div><div class="kpi-value">{{ $total }}</div></div>
    <div class="kpi-card"><div class="kpi-label">In Progress</div><div class="kpi-value">{{ $inProgress }}</div></div>
    <div class="kpi-card"><div class="kpi-label">Review</div><div class="kpi-value">{{ $review }}</div></div>
    <div class="kpi-card"><div class="kpi-label">Selesai</div><div class="kpi-value">{{ $done }}</div></div>
  </section>

  {{-- Table --}}
  <div class="panel table-panel">
    <div class="proj-table">
      <div class="proj-head">
        <div class="th">Tugas</div>
        <div class="th">PIC</div>
        <div class="th">Status</div>
        <div class="th">Tanggal Mulai</div>
        <div class="th">Tanggal Selesai</div>
        <div class="th">Persentase</div>
        <div class="th">Dokumen Kontrak</div>
        <div class="th">Kegiatan</div>
        <div class="th w-actions">Aksi</div>
      </div>

      <div class="proj-body">
        @forelse ($list as $p)
          @php
            $map = [
              'todo'        => ['Belum Mulai', '#64748b'],
              'in_progress' => ['In Progress', '#f59e0b'],
              'review'      => ['Review',      '#3b82f6'],
              'done'        => ['Selesai',     '#22c55e'],
            ];
            [$label,$color] = $map[$p->status] ?? ['-', '#94a3b8'];
          @endphp
          <div class="proj-row">
            <div class="cell">
              <a class="link-project" href="{{ route('projects.show', $p->id) }}"><b>{{ $p->title }}</b></a>
            </div>
            <div class="cell">{{ $p->pic ?: '-' }}</div>
            <div class="cell">
              <span class="pill"><span class="dot" style="background:{{ $color }}"></span>{{ $label }}</span>
            </div>
            <div class="cell">{{ $p->start_date?->translatedFormat('d M Y') ?: '-' }}</div>
            <div class="cell">{{ $p->end_date?->translatedFormat('d M Y')   ?: '-' }}</div>
            <div class="cell">
              <div class="meter"><span style="width:{{ (int)($p->progress ?? 0) }}%"></span></div>
              <small>{{ (int)($p->progress ?? 0) }}%</small>
            </div>
            <div class="cell">{{ $p->outcome  ?: '-' }}</div>
            <div class="cell">{{ $p->activity ?: '-' }}</div>
            <div class="cell actions">
              {{-- Edit (pakai modal) --}}
              <button
                type="button"
                class="btn btn-ghost sm btn-edit"
                data-id="{{ $p->id }}"
                data-title="{{ $p->title }}"
                data-pic="{{ $p->pic }}"
                data-status="{{ $p->status }}"
                data-start="{{ $p->start_date?->format('Y-m-d') }}"
                data-end="{{ $p->end_date?->format('Y-m-d') }}"
                data-progress="{{ (int)($p->progress ?? 0) }}"
                data-outcome="{{ $p->outcome }}"
                data-activity="{{ $p->activity }}"
              >✎</button>

              {{-- Delete --}}
              <form id="del-{{ $p->id }}" class="hidden" method="POST" action="{{ route('projects.destroy',$p) }}">
                @csrf @method('DELETE')
              </form>
              <button type="button" class="btn btn-ghost sm btn-del" data-id="{{ $p->id }}">🗑</button>
            </div>
          </div>
        @empty
          <div class="proj-row is-empty">
            <div class="cell cell-full">
              <div class="empty-card">
                <div class="empty-icon">📋</div>
                <div class="empty-title">Belum ada project</div>
                <div class="empty-desc">Mulai dengan menekan tombol <b>Tambah Project</b>.</div>
              </div>
            </div>
          </div>
        @endforelse
      </div>
    </div>
  </div>

  {{-- ===================== MODAL (Create/Edit) ===================== --}}
  <div id="projectModal" class="modal hidden" aria-hidden="true">
    <div class="modal-backdrop" data-close></div>

    <div class="modal-dialog" role="dialog" aria-modal="true" aria-labelledby="modalTitle">
      <header class="modal-head">
        <div class="modal-title">
          <span class="modal-badge">Baru</span>
          <h3 id="modalTitle">Form Tambah Proyek</h3>
        </div>
        <button type="button" class="btn btn-ghost sm" data-close aria-label="Tutup">✕</button>
      </header>

      <form id="projectForm" method="POST"
            data-action-store="{{ route('projects.store') }}"
            action="{{ route('projects.store') }}">
        @csrf
        <input type="hidden" name="_method" id="methodSpoof" value="POST">

        <div class="modal-body">
          <div class="form-grid">
            <div class="field col-span-12">
              <label>Nama Proyek <span class="req">*</span></label>
              <input name="title" class="i" required>
            </div>
            <div class="field col-span-12 md:col-span-6">
              <label>PIC</label>
              <input name="pic" class="i">
            </div>
            <div class="field col-span-12 md:col-span-6">
              <label>Status</label>
              <select name="status" class="i">
                <option value="todo">Belum Mulai</option>
                <option value="in_progress">In Progress</option>
                <option value="review">Review</option>
                <option value="done">Selesai</option>
              </select>
            </div>
            <div class="field col-span-12 md:col-span-6">
              <label>Tanggal Mulai</label>
              <input type="date" name="start_date" class="i">
            </div>
            <div class="field col-span-12 md:col-span-6">
              <label>Tanggal Selesai</label>
              <input type="date" name="end_date" class="i">
            </div>
            <div class="field col-span-12">
              <label>Progres (%)</label>
              <div class="progress-wrap">
                <input id="progressRange" type="range" min="0" max="100" step="1" value="0">
                <input id="progressInput" type="number" name="progress" class="i compact" min="0" max="100" value="0">
              </div>
              <div class="meter mt-6"><span id="progressBar" style="width:0%"></span></div>
            </div>
            <div class="field col-span-12">
              <label>Dokumen Kontrak</label>
              <input name="outcome" class="i">
            </div>
            <div class="field col-span-12">
              <label>Kegiatan</label>
              <textarea name="activity" class="i" rows="3"></textarea>
            </div>
          </div>
        </div>

        <footer class="modal-foot">
          <button class="btn btn-ghost" type="button" data-close>Batal</button>
          <button class="btn btn-brand" type="submit">Simpan</button>
        </footer>
      </form>
    </div>
  </div>
@endsection

@push('styles')
<style>
  .link-project { color:#0b1727; text-decoration:none }
  .link-project:hover { text-decoration:underline }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
  const modal     = document.getElementById('projectModal');
  const openBtn   = document.getElementById('btnOpenCreate');
  const closes    = modal.querySelectorAll('[data-close]');
  const form      = document.getElementById('projectForm');
  const methodInp = document.getElementById('methodSpoof');

  const f = {
    title:    form.querySelector('[name="title"]'),
    pic:      form.querySelector('[name="pic"]'),
    status:   form.querySelector('[name="status"]'),
    start:    form.querySelector('[name="start_date"]'),
    end:      form.querySelector('[name="end_date"]'),
    progress: form.querySelector('[name="progress"]'),
    outcome:  form.querySelector('[name="outcome"]'),
    activity: form.querySelector('[name="activity"]'),
    badge:    modal.querySelector('.modal-badge'),
    heading:  modal.querySelector('#modalTitle'),
    range:    document.getElementById('progressRange'),
    bar:      document.getElementById('progressBar'),
  };

  const open = () => { modal.classList.remove('hidden'); document.body.classList.add('modal-open'); };
  const close = () => { modal.classList.add('hidden'); document.body.classList.remove('modal-open'); };

  // CREATE
  function toCreateMode(){
    form.action = form.dataset.actionStore;
    methodInp.value = 'POST';
    f.badge.textContent = 'Baru';
    f.heading.textContent = 'Form Tambah Proyek';
    form.reset();
    f.range.value = 0;
    f.bar.style.width = '0%';
  }

  openBtn?.addEventListener('click', () => { toCreateMode(); open(); });
  closes.forEach(el => el.addEventListener('click', close));
  modal.addEventListener('click', e => { if (e.target.classList.contains('modal-backdrop')) close(); });

  // Progress sync
  const progressInput = document.getElementById('progressInput');
  function sync(v){
    v = Math.max(0, Math.min(100, +v || 0));
    f.range.value = v;
    progressInput.value = v;
    f.bar.style.width = v + '%';
  }
  f.range?.addEventListener('input', () => sync(f.range.value));
  progressInput?.addEventListener('input', () => sync(progressInput.value));

  // EDIT
  document.querySelectorAll('.btn-edit').forEach(btn => {
    btn.addEventListener('click', () => {
      const id = btn.dataset.id;
      form.action = "{{ url('projects') }}/" + id;
      methodInp.value = 'PUT';
      f.badge.textContent = 'Edit';
      f.heading.textContent = 'Edit Proyek';
      f.title.value    = btn.dataset.title || '';
      f.pic.value      = btn.dataset.pic || '';
      f.status.value   = btn.dataset.status || 'in_progress';
      f.start.value    = btn.dataset.start || '';
      f.end.value      = btn.dataset.end || '';
      f.progress.value = btn.dataset.progress || 0;
      f.outcome.value  = btn.dataset.outcome || '';
      f.activity.value = btn.dataset.activity || '';
      sync(f.progress.value);
      open();
    });
  });

  // DELETE
  document.querySelectorAll('.btn-del').forEach(btn => {
    btn.addEventListener('click', () => {
      const id = btn.dataset.id;
      if (confirm('Yakin hapus project ini?')) {
        document.getElementById('del-' + id).submit();
      }
    });
  });

  // Jika ada error validasi dari server, buka modal otomatis
  @if ($errors->any())
    open();
  @endif
});
</script>
@endpush
