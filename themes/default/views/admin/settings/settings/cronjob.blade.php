<div class="hidden mt-3" id="tab-cronjob">
    @php $last_ran_at = config('settings::cronjob_last_run'); @endphp
    <!-- Cronjob last ran at -->
    <h1 class="text-2xl font-bold dark:text-darkmodetext">{{ __('Cronjob') }}</h1>

    @isset($last_ran_at)
        <h2 class="col-span-1 md:col-span-2 text-xl text-gray-900 dark:text-darkmodetext ">{{ __('Last Ran At') }}:
            {{ $last_ran_at }}</h2>
        @if ($last_ran_at < now()->subMinutes(10))
            <p class="col-span-1 md:col-span-2 text-lg text-gray-900 dark:text-darkmodetext">
                {{ __('Cronjob has not run in the last') }}
                <span class="text-danger-400 dark:text-danger-300"> {{ now()->diffInMinutes($last_ran_at) }} </span>
                {{ __('minutes.') }}<br />
                {{ __('Please check your cronjob setup.') }}
            </p>
            <a href="https://paymenter.org/docs/getting-started/installation/#cronjob" target="_blank"
                class="col-span-1 md:col-span-2 text-lg text-gray-900 dark:text-darkmodetext hover:text-primary-400 underline">
                {{ __('Click here to learn how to setup cronjob.') }}
            </a>
        @endif
    @else
        <h2 class="col-span-1 md:col-span-2 text-xl text-danger-400 dark:text-danger-300">{{ __('Last Ran At') }}: Never
        </h2>
        <p class="col-span-1 md:col-span-2 text-lg text-gray-900 dark:text-darkmodetext ">
            {{ __('Cronjob is not running. Please setup cronjob to run every minute.') }}<br />
            {{ __('This will cause issues such as invoices not being generated/ mails not being sent etc.') }}
        </p>
        <a href="https://paymenter.org/docs/getting-started/installation/#cronjob" target="_blank"
            class="col-span-1 md:col-span-2 text-lg text-gray-900 dark:text-darkmodetext hover:text-primary-400 underline">
            {{ __('Click here to learn how to setup cronjob.') }}
        </a>
    @endisset


    @if(\Illuminate\Support\Facades\DB::table('jobs')->where('available_at', '<=', now()->timestamp)->count() > 0)
        <h2 class="col-span-1 md:col-span-2 text-xl text-gray-900 dark:text-darkmodetext mt-4">{{ __('Queued Jobs') }}:
            {{ \Illuminate\Support\Facades\DB::table('jobs')->where('available_at', '<=', now()->timestamp)->count() }}</h2>
        <p class="col-span-1 md:col-span-2 text-lg text-gray-900 dark:text-darkmodetext ">
            {{ __('There are some jobs queued. Please check your queue worker.') }}
        </p>
        <a href="https://paymenter.org/docs/getting-started/installation/#create-queue-worker" target="_blank"
            class="col-span-1 md:col-span-2 text-lg text-gray-900 dark:text-darkmodetext hover:text-primary-400 underline">
            {{ __('Click here to learn how to setup queue worker.') }}
        </a>
    @endif
</div>
