<?php

namespace App\Http\Controllers;

use App\Models\{Project, DesignSpec};
use Illuminate\Http\Request;

class DesignSpecController extends Controller
{
    /**
     * Store a newly created Design Spec in storage.
     */
    public function store(Request $request, Project $project)
    {
        $data = $request->validate([
            'requirement_id' => 'required|exists:requirements,id',
            'artifact_type'  => 'required|in:UI,API,DB,Flow',
            'artifact_name'  => 'required|string|max:200',
            'reference_url'  => 'nullable|url|max:300',
            'rationale'      => 'nullable|string',
            'status'         => 'required|in:Draft,Review,Approved',
        ]);

        $project->designSpecs()->create($data);

        return back()->with('ok', 'Design spec berhasil ditambahkan.');
    }

    /**
     * Update the specified Design Spec.
     */
    public function update(Request $request, DesignSpec $design_spec)
    {
        $data = $request->validate([
            'requirement_id' => 'required|exists:requirements,id',
            'artifact_type'  => 'required|in:UI,API,DB,Flow',
            'artifact_name'  => 'required|string|max:200',
            'reference_url'  => 'nullable|url|max:300',
            'rationale'      => 'nullable|string',
            'status'         => 'required|in:Draft,Review,Approved',
        ]);

        $design_spec->update($data);

        return redirect()
            ->route('projects.show', $design_spec->project_id)
            ->with('ok', 'Design spec berhasil diperbarui.');
    }

    /**
     * Remove the specified Design Spec from storage.
     */
    public function destroy(DesignSpec $design_spec)
    {
        $projectId = $design_spec->project_id;
        $design_spec->delete();

        return redirect()
            ->route('projects.show', $projectId)
            ->with('ok', 'Design spec berhasil dihapus.');
    }
}
