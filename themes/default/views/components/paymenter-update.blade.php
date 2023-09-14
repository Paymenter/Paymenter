@if (config('app.version') == 'development')
    @if(Auth::user()->has('ADMINISTRATOR'))
        <div id="update_panel" class="fixed hidden items-center w-full max-w-xs right-5 bottom-5" role="alert">
            <div id="toast-interactive" class="w-full max-w-xs p-3 text-gray-500 bg-white dark:bg-secondary-200 dark:text-gray-400 rounded-lg shadow-md dark:shadow-2xl" role="alert">
                <div class="flex">
                    <div class="inline-flex items-center justify-center flex-shrink-0 w-8 h-8 text-primary-50 bg-primary-400 rounded-lg">
                        <i class="ri-refresh-line"></i>
                    </div>
                    <div class="ml-3 text-sm font-normal">
                        <span class="mb-1 text-sm font-semibold text-gray-900 dark:text-white">{{ __('Update available') }} <span id="latest"></span></span>
                        <div class="mb-2 text-sm font-normal">{{ __('A new paymenter version is available for download.') }}</div>
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <a href="https://paymenter.org/docs/how-to-update" target="_blank" class="inline-flex justify-center w-full px-2 py-1.5 text-xs font-medium text-center text-white bg-primary-400 rounded-lg hover:bg-primary-300 focus:ring-4 focus:outline-none focus:ring-primary-200 transition-all ease-in-out">{{ __('Update') }}</a>
                            </div>
                            <div>
                                <a id="close_update_panel" class="cursor-pointer inline-flex justify-center w-full px-2 py-1.5 text-xs font-medium text-center text-gray-900 bg-white border-0 rounded-lg hover:bg-gray-100 focus:ring-0 focus:outline-none dark:bg-secondary-200 dark:text-white dark:hover:bg-secondary-300 transition-all ease-in-out">{{ __('Not Now') }}</a>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="transition-all ease-in-out ml-auto -mx-1.5 -my-1.5 bg-white items-center justify-center flex-shrink-0 text-gray-400 hover:text-gray-900 rounded-lg focus:ring-2 focus:ring-gray-300 p-1.5 hover:bg-gray-100 inline-flex h-8 w-8 dark:text-gray-500 dark:hover:text-white dark:bg-secondary-200 dark:hover:bg-secondary-300" data-dismiss-target="#toast-interactive" aria-label="Close">
                        <span class="sr-only">Close</span>
                        <i class="ri-close-line"></i>
                    </button>
                </div>
            </div>
        </div>

        <script>
            document.getElementById('latest').innerHTML = '...';
            fetch('https://api.github.com/repos/Paymenter/Paymenter/releases/latest')
                .then(response => response.json())
                .then(data => {
                    if(data.tag_name === 'v{{ config('app.version') }}') {

                    } else {
                        let expirationDate = localStorage.getItem('update_panel_expiration');
                        let currentTime = new Date();

                        if (!expirationDate || new Date(expirationDate) < currentTime) {

                            document.getElementById('update_panel').style.display = 'flex';

                            document.querySelector('#close_update_panel').addEventListener('click', function() {
                                document.getElementById('update_panel').style.display = 'none';

                                let twelveHoursLater = new Date(currentTime.getTime() + (12 * 60 * 60 * 1000));
                                localStorage.setItem('update_panel_expiration', twelveHoursLater.toISOString());
                            });
                        }
                        document.getElementById('latest').innerHTML = data.tag_name;
                    }
                });
        </script>
    @endif
@endif
