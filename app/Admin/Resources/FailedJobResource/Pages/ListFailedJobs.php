<?php

namespace App\Admin\Resources\FailedJobResource\Pages;

use App\Admin\Resources\FailedJobResource;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\DB;

class ListFailedJobs extends ListRecords
{
    protected static string $resource = FailedJobResource::class;

    // Check if jobs table is empty, if not send a notification
    public function mount(): void
    {
        // Check if first job its available_at is at least 5 minutes ago
        if (DB::table('jobs')->count() > 0) {
            $firstJob = DB::table('jobs')->orderBy('available_at', 'asc')->first();
            $firstJobAvailableAt = Carbon::parse($firstJob->available_at);
            $now = Carbon::now();
            $diffInMinutes = $firstJobAvailableAt->diffInMinutes($now);
            if ($diffInMinutes > 5) {
                Notification::make()
                    ->title('Whoops!')
                    ->body('There are ' . DB::table('jobs')->count() . " jobs in the queue.\nThis could mean that your jobs are not being processed correctly.")
                    ->danger()
                    ->actions([
                        Action::make('view')
                            ->label('Browse Documentation')
                            ->button()
                            ->url('https://paymenter.org/docs/installation/install#creating-cronjob-and-service', shouldOpenInNewTab: true),
                    ])
                    ->send();
            }
        }

        parent::mount();
    }
}
