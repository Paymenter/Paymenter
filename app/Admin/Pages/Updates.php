<?php

namespace App\Admin\Pages;

use App\Console\Commands\CheckForUpdates;
use App\Console\Commands\Upgrade;
use App\Helpers\ExtensionHelper;
use App\Models\Extension;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Symfony\Component\Console\Output\BufferedOutput;

class Updates extends Page implements HasActions, HasForms, HasTable
{
    use InteractsWithActions;
    use InteractsWithForms;
    use InteractsWithTable;

    protected static string|\BackedEnum|null $navigationIcon = 'ri-loop-left-line';

    protected static string|\BackedEnum|null $activeNavigationIcon = 'ri-loop-left-fill';

    protected string $view = 'admin.pages.updates';

    protected static string|\UnitEnum|null $navigationGroup = 'System';

    protected function getHeaderActions(): array
    {
        return [
            Action::make('checkUpdates')
                ->action(function () {
                    Artisan::call(CheckForUpdates::class);
                    Cache::forget('paymenter_marketplace_extension_updates');

                    Notification::make()
                        ->title('Update check completed')
                        ->body('System and installed extension update statuses have been refreshed.')
                        ->success()
                        ->send();
                })
                ->label('Check for updates'),
        ];
    }

    public function update(): Action
    {
        return Action::make('update')
            ->requiresConfirmation()
            ->action(function () {
                $output = new BufferedOutput;

                // Check if current chdir is the root of the project
                if (getcwd() !== base_path()) {
                    chdir(base_path());
                }

                if (config('app.version') == 'beta') {
                    Artisan::call(Upgrade::class, ['--no-interaction' => true, '--url' => 'https://api.paymenter.org/beta'], $output);
                } else {
                    Artisan::call(Upgrade::class, ['--no-interaction' => true], $output);
                }
                $this->dispatch('update-completed', [
                    'output' => $output->fetch(),
                ]);
            })
            ->label('Update');
    }

    public function table(Table $table): Table
    {
        return $table
            ->records(fn () => collect($this->getInstalledAddons())
                ->when($this->tableFilters['used']['isActive'] ?? true, fn ($addons) => $addons->where('is_used', true)))
            ->heading('Installed Addons & Extensions')
            ->description('List of all installed addons, gateways, and server integrations with their version numbers and update status.')
            ->columns([
                ImageColumn::make('icon')
                    ->label('')
                    ->square()
                    ->extraImgAttributes(['class' => 'object-contain']),

                TextColumn::make('name')
                    ->label('Name')
                    ->searchable()
                    ->sortable()
                    ->description(fn ($record) => $record['author'] ? 'By ' . $record['author'] : null),

                TextColumn::make('type')
                    ->label('Type')
                    ->badge()
                    ->color(fn (string $state): string => match (strtolower($state)) {
                        'gateway' => 'info',
                        'server' => 'warning',
                        default => 'success',
                    })
                    ->sortable(),

                TextColumn::make('version')
                    ->label('Installed Version')
                    ->badge()
                    ->color(fn ($record): string => $record['is_builtin'] ? 'success' : 'gray')
                    ->formatStateUsing(fn ($state) => (!empty($state) && ctype_digit($state[0])) ? 'v' . $state : $state),

                TextColumn::make('latest_version')
                    ->label('Latest Version')
                    ->badge()
                    ->color(fn ($record): string => $record['is_up_to_date'] ? 'success' : 'warning')
                    ->placeholder('Not available')
                    ->formatStateUsing(fn ($state) => (!empty($state) && ctype_digit($state[0])) ? 'v' . $state : $state),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->url(fn ($record) => $record['marketplace_url'])
                    ->openUrlInNewTab()
                    ->tooltip(fn ($record) => $record['marketplace_url'] ? 'Visit extension page' : ($record['status'] == 'Builtin' ? 'This is a built-in extension and cannot be updated.' : null))
                    ->color(fn (string $state): string => match ($state) {
                        'Up to date' => 'success',
                        'Update available' => 'warning',
                        'Error' => 'danger',
                        default => 'gray',
                    })
                    ->icon(fn (string $state): string => match ($state) {
                        'Up to date' => 'heroicon-m-check-circle',
                        'Update available' => 'heroicon-m-arrow-path',
                        'Error' => 'heroicon-m-x-circle',
                        'Unknown' => 'heroicon-m-question-mark-circle',
                        default => 'heroicon-m-minus-circle',
                    }),
            ])
            ->filters([
                Filter::make('used')
                    ->label('Used integrations only')
                    ->default(),
            ])
            ->actions([
                Action::make('visit_website')
                    ->label('Visit Website')
                    ->icon('ri-external-link-line')
                    ->url(fn ($record) => $record['url'] ?? null)
                    ->openUrlInNewTab()
                    ->visible(fn ($record) => !empty($record['url'])),
            ]);
    }

