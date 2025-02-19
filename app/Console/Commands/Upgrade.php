<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;

class Upgrade extends Command
{
    protected const DEFAULT_URL = 'https://github.com/paymenter/paymenter/releases/%s/paymenter.tar.gz';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:upgrade
        {--user= : The user that PHP runs under. All files will be owned by this user.}
        {--group= : The group that PHP runs under. All files will be owned by this group.}
        {--url= : The specific archive to download.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Upgrade the website to the latest version.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Starting upgrade process...');

        if (version_compare(PHP_VERSION, '8.2.0') < 0) {
            $this->error('Cannot execute self-upgrade process. The minimum required PHP version required is 8.1, you have [' . PHP_VERSION . '].');
        }

        $user = 'www-data';
        $group = 'www-data';
        if ($this->input->isInteractive()) {
            if (is_null($this->option('user'))) {
                $userDetails = posix_getpwuid(fileowner('public'));
                $user = $userDetails['name'] ?? 'www-data';

                if (!$this->confirm("Your webserver user has been detected as <fg=blue>[{$user}]:</> is this correct?", true)) {
                    $user = $this->anticipate(
                        'Please enter the name of the user running your webserver process. This varies from system to system, but is generally "www-data", "nginx", or "apache".',
                        [
                            'www-data',
                            'nginx',
                            'apache',
                        ]
                    );
                }
            }

            if (is_null($this->option('group'))) {
                $groupDetails = posix_getgrgid(filegroup('public'));
                $group = $groupDetails['name'] ?? 'www-data';

                if (!$this->confirm("Your webserver group has been detected as <fg=blue>[{$group}]:</> is this correct?", true)) {
                    $group = $this->anticipate(
                        'Please enter the name of the group running your webserver process. Normally this is the same as your user.',
                        [
                            'www-data',
                            'nginx',
                            'apache',
                        ]
                    );
                }
            }

            if (!$this->confirm('Are you sure you want to run the upgrade process for your Panel?')) {
                $this->warn('Upgrade process terminated by user.');

                return;
            }
        }
        ini_set('output_buffering', '0');
        // Call update.sh <url>
        $this->line('$upgrader> curl -L "https://raw.githubusercontent.com/paymenter/paymenter/master/update.sh" | bash -s -- --user=' . $user . ' --group=' . $group . ' --url=' . $this->getUrl());
        $process = Process::fromShellCommandline('curl -L "https://raw.githubusercontent.com/paymenter/paymenter/master/update.sh" | bash -s -- --user=' . $user . ' --group=' . $group . ' --url=' . $this->getUrl(), null, null, null, 1200);
        $process->run(function ($type, $buffer) {
            $this->{$type === Process::ERR ? 'error' : 'line'}($buffer);
        });

        $this->info('Upgrade process completed successfully!');

        return Command::SUCCESS;
    }

    protected function getUrl(): string
    {
        if ($this->option('url')) {
            return $this->option('url');
        }

        return sprintf(self::DEFAULT_URL, 'latest/download');
    }
}
