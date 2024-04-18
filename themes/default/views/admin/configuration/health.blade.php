<div>
    <div class="grid grid-cols-2 gap-4">
        <div class="flex flex-col gap-1">
            @foreach ($warnings as $warning)
                <div class="bg-orange-500 text-white p-2 rounded-lg">
                    {!! $warning !!}
                </div>
            @endforeach
        </div>
        <div class="flex flex-col gap-1">
            @foreach ($errors as $error)
                <div class="bg-red-500 text-white p-2 rounded-lg">
                    {!! $error !!}
                </div>
            @endforeach
        </div>
    </div>
</div>
