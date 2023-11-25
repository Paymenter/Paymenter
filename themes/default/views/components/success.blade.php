@if (session('success'))
    <div id="success" class="fixed bottom-0 left-1/3 transform md:w-1/3 z-50 flex items-center p-4 mb-16 text-green-800 rounded-lg bg-green-50 dark:bg-gray-800 dark:text-green-400 shadow-md shadow-[#384b44]" role="alert">
      <svg class="flex-shrink-0 w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
        <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
      </svg>
      <span class="sr-only">Info</span>
      <div class="ml-3 text-sm font-medium">
        {{ session('success') }}
      </div>
      <button type="button" class="ml-auto -mx-1.5 -my-1.5 bg-green-50 text-green-500 rounded-lg focus:ring-2 focus:ring-green-400 p-1.5 hover:bg-green-200 inline-flex items-center justify-center h-8 w-8 dark:bg-gray-800 dark:text-green-400 dark:hover:bg-gray-700" data-dismiss-target="#success" aria-label="Close">
        <span class="sr-only">Close</span>
        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
          <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
        </svg>
      </button>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const alertElement = document.getElementById('success');
            alertElement.classList.remove('hidden');
            alertElement.classList.add('flex');
            alertElement.style.transform = 'translateY(500%)';

            requestAnimationFrame(function() {
                alertElement.style.transition = 'transform 0.5s ease-in-out';
                alertElement.style.transform = 'translateY(-50%)';
            });

            setTimeout(function() {
                alertElement.style.transform = 'translateY(500%)';
                alertElement.addEventListener('transitionend', function() {
                    alertElement.classList.remove('flex');
                    alertElement.classList.add('hidden');
                }, {
                    once: true
                });
            }, 6000);
        });
    </script>
@endif
@if(session('error'))
    <div id="error" class="fixed bottom-0 left-1/3 transform md:w-1/3 z-50 flex items-center p-4 mb-16 text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400 shadow-md shadow-[#4b3b38]" role="alert">
      <svg class="flex-shrink-0 w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
        <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
      </svg>
      <span class="sr-only">Info</span>
      <div class="ml-3 text-sm font-medium">
        {{ session('error') }}
      </div>
      <button type="button" class="ml-auto -mx-1.5 -my-1.5 bg-red-50 text-red-500 rounded-lg focus:ring-2 focus:ring-red-400 p-1.5 hover:bg-red-200 inline-flex items-center justify-center h-8 w-8 dark:bg-gray-800 dark:text-red-400 dark:hover:bg-gray-700" data-dismiss-target="#success" aria-label="Close">
        <span class="sr-only">Close</span>
        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
          <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
        </svg>
      </button>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const alertElement = document.getElementById('error');
            alertElement.classList.remove('hidden');
            alertElement.classList.add('flex');
            alertElement.style.transform = 'translateY(500%)';

            requestAnimationFrame(function() {
                alertElement.style.transition = 'transform 0.5s ease-in-out';
                alertElement.style.transform = 'translateY(-50%)';
            });

            setTimeout(function() {
                alertElement.style.transform = 'translateY(500%)';
                alertElement.addEventListener('transitionend', function() {
                    alertElement.classList.remove('flex');
                    alertElement.classList.add('hidden');
                }, {
                    once: true
                });
            }, 6000);
        });
    </script>
@endif
@props(['errors'])

@if ($errors->any())
    <div id="errors" class="fixed bottom-0 left-1/3 transform md:w-1/3 z-50 flex items-center p-4 mb-16 text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400 shadow-md shadow-[#4b3b38]" role="alert">
      <svg class="flex-shrink-0 w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
        <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
      </svg>
      <span class="sr-only">Info</span>
      <div class="ml-3 text-sm font-medium">
        Something went wrong:
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
      </div>
      <button type="button" class="ml-auto -mx-1.5 -my-1.5 bg-red-50 text-red-500 rounded-lg focus:ring-2 focus:ring-red-400 p-1.5 hover:bg-red-200 inline-flex items-center justify-center h-8 w-8 dark:bg-gray-800 dark:text-red-400 dark:hover:bg-gray-700" data-dismiss-target="#success" aria-label="Close">
        <span class="sr-only">Close</span>
        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
          <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
        </svg>
      </button>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const alertElement = document.getElementById('errors');
            alertElement.classList.remove('hidden');
            alertElement.classList.add('flex');
            alertElement.style.transform = 'translateY(500%)';

            requestAnimationFrame(function() {
                alertElement.style.transition = 'transform 0.5s ease-in-out';
                alertElement.style.transform = 'translateY(-50%)';
            });

            setTimeout(function() {
                alertElement.style.transform = 'translateY(500%)';
                alertElement.addEventListener('transitionend', function() {
                    alertElement.classList.remove('flex');
                    alertElement.classList.add('hidden');
                }, {
                    once: true
                });
            }, 6000);
        });
    </script>
@endif
