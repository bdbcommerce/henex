<?php

namespace App\Http\Controllers;

use App\Models\Inquiry;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ContactController extends Controller
{
    public function index(): View
    {
        $products = cache()->remember('products_for_select', 600, fn () =>
            Product::where('is_active', true)
                ->orderBy('sort_order')
                ->get(['id', 'name'])
        );

        return view('pages.contact', compact('products'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'       => 'required|string|max:120',
            'company'    => 'nullable|string|max:120',
            'phone'      => 'required|string|max:50',
            'email'      => 'nullable|email|max:120',
            'product_id' => 'nullable|exists:products,id',
            'message'    => 'nullable|string|max:5000',
        ]);

        Inquiry::create([
            ...$validated,
            'locale' => app()->getLocale(),
        ]);

        return back()->with('success', __('site.contact.sent'));
    }
}
