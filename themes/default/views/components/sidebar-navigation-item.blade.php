@if(!$dropdown)
    <a href="{{ route($route) }}" class="p-2.5 mt-3 h-10 flex items-center rounded-md px-4 duration-200 cursor-pointer hover:bg-blue-600 text-gray-700 dark:text-darkmodetext hover:text-white font-bold text-[15px]">
        <i class="{{ $icon }} mr-4 dark:text-white w-4 text-lg font-normal"></i>
        {{ $slot }}
    </a>
@else
    <a href="{{ route($route) }}" class="p-2.5 py-2 h-10 mt-2 flex items-center rounded-md duration-200 cursor-pointer hover:bg-blue-600 text-gray-700 dark:text-darkmodetext hover:text-white font-bold text-sm">
        <i class="{{ $icon }} mr-4 ml-1 dark:text-white w-4 text-lg font-normal"></i>
        {{ $slot }}
    </a>
@endif
