<?php

namespace App\Http\Controllers;

use App\Http\Requests\ArticleRequest;
use App\Models\Article;
use App\Models\VatRate;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ArticleController extends Controller
{
    public function index(Request $request): Response
    {
        Gate::authorize('artigos.ler');

        $search = $request->input('search');

        $articles = Article::query()
            ->with('vatRate:id,name,value')
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('reference', 'like', "%{$search}%")
                        ->orWhere('name', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->orderBy('reference')
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('articles/Index', [
            'articles' => $articles,
            'filters' => $request->only('search'),
        ]);
    }

    public function create(): Response
    {
        Gate::authorize('artigos.criar');

        return Inertia::render('articles/Form', [
            'vatRates' => VatRate::where('is_active', true)->orderBy('value')->get(['id', 'name', 'value']),
        ]);
    }

    public function store(ArticleRequest $request): RedirectResponse
    {
        Gate::authorize('artigos.criar');

        $dados = $request->validated();

        if ($request->hasFile('photo')) {
            $dados['photo_path'] = $request->file('photo')->store('articles');
        }

        Article::create($dados);

        return redirect()->route('articles.index');
    }

    public function edit(Article $article): Response
    {
        Gate::authorize('artigos.atualizar');

        $article->load('vatRate');

        return Inertia::render('articles/Form', [
            'article' => $article,
            'vatRates' => VatRate::where('is_active', true)->orderBy('value')->get(['id', 'name', 'value']),
        ]);
    }

    public function update(ArticleRequest $request, Article $article): RedirectResponse
    {
        Gate::authorize('artigos.atualizar');

        $dados = $request->validated();

        if ($request->hasFile('photo')) {
            if ($article->photo_path && Storage::exists($article->photo_path)) {
                Storage::delete($article->photo_path);
            }
            $dados['photo_path'] = $request->file('photo')->store('articles');
        }

        $article->update($dados);

        return redirect()->route('articles.index');
    }

    public function destroy(Article $article): RedirectResponse
    {
        Gate::authorize('artigos.eliminar');

        if ($article->photo_path && Storage::exists($article->photo_path)) {
            Storage::delete($article->photo_path);
        }

        $article->delete();

        return back();
    }

    public function photo(Article $article): StreamedResponse
    {
        Gate::authorize('artigos.ler');

        if (!$article->photo_path || !Storage::exists($article->photo_path)) {
            abort(404);
        }

        return Storage::response($article->photo_path);
    }
}