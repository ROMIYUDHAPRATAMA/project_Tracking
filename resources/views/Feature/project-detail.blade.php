@extends('layouts.app')
@section('title','Detail Project')

@push('styles')
  @vite('resources/css/project-detail.css')
@endpush

@section('content')
<section class="project-detail">
  {{-- Breadcrumb --}}
  <nav class="breadcrumb">
    <a href="{{ route('dashboard') }}">Dashboard</a>
    <span>/</span>
    <a href="{{ route('projects.index') }}">Project</a>
    <span>/</span>
    <span>{{ $project->title }}</span>
  </nav>

  {{-- Header --}}
  <header class="detail-hero card">
    <div class="left">
      <h1 class="title">{{ $project->title }}</h1>
      <p class="subtitle">{{ $project->activity ?: 'Tidak ada ringkasan kegiatan.' }}</p>
      <div class="meta">
        @php
          $map = [
            'todo'        => ['Belum Mulai', 'pill todo'],
            'in_progress' => ['In Progress', 'pill in-progress'],
            'review'      => ['Review',      'pill review'],
            'done'        => ['Selesai',     'pill selesai'],
          ];
          [$label,$klass] = $map[$project->status] ?? ['-', 'pill'];
        @endphp
        <span class="{{ $klass }}">{{ $label }}</span>
        <span class="sep">•</span>
        <span>PIC: <strong>{{ $project->pic ?: '-' }}</strong></span>
        <span class="sep">•</span>
        <span>Mulai: <strong>{{ $project->start_date?->translatedFormat('d M Y') ?: '-' }}</strong></span>
        <span class="sep">•</span>
        <span>Selesai: <strong>{{ $project->end_date?->translatedFormat('d M Y') ?: '-' }}</strong></span>
      </div>
    </div>
  </header>

  {{-- KPI ringkas --}}
  <div class="cards-3">
    <div class="card kpi">
      <div class="kpi-label">Progress</div>
      <div class="kpi-value">{{ (int)($project->progress ?? 0) }}%</div>
      <div class="progress"><div class="bar" style="width: {{ (int)($project->progress ?? 0) }}%"></div></div>
    </div>
    <div class="card kpi">
      <div class="kpi-label">Dokumen Kontrak</div>
      <div class="kpi-value">{{ $project->outcome ? 1 : 0 }}</div>
    </div>
    <div class="card kpi">
      <div class="kpi-label">Status</div>
      <div class="kpi-value">{{ $label }}</div>
    </div>
  </div>

  {{-- Stepper SDLC --}}
  <div class="card stepper">
    @php
      use Illuminate\Support\Str;
      $steps = ['Planning','Requirement','Design','Development','Testing','Deployment','Maintenance'];
      $activeStep = ucfirst($phase ?? 'planning');
      $slug = fn(string $s) => Str::slug($s, '-'); // -> planning, requirement, design, ...
    @endphp
    <ul class="steps">
      @foreach ($steps as $s)
        <li class="step {{ $activeStep === $s ? 'active' : '' }}">
          <a href="{{ route('projects.sdlc', ['project' => $project->id, 'phase' => $slug($s)]) }}">
            <span>{{ $s }}</span>
          </a>
        </li>
      @endforeach
    </ul>
    <div class="step-content">
      <p>Fase aktif: <strong>{{ $activeStep }}</strong>. Tambahkan catatan atau checklist untuk fase ini.</p>
    </div>
  </div>

  {{-- ===== CONTENT PER PHASE ===== --}}
  @if(($phase ?? '') === 'requirement')
    {{-- ===================== TAB REQUIREMENT ===================== --}}
    <div class="card req">
      <div class="card-title">Requirements</div>

      {{-- Flash & Error --}}
      @if(session('ok')) <div class="alert success" style="margin-bottom:10px">{{ session('ok') }}</div> @endif
      @if($errors->any())
        <div class="alert danger" style="margin-bottom:10px">
          @foreach($errors->all() as $e) <div>{{ $e }}</div> @endforeach
        </div>
      @endif

      {{-- Form Tambah --}}
      <form class="stack req-form" method="POST" action="{{ route('projects.requirements.store', $project->id) }}">
        @csrf
        <div class="row-1">
          <input class="i" name="title" placeholder="Judul requirement" required>
          <select class="i" name="type" required>
            <option value="FR">FR</option>
            <option value="NFR">NFR</option>
          </select>
          <select class="i" name="priority" required>
            <option>Low</option><option selected>Medium</option><option>High</option>
          </select>
          <select class="i" name="status" required>
            <option selected>Planned</option><option>In Progress</option><option>Done</option>
          </select>
        </div>
        <div class="row-2">
          <textarea class="i" name="acceptance_criteria" rows="3" placeholder="Acceptance criteria (opsional)"></textarea>
          <button class="btn btn-brand" type="submit">Simpan</button>
        </div>
      </form>

      {{-- Tabel --}}
      <div class="table-wrap req-table" style="margin-top:14px; overflow:auto">
        <table class="table">
          <thead>
          <tr>
            <th style="width:36px">#</th>
            <th>Judul</th>
            <th style="width:80px">Type</th>
            <th style="width:90px">Priority</th>
            <th style="width:120px">Status</th>
            <th>Acceptance Criteria</th>
            <th style="width:160px">Aksi</th>
          </tr>
          </thead>
          <tbody>
          @forelse($project->requirements as $i => $r)
            <tr>
              <td>{{ $i+1 }}</td>
              <td>{{ $r->title }}</td>
              <td>{{ $r->type }}</td>
              <td>{{ $r->priority }}</td>
              <td>{{ $r->status }}</td>
              <td class="muted">{{ \Illuminate\Support\Str::limit($r->acceptance_criteria, 80) }}</td>
              <td>
                <div style="display:flex; gap:6px; flex-wrap:wrap">
                  <a href="#edit-{{ $r->id }}" class="btn btn-ghost sm">Edit</a>
                  <form method="POST"
                        action="{{ route('projects.requirements.destroy', [$project->id, $r->id]) }}"
                        onsubmit="return confirm('Hapus requirement ini?')">
                    @csrf @method('DELETE')
                    <button class="btn btn-danger sm">Hapus</button>
                  </form>
                </div>
              </td>
            </tr>

            {{-- Inline Edit --}}
            <tr id="edit-{{ $r->id }}">
              <td colspan="7">
                <form method="POST" action="{{ route('projects.requirements.update', [$project->id, $r->id]) }}"
                      style="display:grid; gap:10px;">
                  @csrf @method('PUT')
                  <div style="display:grid; grid-template-columns:2fr 110px 130px 150px; gap:12px;">
                    <input class="i" name="title" value="{{ $r->title }}" required>
                    <select class="i" name="type" required>
                      <option value="FR"  @selected($r->type==='FR')>FR</option>
                      <option value="NFR" @selected($r->type==='NFR')>NFR</option>
                    </select>
                    <select class="i" name="priority" required>
                      <option @selected($r->priority==='Low')>Low</option>
                      <option @selected($r->priority==='Medium')>Medium</option>
                      <option @selected($r->priority==='High')>High</option>
                    </select>
                    <select class="i" name="status" required>
                      <option @selected($r->status==='Planned')>Planned</option>
                      <option @selected($r->status==='In Progress')>In Progress</option>
                      <option @selected($r->status==='Done')>Done</option>
                    </select>
                  </div>
                  <div style="display:grid; grid-template-columns:2fr 170px; gap:12px;">
                    <textarea class="i" name="acceptance_criteria" rows="2">{{ $r->acceptance_criteria }}</textarea>
                    <div style="display:flex; gap:8px; align-items:center">
                      <button class="btn btn-brand" type="submit">Simpan</button>
                      <a href="#top" class="btn btn-ghost">Batal</a>
                    </div>
                  </div>
                </form>
              </td>
            </tr>
          @empty
            <tr><td colspan="7" class="muted">Belum ada requirement.</td></tr>
          @endforelse
          </tbody>
        </table>
      </div>
    </div>

  @elseif(($phase ?? '') === 'design')
    {{-- ===================== TAB DESIGN ===================== --}}
    <div class="card">
      <div class="card-title">Design Specification (terhubung ke Requirement)</div>

      @if(session('ok')) <div class="alert success" style="margin-bottom:10px">{{ session('ok') }}</div> @endif
      @if($errors->any())
        <div class="alert danger" style="margin-bottom:10px">
          @foreach($errors->all() as $e) <div>{{ $e }}</div> @endforeach
        </div>
      @endif

      {{-- Form Tambah DesignSpec --}}
      <form method="POST" action="{{ route('projects.design-specs.store', $project->id) }}" class="stack" style="margin-bottom:12px">
        @csrf
        <div style="display:grid; grid-template-columns: 1.3fr .8fr 1.2fr .8fr; gap:10px;">
          <select name="requirement_id" class="i" required>
            <option value="">— Pilih Requirement —</option>
            @foreach($project->requirements as $req)
              <option value="{{ $req->id }}">{{ $req->title }} ({{ $req->type }})</option>
            @endforeach
          </select>
          <select name="artifact_type" class="i" required>
            <option>UI</option><option>API</option><option>DB</option><option>Flow</option>
          </select>
          <input type="text" name="artifact_name" class="i" placeholder="Nama komponen / endpoint / tabel" required>
          <select name="status" class="i" required>
            <option>Draft</option><option>Review</option><option>Approved</option>
          </select>
        </div>

        <div style="display:grid; grid-template-columns: 1.2fr 2fr .6fr; gap:10px; margin-top:10px;">
          <input type="url" name="reference_url" class="i" placeholder="Link Figma / Postman / ERD (opsional)">
          <textarea name="rationale" rows="2" class="i" placeholder="Alasan desain / catatan teknis (opsional)"></textarea>
          <button class="btn btn-brand" type="submit">Tambah</button>
        </div>
      </form>

      {{-- Tabel DesignSpec --}}
      <div class="table-wrap" style="overflow:auto">
        <table class="table">
          <thead>
            <tr>
              <th>#</th><th>Requirement</th><th>Tipe</th><th>Nama Artefak</th>
              <th>Status</th><th>Referensi</th><th style="width:160px">Aksi</th>
            </tr>
          </thead>
          <tbody>
          @forelse($project->designSpecs as $i => $d)
            <tr id="row-{{ $d->id }}">
              <td>{{ $i+1 }}</td>
              <td>{{ optional($d->requirement)->title }}</td>
              <td>{{ $d->artifact_type }}</td>
              <td>{{ $d->artifact_name }}</td>
              <td>{{ $d->status }}</td>
              <td>
                @if($d->reference_url)
                  <a class="link-project" href="{{ $d->reference_url }}" target="_blank" rel="noopener">Open</a>
                @else — @endif
              </td>
              <td style="display:flex; gap:6px; flex-wrap:wrap">
                <a href="#edit-design-{{ $d->id }}" class="btn btn-ghost sm">Edit</a>
                <form method="POST" action="{{ route('design-specs.destroy',$d->id) }}"
                      onsubmit="return confirm('Hapus design spec ini?')">
                  @csrf @method('DELETE')
                  <button class="btn sm" style="background:#fee2e2; border-color:#fecaca; color:#7f1d1d">Hapus</button>
                </form>
              </td>
            </tr>

            {{-- Inline Edit --}}
            <tr id="edit-design-{{ $d->id }}">
              <td colspan="7" style="background:#fbfbfd">
                <form method="POST" action="{{ route('design-specs.update',$d->id) }}" style="display:grid; gap:10px;">
                  @csrf @method('PUT')
                  <div style="display:grid; grid-template-columns: 1.3fr .8fr 1.2fr .8fr; gap:10px;">
                    <select name="requirement_id" class="i" required>
                      @foreach($project->requirements as $req)
                        <option value="{{ $req->id }}" @selected($req->id === $d->requirement_id)>
                          {{ $req->title }} ({{ $req->type }})
                        </option>
                      @endforeach
                    </select>
                    <select name="artifact_type" class="i" required>
                      @foreach(['UI','API','DB','Flow'] as $opt)
                        <option @selected($d->artifact_type===$opt)>{{ $opt }}</option>
                      @endforeach
                    </select>
                    <input type="text" name="artifact_name" class="i" value="{{ $d->artifact_name }}" required>
                    <select name="status" class="i" required>
                      @foreach(['Draft','Review','Approved'] as $st)
                        <option @selected($d->status===$st)>{{ $st }}</option>
                      @endforeach
                    </select>
                  </div>
                  <div style="display:grid; grid-template-columns: 1.2fr 2fr .6fr; gap:10px;">
                    <input type="url" name="reference_url" class="i" value="{{ $d->reference_url }}">
                    <textarea name="rationale" class="i" rows="2">{{ $d->rationale }}</textarea>
                    <div style="display:flex; gap:8px; align-items:center">
                      <button class="btn btn-brand" type="submit">Simpan</button>
                      <a class="btn btn-ghost" href="#row-{{ $d->id }}">Batal</a>
                    </div>
                  </div>
                </form>
              </td>
            </tr>
          @empty
            <tr><td colspan="7">Belum ada design spec.</td></tr>
          @endforelse
          </tbody>
        </table>
      </div>
    </div>

  @else
    {{-- ===================== KONTEN DEFAULT (FASE LAIN) ===================== --}}
    <div class="grid-2">
      <div class="card">
        <div class="card-title">Ringkasan</div>
        <table class="table">
          <tr><th style="width:180px">Nama Proyek</th><td>{{ $project->title }}</td></tr>
          <tr><th>PIC</th><td>{{ $project->pic ?: '-' }}</td></tr>
          <tr><th>Status</th><td>{{ $label }}</td></tr>
          <tr><th>Mulai</th><td>{{ $project->start_date?->translatedFormat('d M Y') ?: '-' }}</td></tr>
          <tr><th>Selesai</th><td>{{ $project->end_date?->translatedFormat('d M Y') ?: '-' }}</td></tr>
          <tr><th>Dokumen Kontrak</th><td>{{ $project->outcome ?: '-' }}</td></tr>
          <tr><th>Kegiatan</th><td>{{ $project->activity ?: '-' }}</td></tr>
        </table>
      </div>

      <div class="card">
        <div class="card-title">Timeline Aktivitas</div>
        <ul class="timeline">
          <li>
            <div class="dot"></div>
            <div class="content">
              <div class="when">Dibuat pada {{ $project->created_at?->translatedFormat('d M Y H:i') }}</div>
              <div class="text">Project dibuat.</div>
            </div>
          </li>
          @if($project->updated_at && $project->updated_at->ne($project->created_at))
          <li>
            <div class="dot"></div>
            <div class="content">
              <div class="when">Diupdate {{ $project->updated_at->diffForHumans() }}</div>
              <div class="text">Data project diperbarui.</div>
            </div>
          </li>
          @endif
        </ul>
      </div>
    </div>

    {{-- Gantt Timeline --}}
    <section class="card">
      <div class="card-title">Timeline Project</div>
      <div id="gantt" style="width:100%; min-height:360px;"></div>
    </section>

    {{-- Dokumen --}}
    <div class="card">
      <div class="card-title">Dokumen / Catatan</div>
      <div class="docs">
        @if($project->outcome)
          <span class="doc">{{ $project->outcome }}</span>
        @else
          <span class="muted">Belum ada dokumen tercatat.</span>
        @endif
      </div>
    </div>
  @endif
