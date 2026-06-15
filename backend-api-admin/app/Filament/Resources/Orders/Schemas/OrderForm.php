<?php

namespace App\Filament\Resources\Orders\Schemas;

use App\Enums\OrderStatus;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class OrderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required(),
                TextInput::make('order_number')
                    ->required(),
                TextInput::make('total_amount')
                    ->required()
                    ->numeric(),
                TextInput::make('discount_amount')
                    ->required()
                    ->numeric()
                    ->default(0.0),
                TextInput::make('final_amount')
                    ->required()
                    ->numeric(),
                TextInput::make('promo_code'),
                Select::make('status')
                    ->options(OrderStatus::class)
                    ->default('pending'),
                TextInput::make('payment_method'),
                Textarea::make('payment_url')
                    ->columnSpanFull(),
                TextInput::make('midtrans_order_id'),
                TextInput::make('midtrans_transaction_id'),
                DateTimePicker::make('paid_at'),
            ]);
    }
}
