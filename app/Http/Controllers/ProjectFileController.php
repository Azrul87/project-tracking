<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProjectFileController extends Controller
{
    /**
     * Store a newly uploaded file for a project.
     */
    public function store(Request $request, Project $project)
    {
        $validated = $request->validate([
            'file' => 'required|file|max:102400', // 100 MB
        ]);

        $file = $validated['file'];
        $projectId = $project->project_id;
        $path = $file->storeAs(
            "projects/{$projectId}",
            $file->hashName(),
            'public'
        );

        $projectFile = $project->files()->create([
            'original_name' => $file->getClientOriginalName(),
            'path' => $path,
            'mime_type' => $file->getClientMimeType(),
            'size' => $file->getSize(),
            'uploaded_by' => optional($request->user())->user_id,
        ]);

        return back()->with('success', 'File uploaded successfully.');
    }

    /**
     * Download a stored project file.
     */
    public function download(Project $project, ProjectFile $file)
    {
        abort_unless($file->project_id === $project->project_id, 404);

        return Storage::disk('public')->download($file->path, $file->original_name);
    }

    /**
     * Remove the specified file from storage.
     */
    public function destroy(Project $project, ProjectFile $file)
    {
        abort_unless($file->project_id === $project->project_id, 404);

        Storage::disk('public')->delete($file->path);
        $file->delete();

        return back()->with('success', 'File deleted successfully.');
    }
}

