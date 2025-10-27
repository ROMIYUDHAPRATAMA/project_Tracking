<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Requirement;
use Illuminate\Http\Request;

class RequirementController extends Controller
{
    /**
     * Aturan validasi requirement
     */
    private function rules(): array
    {
        return [
            'title'               => 'required|string|max:200',
            'type'                => 'required|in:FR,NFR',
            'priority'            => 'required|in:Low,Medium,High',
            'status'              => 'required|in:Planned,In Progress,Done',
            'acceptance_criteria' => 'nullable|string',
        ];
    }

    /**
     * Pesan validasi custom
     */
    private function messages(): array
    {
        return [
            'title.required'    => 'Judul requirement wajib diisi.',
            'title.max'         => 'Judul maksimal 200 karakter.',
            'type.in'           => 'Tipe harus FR atau NFR.',
            'priority.in'       => 'Prioritas harus Low / Medium / High.',
            'status.in'         => 'Status harus Planned / In Progress / Done.',
        ];
    }

    /**
     * Helper untuk redirect ke tab Requirement
     */
    private function backToRequirementTab(Project $project)
    {
        return redirect()->route('projects.sdlc', [
            'project' => $project->id,
            'phase'   => 'requirement',
        ]);
    }

    /**
     * CREATE - Tambah requirement baru
     */
    public function store(Request $request, Project $project)
    {
        $data = $request->validate($this->rules(), $this->messages());

        // Rapikan input
        $data['title'] = trim($data['title']);
        if (isset($data['acceptance_criteria'])) {
            $data['acceptance_criteria'] = trim($data['acceptance_criteria']);
        }

        // Simpan ke project terkait
        $project->requirements()->create($data);

        return $this->backToRequirementTab($project)
                    ->with('ok', '✅ Requirement baru berhasil ditambahkan.');
    }

    /**
     * UPDATE - Edit requirement yang sudah ada
     */
    public function update(Request $request, Project $project, Requirement $requirement)
    {
        // Amankan agar requirement tidak bisa dimanipulasi dari project lain
        if ($requirement->project_id !== $project->id) {
            abort(404);
        }

        $data = $request->validate($this->rules(), $this->messages());

        // Rapikan input
        $data['title'] = trim($data['title']);
        if (isset($data['acceptance_criteria'])) {
            $data['acceptance_criteria'] = trim($data['acceptance_criteria']);
        }

        // Update data
        $requirement->update($data);

        return $this->backToRequirementTab($project)
                    ->with('ok', '✏️ Requirement berhasil diperbarui.');
    }

    /**
     * DELETE - Hapus requirement
     */
    public function destroy(Project $project, Requirement $requirement)
    {
        // Pastikan requirement milik project ini
        if ($requirement->project_id !== $project->id) {
            abort(404);
        }

        $requirement->delete();

        return $this->backToRequirementTab($project)
                    ->with('ok', '🗑️ Requirement berhasil dihapus.');
    }
}
