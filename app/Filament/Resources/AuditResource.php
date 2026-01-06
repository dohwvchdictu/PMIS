<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AuditResource\Pages;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Facades\Filament;
use App\Models\Audit;

class AuditResource extends Resource
{
    protected static ?string $model = Audit::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $navigationGroup = 'System Administration';

    protected static ?string $navigationLabel = 'Audit Trail';

    protected static ?int $navigationSort = 100;

    protected static ?string $recordTitleAttribute = 'id';

    public static function canViewAny(): bool
    {
        $user = Filament::auth()->user();
        return $user && ($user->hasRole('super_admin') || $user->hasRole('Admin'));
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit($record): bool
    {
        return false;
    }

    public static function canDelete($record): bool
    {
        return false;
    }

    public static function canDeleteAny(): bool
    {
        return false;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('User')
                    ->searchable()
                    ->sortable()
                    ->default('System')
                    ->icon('heroicon-o-user')
                    ->iconColor('primary'),

                Tables\Columns\BadgeColumn::make('event')
                    ->label('Action')
                    ->colors([
                        'success' => 'created',
                        'warning' => 'updated',
                        'danger' => 'deleted',
                        'info' => 'restored',
                    ])
                    ->icons([
                        'heroicon-o-plus-circle' => 'created',
                        'heroicon-o-pencil' => 'updated',
                        'heroicon-o-trash' => 'deleted',
                        'heroicon-o-arrow-path' => 'restored',
                    ])
                    ->sortable(),

                Tables\Columns\TextColumn::make('auditable_type')
                    ->label('Model')
                    ->formatStateUsing(fn($state) => str_replace('App\\Models\\', '', $state))
                    ->searchable()
                    ->sortable()
                    ->description(fn($record) => 'ID: ' . $record->auditable_id)
                    ->icon('heroicon-o-cube')
                    ->iconColor('gray'),

                Tables\Columns\TextColumn::make('auditable_id')
                    ->label('Record ID')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),


                Tables\Columns\TextColumn::make('user_agent')
                    ->label('Browser')
                    ->formatStateUsing(function ($state) {
                        if (empty($state))
                            return 'N/A';

                        if (str_contains($state, 'Chrome'))
                            return 'Chrome';
                        if (str_contains($state, 'Firefox'))
                            return 'Firefox';
                        if (str_contains($state, 'Safari'))
                            return 'Safari';
                        if (str_contains($state, 'Edge'))
                            return 'Edge';

                        return 'Other';
                    })
                    ->icon('heroicon-o-computer-desktop')
                    ->iconColor('gray')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Date & Time')
                    ->dateTime('M d, Y h:i A')
                    ->timezone('Asia/Manila')
                    ->sortable()
                    ->icon('heroicon-o-clock')
                    ->iconColor('gray'),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('event')
                    ->label('Action')
                    ->options([
                        'created' => 'Created',
                        'updated' => 'Updated',
                        'deleted' => 'Deleted',
                        'restored' => 'Restored',
                    ])
                    ->multiple()
                    ->indicator('Action'),

                Tables\Filters\SelectFilter::make('auditable_type')
                    ->label('Model Type')
                    ->options(function () {
                        return Audit::query()
                            ->select('auditable_type')
                            ->distinct()
                            ->pluck('auditable_type', 'auditable_type')
                            ->map(fn($type) => str_replace('App\\Models\\', '', $type))
                            ->toArray();
                    })
                    ->searchable()
                    ->multiple()
                    ->indicator('Model'),

                Tables\Filters\SelectFilter::make('user_id')
                    ->label('User')
                    ->options(function () {
                        return \App\Models\User::query()
                            ->whereIn('id', Audit::query()->whereNotNull('user_id')->distinct()->pluck('user_id'))
                            ->pluck('name', 'id')
                            ->toArray();
                    })
                    ->searchable()
                    ->preload()
                    ->multiple()
                    ->indicator('User'),

                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('from')
                            ->label('From Date')
                            ->placeholder('Select start date'),
                        Forms\Components\DatePicker::make('until')
                            ->label('Until Date')
                            ->placeholder('Select end date'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'],
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['until'],
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['from'] ?? null) {
                            $indicators['from'] = 'From ' . \Carbon\Carbon::parse($data['from'])->toFormattedDateString();
                        }
                        if ($data['until'] ?? null) {
                            $indicators['until'] = 'Until ' . \Carbon\Carbon::parse($data['until'])->toFormattedDateString();
                        }
                        return $indicators;
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('View Details')
                    ->icon('heroicon-o-eye'),
            ])
            ->bulkActions([
                // No bulk actions for audit trail
            ])
            ->poll('30s')
            ->persistFiltersInSession()
            ->persistSortInSession()
            ->striped();
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAudits::route('/'),
            'view' => Pages\ViewAudit::route('/{record}'),
        ];
    }

    // public static function getNavigationBadge(): ?string
    // {
    //     $count = static::getModel()::whereDate('created_at', today())->count();
    //     return $count > 0 ? (string) $count : null;
    // }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'success';
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'New audit entries today';
    }
}
