<?php

namespace App\Filament\Resources\AuditResource\Pages;

use App\Filament\Resources\AuditResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListAudits extends ListRecords
{
    protected static string $resource = AuditResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('refresh')
                ->label('Refresh')
                ->icon('heroicon-o-arrow-path')
                ->color('gray')
                ->action(fn () => $this->redirect(request()->header('Referer'))),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All')
                ->icon('heroicon-o-clipboard-document-list')
                ->badge(fn () => \OwenIt\Auditing\Models\Audit::count()),

            'today' => Tab::make('Today')
                ->icon('heroicon-o-calendar')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereDate('created_at', today()))
                ->badge(fn () => \OwenIt\Auditing\Models\Audit::whereDate('created_at', today())->count())
                ->badgeColor('success'),

            'this_week' => Tab::make('This Week')
                ->icon('heroicon-o-calendar-days')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]))
                ->badge(fn () => \OwenIt\Auditing\Models\Audit::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count())
                ->badgeColor('info'),

            'this_month' => Tab::make('This Month')
                ->icon('heroicon-o-calendar')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereMonth('created_at', now()->month))
                ->badge(fn () => \OwenIt\Auditing\Models\Audit::whereMonth('created_at', now()->month)->count())
                ->badgeColor('warning'),

            'created' => Tab::make('Created')
                ->icon('heroicon-o-plus-circle')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('event', 'created'))
                ->badge(fn () => \OwenIt\Auditing\Models\Audit::where('event', 'created')->count())
                ->badgeColor('success'),

            'updated' => Tab::make('Updated')
                ->icon('heroicon-o-pencil')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('event', 'updated'))
                ->badge(fn () => \OwenIt\Auditing\Models\Audit::where('event', 'updated')->count())
                ->badgeColor('warning'),

            'deleted' => Tab::make('Deleted')
                ->icon('heroicon-o-trash')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('event', 'deleted'))
                ->badge(fn () => \OwenIt\Auditing\Models\Audit::where('event', 'deleted')->count())
                ->badgeColor('danger'),
        ];
    }
}
