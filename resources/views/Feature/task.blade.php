@extends('layouts.app')
@section('title','Task')

@section('content')
<div class="cards" style="grid-template-columns: 1fr;">
  <div class="card">
    <h2 class="panel-title" style="margin-bottom:12px">Tambah Task</h2>

    <form method="POST" action="{{ route('tasks.store') }}"
          style="display:grid;grid-template-columns:1.2fr .8fr .6fr auto;gap:10px;align-items:center">
      @csrf

      <input type="text" name="title" placeholder="Judul task *" required
             style="height:40px;border:1px solid var(--ring);border-radius:10px;padding:0 12px;background:var(--card);color:var(--ink)">

      <input type="date" name="due_date"
             style="height:40px;border:1px solid var(--ring);border-radius:10px;padding:0 12px;background:var(--card);color:var(--ink)">

      <select name="priority"
              style="height:40px;border:1px solid var(--ring);border-radius:10px;padding:0 10px;background:var(--card);color:var(--ink)">
        <option value="low">Low</option>
        <option value="medium" selected>Medium</option>
        <option value="high">High</option>
      </select>

      <button class="icon-btn" type="submit">Add</button>
    </form>
  </div>
</div>

<div class="cards" style="grid-template-columns: 1fr;">
  <div class="card">
    <h2 class="panel-title" style="margin-bottom:12px">Daftar Task</h2>

    @if($tasks->isEmpty())
      <p class="card-footer">Belum ada task.</p>
    @else
      <div style="display:flex;flex-direction:column;gap:10px">
        @foreach($tasks as $task)
          <div style="display:grid;grid-template-columns:auto 1fr .8fr .6fr auto;gap:12px;align-items:center;border:1px solid var(--ring);border-radius:12px;padding:10px;background:var(--card)">
            {{-- Toggle --}}
            <form method="POST" action="{{ route('tasks.toggle',$task) }}">
              @csrf
              @method('PATCH')
              <button class="icon-btn" title="Toggle complete" type="submit">
                {{ $task->completed ? '✔' : '○' }}
              </button>
            </form>

            {{-- Title & notes --}}
            <div>
              <div style="font-weight:600; {{ $task->completed ? 'text-decoration:line-through; opacity:.6' : '' }}">
                {{ $task->title }}
              </div>
              @if($task->notes)
                <div class="card-footer">{{ $task->notes }}</div>
              @endif
            </div>

            {{-- Due date --}}
            <div style="font-size:12px;color:var(--muted)">
              @if($task->due_date)
                Due: {{ $task->due_date->format('d M Y') }}
              @else
                No due
              @endif
            </div>

            {{-- Priority pill --}}
            <div>
              @php $color = ['low'=>'#10b981','medium'=>'#f59e0b','high'=>'#ef4444'][$task->priority]; @endphp
              <span style="font-size:12px;padding:4px 8px;border-radius:999px;border:1px solid var(--ring); background:rgba(0,0,0,.03);">
                <span style="display:inline-block;width:8px;height:8px;border-radius:50%;background:{{ $color }};margin-right:6px"></span>
                {{ ucfirst($task->priority) }}
              </span>
            </div>

            {{-- Delete --}}
            <form method="POST" action="{{ route('tasks.destroy',$task) }}" onsubmit="return confirm('Hapus task ini?')">
              @csrf
              @method('DELETE')
              <button class="icon-btn" type="submit" title="Delete">🗑</button>
            </form>
          </div>
        @endforeach
      </div>
    @endif
  </div>
</div>
@endsection
