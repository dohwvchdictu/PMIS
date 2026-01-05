<?php

namespace App\Filament\Resources\AuditResource\Pages;

use App\Filament\Resources\AuditResource;
use Filament\Actions;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;

class ViewAudit extends ViewRecord
{
    protected static string $resource = AuditResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Audit Information')
                    ->schema([
                        Infolists\Components\Grid::make(3)
                            ->schema([
                                Infolists\Components\TextEntry::make('user.name')
                                    ->label('User')
                                    ->default('System')
                                    ->icon('heroicon-o-user')
                                    ->iconColor('primary')
                                    ->size(Infolists\Components\TextEntry\TextEntrySize::Large)
                                    ->weight('bold'),

                                Infolists\Components\TextEntry::make('event')
                                    ->label('Action')
                                    ->badge()
                                    ->color(fn(string $state): string => match ($state) {
                                        'created' => 'success',
                                        'updated' => 'warning',
                                        'deleted' => 'danger',
                                        'restored' => 'info',
                                        default => 'gray',
                                    })
                                    ->icon(fn(string $state): string => match ($state) {
                                        'created' => 'heroicon-o-plus-circle',
                                        'updated' => 'heroicon-o-pencil',
                                        'deleted' => 'heroicon-o-trash',
                                        'restored' => 'heroicon-o-arrow-path',
                                        default => 'heroicon-o-information-circle',
                                    })
                                    ->size(Infolists\Components\TextEntry\TextEntrySize::Large),

                                Infolists\Components\TextEntry::make('created_at')
                                    ->label('Date & Time')
                                    ->dateTime('M d, Y h:i:s A')
                                    ->icon('heroicon-o-clock')
                                    ->iconColor('gray')
                                    ->size(Infolists\Components\TextEntry\TextEntrySize::Large),
                            ]),

                        Infolists\Components\Grid::make(2)
                            ->schema([
                                Infolists\Components\TextEntry::make('auditable_type')
                                    ->label('Model')
                                    ->formatStateUsing(fn($state) => str_replace('App\\Models\\', '', $state))
                                    ->icon('heroicon-o-cube')
                                    ->badge()
                                    ->color('gray'),

                                Infolists\Components\TextEntry::make('auditable_id')
                                    ->label('Record ID')
                                    ->icon('heroicon-o-hashtag')
                                    ->badge()
                                    ->color('gray'),

                                Infolists\Components\TextEntry::make('ip_address')
                                    ->label('IP Address')
                                    ->icon('heroicon-o-globe-alt')
                                    ->copyable()
                                    ->copyMessage('IP address copied!')
                                    ->badge()
                                    ->color('info'),

                                Infolists\Components\TextEntry::make('user_agent')
                                    ->label('User Agent')
                                    ->icon('heroicon-o-computer-desktop')
                                    ->limit(100)
                                    ->tooltip(fn($record) => $record->user_agent)
                                    ->default('N/A'),
                            ]),

                        Infolists\Components\TextEntry::make('url')
                            ->label('URL')
                            ->icon('heroicon-o-link')
                            ->copyable()
                            ->copyMessage('URL copied!')
                            ->columnSpanFull()
                            ->default('N/A'),
                    ])
                    ->columns(1)
                    ->collapsible(),

                Infolists\Components\Section::make('Field Changes')
                    ->description('Before and after values for modified fields')
                    ->schema([
                        Infolists\Components\ViewEntry::make('changes')
                            ->label('')
                            ->view('filament.infolists.audit-changes'),
                    ])
                    ->visible(fn($record) => !empty($record->old_values) || !empty($record->new_values))
                ,

                Infolists\Components\Section::make('Raw Data')
                    ->description('JSON representation of the audit data')
                    ->schema([
                        Infolists\Components\TextEntry::make('old_values')
                            ->label('Old Values (JSON)')
                            ->formatStateUsing(fn($state) => json_encode($state, JSON_PRETTY_PRINT))
                            ->copyable()
                            ->copyMessage('Old values copied!'),

                        Infolists\Components\TextEntry::make('new_values')
                            ->label('New Values (JSON)')
                            ->formatStateUsing(fn($state) => json_encode($state, JSON_PRETTY_PRINT))
                            ->copyable()
                            ->copyMessage('New values copied!'),
                    ])
                    ->columns(2)
                    ->collapsed()
                    ->collapsible(),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('back')
                ->label('Back to List')
                ->icon('heroicon-o-arrow-left')
                ->color('gray')
                ->url(AuditResource::getUrl('index')),
        ];
    }
}
