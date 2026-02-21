@extends('admin.layouts.app')
@section('title', 'Combos')

@section('content')
<div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-xl">
    <div class="p-6 text-gray-900 dark:text-gray-100">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">Combo Deals</h1>
            <a href="{{ route('admin.combos.create') }}"
               class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg transition">
                Add New Combo
            </a>
        </div>

        @if($combos->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full border-collapse">
                    <thead>
                        <tr class="bg-gray-100 dark:bg-gray-700">
                            <th class="border dark:border-gray-600 px-4 py-3 text-left">ID</th>
                            <th class="border dark:border-gray-600 px-4 py-3 text-left">Image</th>
                            <th class="border dark:border-gray-600 px-4 py-3 text-left">Name</th>
                            <th class="border dark:border-gray-600 px-4 py-3 text-left">Price</th>
                            <th class="border dark:border-gray-600 px-4 py-3 text-left">Status</th>
                            <th class="border dark:border-gray-600 px-4 py-3 text-left">Sort</th>
                            <th class="border dark:border-gray-600 px-4 py-3 text-left">Created</th>
                            <th class="border dark:border-gray-600 px-4 py-3 text-left">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($combos as $combo)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td class="border dark:border-gray-600 px-4 py-3">{{ $combo->id }}</td>
                                <td class="border dark:border-gray-600 px-4 py-3">
                                    @if($combo->image)
                                        <img src="{{ asset('storage/'.$combo->image) }}" class="w-12 h-12 object-cover rounded-lg">
                                    @endif
                                </td>
                                <td class="border dark:border-gray-600 px-4 py-3 font-semibold">{{ $combo->name }}</td>
                                <td class="border dark:border-gray-600 px-4 py-3">৳{{ number_format($combo->combo_price, 2) }}</td>
                                <td class="border dark:border-gray-600 px-4 py-3">
                                    <span class="px-2 py-1 rounded text-xs {{ $combo->is_active ? 'bg-green-200 text-green-800' : 'bg-red-200 text-red-800' }}">
                                        {{ $combo->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td class="border dark:border-gray-600 px-4 py-3">{{ $combo->sort_order }}</td>
                                <td class="border dark:border-gray-600 px-4 py-3 text-sm">{{ $combo->created_at->format('M d, Y') }}</td>
                                <td class="border dark:border-gray-600 px-4 py-3">
                                    <div class="flex gap-2">
                                        <a href="{{ route('admin.combos.edit', $combo) }}"
                                           class="bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-1 rounded text-sm transition">Edit</a>
                                        <form action="{{ route('admin.combos.destroy', $combo) }}" method="POST"
                                              onsubmit="return confirm('Delete this combo?');" class="inline">
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
        @else
            <div class="text-center py-8 text-gray-500"><p>No combos found. Create your first combo!</p></div>
        @endif
    </div>
</div>
@endsection
