<?php

namespace App\Admin\Pages;

use App\Admin\Clusters\Extensions;
use App\Admin\Resources\ExtensionResource;
use App\Admin\Resources\GatewayResource;
use App\Admin\Resources\ServerResource;
use App\Helpers\ExtensionHelper;
use App\Services\Extensions\UploadExtensionService;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Livewire\Attributes\Url;

class Extension extends Page implements HasActions, HasTable
{
    use InteractsWithActions, InteractsWithTable;

    protected string $view = 'admin.pages.extension';

    // Cluster
    protected static ?string $cluster = Extensions::class;

    protected static string|\BackedEnum|null $navigationIcon = 'ri-download-2-line';

    protected static string|\BackedEnum|null $activeNavigationIcon = 'ri-download-2-fill';

    public static function getNavigationLabel(): string
    {
        return __('extensions.available_extensions');
    }

    public function getTitle(): string|\Illuminate\Contracts\Support\Htmlable
    {
        return __('extensions.available_extensions');
    }

    #[Url(except: 'marketplace', as: 'tab')]
    public string $activeTab = 'marketplace';

    private const PER_PAGE = 12;

    #[Url(except: '', as: 'q')]
    public string $search = '';

    #[Url(except: 'all')]
    public string $filter = 'all';

    public int $loadedItems = self::PER_PAGE;

    public ?array $allExtensions = [];

    public ?string $error = null;

    public function mount(): void
    {
        try {
            $this->allExtensions = Cache::remember('paymenter_marketplace_extensions', now()->addHours(6), function () {
                $response = Http::timeout(15)
                    ->withUserAgent('Paymenter/' . config('app.version') . ' (https://paymenter.org)')
                    ->get('https://api.paymenter.org/extensions', ['limit' => 999]);
                if (!$response->successful()) {
                    logger()->error('Paymenter Marketplace API request failed', ['status' => $response->status(), 'body' => $response->body()]);

                    return null;
                }

                return $response->json('extensions', []);
            });
            if (is_null($this->allExtensions)) {
                $this->error = __('extensions.marketplace_unavailable');
            }
        } catch (ConnectionException $e) {
            $this->error = __('extensions.marketplace_connection_failed');
            logger()->error('Paymenter Marketplace API connection failed: ' . $e->getMessage());
        }
    }

    public function updatedSearch(): void
    {
        $this->resetLoadedItems();
    }

    public function updatedFilter(): void
    {
        $this->resetLoadedItems();
    }

    public function loadMore(): void
    {
        $this->loadedItems += self::PER_PAGE;
    }

    private function resetLoadedItems(): void
    {
        $this->loadedItems = self::PER_PAGE;
    }

    public function getFilteredExtensionsProperty(): Collection
    {
        if (is_null($this->allExtensions)) {
            return collect();
        }

        return collect($this->allExtensions)
            ->when($this->search, fn (Collection $c) => $c->filter(fn ($i) => stripos($i['name'], $this->search) !== false))
            ->when($this->filter !== 'all', fn (Collection $c) => $c->where('type', $this->filter));
    }

    public function getCanLoadMoreProperty(): bool
    {
        return $this->filteredExtensions->count() > $this->loadedItems;
    }

    public function getExtensionsProperty(): Collection
    {
        return $this->filteredExtensions->take($this->loadedItems);
    }

    public function table(Table $table): Table
    {
        return $table
            ->records(fn () => collect(ExtensionHelper::getInstallableExtensions()))
            ->description(__('extensions.table_description'))
            ->columns([
                ImageColumn::make('meta.icon')
                    ->label(__('extensions.icon'))
                    ->state(fn ($record) => $record['meta']?->icon ? $record['meta']->icon : 'ri-puzzle-fill'),
                TextColumn::make('meta.name')
                    ->label(__('extensions.extension_name'))
                    ->searchable()
                    ->sortable()
                    ->state(fn ($record) => $record['meta'] ? $record['meta']->name . ' (' . $record['meta']->author . ')' : $record['name']),
                TextColumn::make('meta.description')
                    ->label(__('extensions.description'))
                    ->searchable()
                    ->sortable(),
            ])
            ->recordActions([
                Action::make('install')
                    ->label(__('extensions.install'))
                    ->action(function ($record) {
                        $extension = \App\Models\Extension::create([
                            'name' => $record['name'],
                            'type' => $record['type'],
                            'extension' => $record['name'],
                        ]);
                        ExtensionHelper::call($extension, 'installed', mayFail: true);

                        Notification::make()
                            ->title(__('extensions.installed_success_title'))
                            ->body(__('extensions.installed_success_body'))
                            ->success()
                            ->send();

                        $this->redirect(ExtensionResource::getUrl('edit', ['record' => $extension->id]), true);
                    })
                    ->requiresConfirmation(),
            ])
            ->headerActions([
                Action::make('upload')
                    ->label(__('extensions.upload_extension'))
                    ->icon('ri-upload-2-line')
                    ->form([
                        FileUpload::make('file')
                            ->label(__('extensions.extension_file'))
                            ->required()
                            ->acceptedFileTypes(['application/zip', 'application/x-zip-compressed'])
                            ->directory('extensions/uploaded')
                            ->preserveFilenames()
                            ->maxSize(10240), // 10 MB
                    ])
                    ->action(function (array $data, UploadExtensionService $service) {
                        try {
                            $type = $service->handle(storage_path('app/' . $data['file']));
                            // Handle the exception, e.g., log it or show an error message
                            switch ($type) {
                                case 'server':
                                    Notification::make()
                                        ->title(__('extensions.uploaded_success'))
                                        ->body(__('extensions.uploaded_server_body', ['url' => ServerResource::getUrl()]))
                                        ->success()
                                        ->send();
                                    break;
                                case 'gateway':
                                    Notification::make()
                                        ->title(__('extensions.uploaded_success'))
                                        ->body(__('extensions.uploaded_gateway_body', ['url' => GatewayResource::getUrl()]))
                                        ->success()
                                        ->send();
                                    break;
                                default:
                                    // Unknown type, just stay on the page
                                    Notification::make()
                                        ->title(__('extensions.uploaded_success'))
                                        ->body(__('extensions.uploaded_generic_body'))
                                        ->success()
                                        ->send();
                                    break;
                            }
                        } catch (\Exception $e) {
                            Notification::make()
                                ->title(__('extensions.upload_failed'))
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),
            ]);
    }

    public static function canAccess(): bool
    {
        return Auth::user()->hasPermission('admin.extensions.viewAny') && Auth::user()->hasPermission('admin.extensions.install');
    }
}
