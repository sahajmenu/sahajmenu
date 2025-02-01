<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MenuResource\Pages;
use App\Filament\Resources\MenuResource\Pages\CreateMenu;
use App\Models\Category;
use App\Models\Menu;
use CodeWithDennis\FilamentSelectTree\SelectTree;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class MenuResource extends Resource
{
    protected static ?string $model = Menu::class;

    protected static ?string $navigationIcon = 'heroicon-o-book-open';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->required()
                    ->string(),
                TextInput::make('price')
                    ->required()
                    ->numeric(),
                SelectTree::make('category_id')
                    ->label('Category')
                    ->withCount()
                    ->searchable()
                    ->relationship('category', 'name', 'parent_id')
                    ->enableBranchNode()
                    ->createOptionForm([
                        TextInput::make('name')
                            ->required(),
                        SelectTree::make('parent_id')
                            ->label('Parent Category')
                            ->withCount()
                            ->searchable()
                            ->relationship('parent', 'name', 'parent_id')
                            ->enableBranchNode(),
                    ])
                    ->createOptionUsing(function (array $data): int {
                        $data = resolve(CreateMenu::class)->injectClientIdBeforeCreate($data);

                        return Category::create($data)->getKey();
                    })->required(),
                Select::make('client_id')
                    ->relationship('client', 'name')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->hidden(auth()->user()->clientAccess()),
                FileUpload::make('images')
                    ->multiple()
                    ->image()
                    ->panelLayout('grid')
                    ->reorderable()
                    ->disk('menus')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name'),
                TextColumn::make('price'),
                TextColumn::make('category.name'),
                TextColumn::make('client.name'),
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
            'index' => Pages\ListMenus::route('/'),
            'create' => Pages\CreateMenu::route('/create'),
            'edit' => Pages\EditMenu::route('/{record}/edit'),
        ];
    }
}
