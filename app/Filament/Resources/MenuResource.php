<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MenuResource\Pages\CreateMenu;
use App\Filament\Resources\MenuResource\Pages\EditMenu;
use App\Filament\Resources\MenuResource\Pages\ListMenus;
use App\Models\Category;
use App\Models\Menu;
use App\Traits\HasActiveIcon;
use CodeWithDennis\FilamentSelectTree\SelectTree;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class MenuResource extends Resource
{
    use HasActiveIcon;

    protected static ?string $model = Menu::class;

    protected static ?string $navigationIcon = 'heroicon-o-book-open';

    protected static ?string $navigationGroup = 'Food';

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
                    ->required()
                    ->maxSize(5120)
                    ->multiple()
                    ->directory(function (Get $get) {
                        return $get('client_id') ?? auth()->user()->client->id;
                    })
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
                TextColumn::make('name')->searchable(),
                TextColumn::make('price'),
                TextColumn::make('category.name')->searchable(),
                TextColumn::make('client.name'),
            ])->searchPlaceholder('Name,Category')
            ->filters([
                //
            ])
            ->actions([
                EditAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
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
            'index' => ListMenus::route('/'),
            'create' => CreateMenu::route('/create'),
            'edit' => EditMenu::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->getClientOwnMenu();
    }
}
