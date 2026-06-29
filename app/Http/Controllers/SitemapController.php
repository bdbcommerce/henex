<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Article;
use App\Models\Category;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;

class SitemapController extends Controller
{
    public function index()
    {
        $sitemap = Sitemap::create();

        $locales = ['uz', 'ru', 'en'];

        // Homepage for each locale
        foreach ($locales as $locale) {
            $sitemap->add(
                Url::create(LaravelLocalization::getLocalizedURL($locale, '/'))
                    ->setLastModificationDate(now())
                    ->setChangeFrequency('weekly')
                    ->setPriority(1.0)
            );
        }

        // Products for each locale
        foreach ($locales as $locale) {
            // Product index
            $sitemap->add(
                Url::create(LaravelLocalization::getLocalizedURL($locale, '/products'))
                    ->setLastModificationDate(now())
                    ->setChangeFrequency('daily')
                    ->setPriority(0.8)
            );

            // Individual products
            foreach (Product::where('is_active', true)->get() as $product) {
                $sitemap->add(
                    Url::create(LaravelLocalization::getLocalizedURL($locale, '/products/' . $product->slug))
                        ->setLastModificationDate($product->updated_at)
                        ->setChangeFrequency('weekly')
                        ->setPriority(0.7)
                );
            }

            // Categories
            $sitemap->add(
                Url::create(LaravelLocalization::getLocalizedURL($locale, '/products'))
                    ->setLastModificationDate(now())
                    ->setChangeFrequency('daily')
                    ->setPriority(0.8)
            );

            // Articles
            $sitemap->add(
                Url::create(LaravelLocalization::getLocalizedURL($locale, '/news'))
                    ->setLastModificationDate(now())
                    ->setChangeFrequency('daily')
                    ->setPriority(0.7)
            );

            foreach (Article::where('is_published', true)->get() as $article) {
                $sitemap->add(
                    Url::create(LaravelLocalization::getLocalizedURL($locale, '/news/' . $article->slug))
                        ->setLastModificationDate($article->updated_at)
                        ->setChangeFrequency('weekly')
                        ->setPriority(0.6)
                );
            }

            // Static pages
            foreach (['/where-to-buy' => 0.7, '/about' => 0.5, '/contact' => 0.5] as $path => $priority) {
                $sitemap->add(
                    Url::create(LaravelLocalization::getLocalizedURL($locale, $path))
                        ->setLastModificationDate(now())
                        ->setChangeFrequency('monthly')
                        ->setPriority($priority)
                );
            }
        }

        return response($sitemap->toXml(), 200, [
            'Content-Type' => 'application/xml; charset=utf-8',
        ]);
    }
}

