<?php

namespace App\Http\Controllers\Admin;

use App\Console\Commands\Upgrade;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Artisan;
use Symfony\Component\Console\Output\Output;
use Symfony\Component\HttpFoundation\StreamedResponse;

class UpdateController extends Controller
{
    public function __invoke(): StreamedResponse
    {
        abort_unless(auth()->user()?->hasPermission('admin.updates.update'), 403);

        return response()->stream(function () {
            set_time_limit(0);
            ignore_user_abort(true);

            // // Release the session lock so other requests aren't blocked during the update
            // session()->save();

            while (ob_get_level() > 0) {
                ob_end_flush();
            }

            $output = new class(function (string $chunk) {
                echo $chunk;
                flush();
            }) extends Output
            {

                public function __construct(private \Closure $writer)
                {
                    parent::__construct(self::VERBOSITY_NORMAL, true);
                }

                protected function doWrite(string $message, bool $newline): void
                {
                    ($this->writer)($message . ($newline ? "\n" : ''));
                }
            };

            // The upgrade script must run from the project root
            if (getcwd() !== base_path()) {
                chdir(base_path());
            }

            $arguments = ['--no-interaction' => true];
            if (config('app.version') == 'beta') {
                $arguments['--url'] = 'https://api.paymenter.org/beta';
            }

            try {
                Artisan::call(Upgrade::class, $arguments, $output);
            } catch (\Throwable $e) {
                $output->writeln('<error>' . $e->getMessage() . '</error>');
            }
        }, 200, [
            'Content-Type' => 'text/plain; charset=utf-8',
            'Cache-Control' => 'no-cache, no-transform',
            'X-Accel-Buffering' => 'no',
        ]);
    }
}
