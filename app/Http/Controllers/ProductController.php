<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(): View
    {
        $categories = Category::where('is_active', true)
            ->whereNull('parent_id')
            ->with('children')
            ->orderBy('sort_order')
            ->get();

        return view('pages.products.index', compact('categories'));
    }

    public function show(Product $product): View
    {
        abort_if(! $product->is_active, 404);

        $product->load([
            'categories',
            'specifications' => fn ($q) => $q->orderBy('sort_order'),
            'media',
        ]);

        $relatedProducts = Product::where('is_active', true)
            ->whereHas('categories', fn ($q) =>
                $q->whereIn('categories.id', $product->categories->pluck('id'))
            )
            ->where('id', '!=', $product->id)
            ->with('media')
            ->limit(4)
            ->get();

        return view('pages.products.show', compact('product', 'relatedProducts'));
    }
}
