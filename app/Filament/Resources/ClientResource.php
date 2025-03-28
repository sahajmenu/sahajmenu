<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Actions\CreateClientPaymentAction;
use App\Enums\PaymentType;
use App\Enums\Plan;
use App\Enums\Status;
use App\Filament\Common\Actions\SuspendUnsuspendAction;
use App\Filament\Resources\ClientResource\Pages\CreateClient;
use App\Filament\Resources\ClientResource\Pages\EditClient;
use App\Filament\Resources\ClientResource\Pages\ListClients;
use App\Filament\Resources\ClientResource\Pages\ViewClient;
use App\Models\Client;
use App\Traits\AdminAccess;
use App\Traits\HasActiveIcon;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Infolists\Components\Section as InfolistSection;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ClientResource extends Resource
{
    use AdminAccess;
    use HasActiveIcon;

    protected static ?string $model = Client::class;

    protected static ?string $navigationIcon = 'heroicon-o-home-modern';

    protected static ?string $navigationGroup = 'Management';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Client Information')
                    ->icon('heroicon-m-information-circle')
                    ->description('You can update or edit the details here.')
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->string()
                            ->maxLength(255),
                        TextInput::make('subdomain')
                            ->required()
                            ->string()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                        TextInput::make('address')
                            ->string()
                            ->maxLength(255),
                        TextInput::make('phone')
                            ->tel(),
                    ])->columns(2),
                Section::make('Client Logo')
                    ->icon('heroicon-m-photo')
                    ->schema([
                        FileUpload::make('logo')
                            ->disk('public')
                            ->directory('logos')
                            ->columnSpanFull(),
                    ]),
            ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name'),
                TextColumn::make('plan')
                    ->badge()
                    ->color(fn (Plan $state): string => $state->color())
                    ->formatStateUsing(fn (Plan $state): string => $state->display()),
                TextColumn::make('latestStatus.status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (Status $state): string => $state->color())
                    ->formatStateUsing(fn (Status $state): string => $state->display()),
                TextColumn::make('expires_at')
                    ->date(),
            ])
            ->filters([
                //
            ])
            ->actions([
                ActionGroup::make([
                    EditAction::make(),
                    ViewAction::make(),
                    ...SuspendUnsuspendAction::make(),
                    Action::make('Renew')
                        ->icon('heroicon-o-banknotes')
                        ->form([
                            TextInput::make('month')
                                ->required()
                                ->helperText('It is subscription month')
                                ->integer()
                                ->minValue(1),
                            Select::make('type')
                                ->options(PaymentType::class)
                                ->required()
                                ->searchable(),
                            TextInput::make('amount')
                                ->required()
                                ->integer()
                                ->prefixIcon('heroicon-o-currency-rupee')
                                ->minValue(1),
                            TextArea::make('note')
                                ->string()
                                ->maxLength(255),
                            FileUpload::make('statement')
                                ->disk('public')
                                ->directory('statements')
                                ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png'])
                                ->maxSize(5120),
                        ])->action(fn (Client $record, array $data, CreateClientPaymentAction $clientPayment) => $clientPayment->handle($record, $data)),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListClients::route('/'),
            'create' => CreateClient::route('/create'),
            'view' => ViewClient::route('/{record}'),
            'edit' => EditClient::route('/{record}/edit'),
        ];
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                InfolistSection::make('Client Information')
                    ->schema([
                        TextEntry::make('name'),
                    ]),
            ]);
    }

    public static function shouldRegisterNavigation(): bool
    {
        return self::hasAdminAccess();
    }
}
