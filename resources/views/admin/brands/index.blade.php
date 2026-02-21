@extends('admin.layouts.app')
@section('title', 'Brands')

@section('content')
<div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-xl">
    <div class="p-6 text-gray-900 dark:text-gray-100">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">Brands</h1>
            <a href="{{ route('admin.brands.create') }}"
               class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg transition">
                Add New Brand
            </a>
        </div>

        @if($brands->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full border-collapse">
                    <thead>
                        <tr class="bg-gray-100 dark:bg-gray-700">
                            <th class="border dark:border-gray-600 px-4 py-3 text-left">ID</th>
                            <th class="border dark:border-gray-600 px-4 py-3 text-left">Name</th>
                            <th class="border dark:border-gray-600 px-4 py-3 text-left">Slug</th>
                            <th class="border dark:border-gray-600 px-4 py-3 text-left">Status</th>
                            <th class="border dark:border-gray-600 px-4 py-3 text-left">Created</th>
                            <th class="border dark:border-gray-600 px-4 py-3 text-left">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($brands as $brand)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td class="border dark:border-gray-600 px-4 py-3">{{ $brand->id }}</td>
                                <td class="border dark:border-gray-600 px-4 py-3 font-semibold">{{ $brand->name }}</td>
                                <td class="border dark:border-gray-600 px-4 py-3 text-sm text-gray-600 dark:text-gray-400">{{ $brand->slug }}</td>
                                <td class="border dark:border-gray-600 px-4 py-3">
                                    <span class="px-2 py-1 rounded text-xs {{ $brand->is_active ? 'bg-green-200 text-green-800' : 'bg-red-200 text-red-800' }}">
                                        {{ $brand->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td class="border dark:border-gray-600 px-4 py-3 text-sm">{{ $brand->created_at->format('M d, Y') }}</td>
                                <td class="border dark:border-gray-600 px-4 py-3">
                                    <div class="flex gap-2">
                                        <a href="{{ route('admin.brands.edit', $brand) }}"
                                           class="bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-1 rounded text-sm transition">Edit</a>
                                        <form action="{{ route('admin.brands.destroy', $brand) }}" method="POST"
                                              onsubmit="return confirm('Delete this brand?');" class="inline">
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
            <div class="mt-4">{{ $brands->links() }}</div>
        @else
            <div class="text-center py-8 text-gray-500"><p>No brands found. Create your first brand!</p></div>
        @endif
    </div>
</div>
@endsection
