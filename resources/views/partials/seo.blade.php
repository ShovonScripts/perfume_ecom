{{-- Dynamic SEO Meta Tags --}}
<title>{{ $metaTitle ?? config('app.name', \App\Models\Setting::get('site_name', 'Perfume Store')) }}</title>
<meta name="description" content="{{ $metaDescription ?? 'Premium authentic perfumes in Bangladesh. Best prices, fast delivery.' }}">

@if(isset($category) && request()->has('page'))
<link rel="canonical" href="{{ route('category.show', $category->slug) }}">
@else
<link rel="canonical" href="{{ url()->current() }}">
@endif

{{-- Open Graph --}}
<meta property="og:title" content="{{ $metaTitle ?? config('app.name') }}">
<meta property="og:description" content="{{ $metaDescription ?? '' }}">
<meta property="og:url" content="{{ url()->current() }}">
<meta property="og:type" content="{{ $ogType ?? 'website' }}">
<meta property="og:site_name" content="{{ config('app.name', \App\Models\Setting::get('site_name', 'Perfume Store')) }}">
@if(isset($metaImage))
<meta property="og:image" content="{{ asset('storage/'.$metaImage) }}">
@endif

{{-- Twitter Card --}}
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $metaTitle ?? config('app.name') }}">
<meta name="twitter:description" content="{{ $metaDescription ?? '' }}">
@if(isset($metaImage))
<meta name="twitter:image" content="{{ asset('storage/'.$metaImage) }}">
@endif

@if(isset($product))
{{-- Product Structured Data --}}
<script type="application/ld+json">
{
    "@@context": "https://schema.org/",
    "@type": "Product",
    "name": "{{ $product->name }}",
    "image": "{{ asset('storage/'.$product->thumbnail_image) }}",
    "description": "{{ strip_tags($product->short_description) }}",
    "brand": {
        "@type": "Brand",
        "name": "{{ $product->brand->name ?? '' }}"
    },
    "aggregateRating": {
        "@type": "AggregateRating",
        "ratingValue": "{{ number_format($product->reviews->avg('rating') ?: 5, 1) }}",
        "reviewCount": "{{ max($product->reviews->count(), 1) }}"
    },
    "offers": {
        "@type": "Offer",
        "url": "{{ url()->current() }}",
        "priceCurrency": "BDT",
        "price": "{{ $product->variants->min('price') ?? collect()->min('price') }}",
        "availability": "https://schema.org/InStock"
    }
}
</script>

{{-- Breadcrumb Schema --}}
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@type": "BreadcrumbList",
    "itemListElement": [{
        "@type": "ListItem",
        "position": 1,
        "name": "Home",
        "item": "{{ url('/') }}"
    },{
        "@type": "ListItem",
        "position": 2,
        "name": "{{ $product->category->name ?? '' }}",
        "item": "{{ route('category.show', $product->category->slug ?? '') }}"
    },{
        "@type": "ListItem",
        "position": 3,
        "name": "{{ $product->name }}",
        "item": "{{ url()->current() }}"
    }]
}
</script>
@endif
