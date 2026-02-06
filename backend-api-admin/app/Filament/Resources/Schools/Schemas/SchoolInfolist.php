<?php

namespace App\Filament\Resources\Schools\Schemas;

use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use App\Enums\SubscriptionPlan;
use Filament\Infolists\Components\RepeatableEntry;
use App\Enums\StudentStatus;
use Filament\Support\Enums\FontWeight;

class SchoolInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->schema([
            Section::make('Informasi Sekolah')
                ->schema([
                    ImageEntry::make('logo_url')
                        ->label('Logo Sekolah')
                        ->disk('public')
                        ->defaultImageUrl(url('/images/default-school-logo.png'))
                        ->columnSpanFull(),

                    TextEntry::make('name')
                        ->label('Nama Sekolah')
                        ->weight(FontWeight::Bold)
                        ->columnSpan(1),

                    TextEntry::make('npsn')
                        ->label('NPSN')
                        ->copyable()
                        ->copyMessage('NPSN disalin!')
                        ->placeholder('Tidak ada')
                        ->columnSpan(1),

                    TextEntry::make('phone')
                        ->label('Nomor Telepon')
                        ->icon('heroicon-m-phone')
                        ->placeholder('Tidak ada')
                        ->columnSpan(1),

                    TextEntry::make('email')
                        ->label('Email')
                        ->icon('heroicon-m-envelope')
                        ->copyable()
                        ->copyMessage('Email disalin!')
                        ->placeholder('Tidak ada')
                        ->columnSpan(1),

                    TextEntry::make('address')
                        ->label('Alamat')
                        ->placeholder('Tidak ada')
                        ->columnSpanFull(),

                    TextEntry::make('is_active')
                        ->label('Status')
                        ->badge()
                        ->color(fn (bool $state): string => $state ? 'success' : 'danger')
                        ->formatStateUsing(fn (bool $state): string => $state ? 'Aktif' : 'Nonaktif')
                        ->columnSpan(1),

                    TextEntry::make('headmaster_name')
                        ->label('Kepala Sekolah')
                        ->icon('heroicon-m-user-circle')
                        ->weight(FontWeight::Bold)
                        ->placeholder('Belum ada kepala sekolah')
                        ->columnSpan(2),

                    TextEntry::make('headmaster_email')
                        ->label('Email Kepala Sekolah')
                        ->icon('heroicon-m-envelope')
                        ->copyable()
                        ->copyMessage('Email disalin!')
                        ->placeholder('Belum ada')
                        ->columnSpan(1),

                    TextEntry::make('headmaster_phone')
                        ->label('Telepon Kepala Sekolah')
                        ->icon('heroicon-m-phone')
                        ->placeholder('Belum ada')
                        ->columnSpan(1),
                ])
                ->columns(2),

            Section::make('Subscription & Batasan')
                ->schema([
                    TextEntry::make('subscription_plan')
                        ->label('Paket Langganan')
                        ->badge()
                        ->color(fn ($state) => match($state) {
                            SubscriptionPlan::PRO => 'success',
                            SubscriptionPlan::FREE => 'warning',
                            default => 'gray',
                        })
                        ->formatStateUsing(fn ($state) => $state instanceof SubscriptionPlan ? $state->label() : $state)
                        ->columnSpan(1),

                    TextEntry::make('subscription_ended_at')
                        ->label('Kadaluarsa')
                        ->date('d F Y')
                        ->placeholder('Tidak ada batas')
                        ->color(fn ($state) => $state && now()->greaterThan($state) ? 'danger' : 'success')
                        ->icon(fn ($state) => $state && now()->greaterThan($state) ? 'heroicon-m-exclamation-circle' : 'heroicon-m-check-circle')
                        ->columnSpan(1),

                    TextEntry::make('student_limit')
                        ->label('Batas Siswa')
                        ->formatStateUsing(fn ($state) => $state >= 9999 ? 'Unlimited' : number_format($state))
                        ->columnSpan(1),

                    TextEntry::make('teacher_limit')
                        ->label('Batas Guru')
                        ->formatStateUsing(fn ($state) => $state >= 9999 ? 'Unlimited' : number_format($state))
                        ->columnSpan(1),
                ])
                ->columns(2),

            Section::make('Data Guru')
                ->description('Daftar guru yang terdaftar di sekolah ini')
                ->schema([
                    RepeatableEntry::make('teachers')
                        ->label('')
                        ->schema([
                            TextEntry::make('user.name')
                                ->label('Nama'),

                            TextEntry::make('specialization')
                                ->label('Spesialisasi')
                                ->badge()
                                ->color('info')
                                ->placeholder('Tidak ada'),
                        ])
                        ->columns(2)
                        ->columnSpanFull()
                        ->state(fn ($record) => $record->teachers()->where('school_id', $record->id)->with('user')->get()),
                ])
                ->collapsible()
                ->collapsed(),

            Section::make('Data Siswa')
                ->description('Daftar siswa yang terdaftar di sekolah ini')
                ->schema([
                    RepeatableEntry::make('students')
                        ->label('')
                        ->schema([
                            TextEntry::make('name')
                                ->label('Nama Siswa'),

                            TextEntry::make('nisn')
                                ->label('NISN')
                                ->badge()
                                ->color('gray')
                                ->placeholder('Tidak ada'),

                            TextEntry::make('class.name')
                                ->label('Kelas')
                                ->badge()
                                ->color('warning')
                                ->placeholder('Belum ada kelas'),

                            TextEntry::make('status')
                                ->label('Status')
                                ->badge()
                                ->color(fn ($state) => match($state->value ?? $state) {
                                    'active' => 'success',
                                    'inactive' => 'danger',
                                    'graduated' => 'info',
                                    default => 'gray',
                                })
                                ->formatStateUsing(fn ($state) => $state instanceof StudentStatus ? $state->label() : $state),
                        ])
                        ->columns(4)
                        ->columnSpanFull()
                        ->state(fn ($record) => $record->students()->where('school_id', $record->id)->with('class')->limit(50)->get()),
                ])
                ->collapsible()
                ->collapsed(),

            Section::make('Data Kelas')
                ->description('Daftar kelas yang tersedia di sekolah ini')
                ->schema([
                    RepeatableEntry::make('classes')
                        ->label('')
                        ->schema([
                            TextEntry::make('name')
                                ->label('Nama Kelas')
                                ->weight('bold'),

                            TextEntry::make('level')
                                ->label('Tingkat')
                                ->badge()
                                ->color('info'),

                            TextEntry::make('capacity')
                                ->label('Kapasitas')
                                ->badge()
                                ->color('success')
                                ->formatStateUsing(fn ($state) => $state . ' siswa'),

                            TextEntry::make('students_count')
                                ->label('Jumlah Siswa')
                                ->counts('students')
                                ->badge()
                                ->color('primary'),

                            TextEntry::make('homeroomTeacher.user.name')
                                ->label('Wali Kelas')
                                ->placeholder('Belum ada wali kelas'),

                            TextEntry::make('academic_year')
                                ->label('Tahun Ajaran')
                                ->badge()
                                ->color('gray'),
                        ])
                        ->columns(3)
                        ->columnSpanFull()
                        ->state(fn ($record) => $record->classes()->where('school_id', $record->id)->with(['homeroomTeacher.user', 'students'])->get()),
                ])
                ->collapsible()
                ->collapsed(),

            Section::make('Statistik')
                ->schema([
                    TextEntry::make('total_students')
                        ->label('Total Siswa')
                        ->numeric()
                        ->icon('heroicon-m-academic-cap')
                        ->columnSpan(1),

                    TextEntry::make('total_teachers')
                        ->label('Total Guru')
                        ->numeric()
                        ->icon('heroicon-m-users')
                        ->columnSpan(1),

                    TextEntry::make('total_classes')
                        ->label('Total Kelas')
                        ->numeric()
                        ->icon('heroicon-m-building-library')
                        ->columnSpan(1),
                ])
                ->columns(3),

            Section::make('Informasi Sistem')
                ->schema([
                    TextEntry::make('created_at')
                        ->label('Dibuat Pada')
                        ->dateTime('d F Y, H:i')
                        ->columnSpan(1),

                    TextEntry::make('updated_at')
                        ->label('Terakhir Diperbarui')
                        ->dateTime('d F Y, H:i')
                        ->since()
                        ->columnSpan(1),
                ])
                ->columns(2),
            ]);
    }
}
