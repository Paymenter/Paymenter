@if (session('success'))
    <div id="alert" role="alert" class="bg-success-400 text-success-50 px-4 py-1 rounded-md flex items-center justify-between mb-4">
        <div class="flex items-centet gap-x-2">
            <i class="ri-check-line"></i>
            <p class="font-semibold">
                {{ session('success') }}
            </p>
        </div>
        
        <button class="button button-success" type="button" aria-label="Close" onclick="document.getElementById('alert').style.display = 'none';">
            <span class="sr-only">Close</span>
            <i class="ri-close-line"></i>
        </button>
    </div>
@endif
@if(session('error'))
    <div id="alert" role="alert" class="bg-danger-400 text-danger-50 px-4 py-1 rounded-md flex items-center justify-between mb-4">
        <div class="flex items-centet gap-x-2">
            <i class="ri-error-warning-line"></i>
            <p class="font-semibold">
                {{ session('error') }}
            </p>
        </div>
        
        <button class="button button-danger" type="button" aria-label="Close" onclick="document.getElementById('alert').style.display = 'none';">
            <span class="sr-only">Close</span>
            <i class="ri-close-line"></i>
        </button>
    </div>
@endif
@props(['errors'])

@if ($errors->any())
    <div class="flex justify-center">
        <div id="alert"
            class="flex p-4 mx-4 text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400 max-w-3xl w-full"
            role="alert">
            <svg aria-hidden="true" class="flex-shrink-0 w-5 h-5" fill="currentColor" viewBox="0 0 20 20"
                xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd"
                    d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                    clip-rule="evenodd"></path>
            </svg>
            <span class="sr-only">Info</span>
            <div class="ml-3 text-sm font-medium">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </div>
            <button type="button"
                class="ml-auto -mx-1.5 -my-1.5 bg-red-50 text-red-500 rounded-lg focus:ring-2 focus:ring-red-400 p-1.5 hover:bg-red-200 inline-flex h-8 w-8 dark:bg-gray-800 dark:text-red-400 dark:hover:bg-gray-700"
                aria-label="Close" onclick="document.getElementById('alert').style.display = 'none';">
                <span class="sr-only">Close</span>
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd"
                        d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                        clip-rule="evenodd"></path>
                </svg>
            </button>
        </div>
    </div>
@endif
