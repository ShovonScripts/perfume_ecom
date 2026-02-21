<div class="space-y-8">
    <!-- Categories -->
    <div>
        <h3 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider mb-4">Categories</h3>
        <div class="space-y-2">
            @foreach($categories as $category)
            <div class="flex items-center">
                <input id="cat-{{ $category->id }}" 
                       type="checkbox" 
                       value="{{ $category->slug }}"
                       {{ in_array($category->slug, explode(',', request('category'))) ? 'checked' : '' }}
                       onchange="updateFilter('category', this.value)"
                       class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 bg-gray-100 dark:bg-gray-800 dark:border-gray-700">
                <label for="cat-{{ $category->id }}" class="ml-3 text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white cursor-pointer select-none">
                    {{ $category->name }} <span class="text-xs text-gray-400">({{ $category->products_count }})</span>
                </label>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Price Range -->
    <div x-data="{ min: {{ request('min_price', 0) }}, max: {{ request('max_price', 50000) }} }">
        <h3 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider mb-4">Price</h3>
        <div class="flex items-center gap-4 mb-4">
            <input type="number" x-model="min" class="w-20 rounded border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-sm py-1 px-2">
            <span class="text-gray-400">-</span>
            <input type="number" x-model="max" class="w-20 rounded border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-sm py-1 px-2">
        </div>
        <button @click="updatePriceFilter(min, max)" class="w-full bg-gray-200 dark:bg-gray-800 text-gray-900 dark:text-white text-xs font-bold py-2 rounded hover:bg-gray-300 dark:hover:bg-gray-700 transition">
            Apply
        </button>
    </div>

    <!-- Brands -->
    <div>
        <h3 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider mb-4">Brands</h3>
        <div class="space-y-2 max-h-60 overflow-y-auto pr-2 scrollbar-thin scrollbar-thumb-gray-200 dark:scrollbar-thumb-gray-700">
            @foreach($brands as $brand)
            <div class="flex items-center">
                <input id="brand-{{ $brand->id }}" 
                       type="checkbox" 
                       value="{{ $brand->slug }}"
                       {{ in_array($brand->slug, explode(',', request('brand'))) ? 'checked' : '' }}
                       onchange="updateFilter('brand', this.value)"
                       class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 bg-gray-100 dark:bg-gray-800 dark:border-gray-700">
                <label for="brand-{{ $brand->id }}" class="ml-3 text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white cursor-pointer select-none">
                    {{ $brand->name }}
                </label>
            </div>
            @endforeach
        </div>
    </div>
</div>

<script>
    function updateFilter(key, value) {
        const url = new URL(window.location.href);
        const params = new URLSearchParams(url.search);
        let current = params.get(key) ? params.get(key).split(',') : [];
        
        if (current.includes(value)) {
            current = current.filter(item => item !== value);
        } else {
            current.push(value);
        }

        if (current.length > 0) {
            params.set(key, current.join(','));
        } else {
            params.delete(key);
        }
        
        // Reset pagination when filtering
        params.delete('page');

        window.location.href = `${url.pathname}?${params.toString()}`;
    }

    function updatePriceFilter(min, max) {
        const url = new URL(window.location.href);
        const params = new URLSearchParams(url.search);
        params.set('min_price', min);
        params.set('max_price', max);
        params.delete('page');
        window.location.href = `${url.pathname}?${params.toString()}`;
    }
</script>
