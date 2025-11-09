<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SettingResource\Pages;
use App\Models\Setting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SettingResource extends Resource
{
    protected static ?string $model = Setting::class;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Hidden::make('key')
                    ->default('global_settings'),

                Forms\Components\Section::make('Site Settings')
                    ->schema([
                        Forms\Components\TextInput::make('value.site_title')
                            ->label('Site Title')
                            ->required()
                            ->maxLength(255)
                            ->default('My Website'),

                        Forms\Components\FileUpload::make('value.logo_image')
                            ->label('Logo Image')
                            ->image()
                            ->directory('settings')
                            ->maxSize(2048),

                        Forms\Components\FileUpload::make('value.favicon')
                            ->label('Favicon')
                            ->image()
                            ->directory('settings')
                            ->acceptedFileTypes(['image/x-icon', 'image/png'])
                            ->maxSize(1024),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Navigation')
                    ->schema([
                        Forms\Components\Repeater::make('value.primary_navigation')
                            ->label('Primary Navigation')
                            ->schema([
                                Forms\Components\TextInput::make('label')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('url')
                                    ->required()
                                    ->url()
                                    ->maxLength(500),
                            ])
                            ->columns(2)
                            ->default([]),

                        Forms\Components\Repeater::make('value.secondary_navigation')
                            ->label('Secondary Navigation (Footer)')
                            ->schema([
                                Forms\Components\TextInput::make('label')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('url')
                                    ->required()
                                    ->url()
                                    ->maxLength(500),
                            ])
                            ->columns(2)
                            ->default([]),
                    ]),

                Forms\Components\Section::make('Footer')
                    ->schema([
                        Forms\Components\Textarea::make('value.footer_text')
                            ->label('Footer Text')
                            ->rows(3)
                            ->default('© 2024 All rights reserved.'),

                        Forms\Components\Repeater::make('value.footer_links')
                            ->label('Footer Links')
                            ->schema([
                                Forms\Components\TextInput::make('label')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('url')
                                    ->required()
                                    ->url()
                                    ->maxLength(500),
                            ])
                            ->columns(2)
                            ->default([]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('key')
                    ->label('Setting Key')
                    ->searchable(),

                Tables\Columns\TextColumn::make('value.site_title')
                    ->label('Site Title'),

                Tables\Columns\TextColumn::make('value.logo_image')
                    ->label('Logo')
                    ->formatStateUsing(fn ($state) => $state ? '✓' : '✗'),

                Tables\Columns\TextColumn::make('value.favicon')
                    ->label('Favicon')
                    ->formatStateUsing(fn ($state) => $state ? '✓' : '✗'),

                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->modifyQueryUsing(function ($query) {
                return $query->where('key', 'global_settings');
            });
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
            'index' => Pages\ListSettings::route('/'),
            'create' => Pages\CreateSetting::route('/create'),
            'edit' => Pages\EditSetting::route('/{record}/edit'),
        ];
    }
}
