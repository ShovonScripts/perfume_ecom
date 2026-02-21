@extends('admin.layouts.app')
@section('title', 'Products')

@section('content')
<div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-xl">
    <div class="p-6 text-gray-900 dark:text-gray-100">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">Products</h1>
            <a href="{{ route('admin.products.create') }}"
               class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg transition">
                Add New Product
            </a>
        </div>

        @if($products->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full border-collapse">
                    <thead>
                        <tr class="bg-gray-100 dark:bg-gray-700">
                            <th class="border dark:border-gray-600 px-4 py-3 text-left">ID</th>
                            <th class="border dark:border-gray-600 px-4 py-3 text-left">Image</th>
                            <th class="border dark:border-gray-600 px-4 py-3 text-left">Name</th>
                            <th class="border dark:border-gray-600 px-4 py-3 text-left">Brand</th>
                            <th class="border dark:border-gray-600 px-4 py-3 text-left">Category</th>
                            <th class="border dark:border-gray-600 px-4 py-3 text-left">Status</th>
                            <th class="border dark:border-gray-600 px-4 py-3 text-left">Created</th>
                            <th class="border dark:border-gray-600 px-4 py-3 text-left">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($products as $product)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td class="border dark:border-gray-600 px-4 py-3">{{ $product->id }}</td>
                                <td class="border dark:border-gray-600 px-4 py-3">
                                    @if($product->thumbnail_image)
                                        <img src="{{ asset('storage/'.$product->thumbnail_image) }}"
                                             alt="{{ $product->name }}" class="w-12 h-12 object-cover rounded-lg">
                                    @else
                                        <div class="w-12 h-12 bg-gray-200 dark:bg-gray-600 rounded-lg flex items-center justify-center">
                                            <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                        </div>
                                    @endif
                                </td>
                                <td class="border dark:border-gray-600 px-4 py-3 font-semibold">{{ $product->name }}</td>
                                <td class="border dark:border-gray-600 px-4 py-3">
                                    <span class="text-xs bg-blue-200 dark:bg-blue-800 px-2 py-1 rounded">{{ $product->brand->name }}</span>
                                </td>
                                <td class="border dark:border-gray-600 px-4 py-3">
                                    <span class="text-xs bg-purple-200 dark:bg-purple-800 px-2 py-1 rounded">{{ $product->category->name }}</span>
                                </td>
                                <td class="border dark:border-gray-600 px-4 py-3">
                                    <span class="px-2 py-1 rounded text-xs {{ $product->is_active ? 'bg-green-200 text-green-800' : 'bg-red-200 text-red-800' }}">
                                        {{ $product->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                    @if($product->is_active && $product->variants->where('is_active', true)->isEmpty())
                                        <span class="block mt-1 text-xs bg-orange-100 text-orange-800 px-2 py-1 rounded font-bold">
                                            No Active Variants
                                        </span>
                                    @endif
                                </td>
                                <td class="border dark:border-gray-600 px-4 py-3 text-sm">{{ $product->created_at->format('M d, Y') }}</td>
                                <td class="border dark:border-gray-600 px-4 py-3">
                                    <div class="flex gap-2">
                                        <a href="{{ route('admin.products.edit', $product) }}"
                                           class="bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-1 rounded text-sm transition">Edit</a>
                                        <form action="{{ route('admin.products.destroy', $product) }}" method="POST"
                                              onsubmit="return confirm('Delete this product?');" class="inline">
                                            @csrf @method('DELETE')
                                            <button class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-sm transition">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $products->links() }}</div>
        @else
            <div class="text-center py-8 text-gray-500"><p>No products found. Create your first product!</p></div>
        @endif
    </div>
</div>
@endsection
