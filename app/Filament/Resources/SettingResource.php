<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SettingResource\Pages;
use App\Models\Setting;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use UnitEnum;

class SettingResource extends Resource
{
    protected static ?string $model = Setting::class;

    protected static ?string $navigationLabel = 'Instellingen';

    protected static UnitEnum|string|null $navigationGroup = 'Configuratie';

    protected static ?string $modelLabel = 'Instelling';

    protected static ?string $pluralLabel = 'Globale instellingen';

    protected static ?string $label = 'Instelling';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Forms\Components\Hidden::make('key')
                ->default('global_settings'),
            Section::make('Site-identiteit')
                ->schema([
                    Forms\Components\TextInput::make('value.site_title')
                        ->label('Website titel')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\FileUpload::make('value.logo_image')
                        ->label('Logo-afbeelding')
                        ->image()
                        ->directory('settings')
                        ->maxSize(2048),
                    Forms\Components\FileUpload::make('value.favicon')
                        ->label('Favicon')
                        ->image()
                        ->directory('settings')
                        ->acceptedFileTypes(['image/png', 'image/x-icon'])
                        ->maxSize(1024),
                ])
                ->columns(3)
                ->columnSpanFull(),
            Section::make('Navigatie')
                ->schema([
                    Forms\Components\Repeater::make('value.primary_navigation')
                        ->label('Hoofdmenu')
                        ->schema([
                            Forms\Components\TextInput::make('label')
                                ->label('Naam')
                                ->required()
                                ->maxLength(255),
                            Forms\Components\TextInput::make('url')
                                ->label('URL')
                                ->required()
                                ->maxLength(500),
                        ])
                        ->columns(2)
                        ->default([])
                        ->reorderable()
                        ->addActionLabel('Link toevoegen'),
                    Forms\Components\Repeater::make('value.secondary_navigation')
                        ->label('Voetmenu')
                        ->schema([
                            Forms\Components\TextInput::make('label')
                                ->label('Naam')
                                ->required()
                                ->maxLength(255),
                            Forms\Components\TextInput::make('url')
                                ->label('URL')
                                ->required()
                                ->maxLength(500),
                        ])
                        ->columns(2)
                        ->default([])
                        ->reorderable()
                        ->addActionLabel('Link toevoegen'),
                ])
                ->columns(1)
                ->columnSpanFull(),
            Section::make('Footer')
                ->schema([
                    Forms\Components\Textarea::make('value.footer_text')
                        ->label('Voettekst')
                        ->rows(3),
                    Forms\Components\Repeater::make('value.footer_links')
                        ->label('Links in voettekst')
                        ->schema([
                            Forms\Components\TextInput::make('label')
                                ->label('Naam')
                                ->required()
                                ->maxLength(255),
                            Forms\Components\TextInput::make('url')
                                ->label('URL')
                                ->required()
                                ->maxLength(500),
                        ])
                        ->columns(2)
                        ->default([])
                        ->reorderable()
                        ->addActionLabel('Link toevoegen'),
                ])
                ->columns(1)
                ->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('key')
                    ->label('Sleutel')
                    ->searchable(),
                Tables\Columns\TextColumn::make('value.site_title')
                    ->label('Website titel')
                    ->default('—'),
                Tables\Columns\IconColumn::make('value.logo_image')
                    ->label('Logo')
                    ->boolean(fn ($state) => filled($state))
                    ->trueIcon('heroicon-o-check')
                    ->falseIcon('heroicon-o-x-mark'),
                Tables\Columns\IconColumn::make('value.favicon')
                    ->label('Favicon')
                    ->boolean(fn ($state) => filled($state))
                    ->trueIcon('heroicon-o-check')
                    ->falseIcon('heroicon-o-x-mark'),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Bijgewerkt op')
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
            ])
            ->modifyQueryUsing(fn ($query) => $query->where('key', 'global_settings'));
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSettings::route('/'),
            'create' => Pages\CreateSetting::route('/create'),
            'edit' => Pages\EditSetting::route('/{record}/edit'),
        ];
    }
}
