@extends('admin.layouts.app')
@section('title', 'Coupons')

@section('content')
<div class="space-y-5">

    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Coupons</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Manage discount codes and promotions</p>
        </div>
        <a href="{{ route('admin.coupons.create') }}"
           class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-xl text-sm font-medium transition shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Add Coupon
        </a>
    </div>

    <!-- Table -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border dark:border-gray-700 overflow-hidden">
        @if($coupons->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 dark:bg-gray-700/30 border-b dark:border-gray-700">
                        <th class="py-3 px-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Code</th>
                        <th class="py-3 px-4 text-center text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Type</th>
                        <th class="py-3 px-4 text-right text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Value</th>
                        <th class="py-3 px-4 text-right text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Min Subtotal</th>
                        <th class="py-3 px-4 text-center text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Validity</th>
                        <th class="py-3 px-4 text-center text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Status</th>
                        <th class="py-3 px-4 text-center text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($coupons as $coupon)
                    <tr class="border-b dark:border-gray-700 hover:bg-blue-50/50 dark:hover:bg-gray-700/50 transition">
                        <td class="py-3 px-4">
                            <span class="font-mono font-bold text-gray-900 dark:text-white bg-gray-100 dark:bg-gray-700 px-2.5 py-1 rounded-lg text-xs">
                                {{ $coupon->code }}
                            </span>
                        </td>
                        <td class="py-3 px-4 text-center">
                            <span class="px-2.5 py-1 rounded-full text-xs font-semibold
                                {{ $coupon->type === 'percent' ? 'bg-purple-100 text-purple-800 dark:bg-purple-900/50 dark:text-purple-300' : 'bg-blue-100 text-blue-800 dark:bg-blue-900/50 dark:text-blue-300' }}">
                                {{ ucfirst($coupon->type) }}
                            </span>
                        </td>
                        <td class="py-3 px-4 text-right font-semibold text-gray-900 dark:text-white">
                            {{ $coupon->type === 'percent' ? $coupon->value . '%' : '৳' . number_format($coupon->value) }}
                        </td>
                        <td class="py-3 px-4 text-right text-gray-500 dark:text-gray-400">
                            {{ $coupon->min_subtotal ? '৳' . number_format($coupon->min_subtotal) : '—' }}
                        </td>
                        <td class="py-3 px-4 text-center text-xs text-gray-500 dark:text-gray-400">
                            @if($coupon->starts_at || $coupon->ends_at)
                                <div>{{ $coupon->starts_at ? $coupon->starts_at->format('d M Y') : '—' }}</div>
                                <div class="text-gray-400">to</div>
                                <div>{{ $coupon->ends_at ? $coupon->ends_at->format('d M Y') : '∞' }}</div>
                            @else
                                <span class="text-gray-400">No limit</span>
                            @endif
                        </td>
                        <td class="py-3 px-4 text-center">
                            @if($coupon->is_active)
                                <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800 dark:bg-green-900/50 dark:text-green-300">Active</span>
                            @else
                                <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800 dark:bg-red-900/50 dark:text-red-300">Inactive</span>
                            @endif
                        </td>
                        <td class="py-3 px-4 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('admin.coupons.edit', $coupon) }}"
                                   class="bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 hover:bg-indigo-100 dark:hover:bg-indigo-900/50 px-3 py-1.5 rounded-lg text-xs font-medium transition">
                                    Edit
                                </a>
                                <form action="{{ route('admin.coupons.destroy', $coupon) }}" method="POST" class="inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" onclick="return confirm('Delete this coupon?')"
                                            class="bg-red-50 dark:bg-red-900/30 text-red-600 dark:text-red-400 hover:bg-red-100 dark:hover:bg-red-900/50 px-3 py-1.5 rounded-lg text-xs font-medium transition">
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="px-6 py-4 border-t dark:border-gray-700">
            {{ $coupons->links() }}
        </div>
        @else
        <div class="text-center py-16">
            <svg class="w-12 h-12 mx-auto text-gray-300 dark:text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/>
            </svg>
            <p class="text-gray-500 dark:text-gray-400 font-medium">No coupons yet</p>
            <p class="text-sm text-gray-400 dark:text-gray-500 mt-1">Create your first discount code to get started</p>
            <a href="{{ route('admin.coupons.create') }}" class="inline-block mt-4 bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2 rounded-xl text-sm font-medium transition">
                Create Coupon
            </a>
        </div>
        @endif
    </div>

</div>
@endsection
