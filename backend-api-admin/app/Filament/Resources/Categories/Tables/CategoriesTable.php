<?php

namespace App\Filament\Resources\Categories\Tables;

use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use App\Services\Content\CategoryService;
use Filament\Tables\Table;

class CategoriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nama Kategori')
                    ->searchable()
                    ->sortable()
                    ->limit(50)
                    ->weight('medium')
                    ->wrap(),

                TextColumn::make('slug')
                    ->label('Slug')
                    ->searchable()
                    ->copyable()
                    ->badge()
                    ->color('gray')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('type')
                    ->label('Tipe')
                    ->badge()
                    ->color(fn ($state) => match($state instanceof \App\Enums\CategoryType ? $state->value : $state) {
                        'course' => 'success',
                        'product' => 'warning',
                        'article' => 'info',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => $state instanceof \App\Enums\CategoryType ? $state->label() : $state)
                    ->sortable(),

                TextColumn::make('courses_count')
                    ->label('Kursus')
                    ->counts('courses')
                    ->badge()
                    ->color('success')
                    ->alignCenter()
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('products_count')
                    ->label('Produk')
                    ->counts('products')
                    ->badge()
                    ->color('warning')
                    ->alignCenter()
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('articles_count')
                    ->label('Artikel')
                    ->counts('articles')
                    ->badge()
                    ->color('info')
                    ->alignCenter()
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('description')
                    ->label('Deskripsi')
                    ->limit(50)
                    ->wrap()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('Diperbarui')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->label('Tipe Kategori')
                    ->options([
                        'course' => 'Kategori Kursus',
                        'product' => 'Kategori Produk',
                        'article' => 'Kategori Artikel',
                    ])
                    ->placeholder('Semua Tipe'),
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make()
                        ->label('Lihat'),
                    EditAction::make()
                        ->label('Edit'),
                    DeleteAction::make()
                        ->label('Hapus')
                        ->modalHeading('Hapus Kategori?')
                        ->modalDescription('Data kategori akan dihapus permanent dan tidak dapat dikembalikan, kategori yang masih terhubung dengan konten lain tidak dapat dihapus.')
                        ->before(function ($record, $action) {
                            $categoryService = app(CategoryService::class);
                            if (!$categoryService->canBeDeleted($record)) {
                                Notification::make()
                                    ->danger()
                                    ->title('Tidak Dapat Menghapus Kategori')
                                    ->body($categoryService->getRelatedContentMessage($record))
                                    ->send();
                                $action->cancel();
                            }
                        })
                        ->action(function ($record) {
                            $categoryService = app(CategoryService::class);
                            $categoryService->deleteCategory($record);
                        })
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title('Kategori berhasil dihapus')
                                ->body('Data kategori berhasil dihapus.')
                        ),
                ])
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label('Hapus yang Dipilih')
                        ->modalHeading('Hapus Webinar Terpilih?')
                        ->modalDescription('Webinar yang dipilih akan dihapus sementara.')
                        ->successNotificationTitle('Webinar berhasil dihapus'),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->emptyStateHeading('Belum ada kategori')
            ->emptyStateDescription('Mulai membuat kategori pertama untuk mengorganisir konten.')
            ->emptyStateIcon('heroicon-o-tag');
    }
}