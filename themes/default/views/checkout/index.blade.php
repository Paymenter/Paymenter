<x-app-layout>
    <x-slot name="title">
        {{ __('Checkout') }}
    </x-slot>
    <form method="POST" action="{{ route('checkout.pay') }}">
        @csrf

        <div class="flex flex-col md:p-12">
            <x-success class="mb-4" />
            @foreach ($products as $product)
                <div class="flex flex-row justify-between items-center">
                    <div class="flex flex-row items-center">
                        <img src="{{ $product->image }}" alt="{{ $product->name }}" class="w-10 h-10">
                        <div class="flex flex-col ml-4">
                            <span class="text-lg font-bold">{{ $product->name }}</span>
                            <span class="text-gray-500">{{ $product->description }}</span>
                        </div>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-lg font-bold">{{ App\Models\Settings::first()->currency_sign }}
                            {{ $product->price }}</span>
                        <span class="text-gray-500">Quantity: {{ $product->quantity }}</span>
                    </div>
                </div>
            @endforeach

            <div class="flex flex-row justify-between items-center mt-4">
                <div class="flex flex-row items-center">
                    <span class="text-lg font-bold">Total</span>
                </div>
                <div class="flex flex-col">
                    <span class="text-lg font-bold">{{ App\Models\Settings::first()->currency_sign }}
                        {{ $total }}</span>
                </div>
            </div>
            <br><br>
            <div class="flex flex-col">
                <label for="payment_method" class="block text-sm font-medium text-gray-700">Payment method</label>
                <select id="payment_method" name="payment_method" autocomplete="payment_method"
                    class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    @foreach (App\Models\Extensions::where('type', 'gateway')->where('enabled', true)->get() as $gateway)
                        <option value="{{ $gateway->id }}">{{ $gateway->name }}</option>
                    @endforeach
                </select>

            </div>
            <div class="flex flex-row justify-end mt-4">
                <button type="submit"
                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded dark:text-darkmodetext">
                    {{ __('Pay') }}
                </button>
            </div>
        </div>
    </form>
</x-app-layout>
