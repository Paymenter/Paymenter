<div class="grid grid-cols-4">
    <div class="flex flex-col gap-4 w-full col-span-3">
        <h1 class="text-3xl font-bold">{{ $product->name }}</h1>
        <div class="flex flex-row w-full gap-4">
            @if ($product->image)
                <img src="{{ url()->to($product->image) }}" alt="{{ $product->name }}" class="max-w-40 h-fit">
            @endif
            <article class="prose prose-invert prose-sm">
                {!! $product->description !!}
            </article>
        </div>
        @if ($product->availablePlans()->count() > 1)
            <div class="flex flex-col">
                <label for="plan" class="text-sm font-medium my-2">Select a plan</label>
                <select wire:model.live="plan" class="text-white bg-primary-800 px-2.5 py-2.5 rounded-md w-fit">
                    @foreach ($product->availablePlans() as $availablePlan)
                        <option value="{{ $availablePlan->id }}">
                            {{ $availablePlan->name }} -
                            {{ $availablePlan->price() }}
                            @if ($availablePlan->price()->has_setup_fee)
                                + {{ $availablePlan->price()->setup_fee }} setup fee
                            @endif
                        </option>
                    @endforeach
                </select>
            </div>
        @endif
    </div>
    <div class="flex flex-col gap-4 w-full col-span-1">
        <h2 class="text-2xl font-semibold">Price</h2>

        <h3 class="text-xl font-semibold">
            {{ $product->price($plan) }}
        </h3>
        @if (($product->stock > 0 || !$product->stock) && $product->price()->available)
            <div>
                <x-button.primary>
                    Checkout
                </x-button.primary>
            </div>
        @endif
        {{-- 
        <div class="flex flex-col"> 
            @if ($product->stock === 0) not sure if it will work etc, recently got acces to the beta though. but to many options and im wayy to lazy to check them all out with no guides
                <span class="text-xs font-medium me-2 px-2.5 py-0.5 rounded bg-red-900 text-red-300 w-fit mb-3">
                    Out of stock
                </span>
            @elseif($product->stock > 0)
                <span class="text-xs font-medium me-2 px-2.5 py-0.5 rounded bg-green-900 text-green-300 w-fit mb-3">
                    In stock
                </span>
            @endif
            <div class="flex flex-row justify-between">
                <div>
                    <h2 class="text-3xl font-bold">{{ $product->name }}</h2>                   <h3 class="text-xl font-semibold">
                        {{ $product->price() }} 
                    </h3>{ $p
                    <selere:model.live="plan" class="mt-2 text-white bg-primary-800 px-2.5 py-2.5 rounded-md">
                        @foreach ($product->availablePlans() as $plan)
                            <option value="{{ $plan->id }}">
                                {{ $plan->name }} -
                                {{ $plan->price() }}
                                @if ($plan->price()->has_setup_fee)
                                    + {{ $plan->price()->setup_fee }} setup fee
                                @endif
                            </option>
                        @endforeach
                    </select>
                </div>
                @if (($product->stock > 0 || !$product->stock) && $product->price()->available)
                    <div>
                        <x-button.secondary>
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="currentColor" class="size-6">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007ZM8.625 10.5a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm7.5 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                            </svg>
                        </x-button.secondary>
                    </div>
                @endif
            </div>

            @if (($product->stock > 0 || !$product->stock) && $product->price()->available)
                <x-button.primary wire:click='addToCart'>Add to cart</x-button.primary>
            @endif
        </div> --}}
    </div>
</div>
