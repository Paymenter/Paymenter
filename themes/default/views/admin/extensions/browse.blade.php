<x-admin-layout title="Browse extensions">
    <h1 class="text-2xl font-bold">Browse extensions</h1>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        @foreach ($extensions as $extension)
            @php $extension = (object) $extension; @endphp
            <div class="!bg-secondary-300 content-box">
                <img src="{{ config('app.marketplace') . '../../storage/' . $extension->icon }}" alt="{{ $extension->name }}" class="w-full">
                <h1 class="text-xl text-center">{{ $extension->name }}</h1>
                <p class="text-center">{{ $extension->slogan }}</p>
                @if ($extension->price == 0)
                    <p class="text-center">Free</p>
                    <div class="flex justify-center">
                        <form action="{{ route('admin.extensions.install', $extension->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="button button-primary">Download</button>
                        </form>
                    </div>
                @else
                    <p class="text-center">{{ $extension->price }}$</p>
                    <div class="flex justify-center">
                        <a href="{{ config('app.marketplace') . '/extension/' . $extension->id  . '/' . $extension->name }}"
                            class="button button-primary">Buy</a>
                    </div>
                @endif
            </div>
        @endforeach
    </div>
</x-admin-layout>
