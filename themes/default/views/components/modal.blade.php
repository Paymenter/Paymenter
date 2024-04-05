
<div id=@isset($id) "{{ $id }}" @else "defaultModal" @endisset tabindex="-1" aria-hidden="true" class="fixed top-0 hidden left-0 right-0 z-50 w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full">
    <div class="relative w-full max-h-full @isset($fullWidth) max-w-7xl @else max-w-3xl @endisset mx-auto my-6">
        <!-- Modal content -->
        <div class="relative bg-secondary-100 rounded-lg shadow dark:bg-secondary-200">
            <!-- Modal header -->
            <div class="flex items-start justify-between p-4 border-b rounded-t dark:border-gray-600">
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                    @isset($title)
                        {{ $title }}
                    @endisset
                </h3>
                <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-hide=@isset($id) "{{ $id }}" @else "defaultModal"  @endisset>
                    <svg aria-hidden="true" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                    <span class="sr-only">Close modal</span>
                </button>
            </div>
            <!-- Modal body -->
            <div class="p-6 space-y-6">
                {{ $slot }}
            </div>
            <!-- Modal footer -->
            @isset($footer)
                <div class="flex items-center p-6 space-x-2 border-t border-gray-200 rounded-b dark:border-gray-600">
                    {{ $footer }}
                </div>
            @endisset
        </div>
    </div>
</div>
@isset($open)
    <script>
        let id = @js($id ?? "defaultModal");
        const target = document.getElementById(id);
        const modal = new Modal(target, {}, {
            override: true
        });
        modal.show();
    </script>
@endisset