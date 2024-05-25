<div class="flex flex-col @if($product->image) md:grid grid-cols-2 @endif gap-4">
    @if($product->image)
        <img src="{{ url()->to($product->image) }}" alt="{{ $product->name }}" class="w-full h-96 object-cover object-center">
    @endif
    {{-- If your happiness depends on money, you will never be happy with yourself. --}}
    <div class="flex flex-col">
        <h2 class="text-3xl font-bold">{{ $product->name }}</h2>
        <h3 class="text-xl font-semibold">
            
            {{ $product->price() }}
        </h3>
    </div>
</div>
