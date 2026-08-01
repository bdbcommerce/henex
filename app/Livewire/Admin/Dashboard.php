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
        $newProductsThisWeek = Product::where('created_at', '>=', now()->subDays(7))->count();
        $newArticlesThisMonth = Article::where('created_at', '>=', now()->startOfMonth())->count();
        $unreadInquiries = Inquiry::where('is_read', false)->count();
        $regionsCovered = Reseller::where('is_active', true)->distinct('region_id')->count('region_id');

        $stats = [
            [
                'label' => 'Products',
                'value' => Product::count(),
                'icon' => 'products',
                'change' => $newProductsThisWeek > 0 ? "+{$newProductsThisWeek} this week" : 'No new products this week',
                'color' => 'blue'
            ],
            [
                'label' => 'Categories',
                'value' => Category::count(),
                'icon' => 'categories',
                'change' => Category::where('is_active', true)->count() . ' active',
                'color' => 'purple'
            ],
            [
                'label' => 'New Inquiries',
                'value' => $unreadInquiries,
                'icon' => 'inquiries',
                'change' => $unreadInquiries > 0 ? 'Needs attention' : 'All caught up',
                'color' => 'red'
            ],
            [
                'label' => 'Active Resellers',
                'value' => Reseller::where('is_active', true)->count(),
                'icon' => 'resellers',
                'change' => $regionsCovered > 0 ? "Across {$regionsCovered} region" . ($regionsCovered === 1 ? '' : 's') : 'No coverage yet',
                'color' => 'green'
            ],
            [
                'label' => 'Published Articles',
                'value' => Article::where('is_published', true)->count(),
                'icon' => 'articles',
                'change' => $newArticlesThisMonth > 0 ? "+{$newArticlesThisMonth} this month" : 'No new articles this month',
                'color' => 'yellow'
            ],
            [
                'label' => 'Admin Users',
                'value' => User::count(),
                'icon' => 'users',
                'change' => User::role('super_admin')->count() . ' super admin(s)',
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
