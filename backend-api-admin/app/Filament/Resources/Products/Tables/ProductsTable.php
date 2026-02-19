<?php

namespace App\Filament\Resources\Products\Tables;

use App\Models\Product;
use App\Services\Content\ProductService;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class ProductsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('thumbnail_url')
                    ->label('Thumbnail')
                    ->imageHeight(50)
                    ->defaultImageUrl(url('/images/default-product.png')),

                TextColumn::make('title')
                    ->label('Judul Produk')
                    ->searchable()
                    ->sortable()
                    ->limit(50)
                    ->weight('medium')
                    ->wrap(),

                TextColumn::make('category.name')
                    ->label('Kategori')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('info')
                    ->placeholder('Tanpa Kategori'),

                TextColumn::make('price')
                    ->label('Harga')
                    ->money('IDR')
                    ->sortable()
                    ->badge()
                    ->color('success'),

                TextColumn::make('discount_percentage')
                    ->label('Diskon')
                    ->getStateUsing(fn (Product $record): ?string => 
                        $record->discount_percentage ? $record->discount_percentage . '%' : null
                    )
                    ->badge()
                    ->color('danger')
                    ->placeholder('-'),

                TextColumn::make('total_purchases')
                    ->label('Terjual')
                    ->badge()
                    ->color('primary')
                    ->icon('heroicon-o-shopping-cart')
                    ->alignCenter(),

                IconColumn::make('is_active')
                    ->label('Status')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->alignCenter(),

                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('Diupdate')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('deleted_at')
                    ->label('Dihapus')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->badge()
                    ->color('danger')
                    ->icon('heroicon-o-trash')
                    ->placeholder('-')
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('category_id')
                    ->label('Kategori')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload()
                    ->placeholder('Semua Kategori'),

                TernaryFilter::make('is_active')
                    ->label('Status Aktif')
                    ->placeholder('Semua Status')
                    ->trueLabel('Aktif')
                    ->falseLabel('Tidak Aktif')
                    ->native(false),
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make()
                        ->label('Lihat'),
                    
                    EditAction::make()
                        ->label('Edit')
                        ->visible(fn (Product $record): bool => $record->deleted_at === null),
                    
                    Action::make('toggle_active')
                        ->label(fn (Product $record) => $record->is_active ? 'Nonaktifkan' : 'Aktifkan')
                        ->icon(fn (Product $record) => $record->is_active ? 'heroicon-o-eye-slash' : 'heroicon-o-eye')
                        ->color(fn (Product $record) => $record->is_active ? 'warning' : 'success')
                        ->requiresConfirmation()
                        ->modalHeading(fn (Product $record) => $record->is_active ? 'Nonaktifkan Produk?' : 'Aktifkan Produk?')
                        ->modalDescription('Produk yang tidak aktif tidak akan ditampilkan di halaman publik.')
                        ->action(function (Product $record) {
                            $service = app(ProductService::class);
                            $service->toggleActiveStatus($record);
                            
                            Notification::make()
                                ->title($record->is_active ? 'Produk Diaktifkan' : 'Produk Dinonaktifkan')
                                ->success()
                                ->send();
                        })
                        ->visible(fn (Product $record): bool => $record->deleted_at === null),
                    
                    DeleteAction::make()
                        ->label('Hapus')
                        ->modalHeading('Hapus Produk?')
                        ->modalDescription('Produk akan dihapus sementara dan dapat dipulihkan nanti.')
                        ->successNotificationTitle('Produk berhasil dihapus')
                        ->visible(fn (Product $record): bool => $record->deleted_at === null),
                    
                    RestoreAction::make()
                        ->label('Pulihkan')
                        ->successNotificationTitle('Produk berhasil dipulihkan')
                        ->visible(fn (Product $record): bool => $record->deleted_at !== null),
                    
                    ForceDeleteAction::make()
                        ->label('Hapus Permanen')
                        ->modalHeading('Hapus Permanen?')
                        ->modalDescription('Data akan dihapus permanent dan tidak dapat dikembalikan!')
                        ->successNotificationTitle('Produk berhasil dihapus permanen')
                        ->visible(fn (Product $record): bool => $record->deleted_at !== null),
                ])
                ->icon('heroicon-m-ellipsis-vertical')
                ->tooltip('Aksi'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label('Hapus Semua')
                        ->modalHeading('Hapus Produk Terpilih?')
                        ->modalDescription('Produk yang dipilih akan dihapus sementara.')
                        ->successNotificationTitle('Produk berhasil dihapus'),
                    
                    RestoreBulkAction::make()
                        ->label('Pulihkan Semua')
                        ->modalHeading('Pulihkan Produk Terpilih?')
                        ->modalDescription('Produk yang dipilih akan dipulihkan.')
                        ->successNotificationTitle('Produk berhasil dipulihkan'),
                    
                    ForceDeleteBulkAction::make()
                        ->label('Hapus Permanen Semua')
                        ->modalHeading('Hapus Permanen Produk Terpilih?')
                        ->modalDescription('Data akan dihapus permanent dan tidak dapat dikembalikan!')
                        ->successNotificationTitle('Produk berhasil dihapus permanen'),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordClasses(fn (Product $record) => $record->deleted_at ? 'opacity-50 bg-gray-50 dark:bg-gray-900' : null)
            ->emptyStateHeading('Belum ada Produk')
            ->emptyStateDescription('Buat produk digital pertama Anda untuk memulai.')
            ->emptyStateIcon('heroicon-o-shopping-bag');
    }
}
