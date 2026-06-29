<!-- SEO Meta Tags -->
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="description" content="{{ $meta_description ?? 'Official HENEX barcode scanner distributor in Uzbekistan. Official warranty, technical support, fast delivery.' }}">
<meta name="keywords" content="barcode scanner, HENEX, Uzbekistan, shtrix-kod, сканер, бизнес">
<meta name="robots" content="index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1">
<meta name="referrer" content="strict-origin-when-cross-origin">
<meta name="theme-color" content="#E63329">

<!-- Open Graph / Facebook -->
<meta property="og:type" content="{{ $meta_type ?? 'website' }}">
<meta property="og:url" content="{{ $meta_url ?? url()->current() }}">
<meta property="og:title" content="{{ $meta_title ?? 'HENEX Uzbekistan' }}">
<meta property="og:description" content="{{ $meta_description ?? 'Official HENEX barcode scanner distributor in Uzbekistan.' }}">
<meta property="og:image" content="{{ $meta_image ?? asset('images/henex-og.jpg') }}">
<meta property="og:image:width" content="1200">
<meta property="og:image:height" content="630">
<meta property="og:site_name" content="HENEX Uzbekistan">

<!-- Twitter -->
<meta property="twitter:card" content="summary_large_image">
<meta property="twitter:url" content="{{ $meta_url ?? url()->current() }}">
<meta property="twitter:title" content="{{ $meta_title ?? 'HENEX Uzbekistan' }}">
<meta property="twitter:description" content="{{ $meta_description ?? 'Official HENEX barcode scanner distributor in Uzbekistan.' }}">
<meta property="twitter:image" content="{{ $meta_image ?? asset('images/henex-og.jpg') }}">

<!-- Canonical URL -->
<link rel="canonical" href="{{ $meta_url ?? url()->current() }}">

<!-- Note: Hreflang tags omitted for cookie-based localization -->
<!-- All language versions use the same URL. Language is detected from 'locale' cookie. -->

<!-- Google Search Console Verification -->
<meta name="google-site-verification" content="YOUR_GOOGLE_VERIFICATION_CODE_HERE">

<!-- Yandex Search Console Verification -->
<meta name="yandex-verification" content="YOUR_YANDEX_VERIFICATION_CODE_HERE">

<!-- Google Analytics 4 -->
<script async src="https://www.googletagmanager.com/gtag/js?id=YOUR_GA4_ID_HERE"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());
  gtag('config', 'YOUR_GA4_ID_HERE', {
    'page_path': '{{ request()->getPathInfo() }}',
    'allow_google_signals': true,
    'allow_ad_personalization_signals': true
  });
</script>

<!-- Yandex Metrica -->
<script type="text/javascript">
  (function(m,e,t,r,i,k,a){m[i]=m[i]||function(){(m[i].a=m[i].a||[]).push(arguments)};
  m[i].l=1*new Date();k=e.createElement(t),a=e.getElementsByTagName(t)[0],k.async=1,k.src=r,a.parentNode.insertBefore(k,a)})
  (window, document, "script", "https://mc.yandex.ru/metrika/tag.js", "ym");

  ym(YOUR_YANDEX_METRICA_ID, "init", {
    clickmap:true,
    trackLinks:true,
    accurateTrackBounce:true,
    webvisor:true,
    ecommerce:"dataLayer"
  });
</script>
<noscript><div><img src="https://mc.yandex.ru/watch/YOUR_YANDEX_METRICA_ID" style="position:absolute; left:-9999px;" alt=""></div></noscript>

<!-- Structured Data: Organization -->
<script type="application/ld+json">
{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'Organization',
    'name' => 'HENEX Uzbekistan',
    'url' => url('/'),
    'logo' => asset('images/henex-logo.png'),
    'description' => 'Official distributor of HENEX barcode scanners in Uzbekistan',
    'sameAs' => [
        'https://www.facebook.com/henex.uz',
        'https://www.instagram.com/henex.uz',
        'https://t.me/henex_uz'
    ],
    'contactPoint' => [
        '@type' => 'ContactPoint',
        'telephone' => '+998-90-123-4567',
        'contactType' => 'Sales'
    ],
    'address' => [
        '@type' => 'PostalAddress',
        'addressCountry' => 'UZ',
        'addressLocality' => 'Tashkent'
    ]
]) !!}
</script>
