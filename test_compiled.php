
<title><?php echo e($metaTitle ?? config('app.name', \App\Models\Setting::get('site_name', 'Perfume Store'))); ?></title>
<meta name="description" content="<?php echo e($metaDescription ?? 'Premium authentic perfumes in Bangladesh. Best prices, fast delivery.'); ?>">

<?php if(isset($category) && request()->has('page')): ?>
<link rel="canonical" href="<?php echo e(route('category.show', $category->slug)); ?>">
<?php else: ?>
<link rel="canonical" href="<?php echo e(url()->current()); ?>">
<?php endif; ?>


<meta property="og:title" content="<?php echo e($metaTitle ?? config('app.name')); ?>">
<meta property="og:description" content="<?php echo e($metaDescription ?? ''); ?>">
<meta property="og:url" content="<?php echo e(url()->current()); ?>">
<meta property="og:type" content="<?php echo e($ogType ?? 'website'); ?>">
<meta property="og:site_name" content="<?php echo e(config('app.name', \App\Models\Setting::get('site_name', 'Perfume Store'))); ?>">
<?php if(isset($metaImage)): ?>
<meta property="og:image" content="<?php echo e(asset('storage/'.$metaImage)); ?>">
<?php endif; ?>


<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="<?php echo e($metaTitle ?? config('app.name')); ?>">
<meta name="twitter:description" content="<?php echo e($metaDescription ?? ''); ?>">
<?php if(isset($metaImage)): ?>
<meta name="twitter:image" content="<?php echo e(asset('storage/'.$metaImage)); ?>">
<?php endif; ?>

<?php if(isset($product)): ?>

<script type="application/ld+json">
{
    "<?php $__contextArgs = [];
if (context()->has($__contextArgs[0])) :
if (isset($value)) { $__contextPrevious[] = $value; }
$value = context()->get($__contextArgs[0]); ?>": "https://schema.org/",
    "@type": "Product",
    "name": "<?php echo e($product->name); ?>",
    "image": "<?php echo e(asset('storage/'.$product->thumbnail_image)); ?>",
    "description": "<?php echo e(strip_tags($product->short_description)); ?>",
    "brand": {
        "@type": "Brand",
        "name": "<?php echo e($product->brand->name ?? ''); ?>"
    },
    "aggregateRating": {
        "@type": "AggregateRating",
        "ratingValue": "<?php echo e(number_format($product->reviews->avg('rating') ?: 5, 1)); ?>",
        "reviewCount": "<?php echo e(max($product->reviews->count(), 1)); ?>"
    },
    "offers": {
        "@type": "Offer",
        "url": "<?php echo e(url()->current()); ?>",
        "priceCurrency": "BDT",
        "price": "<?php echo e($product->variants->min('price') ?? collect()->min('price')); ?>",
        "availability": "https://schema.org/InStock"
    }
}
</script>


<script type="application/ld+json">
{
    "<?php $__contextArgs = [];
if (context()->has($__contextArgs[0])) :
if (isset($value)) { $__contextPrevious[] = $value; }
$value = context()->get($__contextArgs[0]); ?>": "https://schema.org",
    "@type": "BreadcrumbList",
    "itemListElement": [{
        "@type": "ListItem",
        "position": 1,
        "name": "Home",
        "item": "<?php echo e(url('/')); ?>"
    },{
        "@type": "ListItem",
        "position": 2,
        "name": "<?php echo e($product->category->name ?? ''); ?>",
        "item": "<?php echo e(route('category.show', $product->category->slug ?? '')); ?>"
    },{
        "@type": "ListItem",
        "position": 3,
        "name": "<?php echo e($product->name); ?>",
        "item": "<?php echo e(url()->current()); ?>"
    }]
}
</script>
<?php endif; ?>
