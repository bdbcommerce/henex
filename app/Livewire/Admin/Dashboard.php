<?php

namespace App\Livewire\Admin;

use App\Models\Article;
use App\Models\Category;
use App\Models\Inquiry;
use App\Models\Product;
use App\Models\Reseller;
use App\Models\User;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Dashboard')]
class Dashboard extends Component
{
    public function render()
    {
        $stats = [
            [
                'label' => 'Products',
                'value' => Product::count(),
                'icon' => '📦',
                'change' => '+3 this week',
                'color' => 'blue'
            ],
            [
                'label' => 'Categories',
                'value' => Category::count(),
                'icon' => '🗂',
                'change' => 'No change',
                'color' => 'purple'
            ],
            [
                'label' => 'New Inquiries',
                'value' => Inquiry::where('is_read', false)->count(),
                'icon' => '💬',
                'change' => 'Needs attention',
                'color' => 'red'
            ],
            [
                'label' => 'Active Resellers',
                'value' => Reseller::where('is_active', true)->count(),
                'icon' => '🏪',
                'change' => 'Across 14 regions',
                'color' => 'green'
            ],
            [
                'label' => 'Published Articles',
                'value' => Article::where('is_published', true)->count(),
                'icon' => '📰',
                'change' => '+2 this month',
                'color' => 'yellow'
            ],
            [
                'label' => 'Admin Users',
                'value' => User::count(),
                'icon' => '👥',
                'change' => 'All active',
                'color' => 'gray'
            ],
        ];

        $recentInquiries = Inquiry::orderByDesc('created_at')
            ->with('product')
            ->limit(8)
            ->get();

        $recentProducts = Product::orderByDesc('created_at')
            ->select('id', 'slug', 'name', 'sku', 'is_active', 'created_at')
            ->limit(5)
            ->get();

        $recentArticles = Article::orderByDesc('created_at')
            ->select('id', 'slug', 'title', 'is_published', 'published_at')
            ->limit(5)
            ->get();

        return view('livewire.admin.dashboard', compact('stats', 'recentInquiries', 'recentProducts', 'recentArticles'))
            ->layout('layouts.admin');
    }
}
