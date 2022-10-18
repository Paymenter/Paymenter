<x-app-layout>
    <x-slot name="title">
        {{ __('Checkout') }}
    </x-slot>
    <a href="/stripe" class="text-xl">CHECKOUT</a>
    <!-- dropdown to select a payment method -->
    <form method="POST" action="">
        @csrf
        <div class="flex flex-col">
            <div class="flex flex-col">
                <label for="payment_method" class="block text-sm font-medium text-gray-700">Payment method</label>
                <select id="payment_method" name="payment_method" autocomplete="payment_method"
                    class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    @foreach (App\Models\Extensions::where('type', 'gateway')->where('enabled', true)->get() as $gateway)
                        <option value="{{ $gateway->id }}">{{ $gateway->name }}</option>
                    @endforeach
                </select>

            </div>
            <button type="submit" class="mt-4 py-2 px-4 bg-blue-500 hover:bg-blue-700 text-white font-bold rounded">
                {{ __('Continue') }}
            </button>

        </div>
    </form>


    @dump($products)
</x-app-layout>
