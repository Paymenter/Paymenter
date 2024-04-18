<div class="text-sm font-medium text-center border-b text-gray-400 border-gray-700">
    <ul class="flex -mb-px overflow-auto">
        <li class="mr-2">
            <a href="{{ route('admin.users.show', $user) }}"
                class="inline-block p-4 rounded-t-lg border-b-2 border-transparent whitespace-nowrap {{ request()->routeIs('admin.users.show') ? 'border-blue-500 text-blue-500' : 'text-gray-500 hover:border-gray-300 hover:text-gray-300' }}">
                Overview
            </a>
        </li>        
        <li class="mr-2">
            <a href="{{ route('admin.users.edit', $user) }}"
                class="inline-block p-4 rounded-t-lg border-b-2 border-transparent whitespace-nowrap {{ request()->routeIs('admin.users.edit') ? 'border-blue-500 text-blue-500' : 'text-gray-500 hover:border-gray-300 hover:text-gray-300' }}">
                Edit
            </a>
        </li>
    </ul>
</div>
