<div>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">

<section wire:ignore class="banner relative overflow-hidden bg-black">

    <div class="swiper-container swiper-banner">
        <div class="swiper-wrapper">

        @forelse($slides as $slide)
        @if(!($slide instanceof \App\Models\Slide)) @continue @endif

            <div class="swiper-slide">
                @if($slide->link)
                    <a href="{{ $slide->link }}">
                @endif
                    @if($slide->getFirstMediaUrl('slide'))
                        <div class="aspect-[21/9] w-full overflow-hidden">
                            <img src="{{ $slide->getFirstMediaUrl('slide') }}"
                                 alt="{{ $slide->getTranslation('title', app()->getLocale()) }}"
                                 class="w-full h-full object-cover block"
                                 {{ $loop->first ? 'fetchpriority="high"' : 'loading="lazy"' }}>
                        </div>
                    @endif
                @if($slide->link)
                    </a>
                @endif
            </div>

        @empty

            {{-- ── Exact copies of henex.cn homepage slides ── --}}

            <div class="swiper-slide">
                <a href="{{ route('products.index') }}">
                    <div class="aspect-[21/9] w-full overflow-hidden">
                        <img src="https://www.henex.cn/uploadfile/202602/c967dfa09766944.gif" alt="H310RC" class="w-full h-full object-cover block" fetchpriority="high">
                    </div>
                </a>
            </div>

            <div class="swiper-slide">
                <a href="{{ route('products.index') }}">
                    <div class="aspect-[21/9] w-full overflow-hidden">
                        <img src="https://www.henex.cn/uploadfile/202405/24800cd49e69.gif" alt="H8000" class="w-full h-full object-cover block" loading="lazy">
                    </div>
                </a>
            </div>

            <div class="swiper-slide">
                <a href="{{ route('products.index') }}">
                    <div class="aspect-[21/9] w-full overflow-hidden">
                        <img src="https://www.henex.cn/uploadfile/202409/1982a786db0bf60.gif" alt="HENEX" class="w-full h-full object-cover block" loading="lazy">
                    </div>
                </a>
            </div>

            <div class="swiper-slide">
                <a href="{{ route('products.index') }}">
                    <div class="aspect-[21/9] w-full overflow-hidden">
                        <img src="https://www.henex.cn/uploadfile/202405/1a4136843b5dfb6.gif" alt="H500" class="w-full h-full object-cover block" loading="lazy">
                    </div>
                </a>
            </div>

            <div class="swiper-slide">
                <a href="{{ route('products.index') }}">
                    <div class="aspect-[21/9] w-full overflow-hidden">
                        <img src="https://www.henex.cn/uploadfile/202405/4dad08ab53d52e7.gif" alt="H4" class="w-full h-full object-cover block" loading="lazy">
                    </div>
                </a>
            </div>

            <div class="swiper-slide">
                <a href="{{ route('products.index') }}">
                    <div class="aspect-[21/9] w-full overflow-hidden">
                        <img src="https://www.henex.cn/uploadfile/202405/aec5a79651d6b8c.gif" alt="H5" class="w-full h-full object-cover block" loading="lazy">
                    </div>
                </a>
            </div>

        @endforelse

        </div>

        <div class="swiper-pagination"></div>
        <div class="swiper-button-prev"></div>
        <div class="swiper-button-next"></div>
    </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js" defer></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    new Swiper('.swiper-banner', {
        loop: true,
        speed: 600,
        autoplay: { delay: 5000, disableOnInteraction: false, pauseOnMouseEnter: true },
        pagination: { el: '.swiper-pagination', clickable: true },
        navigation: { nextEl: '.swiper-button-next', prevEl: '.swiper-button-prev' },
    });
});
</script>
</div>
