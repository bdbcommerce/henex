<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Category;
use App\Models\Product;
use App\Models\Slide;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $slides = Slide::where('is_active', true)->orderBy('sort_order')->get();

        $categories = Category::where('is_active', true)
            ->whereNull('parent_id')
            ->orderBy('sort_order')
            ->limit(8)
            ->get();

        $featuredProducts = Product::where('is_featured', true)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->with(['categories', 'media'])
            ->limit(8)
            ->get();

        $latestArticles = Article::where('is_published', true)
            ->whereNotNull('published_at')
            ->orderByDesc('published_at')
            ->limit(3)
            ->get();

        return view('pages.home', compact('slides', 'categories', 'featuredProducts', 'latestArticles'));
    }
}
