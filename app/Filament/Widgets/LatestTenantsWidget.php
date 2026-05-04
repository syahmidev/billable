<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Models\Tenant;
use Filament\Actions\Action;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestTenantsWidget extends BaseWidget
{
    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->heading('Latest Workspaces')
            ->query(Tenant::query()->latest()->limit(10))
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->limit(8)
                    ->tooltip(fn ($record) => $record->id),

                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('domains.domain')
                    ->label('Domain')
                    ->badge(),

                Tables\Columns\IconColumn::make('is_suspended')
                    ->label('Suspended')
                    ->boolean()
                    ->trueColor('danger')
                    ->falseColor('success'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Registered')
                    ->dateTime('M d, Y')
                    ->sortable(),
            ])
            ->actions([
                Action::make('suspend')
                    ->label('Suspend')
                    ->color('danger')
                    ->icon('heroicon-o-x-circle')
                    ->requiresConfirmation()
                    ->action(fn (Tenant $record) => $record->update(['is_suspended' => true]))
                    ->visible(fn (Tenant $record) => ! $record->is_suspended),

                Action::make('activate')
                    ->label('Activate')
                    ->color('success')
                    ->icon('heroicon-o-check-circle')
                    ->requiresConfirmation()
                    ->action(fn (Tenant $record) => $record->update(['is_suspended' => false]))
                    ->visible(fn (Tenant $record) => (bool) $record->is_suspended),
            ]);
    }
}
