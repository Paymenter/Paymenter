@if(Auth::check() && Auth::user()->has('ADMINISTRATOR'))
    @if(config('app.commit') && config('app.version') === 'beta' && config('app.commit') !== config('settings::latest_version'))
        <div id="modal_update" tabindex="-1" class="fixed top-0 left-0 right-0 z-50 hidden p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full">
            <div class="relative w-full max-w-xl max-h-full">
                <div class="relative bg-white rounded-lg shadow dark:bg-secondary-200">
                    <button type="button" class="absolute top-3 right-2.5 text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ml-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-hide="popup-modal">
                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                        </svg>
                        <span class="sr-only">Close modal</span>
                    </button>
                    <div class="p-6 text-center">
                        <i class="ri-error-warning-line text-6xl mb-2"></i>
                        <h3 class="mb-5 text-lg font-normal text-gray-500 dark:text-gray-400">{{ __('To download the beta version of Paymenter run:') }}</h3>
                        <blockquote class="p-4 my-4 border-l-4 border-secondary-300 bg-secondary-100 relative">
                            <p class="text-md italic font-medium leading-relaxed text-gray-900 dark:text-white copy_command">php artisan p:upgrade --url https://api.paymenter.org/beta</p>
                            <button id="copyButton" class="z-50 absolute top-2 right-2 bg-primary-500 text-secondary-400 p-2 rounded hover:bg-secondary-200 transition-all ease-in-out">
                                <i class="ri-file-copy-line"></i>
                            </button>
                        </blockquote>
                        <h3 class="mb-5 text-lg font-normal text-gray-500 dark:text-gray-400">{{ __('in the paymenter directory') }}</h3>
                        <br />
                        <br />
                        <span class="text-sm text-gray-500">{{ __('THIS IS A BETA VERSION AND MAY CONTAIN BUGS. USE AT YOUR OWN RISK.') }}</span>
                    </div>
                </div>
            </div>
        </div>
        <script>
            document.getElementById('copyButton').addEventListener('click', function() {
                const textToCopy = document.querySelector('.copy_command').textContent;
                const textArea = document.createElement('textarea');
                textArea.value = textToCopy;
                document.body.appendChild(textArea);
                textArea.select();
                textArea.setSelectionRange(0, 99999);
                document.execCommand('copy');
                document.body.removeChild(textArea);
                this.textContent = "{{ __('Copied!') }}";
                this.classList.add('bg-secondary-200');
                setTimeout(() => {
                    this.innerHTML = "<i class='ri-file-copy-line'></i>";
                    this.classList.remove('bg-secondary-200');
                }, 700);
            });
        </script>
    @endif
    @if((config('app.version') == 'beta' && config('app.commit') !== config('settings::latest_version')) || (config('app.version') !== 'beta' && config('app.version') !== 'development' && config('app.version') !== config('settings::latest_version')))
        <div id="update_panel" class="fixed hidden items-center w-full max-w-xs right-5 bottom-5" role="alert">
            <div id="toast-interactive" class="w-full max-w-xs p-3 text-gray-500 bg-white dark:bg-secondary-200 dark:text-gray-400 rounded-lg shadow-md dark:shadow-2xl" role="alert">
                <div class="flex">
                    <div class="inline-flex items-center justify-center flex-shrink-0 w-8 h-8 text-primary-50 bg-primary-400 rounded-lg">
                        <i class="ri-refresh-line"></i>
                    </div>
                    <div class="ml-3 text-sm font-normal">
                        <span class="mb-1 text-sm font-semibold text-gray-900 dark:text-white flex">
                            {{ __('Update available') }}
                            (
                            <div class="max-w-[90px] truncate">
                                <span id="latest">
                                    {{ config('settings::latest_version') }}
                                </span>
                            </div>
                            )
                        </span>
                        <div class="mb-2 text-sm font-normal">
                            @if(config('app.commit') && config('app.version') === 'beta')
                                {{ __('A new paymenter beta version is available for download.') }}
                            @else
                                {{ __('A new paymenter stable version is available for download.') }}
                            @endif
                        </div>
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <a
                                    @if(config('app.commit') && config('app.version') === 'beta')
                                        data-modal-target="modal_update"
                                        data-modal-toggle="modal_update"
                                    @else
                                        href="https://paymenter.org/docs/how-to-update"
                                        target="_blank"
                                    @endif
                                    class="cursor-pointer inline-flex justify-center w-full px-2 py-1.5 text-xs font-medium text-center text-white bg-primary-400 rounded-lg hover:bg-primary-300 focus:ring-4 focus:outline-none focus:ring-primary-200 transition-all ease-in-out"
                                >
                                    {{ __('Update') }}
                                </a>
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
            let expirationDate = localStorage.getItem('update_panel_expiration');
            let currentTime = new Date();
        
            if (!expirationDate || new Date(expirationDate) < currentTime) {
                document.getElementById('update_panel').style.display = 'flex';
            
                document.querySelector('#close_update_panel').addEventListener('click', function() {
                    document.getElementById('update_panel').style.display = 'none';
                
                    let date = new Date(currentTime.getTime() + (72 * 60 * 60 * 1000));
                    localStorage.setItem('update_panel_expiration', date.toISOString());
                });
            }
        </script>
    @endif
@endif
