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
                    ->circular()
                    ->defaultImageUrl(url('/images/default-product.png')),

                TextColumn::make('title')
                    ->label('Judul Produk')
                    ->searchable()
                    ->sortable()
                    ->limit(50)
                    ->tooltip(fn (Product $record): string => $record->title)
                    ->weight('medium'),

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
                        ->label('Edit'),
                    
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
                        }),
                    
                    DeleteAction::make()
                        ->label('Hapus')
                        ->modalHeading('Hapus Produk?')
                        ->modalDescription('Produk akan dihapus sementara dan dapat dipulihkan nanti.')
                        ->successNotificationTitle('Produk berhasil dihapus'),
                    
                    RestoreAction::make()
                        ->label('Pulihkan')
                        ->successNotificationTitle('Produk berhasil dipulihkan'),
                    
                    ForceDeleteAction::make()
                        ->label('Hapus Permanen')
                        ->modalHeading('Hapus Permanen?')
                        ->modalDescription('Data akan dihapus permanent dan tidak dapat dikembalikan!')
                        ->successNotificationTitle('Produk berhasil dihapus permanen'),
                ])
                ->icon('heroicon-m-ellipsis-vertical')
                ->tooltip('Aksi'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label('Hapus yang Dipilih')
                        ->modalHeading('Hapus Produk Terpilih?')
                        ->modalDescription('Produk yang dipilih akan dihapus sementara.')
                        ->successNotificationTitle('Produk berhasil dihapus'),
                    
                    RestoreBulkAction::make()
                        ->label('Pulihkan yang Dipilih')
                        ->successNotificationTitle('Produk berhasil dipulihkan'),
                    
                    ForceDeleteBulkAction::make()
                        ->label('Hapus Permanen')
                        ->modalHeading('Hapus Permanen?')
                        ->modalDescription('Data akan dihapus permanent dan tidak dapat dikembalikan!')
                        ->successNotificationTitle('Produk berhasil dihapus permanen'),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->emptyStateHeading('Belum ada Produk')
            ->emptyStateDescription('Buat produk digital pertama Anda untuk memulai.')
            ->emptyStateIcon('heroicon-o-shopping-bag');
    }
}
