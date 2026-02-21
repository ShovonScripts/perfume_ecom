<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Checkout') }}
        </h2>
    </x-slot>

    <div class="py-12" x-data="checkout({
        subtotal: {{ $subtotal }},
        discount: {{ $discount }},
        comboDiscount: {{ $comboDiscount ?? 0 }},
        insideFee: {{ $insideFee }},
        outsideFee: {{ $outsideFee }},
        oldCity: '{{ old('city', auth()->user()->city ?? '') }}'
    })">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h1 class="text-2xl font-bold mb-6">Checkout</h1>

                    <form method="POST" action="{{ route('checkout.store') }}">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <!-- Name -->
                            <div>
                                <label for="name" class="block text-sm font-medium mb-2">
                                    Full Name <span class="text-red-500">*</span>
                                </label>
                                <input type="text" 
                                       name="name" 
                                       id="name" 
                                       value="{{ old('name', auth()->user()->name ?? '') }}"
                                       class="w-full px-4 py-3 text-base border dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500"
                                       required>
                                @error('name')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Phone -->
                            <div>
                                <label for="phone" class="block text-sm font-medium mb-2">
                                    Phone <span class="text-red-500">*</span>
                                </label>
                                <input type="text" 
                                       name="phone" 
                                       id="phone" 
                                       value="{{ old('phone') }}"
                                       placeholder="01XXXXXXXXX"
                                       class="w-full px-4 py-3 text-base border dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500"
                                       required>
                                @error('phone')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Email -->
                            <div>
                                <label for="email" class="block text-sm font-medium mb-2">
                                    Email (Optional)
                                </label>
                                <input type="email" 
                                       name="email" 
                                       id="email" 
                                       value="{{ old('email', auth()->user()->email ?? '') }}"
                                       class="w-full px-4 py-3 text-base border dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500">
                                @error('email')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- City -->
                            <div>
                                <label for="city" class="block text-sm font-medium mb-2">
                                    City <span class="text-red-500">*</span>
                                </label>
                                <select name="city" 
                                        id="city" 
                                        x-model="city"
                                        class="w-full px-4 py-3 text-base border dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500"
                                        required>
                                    <option value="" disabled>Select City</option>
                                    @foreach($districts as $district)
                                        <option value="{{ $district }}">{{ $district }}</option>
                                    @endforeach
                                </select>
                                @error('city')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                                <p class="text-xs text-gray-500 mt-1" x-show="city">
                                    Shipping: 
                                    <span x-text="city.toLowerCase() === 'dhaka' ? 'Inside Dhaka (৳' + insideFee + ')' : 'Outside Dhaka (৳' + outsideFee + ')'"></span>
                                </p>
                            </div>

                            <!-- Area -->
                            <div>
                                <label for="area" class="block text-sm font-medium mb-2">
                                    Area <span class="text-red-500">*</span>
                                </label>
                                <input type="text" 
                                       name="area" 
                                       id="area" 
                                       value="{{ old('area') }}"
                                       placeholder="Mirpur"
                                       class="w-full px-4 py-3 text-base border dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500"
                                       required>
                                @error('area')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Address -->
                        <div class="mb-6">
                            <label for="address_line" class="block text-sm font-medium mb-2">
                                Full Address <span class="text-red-500">*</span>
                            </label>
                            <textarea name="address_line" 
                                      id="address_line" 
                                      rows="3"
                                      class="w-full px-4 py-3 text-base border dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500"
                                      required>{{ old('address_line') }}</textarea>
                            @error('address_line')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Order Summary -->
                        <hr class="my-6 border-gray-300 dark:border-gray-600">

                        <h2 class="text-xl font-bold mb-4">Order Summary</h2>
                        
                        @foreach($items as $item)
                            <div class="flex justify-between py-2 text-sm">
                                <span>{{ $item['name'] }} ({{ $item['ml_value'] }}{{ $item['ml_unit'] }}) x {{ $item['quantity'] }}</span>
                                <span>৳{{ number_format($item['line_total'], 2) }}</span>
                            </div>
                        @endforeach

                        <hr class="my-4 border-gray-300 dark:border-gray-600">

                        <div class="flex justify-between py-2">
                            <span>Subtotal:</span>
                            <span class="font-semibold">৳{{ number_format($subtotal, 2) }}</span>
                        </div>

                        @if($discount > 0)
                            <div class="flex justify-between py-2 text-green-600">
                                <span>Discount:</span>
                                <span>-৳{{ number_format($discount, 2) }}</span>
                            </div>
                        @endif

                        @if(isset($comboDiscount) && $comboDiscount > 0)
                            <div class="flex justify-between py-2 text-green-600">
                                <span>Combo Discount:</span>
                                <span>-৳{{ number_format($comboDiscount, 2) }}</span>
                            </div>
                        @endif

                        <div class="flex justify-between py-2 text-sm">
                            <span>Shipping Fee:</span>
                            <span class="text-gray-600 dark:text-gray-400" x-text="shippingFee > 0 ? '৳' + shippingFee.toFixed(2) : 'Calculate based on city'"></span>
                        </div>

                        <div class="flex justify-between py-2 text-xl font-bold border-t border-gray-300 dark:border-gray-600 mt-2 pt-4">
                            <span>Total:</span>
                            <span class="text-blue-600 dark:text-blue-400">
                                ৳<span x-text="total"></span>
                            </span>
                        </div>
                        
                        <hr class="my-4 border-gray-300 dark:border-gray-600">

                        <!-- Submit -->
                        <div class="flex gap-4">
                            <button type="submit" 
                                    class="flex-1 bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg transition font-semibold">
                                Place Order (COD)
                            </button>
                            <a href="{{ route('cart.index') }}" 
                               class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded-lg transition">
                                Back to Cart
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function checkout(config) {
            return {
                city: config.oldCity,
                subtotal: Number(config.subtotal),
                discount: Number(config.discount),
                comboDiscount: Number(config.comboDiscount),
                insideFee: Number(config.insideFee),
                outsideFee: Number(config.outsideFee),
                shippingFee: 0,
                
                get total() {
                    let t = this.subtotal - this.discount - this.comboDiscount + this.shippingFee;
                    return t.toFixed(2);
                },

                init() {
                    this.updateFee();
                    this.$watch('city', () => this.updateFee());
                },

                updateFee() {
                    if (!this.city) {
                        this.shippingFee = 0;
                    } else if (this.city.toLowerCase() === 'dhaka') {
                        this.shippingFee = this.insideFee;
                    } else {
                        this.shippingFee = this.outsideFee;
                    }
                }
            }
        }
    </script>
</x-app-layout>
