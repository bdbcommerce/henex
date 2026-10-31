<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ArticleController extends Controller
{
    public function index(Request $request): View
    {
        $query = Article::where('is_published', true)
            ->whereNotNull('published_at')
            ->orderByDesc('published_at');

        if ($type = $request->query('type')) {
            $query->where('type', $type);
        }

        $articles = $query->paginate(10)->withQueryString();

        return view('pages.news.index', compact('articles'));
    }

    public function show(Article $article): View
    {
        abort_if(! $article->is_published, 404);

        $recentArticles = Article::where('is_published', true)
            ->whereNotNull('published_at')
            ->where('id', '!=', $article->id)
            ->orderByDesc('published_at')
            ->limit(5)
            ->get();

        $categories = Category::where('is_active', true)
            ->whereNull('parent_id')
            ->orderBy('sort_order')
            ->get();

        return view('pages.news.show', compact('article', 'recentArticles', 'categories'));
    }
}
