<?php

namespace App\Http\Controllers;

use App\Models\Gallery;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class GalleryController extends Controller
{
    public function index(Project $project): View
    {
        $images = $project->gallery()->latest()->paginate(24);

        return view('gallery.index', compact('project', 'images'));
    }

    public function store(Request $request, Project $project): RedirectResponse
    {
        $this->authorize('update', $project);

        $validated = $request->validate([
            'image' => ['required', 'image', 'max:5120'],
            'caption' => ['nullable', 'string', 'max:255'],
        ]);

        $path = $request->file('image')->store('gallery', 'public');

        Gallery::create([
            'project_id' => $project->id,
            'image_path' => $path,
            'caption' => $validated['caption'] ?? null,
        ]);

        return redirect()
            ->route('projects.gallery.index', $project)
            ->with('success', 'Image uploaded successfully.');
    }

    public function destroy(Project $project, Gallery $image): RedirectResponse
    {
        $this->authorize('update', $project);

        Storage::disk('public')->delete($image->image_path);
        $image->delete();

        return redirect()
            ->route('projects.gallery.index', $project)
            ->with('success', 'Image removed successfully.');
    }
}
