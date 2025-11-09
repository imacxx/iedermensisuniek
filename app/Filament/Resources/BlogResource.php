<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BlogResource\Pages;
use App\Models\Blog;
use Filament\Forms;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Filament\Actions;
use UnitEnum;

class BlogResource extends Resource
{
    protected static ?string $model = Blog::class;

    protected static ?string $navigationLabel = 'Blogartikelen';

    protected static UnitEnum|string|null $navigationGroup = 'Inhoud';

    protected static ?string $modelLabel = 'Blogartikel';

    protected static ?string $pluralLabel = 'Blogartikelen';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Blog Details')
                ->schema([
                    Forms\Components\TextInput::make('title')
                        ->label('Titel')
                        ->required()
                        ->maxLength(255)
                        ->live(onBlur: true)
                        ->afterStateUpdated(function (?string $state, Set $set): void {
                            if (filled($state) && blank($set('slug'))) {
                                $set('slug', Str::slug($state));
                            }
                        }),
                    Forms\Components\TextInput::make('slug')
                        ->label('Slug')
                        ->required()
                        ->maxLength(255)
                        ->unique(ignoreRecord: true),
                    Forms\Components\Textarea::make('excerpt')
                        ->label('Samenvatting')
                        ->maxLength(500)
                        ->rows(3),
                    Forms\Components\RichEditor::make('body')
                        ->label('Inhoud')
                        ->columnSpanFull()
                        ->required(),
                    Forms\Components\Select::make('categories')
                        ->relationship('categories', 'name')
                        ->label('Categorieën')
                        ->multiple()
                        ->preload(),
                    Forms\Components\FileUpload::make('featured_image')
                        ->label('Uitgelichte afbeelding')
                        ->image()
                        ->directory('blogs')
                        ->enableOpen()
                        ->enableDownload(),
                    Forms\Components\DatePicker::make('published_at')
                        ->label('Publicatiedatum'),
                    Forms\Components\Toggle::make('is_published')
                        ->label('Gepubliceerd')
                        ->default(false),
                ])
                ->columns(2)
                ->columnSpanFull(),
            Section::make('SEO')
                ->schema([
                    Forms\Components\TextInput::make('seo_title')
                        ->label('SEO-titel')
                        ->maxLength(255),
                    Forms\Components\Textarea::make('seo_description')
                        ->label('SEO-beschrijving')
                        ->rows(3),
                    Forms\Components\TextInput::make('meta_keywords')
                        ->label('Meta-trefwoorden')
                        ->helperText('Komma-gescheiden trefwoorden')
                        ->maxLength(255),
                ])
                ->columns(1)
                ->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Titel')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('slug')
                    ->label('Slug')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_published')
                    ->label('Gepubliceerd')
                    ->boolean(),
                Tables\Columns\TextColumn::make('published_at')
                    ->label('Publicatiedatum')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('categories.name')
                    ->label('Categorieën')
                    ->badge()
                    ->separator(', '),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Bijgewerkt op')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_published')
                    ->label('Gepubliceerd')
                    ->boolean(),
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
            'index' => Pages\ListBlogs::route('/'),
            'create' => Pages\CreateBlog::route('/create'),
            'edit' => Pages\EditBlog::route('/{record}/edit'),
        ];
    }
}
