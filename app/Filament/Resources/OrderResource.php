<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Enums\OrderStatus;
use App\Filament\Resources\OrderResource\Pages\CreateOrder;
use App\Filament\Resources\OrderResource\Pages\EditOrder;
use App\Filament\Resources\OrderResource\Pages\ListOrders;
use App\Models\Menu;
use App\Models\Order;
use App\Services\TableService;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    protected static ?string $navigationGroup = 'Food';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Group::make()
                    ->schema([
                        Section::make('Order Information')
                            ->schema(static::getOrderDetailsFormSchema()),
                        Section::make('Order Items')
                            ->schema(static::getOrderItemsFormSchema()),
                    ])->columnSpan(2),

                Section::make()
                    ->schema([
                        Placeholder::make('Created At')
                            ->content(fn (Order $record): string => $record->created_at->diffForHumans()),
                        Placeholder::make('Updated At')
                            ->label('Last Modified At')
                            ->content(fn (Order $record): string => $record->updated_at->diffForHumans()),
                    ])->columnSpan(1)->hiddenOn('create'),
            ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
            ])
            ->filters([
                //
            ])
            ->actions([
                EditAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
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
            'index' => ListOrders::route('/'),
            'create' => CreateOrder::route('/create'),
            'edit' => EditOrder::route('/{record}/edit'),
        ];
    }

    public static function getOrderDetailsFormSchema(): array
    {
        return [
            Select::make('client_id')
                ->relationship('client', 'name')
                ->required()
                ->live()
                ->afterStateUpdated(fn (Set $set) => $set('table_id', null))
                ->searchable()
                ->preload()
                ->visible(Auth::user()->adminAccess()),

            Select::make('table_id')
                ->label('Table')
                ->options(fn (Get $get, TableService $tableService) => $tableService->getTableOptionsForOrder($get('client_id'))
                )
                ->required()
                ->searchable()
                ->preload(),

            ToggleButtons::make('status')
                ->inline()
                ->options(OrderStatus::class)
                ->required(),
        ];
    }

    public static function getOrderItemsFormSchema(): array
    {
        return [
            Repeater::make('orderItems')
                ->relationship()
                ->schema([
                    Select::make('menu_id')
                        ->relationship(
                            'menu',
                            'name',
                            fn (Builder $query) => Auth::user()->clientAccess() ? $query->getClientOwnMenu() : $query
                        )
                        ->required()
                        ->searchable()
                        ->preload(),
                    TextInput::make('quantity')
                        ->integer()
                        ->live()
                        ->afterStateUpdated(function (?int $state, Set $set, Get $get): void {
                            if ($state) {
                                $menu = Menu::findOrFail($get('menu_id'));
                                $price = $menu->price * $state;
                                $set('price', $price);
                            } else {
                                $set('price', null);
                            }
                        })
                        ->required(),
                    TextInput::make('price')
                        ->integer()
                        ->required(),
                ])->hiddenLabel()->columns(3),

            MarkdownEditor::make('note')
                ->columnSpanFull(),
        ];
    }
}
