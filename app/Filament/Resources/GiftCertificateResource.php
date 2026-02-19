<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GiftCertificateResource\Pages;
use App\Models\GiftCertificate;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class GiftCertificateResource extends Resource
{
    protected static ?string $model = GiftCertificate::class;
    protected static ?string $navigationIcon = 'heroicon-o-gift';
    protected static ?string $navigationLabel = 'Сертификаты';
    protected static ?string $modelLabel = 'Сертификат';
    protected static ?string $pluralModelLabel = 'Сертификаты';
    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')->label('Код')->searchable(),
                Tables\Columns\TextColumn::make('amount_rub')->label('Номинал')->suffix(' ₽'),
                Tables\Columns\IconColumn::make('is_used')->label('Использован')->boolean(),
                Tables\Columns\TextColumn::make('buyer.name')->label('Покупатель'),
                Tables\Columns\TextColumn::make('usedByOrder.id')->label('Использован в заказе #'),
                Tables\Columns\TextColumn::make('used_at')->label('Использован')->dateTime('d.m.Y'),
                Tables\Columns\TextColumn::make('created_at')->label('Куплен')->dateTime('d.m.Y'),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListGiftCertificates::route('/'),
        ];
    }
}
