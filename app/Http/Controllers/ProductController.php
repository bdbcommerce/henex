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

    public function category(string $slug): View
    {
        $category = Category::findBySlugOrRedirect($slug);   // old slugs 301 to the current URL
        abort_unless($category->is_active, 404);

        $categories = Category::where('is_active', true)
            ->whereNull('parent_id')
            ->with('children')
            ->orderBy('sort_order')
            ->get();

        return view('pages.products.index', compact('category', 'categories'));
    }

    public function show(Product $product): View
    {
        abort_if(! $product->is_active, 404);

        $product->load(['categories', 'media', 'attributeValues.attribute']);

        // Spec table rows from attributes: "Connectivity" → "USB cable, Bluetooth"
        $locale = app()->getLocale();
        $specs = $product->attributeValues
            ->filter(fn ($v) => $v->attribute->is_active)
            ->sortBy([fn ($a, $b) => $a->attribute->sort_order <=> $b->attribute->sort_order, fn ($a, $b) => $a->sort_order <=> $b->sort_order])
            ->groupBy('attribute_id')
            ->map(fn ($values) => [
                'key'   => $values->first()->attribute->getTranslation('name', $locale),
                'value' => $values->map(fn ($v) => $v->getTranslation('value', $locale))->join(', '),
            ])->values();

        $relatedProducts = Product::where('is_active', true)
            ->whereHas('categories', fn ($q) =>
                $q->whereIn('categories.id', $product->categories->pluck('id'))
            )
            ->where('id', '!=', $product->id)
            ->with('media')
            ->limit(4)
            ->get();

        return view('pages.products.show', compact('product', 'relatedProducts', 'specs'));
    }
}
