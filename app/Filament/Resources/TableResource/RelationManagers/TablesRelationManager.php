<?php

namespace App\Filament\Resources\TableResource\RelationManagers;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\Split;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ViewEntry;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TablesRelationManager extends RelationManager
{
    protected static string $relationship = 'tables';
    protected static ?string $icon = 'heroicon-m-computer-desktop';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('name'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->actions([
                ActionGroup::make([
                    EditAction::make()
                        ->slideOver(),
                    DeleteAction::make(),
                    ViewAction::make()
                        ->infolist([
                            Split::make([
                                Section::make('Table Information')
                                    ->schema([
                                        TextEntry::make('name'),
                                        TextEntry::make('client.name')
                                            ->label('Restaurant'),
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
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
