<?php

namespace Paymenter\Extensions\Others\CyberpunkTheme\Admin\Resources;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Paymenter\Extensions\Others\CyberpunkTheme\Admin\Resources\PostResource\Pages\EditPost;
use Paymenter\Extensions\Others\CyberpunkTheme\Admin\Resources\PostResource\Pages\ListPosts;
use Paymenter\Extensions\Others\CyberpunkTheme\Models\Post;

/**
 * Moderación de las publicaciones de la comunidad.
 */
class PostResource extends Resource
{
    protected static ?string $model = Post::class;

    protected static string|\UnitEnum|null $navigationGroup = 'Extensions';

    protected static ?string $navigationLabel = 'Comunidad · Publicaciones';

    protected static ?string $modelLabel = 'publicación';

    protected static ?string $pluralModelLabel = 'publicaciones';

    protected static string|\BackedEnum|null $navigationIcon = 'ri-chat-smile-2-line';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('title')->label('Título')->maxLength(120),
            Textarea::make('content')->label('Contenido')->rows(6)->required()->columnSpanFull(),
            Toggle::make('approved')->label('Aprobada'),
            Toggle::make('pinned')->label('Destacada'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('user.name')->label('Usuario')->searchable()->sortable(),
                TextColumn::make('title')->label('Título')->searchable()->limit(30)->placeholder('—'),
                TextColumn::make('content')->label('Contenido')->limit(60)->wrap(),
                TextColumn::make('media_count')->label('Archivos')->counts('media'),
                TextColumn::make('likes_count')->label('Likes')->sortable(),
                TextColumn::make('comments_count')->label('Comentarios')->sortable(),
                IconColumn::make('approved')->label('Aprobada')->boolean(),
                IconColumn::make('pinned')->label('Destacada')->boolean(),
                TextColumn::make('created_at')->label('Fecha')->dateTime('d/m/Y H:i')->sortable(),
            ])
            ->filters([
                TernaryFilter::make('approved')->label('Aprobada'),
                TernaryFilter::make('pinned')->label('Destacada'),
            ])
            ->recordActions([
                Action::make('toggleApproved')
                    ->label(fn (Post $record) => $record->approved ? 'Ocultar' : 'Aprobar')
                    ->icon(fn (Post $record) => $record->approved ? 'ri-eye-off-line' : 'ri-check-line')
                    ->color(fn (Post $record) => $record->approved ? 'warning' : 'success')
                    ->action(fn (Post $record) => $record->update(['approved' => !$record->approved])),
                Action::make('togglePinned')
                    ->label(fn (Post $record) => $record->pinned ? 'Quitar destacado' : 'Destacar')
                    ->icon('ri-pushpin-line')
                    ->color('gray')
                    ->action(fn (Post $record) => $record->update(['pinned' => !$record->pinned])),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPosts::route('/'),
            'edit' => EditPost::route('/{record}/edit'),
        ];
    }

    public static function canAccess(): bool
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        return $user && ($user->hasPermission('admin.settings.view') || $user->hasPermission('admin.cyberpunk.moderate'));
    }
}
