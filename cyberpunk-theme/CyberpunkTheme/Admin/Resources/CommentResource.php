<?php

namespace Paymenter\Extensions\Others\CyberpunkTheme\Admin\Resources;

use App\Models\Product;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Paymenter\Extensions\Others\CyberpunkTheme\Admin\Resources\CommentResource\Pages\EditComment;
use Paymenter\Extensions\Others\CyberpunkTheme\Admin\Resources\CommentResource\Pages\ListComments;
use Paymenter\Extensions\Others\CyberpunkTheme\Models\Comment;
use Paymenter\Extensions\Others\CyberpunkTheme\Models\Post;
use Paymenter\Extensions\Others\CyberpunkTheme\Support\Reviews;

/**
 * Moderación de comentarios (comunidad y productos).
 */
class CommentResource extends Resource
{
    protected static ?string $model = Comment::class;

    protected static string|\UnitEnum|null $navigationGroup = 'Extensions';

    protected static ?string $navigationLabel = 'Comunidad · Comentarios';

    protected static ?string $modelLabel = 'comentario';

    protected static ?string $pluralModelLabel = 'comentarios';

    protected static string|\BackedEnum|null $navigationIcon = 'ri-chat-3-line';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Textarea::make('content')->label('Contenido')->rows(5)->required()->columnSpanFull(),
            Toggle::make('approved')->label('Aprobado'),
            Toggle::make('featured')
                ->label('Destacar en el inicio')
                ->helperText('Sólo tiene efecto en las reseñas con estrellas.'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('user.name')->label('Usuario')->searchable()->sortable(),
                TextColumn::make('commentable_type')
                    ->label('En')
                    ->formatStateUsing(fn (?string $state) => match ($state) {
                        Post::class => 'Comunidad',
                        Product::class => 'Plan',
                        Comment::GENERAL => 'El servicio',
                        default => class_basename((string) $state),
                    })
                    ->badge(),
                TextColumn::make('commentable_id')->label('ID')->toggleable(),
                TextColumn::make('rating')
                    ->label('Estrellas')
                    ->sortable()
                    ->placeholder('—')
                    ->formatStateUsing(fn (?int $state) => $state
                        ? str_repeat('★', $state) . str_repeat('☆', 5 - $state)
                        : null),
                TextColumn::make('content')->label('Comentario')->limit(70)->wrap()->searchable(),
                TextColumn::make('parent_id')->label('Respuesta a')->placeholder('—'),
                TextColumn::make('likes_count')->label('Útil')->sortable()->toggleable(),
                IconColumn::make('featured')
                    ->label('En el inicio')
                    ->boolean()
                    ->trueIcon('ri-award-fill')
                    ->falseIcon('ri-subtract-line')
                    ->trueColor('warning')
                    ->falseColor('gray'),
                IconColumn::make('approved')->label('Aprobado')->boolean(),
                TextColumn::make('created_at')->label('Fecha')->dateTime('d/m/Y H:i')->sortable(),
            ])
            ->filters([
                TernaryFilter::make('approved')->label('Aprobado'),
                TernaryFilter::make('featured')->label('Destacada en el inicio'),
                TernaryFilter::make('rating')
                    ->label('Sólo reseñas con estrellas')
                    ->queries(
                        true: fn ($query) => $query->whereNotNull('rating'),
                        false: fn ($query) => $query->whereNull('rating'),
                        blank: fn ($query) => $query,
                    ),
                SelectFilter::make('commentable_type')
                    ->label('Origen')
                    ->options([
                        Post::class => 'Comunidad',
                        Product::class => 'Reseña de un plan',
                        Comment::GENERAL => 'Reseña del servicio',
                    ]),
            ])
            ->recordActions([
                Action::make('toggleFeatured')
                    ->label(fn (Comment $record) => $record->featured ? 'Quitar del inicio' : 'Destacar en el inicio')
                    ->icon('ri-award-fill')
                    ->color(fn (Comment $record) => $record->featured ? 'gray' : 'warning')
                    ->visible(fn (Comment $record) => $record->isReview())
                    ->action(function (Comment $record) {
                        $record->update(['featured' => !$record->featured]);
                        Reviews::flush();
                    }),
                Action::make('toggleApproved')
                    ->label(fn (Comment $record) => $record->approved ? 'Ocultar' : 'Aprobar')
                    ->icon(fn (Comment $record) => $record->approved ? 'ri-eye-off-line' : 'ri-check-line')
                    ->color(fn (Comment $record) => $record->approved ? 'warning' : 'success')
                    ->action(fn (Comment $record) => $record->update(['approved' => !$record->approved])),
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
            'index' => ListComments::route('/'),
            'edit' => EditComment::route('/{record}/edit'),
        ];
    }

    public static function canAccess(): bool
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        return $user && ($user->hasPermission('admin.settings.view') || $user->hasPermission('admin.cyberpunk.moderate'));
    }
}
