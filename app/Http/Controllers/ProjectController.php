<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class ProjectController extends Controller
{
    /* ============ LIST & DETAIL ============ */

    public function index()
    {
        $projects = Project::mine()->latest('id')->get();
        return view('Feature.project', compact('projects'));
    }

    public function show(Project $project)
    {
        $this->authorizeOwner($project);

        // Arahkan ke SDLC tab planning
        return redirect()->route('projects.sdlc', [
            'project' => $project->id,
            'phase'   => 'planning',
        ]);
    }

    /* ============ CRUD ============ */

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['user_id']  = $request->user()->id;
        $data['progress'] = $data['progress'] ?? 0;

        Project::create($data);

        return redirect()->route('projects.index')->with('success', 'Project berhasil ditambahkan.');
    }

    public function update(Request $request, Project $project)
    {
        $this->authorizeOwner($project);

        $data = $this->validated($request);
        $data['progress'] = $data['progress'] ?? 0;

        $project->update($data);

        return redirect()->route('projects.index')->with('success', 'Project berhasil diperbarui.');
    }

    public function destroy(Project $project)
    {
        $this->authorizeOwner($project);

        $project->delete();

        return redirect()->route('projects.index')->with('success', 'Project berhasil dihapus.');
    }

    /* ============ SDLC (TAB PER PHASE) ============ */

    public function showPhase(Project $project, string $phase)
    {
        $this->authorizeOwner($project);

        $phases = [
            'planning'     => 'Planning',
            'analys'       => 'Analys',
            'requirement'  => 'Requirement',
            'design'       => 'Design',
            'development'  => 'Development',
            'implementasi' => 'Implementasi',
            'testing'      => 'Testing',
            'deployment'   => 'Deployment',
            'maintenance'  => 'Maintenance',
        ];

        $key = Str::of($phase)->lower()->value();
        if (! array_key_exists($key, $phases)) {
            $key = 'planning';
        }

        $data = [
            'project'   => $project,
            'phase'     => $key,
            'phaseName' => $phases[$key],
            'phases'    => $phases,
        ];

        if ($key === 'requirement') {
            $data['requirements'] = $project->requirements()->get();
        }

        return view('Feature.project-detail', $data);
    }

    /* ============ VALIDATION & AUTH ============ */

    protected function validated(Request $req): array
    {
        return $req->validate([
            'title'       => ['required','string','max:255'],
            'pic'         => ['nullable','string','max:255'],
            'status'      => ['required', Rule::in(['todo','in_progress','review','done'])],
            'start_date'  => ['nullable','date'],
            'end_date'    => ['nullable','date','after_or_equal:start_date'],
            'progress'    => ['nullable','integer','min:0','max:100'],
            'outcome'     => ['nullable','string','max:255'],
            'activity'    => ['nullable','string','max:255'],
        ]);
    }

    protected function authorizeOwner(Project $project): void
    {
        abort_unless($project->user_id === auth()->id(), 403, 'Tidak diizinkan mengakses project ini.');
    }
}
