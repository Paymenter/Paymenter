<?php

namespace Paymenter\Extensions\Others\Knowledgebase\Admin\Resources\KnowledgeCategoryResource\RelationManagers;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Actions\Action;
use Paymenter\Extensions\Others\Knowledgebase\Admin\Resources\KnowledgeArticleResource;
use Illuminate\Support\Str;

class ArticlesRelationManager extends RelationManager
{
    protected static string $relationship = 'articles';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->label('Title')
                    ->required()
                    ->maxLength(255)
                    ->live(onBlur: true)
                    ->afterStateUpdated(function (Get $get, Set $set, ?string $old, ?string $state) {
                        if (($get('slug') ?? '') !== Str::slug($old)) {
                            return;
                        }

                        $set('slug', Str::slug((string) $state));
                    }),
                TextInput::make('slug')
                    ->label('Slug')
                    ->required()
                    ->maxLength(255)
                    ->unique(table: 'ext_kb_articles', column: 'slug', ignoreRecord: true),
                TextInput::make('summary')
                    ->label('Summary')
                    ->maxLength(500)
                    ->columnSpanFull(),
                Select::make('is_active')
                    ->label('Visible to clients')
                    ->options([
                        true => 'Yes',
                        false => 'No',
                    ])
                    ->default(true)
                    ->required(),
                DateTimePicker::make('published_at')
                    ->label('Publish At')
                    ->seconds(false),
                TextInput::make('sort_order')
                    ->label('Sort Order')
                    ->numeric()
                    ->default(0),
                TextInput::make('view_count')
                    ->label('View Count')
                    ->numeric()
                    ->minValue(0)
                    ->default(0),
                RichEditor::make('content')
                    ->label('Content')
                    ->columnSpanFull()
                    ->required(),
            ])
            ->columns(2);
    }

    public function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Title')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('published_at')
                    ->label('Published At')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Updated At')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),
                Tables\Columns\TextColumn::make('view_count')
                    ->label('Views')
                    ->sortable()
                    ->badge()
                    ->formatStateUsing(fn(?int $state) => $state ?? 0),
            ])
            ->defaultSort('sort_order')
            ->headerActions([
                Action::make('createArticle')
                    ->label('Create article')
                    ->icon('heroicon-o-plus')
                    ->url(fn() => KnowledgeArticleResource::getUrl('create', [
                        'category' => $this->getOwnerRecord()->getKey(),
                    ])),
            ])
            ->recordActions([
                Action::make('manage')
                    ->label('Manage')
                    ->icon('heroicon-o-pencil-square')
                    ->url(fn($record) => KnowledgeArticleResource::getUrl('edit', [
                        'record' => $record,
                    ])),
            ]);
    }
}
