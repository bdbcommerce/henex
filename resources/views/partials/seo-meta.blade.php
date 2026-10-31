<!-- Global static meta -->
<meta name="keywords" content="barcode scanner, HENEX, Uzbekistan, shtrix-kod, сканер, бизнес">
<meta name="robots" content="index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1">
<meta name="referrer" content="strict-origin-when-cross-origin">
<meta name="theme-color" content="#E63329">

<!-- Note: Hreflang tags omitted for cookie-based localization -->

@if(settings('google_verification'))
<meta name="google-site-verification" content="{{ settings('google_verification') }}">
@endif

@if(settings('yandex_verification'))
<meta name="yandex-verification" content="{{ settings('yandex_verification') }}">
@endif

@if($ga4 = settings('analytics_ga4_id'))
<script async src="https://www.googletagmanager.com/gtag/js?id={{ $ga4 }}"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());
  gtag('config', '{{ $ga4 }}', { page_path: '{{ request()->getPathInfo() }}' });
</script>
@endif

@if($ym = settings('analytics_yandex_id'))
<script type="text/javascript">
  (function(m,e,t,r,i,k,a){m[i]=m[i]||function(){(m[i].a=m[i].a||[]).push(arguments)};
  m[i].l=1*new Date();k=e.createElement(t),a=e.getElementsByTagName(t)[0],k.async=1,k.src=r,a.parentNode.insertBefore(k,a)})
  (window, document, "script", "https://mc.yandex.ru/metrika/tag.js", "ym");
  ym({{ $ym }}, "init", { clickmap:true, trackLinks:true, accurateTrackBounce:true, webvisor:true, ecommerce:"dataLayer" });
</script>
<noscript><div><img src="https://mc.yandex.ru/watch/{{ $ym }}" style="position:absolute; left:-9999px;" alt=""></div></noscript>
@endif

<!-- Structured Data: Organization -->
<script type="application/ld+json">
{!! json_encode([
    '@context'     => 'https://schema.org',
    '@type'        => 'Organization',
    'name'         => 'HENEX Uzbekistan',
    'url'          => url('/'),
    'logo'         => asset('images/henex-logo.png'),
    'description'  => 'Official distributor of HENEX barcode scanners in Uzbekistan',
    'sameAs'       => array_filter([
        settings('social_instagram'),
        settings('social_youtube'),
        settings('social_telegram'),
    ]),
    'contactPoint' => [
        '@type'       => 'ContactPoint',
        'telephone'   => settings('site_phone', '+998-90-000-0000'),
        'contactType' => 'Sales',
    ],
    'address' => [
        '@type'           => 'PostalAddress',
        'addressCountry'  => 'UZ',
        'addressLocality' => 'Tashkent',
        'streetAddress'   => settings('address_en', ''),
    ],
]) !!}
</script>