    public function getInstalledAddons(): array
    {
        $available = ExtensionHelper::getAvailableExtensions();
        $usedExtensions = Extension::query()
            ->get(['type', 'extension'])
            ->mapWithKeys(fn (Extension $extension) => [strtolower($extension->type . '_' . $extension->extension) => true]);

        $extensionIds = collect($available)
            ->map(fn ($extension) => $extension['meta']?->extensionId)
            ->filter(fn ($extensionId) => !empty($extensionId) && $extensionId !== 'unknown')
            ->map(fn ($extensionId) => (string) $extensionId)
            ->unique()
            ->values()
            ->all();

        $updatesByResourceId = collect($this->getExtensionUpdates($extensionIds))
            ->filter(fn ($update) => isset($update['resource_id']))
            ->keyBy(fn ($update) => (string) $update['resource_id']);

        return collect($available)->map(function ($ext) use ($updatesByResourceId, $usedExtensions) {
            $meta = $ext['meta'] ?? null;
            $name = $meta?->name ?? $ext['name'];
            $rawVersion = $meta?->version ?? null;

            $version = match (true) {
                $rawVersion === 'builtin' => config('app.version'),
                empty($rawVersion) => 'unknown',
                default => $rawVersion,
            };

            $extensionId = $meta?->extensionId;
            $hasExtensionId = !empty($extensionId) && $extensionId !== 'unknown';
            $updateData = $hasExtensionId
                ? $updatesByResourceId->get((string) $extensionId)
                : null;

            $latestVersion = $updateData['version'] ?? null;
            $cleanInstalledVersion = ($version !== 'unknown') ? ltrim($version, 'v') : 'unknown';
            $cleanLatestVersion = $latestVersion ? ltrim($latestVersion, 'v') : null;

            if ($rawVersion === 'builtin') {
                $status = 'Builtin';
            } elseif (!$hasExtensionId) {
                $status = 'Unknown';
            } elseif (!$this->isComparableVersion($cleanInstalledVersion) || !$this->isComparableVersion($cleanLatestVersion)) {
                $status = 'Error';
            } elseif (version_compare($cleanInstalledVersion, $cleanLatestVersion, '<')) {
                $status = 'Update available';
            } else {
                $status = 'Up to date';
            }

            return [
                'id' => $ext['type'] . '_' . $ext['name'],
                'name' => $name,
                'extension_name' => $ext['name'],
                'type' => ucfirst($ext['type']),
                'raw_type' => $ext['type'],
                'is_used' => $usedExtensions->has(strtolower($ext['type'] . '_' . $ext['name'])),
                'icon' => $meta?->icon ?? null,
                'author' => $meta?->author,
                'description' => $meta?->description ?? '',
                'version' => $cleanInstalledVersion,
                'is_builtin' => $rawVersion === 'builtin',
                'latest_version' => $cleanLatestVersion,
                'is_up_to_date' => $status === 'Up to date',
                'status' => $status,
                'url' => $meta?->url,
                'marketplace_url' => $updateData['url'] ?? null,
            ];
        })->all();
    }

    private function isComparableVersion(?string $version): bool
    {
        return $version !== null && preg_match('/^\d+(?:\.\d+)*(?:-[0-9A-Za-z.-]+)?(?:\+[0-9A-Za-z.-]+)?$/', $version) === 1;
    }

    private function getExtensionUpdates(array $extensionIds): array
    {
        if (empty($extensionIds)) {
            return [];
        }

        try {
            return Cache::remember('paymenter_marketplace_extension_updates' . md5(implode(',', $extensionIds)), now()->addHours(6), function () use ($extensionIds) {
                $response = Http::timeout(10)
                    ->withUserAgent('Paymenter/' . config('app.version') . ' (https://paymenter.org)')
                    ->get('https://api.paymenter.org/extensions/updates', [
                        'extensions' => implode(',', $extensionIds),
                    ]);

                if (!$response->successful()) {
                    return [];
                }

                return $response->json('updates', []);
            }) ?? [];
        } catch (\Throwable $e) {
            return [];
        }
    }

    public static function canAccess(): bool
    {
        return auth()->check() && auth()->user()->hasPermission('admin.updates.update');
    }
}
