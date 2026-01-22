@if($seoData)
    {{-- Basic Meta Tags --}}
    @if($seoData->title)
        <title>{{ $seoData->title }}</title>
        <meta name="title" content="{{ $seoData->title }}">
    @endif

    @if($seoData->description)
        <meta name="description" content="{{ $seoData->description }}">
    @endif

    @if($seoData->focus_keyword)
        <meta name="keywords" content="{{ $seoData->focus_keyword }}">
    @endif

    @if($seoData->canonical_url)
        <link rel="canonical" href="{{ $seoData->canonical_url }}">
    @endif

    @if($seoData->robots_directive)
        <meta name="robots" content="{{ $seoData->robots_directive }}">
    @endif

    {{-- Open Graph Tags --}}
    @if($seoData->getOgTitle())
        <meta property="og:title" content="{{ $seoData->getOgTitle() }}">
    @endif

    @if($seoData->getOgDescription())
        <meta property="og:description" content="{{ $seoData->getOgDescription() }}">
    @endif

    @if($seoData->getOgType())
        <meta property="og:type" content="{{ $seoData->getOgType() }}">
    @endif

    @if($seoData->getOgImage())
        <meta property="og:image" content="{{ $seoData->getOgImage() }}">
    @endif

    @if($seoData->canonical_url)
        <meta property="og:url" content="{{ $seoData->canonical_url }}">
    @endif

    @if($siteName = $seoData->open_graph['site_name'] ?? null)
        <meta property="og:site_name" content="{{ $siteName }}">
    @endif

    @if($locale = $seoData->open_graph['locale'] ?? null)
        <meta property="og:locale" content="{{ $locale }}">
    @endif

    {{-- Twitter Card Tags --}}
    @if($seoData->getTwitterCardType())
        <meta name="twitter:card" content="{{ $seoData->getTwitterCardType() }}">
    @endif

    @if($seoData->getTwitterTitle())
        <meta name="twitter:title" content="{{ $seoData->getTwitterTitle() }}">
    @endif

    @if($seoData->getTwitterDescription())
        <meta name="twitter:description" content="{{ $seoData->getTwitterDescription() }}">
    @endif

    @if($seoData->getTwitterImage())
        <meta name="twitter:image" content="{{ $seoData->getTwitterImage() }}">
    @endif

    @if($site = $seoData->twitter_card['site'] ?? null)
        <meta name="twitter:site" content="@{{ ltrim($site, '@') }}">
    @endif

    @if($creator = $seoData->twitter_card['creator'] ?? null)
        <meta name="twitter:creator" content="@{{ ltrim($creator, '@') }}">
    @endif

    {{-- JSON-LD Structured Data --}}
    @if($jsonLd)
        <script type="application/ld+json">
            {!! $jsonLd !!}
        </script>
    @endif
@endif
