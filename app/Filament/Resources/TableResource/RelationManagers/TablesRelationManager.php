<?php

namespace App\Filament\Resources\TableResource\RelationManagers;

use App\Actions\ImportBulkTableAction;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\Split;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ViewEntry;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Support\Colors\Color;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Validation\Rules\Unique;

class TablesRelationManager extends RelationManager
{
    protected static string $relationship = 'tables';
    protected static ?string $icon = 'heroicon-m-computer-desktop';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('number')
                    ->required()
                    ->integer()
                    ->minValue(1)
                    ->rules(['gt:0'])
                    ->prefix('Table')
                    ->autofocus(false)
                    ->unique(table: 'tables', column: 'number', ignoreRecord: true, modifyRuleUsing: function (Unique $rule) {
                        return $rule->where('client_id', $this->ownerRecord->id);
                    }),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('number')
            ->columns([
                TextColumn::make('number')
                    ->prefix('Table '),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make(),
                Action::make('Bulk Create')
                    ->color(Color::Green)
                    ->form([
                        Grid::make()
                            ->schema([
                                TextInput::make('total')
                                    ->required()
                                    ->integer()
                                    ->minValue(1)
                                    ->rules(['gt:0'])
                                    ->prefix('Table'),
                            ]),
                    ])->action(function (array $data): void {
                        resolve(ImportBulkTableAction::class)
                            ->handle($data['total'], $this->ownerRecord);
                        Notification::make()
                            ->title('Bulk Table Created')
                            ->success()
                            ->send();
                    }),
            ])
            ->actions([
                ActionGroup::make([
                    EditAction::make()
                        ->slideOver(),
                    ViewAction::make()
                        ->infolist([
                            Split::make([
                                Section::make('Table Information')
                                    ->schema([
                                        TextEntry::make('number'),
                                        TextEntry::make('client.name')
                                            ->label('Restaurant'),
                                        TextEntry::make('table_link')
                                            ->label('Table Link')
                                            ->copyable()
                                            ->copyMessage('Copied!')
                                            ->copyMessageDuration(1500)
                                            ->icon('heroicon-m-link'),
                                    ])
                                    ->icon('heroicon-o-information-circle'),
                                Section::make('QR Code')
                                    ->schema([
                                        ViewEntry::make('QR')
                                            ->view('filament.infolists.entries.qr'),
                                    ])
                                    ->icon('heroicon-m-qr-code')
                                    ->grow(false),
                            ])->from('md'),
                        ])->slideOver(),
                ]),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                ]),
            ])->defaultSort('number');
    }
}
