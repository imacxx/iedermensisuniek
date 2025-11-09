<?php

namespace App\Filament\Pages;

use App\Models\Page;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationLabel = 'Overzicht';

    protected static ?string $title = 'Overzicht';

    /**
     * @return array<int, Action>
     */
    protected function getHeaderActions(): array
    {
        return [
            Action::make('editHomePage')
                ->label('Edit Home Page')
                ->icon('heroicon-o-pencil-square')
                ->color('primary')
                ->url('/')
                ->openUrlInNewTab()
                ->visible(function (): bool {
                    $user = Filament::auth()->user();
                    if (! $user) {
                        return false;
                    }

                    $page = Page::firstWhere('slug', 'home');

                    return $page ? $user->can('update', $page) : false;
                }),
        ];
    }
}
