<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PageResource\Pages;
use App\Models\Page;
use Filament\Actions;
use Filament\Forms;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Builder;
use Filament\Forms\Components\Builder\Block;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use UnitEnum;

class PageResource extends Resource
{
    protected static ?string $model = Page::class;

    protected static ?string $navigationLabel = 'Pagina\'s';

    protected static UnitEnum|string|null $navigationGroup = 'Inhoud';

    protected static ?string $modelLabel = 'Pagina';

    protected static ?string $pluralLabel = 'Pagina\'s';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            // SEO section on top, full width
            Section::make('SEO')
                ->schema([
                    Forms\Components\TextInput::make('meta_title')
                        ->label('Meta Title')
                        ->maxLength(255),
                    Forms\Components\Textarea::make('meta_description')
                        ->label('Meta Description')
                        ->rows(3),
                    Forms\Components\TextInput::make('meta_keywords')
                        ->label('Meta Keywords')
                        ->helperText('Comma separated keywords')
                        ->maxLength(255),
                ])
                ->columns(1)
                ->columnSpanFull(),

            // Page details below, full width with 2 internal columns
            Section::make('Page Details')
                ->schema([
                    Forms\Components\TextInput::make('title')
                        ->required()
                        ->maxLength(255)
                        ->live(onBlur: true),
                    Forms\Components\TextInput::make('slug')
                        ->required()
                        ->maxLength(255)
                        ->unique(ignoreRecord: true),
                    Forms\Components\Toggle::make('is_published')
                        ->label('Published')
                        ->inline(false)
                        ->default(false),
                    Builder::make('blocks')
                        ->label('Content blocks')
                        ->columnSpanFull()
                        ->blocks([
                            Block::make('hero')
                                ->label('Hero')
                                ->schema([
                                    Forms\Components\TextInput::make('title')
                                        ->label('Title')
                                        ->required(),
                                    Forms\Components\Textarea::make('subtitle')
                                        ->label('Subtitle')
                                        ->rows(2),
                                    Forms\Components\TextInput::make('eyebrow')
                                        ->label('Eyebrow')
                                        ->maxLength(120),
                                    Forms\Components\FileUpload::make('background_image')
                                        ->label('Background image')
                                        ->directory('pages')
                                        ->image()
                                        ->enableOpen()
                                        ->enableDownload(),
                                ]),
                            Block::make('text')
                                ->label('Text')
                                ->schema([
                                    Forms\Components\RichEditor::make('content')
                                        ->label('Content')
                                        ->required()
                                        ->columnSpanFull(),
                                    Forms\Components\TextInput::make('eyebrow')
                                        ->label('Eyebrow')
                                        ->maxLength(120),
                                ]),
                            Block::make('cta')
                                ->label('Call to action')
                                ->schema([
                                    Forms\Components\TextInput::make('title')
                                        ->label('Title')
                                        ->required(),
                                    Forms\Components\Textarea::make('subtitle')
                                        ->label('Subtitle')
                                        ->rows(2),
                                    Forms\Components\TextInput::make('button_text')
                                        ->label('Button text')
                                        ->required(),
                                    Forms\Components\TextInput::make('button_url')
                                        ->label('Button URL')
                                        ->required()
                                        ->url(),
                                ]),
                        ])
                        ->helperText('Manage the page content using visual blocks.'),
                ])
                ->columns(2)
                ->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\IconColumn::make('is_published')
                    ->label('Published')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('slug')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Updated')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                Actions\EditAction::make(),
            ])
            ->toolbarActions([
                Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPages::route('/'),
            'create' => Pages\CreatePage::route('/create'),
            'edit' => Pages\EditPage::route('/{record}/edit'),
        ];
    }
}
