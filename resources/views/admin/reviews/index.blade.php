@extends('admin.layouts.app')
@section('title', 'Product Reviews')

@section('content')
<div class="bg-white rounded-lg shadow-sm border border-gray-100">
    <div class="p-6 border-b border-gray-100 flex justify-between items-center">
        <h2 class="text-xl font-bold text-gray-800">Product Reviews</h2>
        <div class="text-sm text-gray-500">
            Total Reviews: {{ $reviews->total() }}
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead class="bg-gray-50 text-gray-600 font-medium text-sm">
                <tr>
                    <th class="px-6 py-3">Product</th>
                    <th class="px-6 py-3">Customer</th>
                    <th class="px-6 py-3">Rating</th>
                    <th class="px-6 py-3">Review</th>
                    <th class="px-6 py-3">Status</th>
                    <th class="px-6 py-3">Date</th>
                    <th class="px-6 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @if($reviews->count() > 0)
                    @foreach ($reviews as $review)
                    <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-4">
                        @if($review->product)
                            <div class="flex items-center gap-3">
                                @if($review->product->thumbnail_image)
                                    <img src="{{ asset('storage/'.$review->product->thumbnail_image) }}" alt="" class="w-10 h-10 object-cover rounded-md">
                                @else
                                    <div class="w-10 h-10 bg-gray-100 rounded-md flex items-center justify-center text-gray-400">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    </div>
                                @endif
                                <div>
                                    <div class="font-medium text-gray-900 line-clamp-1">{{ $review->product->name }}</div>
                                    <a href="{{ route('product.show', $review->product->slug) }}" target="_blank" class="text-xs text-indigo-600 hover:text-indigo-800">View Product</a>
                                </div>
                            </div>
                        @else
                            <span class="text-gray-400 italic">Deleted Product</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">
                        <div class="font-medium text-gray-900">{{ $review->user->name }}</div>
                        <div class="text-xs text-gray-500">{{ $review->user->email }}</div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex text-yellow-400">
                            @for($i=1; $i<=5; $i++)
                                @if($i <= $review->rating)
                                    <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                @else
                                    <svg class="w-4 h-4 text-gray-300 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                @endif
                            @endfor
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm text-gray-600 max-w-xs line-clamp-3" title="{{ $review->review_text }}">
                            {{ $review->review_text ?? 'No written review' }}
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        @if($review->is_approved)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                Approved
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                Pending
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap">
                        {{ $review->created_at->format('M d, Y') }}
                        <div class="text-xs">{{ $review->created_at->format('h:i A') }}</div>
                    </td>
                    <td class="px-6 py-4 text-right space-x-2">
                        @if(!$review->is_approved)
                        <form action="{{ route('admin.reviews.update', $review) }}" method="POST" class="inline-block">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="is_approved" value="1">
                            <button type="submit" class="bg-green-100 hover:bg-green-200 text-green-700 px-3 py-1 rounded-md text-sm font-medium transition-colors">
                                Approve
                            </button>
                        </form>
                        @else
                        <form action="{{ route('admin.reviews.update', $review) }}" method="POST" class="inline-block">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="is_approved" value="0">
                            <button type="submit" class="bg-yellow-100 hover:bg-yellow-200 text-yellow-700 px-3 py-1 rounded-md text-sm font-medium transition-colors">
                                Reject
                            </button>
                        </form>
                        @endif
                        
                        <form action="{{ route('admin.reviews.destroy', $review) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this review?');">
                            @csrf
                            @method('DELETE')
                            <button class="text-red-600 hover:text-red-900 p-1">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </form>
                    </td>
                </tr>
                    @endforeach
                @else
                <tr>
                    <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                        <div class="flex flex-col items-center justify-center">
                            <svg class="w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                            <span class="text-lg font-medium text-gray-400">No reviews found</span>
                        </div>
                    </td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>

    @if($reviews->hasPages())
    <div class="p-6 border-t border-gray-100">
        {{ $reviews->links() }}
    </div>
    @endif
</div>
@endsection
