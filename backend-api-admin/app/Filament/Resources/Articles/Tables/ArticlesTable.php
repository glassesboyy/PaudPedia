<?php

namespace App\Filament\Resources\Articles\Tables;

use App\Models\Article;
use App\Services\Content\ArticleService;
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

class ArticlesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('featured_image_url')
                    ->label('Gambar')
                    ->defaultImageUrl(url('/images/placeholder.png')),

                TextColumn::make('title')
                    ->label('Judul')
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

                TextColumn::make('author.name')
                    ->label('Penulis')
                    ->badge()
                    ->color('success')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('tags')
                    ->label('Tags')
                    ->badge()
                    ->separator(',')
                    ->limit(3)
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('view_count')
                    ->label('Views')
                    ->numeric()
                    ->alignCenter()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                IconColumn::make('is_featured')
                    ->label('Featured')
                    ->boolean()
                    ->trueIcon('heroicon-o-star')
                    ->falseIcon('heroicon-o-star')
                    ->trueColor('warning')
                    ->falseColor('gray')
                    ->alignCenter()
                    ->sortable(),

                IconColumn::make('is_published')
                    ->label('Published')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->alignCenter()
                    ->sortable(),

                TextColumn::make('published_at')
                    ->label('Tanggal Publikasi')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('Diperbarui')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('category_id')
                    ->label('Kategori')
                    ->relationship('category', 'name', function ($query) {
                        $query->where('type', 'article');
                    })
                    ->searchable()
                    ->preload(),

                SelectFilter::make('author_id')
                    ->label('Penulis')
                    ->relationship('author', 'name')
                    ->searchable()
                    ->preload(),

                TernaryFilter::make('is_featured')
                    ->label('Featured')
                    ->placeholder('Semua artikel')
                    ->trueLabel('Featured saja')
                    ->falseLabel('Non-featured saja'),

                TernaryFilter::make('is_published')
                    ->label('Status Publikasi')
                    ->placeholder('Semua artikel')
                    ->trueLabel('Published saja')
                    ->falseLabel('Draft saja'),
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make()
                        ->label('Lihat'),
                    
                    EditAction::make()
                        ->label('Edit'),
                    
                    Action::make('toggle_featured')
                        ->label(fn (Article $record) => $record->is_featured ? 'Hapus Featured' : 'Jadikan Featured')
                        ->icon('heroicon-o-star')
                        ->color('warning')
                        ->requiresConfirmation()
                        ->modalHeading(fn (Article $record) => $record->is_featured ? 'Hapus dari Featured?' : 'Jadikan Featured?')
                        ->modalDescription('Status featured akan diubah untuk artikel ini.')
                        ->action(function (Article $record) {
                            $service = app(ArticleService::class);
                            $service->toggleFeaturedStatus($record);
                            
                            Notification::make()
                                ->title($record->is_featured ? 'Artikel menjadi Featured' : 'Artikel bukan Featured')
                                ->success()
                                ->send();
                        }),
                    
                    Action::make('toggle_published')
                        ->label(fn (Article $record) => $record->is_published ? 'Unpublish' : 'Publish')
                        ->icon('heroicon-o-check-circle')
                        ->color(fn (Article $record) => $record->is_published ? 'warning' : 'success')
                        ->requiresConfirmation()
                        ->modalHeading(fn (Article $record) => $record->is_published ? 'Unpublish Artikel?' : 'Publish Artikel?')
                        ->modalDescription('Status publikasi akan diubah untuk artikel ini.')
                        ->action(function (Article $record) {
                            $service = app(ArticleService::class);
                            $service->togglePublishedStatus($record);
                            
                            Notification::make()
                                ->title($record->is_published ? 'Artikel Dipublikasikan' : 'Artikel Menjadi Draft')
                                ->success()
                                ->send();
                        }),
                    
                    DeleteAction::make()
                        ->label('Hapus')
                        ->modalHeading('Hapus Artikel?')
                        ->modalDescription('Artikel akan dihapus sementara dan dapat dipulihkan nanti.')
                        ->successNotificationTitle('Artikel berhasil dihapus'),
                    
                    RestoreAction::make()
                        ->label('Pulihkan')
                        ->successNotificationTitle('Artikel berhasil dipulihkan'),
                    
                    ForceDeleteAction::make()
                        ->label('Hapus Permanen')
                        ->modalHeading('Hapus Permanen?')
                        ->modalDescription('Data akan dihapus permanent dan tidak dapat dikembalikan!')
                        ->successNotificationTitle('Artikel berhasil dihapus permanen'),
                ])
                ->icon('heroicon-m-ellipsis-vertical')
                ->tooltip('Aksi'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label('Hapus yang Dipilih')
                        ->modalHeading('Hapus Artikel Terpilih?')
                        ->modalDescription('Artikel yang dipilih akan dihapus sementara.')
                        ->successNotificationTitle('Artikel berhasil dihapus'),
                    
                    RestoreBulkAction::make()
                        ->label('Pulihkan yang Dipilih')
                        ->successNotificationTitle('Artikel berhasil dipulihkan'),
                    
                    ForceDeleteBulkAction::make()
                        ->label('Hapus Permanen')
                        ->modalHeading('Hapus Permanen?')
                        ->modalDescription('Data akan dihapus permanent dan tidak dapat dikembalikan!')
                        ->successNotificationTitle('Artikel berhasil dihapus permanen'),
                ]),
            ])
            ->defaultSort('published_at', 'desc')
            ->emptyStateHeading('Belum ada artikel')
            ->emptyStateDescription('Buat artikel digital pertama Anda untuk memulai.')
            ->emptyStateIcon('heroicon-o-document-text');
    }
}