</section>
@endsection

@push('scripts')
@if(!in_array(($phase ?? ''), ['requirement','design']))
<script>
(function(){
  const parse = (s)=> s ? new Date(s+'T00:00:00') : null;
  const fmt = (d)=> d.toISOString().slice(0,10);
  const addDays = (d, n)=> { const x=new Date(d); x.setDate(x.getDate()+n); return x; };

  const start = "{{ $project->start_date?->format('Y-m-d') }}";
  const end   = "{{ $project->end_date?->format('Y-m-d') }}";
  const title = @json($project->title);
  const progress = {{ (int)($project->progress ?? 0) }};

  function splitPhases(s, e){
    const names = ['Planning','Requirement','Design','Development','Testing','Deployment','Maintenance'];
    const sd = parse(s), ed = parse(e);
    if(!sd || !ed || sd > ed) return null;

    const total = Math.max(1, Math.ceil((ed - sd) / (1000*60*60*24)) + 1);
    const step = Math.max(1, Math.floor(total / names.length));
    let cursor = new Date(sd);
    const tasks = [];

    names.forEach((name, i)=>{
      const st = new Date(cursor);
      let en = addDays(st, step-1);
      if(i === names.length - 1 || en > ed) en = new Date(ed);
      tasks.push({ id:`phase-${i+1}`, name, start:fmt(st), end:fmt(en), progress:(i<names.length-1)?100:progress });
      cursor = addDays(en, 1);
    });
    return tasks;
  }

  const defaultTask = [{ id: "p-{{ $project->id }}", name: title, start: fmt(new Date()), end: fmt(addDays(new Date(), 7)), progress }];
  const tasks = splitPhases(start, end) ?? defaultTask;

  function renderGantt(GanttCtor){
    new GanttCtor("#gantt", tasks, {
      view_modes: ['Day','Week','Month'],
      view_mode: 'Week',
      date_format: 'YYYY-MM-DD',
      bar_height: 26,
      padding: 20,
      arrow_curve: 4,
      custom_popup_html: (task)=>`
        <div class="popup">
          <h5 style="margin:0 0 6px 0;">${task.name}</h5>
          <p style="margin:0">Start: ${task.start}</p>
          <p style="margin:0">End: ${task.end}</p>
          <p style="margin:6px 0 0 0"><b>Progress:</b> ${task.progress}%</p>
        </div>`
    });
  }

  if (window.Gantt) { renderGantt(window.Gantt); return; }

  const css = document.createElement('link'); css.rel='stylesheet';
  css.href='https://unpkg.com/frappe-gantt@0.6.1/dist/frappe-gantt.css'; document.head.appendChild(css);
  const s = document.createElement('script'); s.src='https://unpkg.com/frappe-gantt@0.6.1/dist/frappe-gantt.min.js';
  s.onload = () => renderGantt(window.Gantt); document.body.appendChild(s);
})();
</script>
@endif
@endpush
