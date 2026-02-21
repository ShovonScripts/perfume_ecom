<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Payment Confirmation') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="max-w-md mx-auto">
                        <div class="text-center mb-8">
                            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-green-100 dark:bg-green-900 mb-4">
                                <i data-lucide="banknote" class="w-8 h-8 text-green-600 dark:text-green-400"></i>
                            </div>
                            <h2 class="text-2xl font-bold mb-2">Complete Delivery Charge Payment</h2>
                            <p class="text-gray-600 dark:text-gray-400">
                                Order #{{ $order->order_number }}
                            </p>
                        </div>

                            @php
                                $bkashNumber = \App\Models\Setting::get('bkash_number');
                                $nagadNumber = \App\Models\Setting::get('nagad_number');
                                $bkashQr = \App\Models\Setting::get('bkash_qr');
                                $nagadQr = \App\Models\Setting::get('nagad_qr');
                            @endphp

                            <div class="grid md:grid-cols-2 gap-6 mt-6">
                                {{-- bKash --}}
                                @if($bkashQr)
                                    <div class="text-center bg-white dark:bg-gray-800 border dark:border-gray-700 p-4 rounded-xl shadow-sm">
                                        <h3 class="font-bold mb-3 text-pink-600">bKash Payment</h3>
                                        <div class="bg-white p-2 rounded-lg inline-block shadow-sm mb-3">
                                            <img src="{{ asset('storage/'.$bkashQr) }}" class="w-40 h-40 object-cover rounded">
                                        </div>
                                        <div class="flex items-center justify-center gap-2 bg-gray-100 dark:bg-gray-700 py-2 px-4 rounded-lg">
                                            <span class="font-mono font-bold text-lg dark:text-white select-all">{{ $bkashNumber }}</span>
                                        </div>
                                        <p class="text-xs text-gray-500 mt-2">Scan QR or Copy Number</p>
                                    </div>
                                @elseif($bkashNumber)
                                    <div class="flex items-center justify-between p-4 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
                                        <div class="flex items-center gap-3">
                                            <div class="p-2 bg-pink-100 rounded-lg"><i data-lucide="smartphone" class="w-5 h-5 text-pink-600"></i></div>
                                            <span class="font-medium text-pink-600">bKash (Send Money)</span>
                                        </div>
                                        <span class="font-mono text-lg font-bold select-all dark:text-white">{{ $bkashNumber }}</span>
                                    </div>
                                @endif

                                {{-- Nagad --}}
                                @if($nagadQr)
                                    <div class="text-center bg-white dark:bg-gray-800 border dark:border-gray-700 p-4 rounded-xl shadow-sm">
                                        <h3 class="font-bold mb-3 text-orange-600">Nagad Payment</h3>
                                        <div class="bg-white p-2 rounded-lg inline-block shadow-sm mb-3">
                                            <img src="{{ asset('storage/'.$nagadQr) }}" class="w-40 h-40 object-cover rounded">
                                        </div>
                                        <div class="flex items-center justify-center gap-2 bg-gray-100 dark:bg-gray-700 py-2 px-4 rounded-lg">
                                            <span class="font-mono font-bold text-lg dark:text-white select-all">{{ $nagadNumber }}</span>
                                        </div>
                                        <p class="text-xs text-gray-500 mt-2">Scan QR or Copy Number</p>
                                    </div>
                                @elseif($nagadNumber)
                                    <div class="flex items-center justify-between p-4 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
                                        <div class="flex items-center gap-3">
                                            <div class="p-2 bg-orange-100 rounded-lg"><i data-lucide="smartphone" class="w-5 h-5 text-orange-600"></i></div>
                                            <span class="font-medium text-orange-600">Nagad (Send Money)</span>
                                        </div>
                                        <span class="font-mono text-lg font-bold select-all dark:text-white">{{ $nagadNumber }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <form method="POST" action="{{ route('orders.payment.submit', $order) }}" class="space-y-6">
                            @csrf

                            <div>
                                <label for="payment_sender_number" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Your bKash/Nagad Number
                                </label>
                                <input type="text" name="payment_sender_number" id="payment_sender_number" required
                                    class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    placeholder="01XXXXXXXXX">
                                @error('payment_sender_number')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="payment_amount" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Amount Paid (৳)
                                </label>
                                <input type="number" step="0.01" name="payment_amount" id="payment_amount" 
                                    value="{{ old('payment_amount', $order->shipping_fee) }}" required
                                    class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @error('payment_amount')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="payment_transaction_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Transaction ID (Optional)
                                </label>
                                <input type="text" name="payment_transaction_id" id="payment_transaction_id"
                                    class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    placeholder="TrxID...">
                                @error('payment_transaction_id')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <button type="submit" 
                                class="w-full inline-flex justify-center items-center px-4 py-3 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Submit Payment Details
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
