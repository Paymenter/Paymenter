<?php

namespace App\Admin\Pages;

use App\Admin\Clusters\Extensions;
use App\Admin\Resources\ExtensionResource;
use App\Helpers\ExtensionHelper;
use App\Services\Extensions\UploadExtensionService;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;

class Extension extends Page implements HasActions, HasTable
{
    use InteractsWithActions, InteractsWithTable;

    protected string $view = 'admin.pages.extension';

    // Cluster
    protected static ?string $cluster = Extensions::class;

    protected static string|\BackedEnum|null $navigationIcon = 'ri-download-2-line';

    protected static string|\BackedEnum|null $activeNavigationIcon = 'ri-download-2-fill';

    // Label for the navigation item
    protected static ?string $navigationLabel = 'Available Extensions';

    public function table(Table $table): Table
    {
        return $table
            ->records(function () {
                return collect(ExtensionHelper::getInstallableExtensions());
            })
            ->description('List of available extensions (not gateway or server extensions) that can be installed.')
            ->columns([
                TextColumn::make('meta.name')
                    ->label('Extension Name')
                    ->searchable()
                    ->sortable()
                    ->state(fn ($record) => $record['meta'] ? $record['meta']->name . ' (' . $record['meta']->author . ')' : $record['name']),
                TextColumn::make('meta.description')
                    ->label('Description')
                    ->searchable()
                    ->sortable(),
            ])
            ->recordActions([
                Action::make('install')
                    ->label('Install')
                    ->action(function ($record) {
                        $extension = \App\Models\Extension::create([
                            'name' => $record['name'],
                            'type' => $record['type'],
                            'extension' => $record['name'],
                        ]);
                        ExtensionHelper::call($extension, 'installed', mayFail: true);

                        Notification::make()
                            ->title('Extension Installed')
                            ->body('The extension has been successfully installed.')
                            ->success()
                            ->send();

                        $this->redirect(ExtensionResource::getUrl('edit', [
                            'record' => $extension->id,
                        ]), true);
                    })
                    ->requiresConfirmation(),
            ])
            ->headerActions([
                // Upload action to install new extensions
                Action::make('upload')
                    ->label('Upload Extension')
                    ->icon('ri-upload-2-line')
                    ->schema([
                        FileUpload::make('file')
                            ->label('Extension File')
                            ->required()
                            ->acceptedFileTypes(['application/zip', 'application/x-zip-compressed'])
                            ->directory('extensions/uploaded')
                            ->preserveFilenames()
                            ->maxSize(10240), // 10 MB
                    ])
                    ->action(function ($data, UploadExtensionService $service) {
                        try {
                            $service->handle(storage_path('app/' . $data['file']));
                        } catch (\Exception $e) {
                            // Handle the exception, e.g., log it or show an error message
                            Notification::make()
                                ->title('Failed to upload extension')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),
            ]);
    }
}
