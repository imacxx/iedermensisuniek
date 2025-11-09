<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BlogResource\Pages;
use App\Models\Blog;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class BlogResource extends Resource
{
    protected static ?string $model = Blog::class;


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('slug')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),

                Forms\Components\Textarea::make('excerpt')
                    ->maxLength(500),

                Forms\Components\RichEditor::make('content')
                    ->required(),

                Forms\Components\Select::make('category_id')
                    ->relationship('category', 'name')
                    ->required()
                    ->searchable(),

                Forms\Components\FileUpload::make('featured_image')
                    ->image()
                    ->directory('blogs'),

                Forms\Components\DatePicker::make('published_at'),

                Forms\Components\Toggle::make('is_published')
                    ->label('Published'),

                Forms\Components\TextInput::make('meta_title')
                    ->label('Meta Title')
                    ->maxLength(255),

                Forms\Components\Textarea::make('meta_description')
                    ->label('Meta Description')
                    ->maxLength(500),

                Forms\Components\TextInput::make('meta_keywords')
                    ->label('Meta Keywords')
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable(),

                Tables\Columns\TextColumn::make('slug')
                    ->searchable(),

                Tables\Columns\TextColumn::make('category.name')
                    ->label('Category'),

                Tables\Columns\IconColumn::make('is_published')
                    ->boolean(),

                Tables\Columns\TextColumn::make('published_at')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category')
                    ->relationship('category', 'name'),
                Tables\Filters\TernaryFilter::make('is_published'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
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
