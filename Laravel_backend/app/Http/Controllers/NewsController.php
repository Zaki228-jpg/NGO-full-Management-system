<?php

namespace App\Http\Controllers;

use App\Models\News;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class NewsController extends Controller
{
    public function index(): View
    {
        $news = News::with('project')->latest('published_at')->paginate(10);

        return view('news.index', compact('news'));
    }

    public function create(): View
    {
        $this->authorize('create', News::class);

        $projects = Project::orderBy('title')->get();

        return view('news.create', compact('projects'));
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', News::class);

        $validated = $request->validate([
            'project_id' => ['nullable', 'exists:projects,id'],
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'cover_image' => ['nullable', 'string'],
            'published_at' => ['nullable', 'date'],
        ]);

        $article = News::create($validated);

        return redirect()
            ->route('news.show', $article)
            ->with('success', 'Article published successfully.');
    }

    public function show(News $news): View
    {
        return view('news.show', compact('news'));
    }

    public function destroy(News $news): RedirectResponse
    {
        $this->authorize('delete', $news);

        $news->delete();

        return redirect()
            ->route('news.index')
            ->with('success', 'Article deleted successfully.');
    }
}
