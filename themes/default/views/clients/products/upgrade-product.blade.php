<x-app-layout clients title="{{ __('Product') }} {{ $product->name }}">
    <script>
        function removeElement(element) {
            element.remove();
            this.error = true;
        }
    </script>
    <div class="content grid md:grid-cols-3 gap-4">
        <div class="content-box h-full">
            <h1 class="text-xl font-semibold text-secondary-900">Upgrading from {{ $orderProduct->product->name }}</h1>
            @if ($orderProduct->product->image !== 'null')
                <img src="{{ $orderProduct->product->image }}" class="w-20 rounded-md mr-4" onerror="removeElement(this);" />
            @endif
            <div class="prose dark:prose-invert">
                @markdownify($orderProduct->product->description)
            </div>
            <p class="mt-4">
                Current Price: <x-money :amount="$orderProduct->price" />
            </p>
        </div>
        <div class="content-box h-full">
            <h1 class="text-xl font-semibold text-secondary-900">Upgrading to {{ $product->name }}</h1>
            @if ($product->image !== 'null')
                <img src="{{ $product->image }}" class="w-20 rounded-md mr-4" onerror="removeElement(this);" />
            @endif
            <div class="prose dark:prose-invert">
                @markdownify($product->description)
            </div>
            <p class="mt-4">
                {{ __('Total due today') }} <x-money :amount="$amount" />
                <br />
                {{ __('Next') }} {{ ucfirst($orderProduct->billing_cycle) }}: <x-money :amount="$product->price($orderProduct->billing_cycle)" />
            </p>
        </div>

        <div class="content-box h-full">
            <div class="flex flex-row items-center justify-between mt-2">
                <div class="flex flex-row items-center">
                    Subtotal
                </div>
                <div class="flex flex-col items-end">
                    <x-money :amount="$amount - $tax['amount']" />
                </div>
            </div>
            @if($tax['tax'])
                <div class="flex flex-row items-center justify-between mt-2">
                    <div class="flex flex-row items-center">
                        {{ $tax['tax']['name'] }} ({{ $tax['tax']['rate'] }}%)
                    </div>
                    <div class="flex flex-col items-end">
                        <x-money :amount="$tax['amount']" />
                    </div>
                </div>
            @endif
            <div class="flex flex-row items-center justify-between mt-2">
                <div class="flex flex-row items-center">
                    <span class="text-lg font-bold">{{ __('Total Today') }}</span>
                </div>
                <div class="flex flex-col items-end">
                    <span class="text-lg font-bold">
                        <x-money :amount="$amount" />
                    </span>
                </div>
            </div>
            <hr class="my-4 border-secondary-300">
            <form action="{{ route('clients.active-products.upgrade-product.post', [$orderProduct, $product->id]) }}"
                method="POST">
                @csrf
                @if($amount <= 0)
                    <input type="hidden" name="payment_method" value="free" />
                    <h1 class="text-lg font-semibold text-secondary-900">{{ __('Remaining balance will be credited to your account') }}</h1>
                @else 
                    <div class="flex flex-col">
                        <label for="payment_method" class="text-sm text-secondary-600">{{ __('Payment method') }}</label>
                        <select id="payment_method" name="payment_method" autocomplete="payment_method"
                            class="py-2 bg-secondary-200 text-secondary-800 font-medium rounded-md placeholder-secondary-500 outline-none w-full border focus:ring-2 focus:ring-offset-2 ring-offset-secondary-50 dark:ring-offset-secondary-100 duration-300 border-secondary-300 focus:border-secondary-400 focus:ring-primary-400">
                            @foreach (App\Helpers\ExtensionHelper::getAvailableGateways($amount, [$product]) as $gateway)
                                <option value="{{ $gateway->id }}">
                                    {{ isset($gateway->display_name) ? $gateway->display_name : $gateway->name }}
                                </option>
                            @endforeach
                            @if (config('settings::credits') && auth()->user() && auth()->user()->credits > 0)
                                <option value="credits">
                                    {{ __('Pay with credits') }}
                                </option>
                            @endif
                        </select>
                    </div>
                @endif
                <button type="submit"
                    class="button button-primary bg-primary-300 hover:bg-primary-400 text-white w-full mt-4">
                    {{ __('Upgrade') }} <i class="ri-shopping-cart-2-line"></i>
                </button>
            </form>
        </div>
    </div>



</x-app-layout>
