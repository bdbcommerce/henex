<?php

namespace App\Helpers;

class SeoHelper
{
    public static function setMetaTags(
        string $title,
        string $description,
        ?string $image = null,
        ?string $url = null,
        ?string $type = 'website'
    ): void {
        $locale = app()->getLocale();

        // Basic Meta Tags
        view()->share([
            'meta_title' => $title,
            'meta_description' => $description,
            'meta_image' => $image ?? asset('images/henex-og.jpg'),
            'meta_url' => $url ?? url()->current(),
            'meta_type' => $type,
        ]);
    }

    public static function jsonLdOrganization(): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'Organization',
            'name' => 'HENEX Uzbekistan',
            'url' => url('/'),
            'logo' => asset('images/henex-logo.png'),
            'description' => 'Official distributor of HENEX barcode scanners in Uzbekistan',
            'telephone' => '+998-90-123-4567',
            'email' => 'info@henex.uz',
            'address' => [
                '@type' => 'PostalAddress',
                'addressCountry' => 'UZ',
                'addressLocality' => 'Tashkent',
            ],
            'sameAs' => [
                'https://www.facebook.com/henex.uz',
                'https://www.instagram.com/henex.uz',
                'https://t.me/henex_uz',
            ],
        ];
    }

    public static function jsonLdProduct($product): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'Product',
            'name' => $product->getTranslation('name', app()->getLocale()),
            'description' => $product->getTranslation('short_description', app()->getLocale()),
            'sku' => $product->sku,
            'image' => $product->getFirstMediaUrl('gallery') ?? asset('images/placeholder.jpg'),
            'brand' => [
                '@type' => 'Brand',
                'name' => 'HENEX',
            ],
            'offers' => [
                '@type' => 'Offer',
                'availability' => 'https://schema.org/InStoreOnly',
                'priceCurrency' => 'USD',
                'seller' => [
                    '@type' => 'Organization',
                    'name' => 'HENEX Uzbekistan',
                    'url' => url('/'),
                ],
            ],
        ];
    }

    public static function jsonLdArticle($article): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'NewsArticle',
            'headline' => $article->getTranslation('title', app()->getLocale()),
            'description' => $article->getTranslation('excerpt', app()->getLocale()),
            'image' => $article->getFirstMediaUrl('cover') ?? asset('images/placeholder.jpg'),
            'datePublished' => $article->published_at?->toIso8601String(),
            'dateModified' => $article->updated_at->toIso8601String(),
            'author' => [
                '@type' => 'Person',
                'name' => $article->user?->name ?? 'HENEX Team',
            ],
            'publisher' => [
                '@type' => 'Organization',
                'name' => 'HENEX Uzbekistan',
                'logo' => [
                    '@type' => 'ImageObject',
                    'url' => asset('images/henex-logo.png'),
                ],
            ],
        ];
    }

    public static function jsonLdLocalBusiness($reseller): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'LocalBusiness',
            'name' => $reseller->name,
            'telephone' => $reseller->phone,
            'email' => $reseller->email,
            'url' => $reseller->website,
            'address' => [
                '@type' => 'PostalAddress',
                'streetAddress' => $reseller->getTranslation('address', app()->getLocale()),
                'addressLocality' => $reseller->region->getTranslation('name', app()->getLocale()),
                'addressCountry' => 'UZ',
            ],
            'geo' => $reseller->latitude && $reseller->longitude ? [
                '@type' => 'GeoCoordinates',
                'latitude' => $reseller->latitude,
                'longitude' => $reseller->longitude,
            ] : null,
        ];
    }

    public static function getCanonicalUrl(?string $url = null): string
    {
        return $url ?? url()->current();
    }

    public static function getHreflangs(): array
    {
        $locales = ['uz', 'ru', 'en'];
        $hreflangs = [];

        foreach ($locales as $locale) {
            $hreflangs[$locale] = LaravelLocalization::getLocalizedURL($locale);
        }

        return $hreflangs;
    }
}
