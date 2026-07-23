<?php

namespace App\Filament\Resources\Users\RelationManagers;

use App\Enums\RoleType;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\BulkActionGroup;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Filament\Notifications\Notification;

class SchoolMembershipsRelationManager extends RelationManager
{
    protected static string $relationship = 'schoolMemberships';
    
    protected static ?string $title = 'Keanggotaan Sekolah';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Forms\Components\Select::make('school_id')
                    ->label('Sekolah')
                    ->relationship('school', 'name')
                    ->searchable()
                    ->required()
                    ->preload()
                    ->unique(
                        table: 'school_members',
                        modifyRuleUsing: function (\Illuminate\Validation\Rules\Unique $rule, RelationManager $livewire, Get $get) {
                            return $rule
                                ->where('user_id', $livewire->getOwnerRecord()->id)
                                ->where('role_type', $get('role_type'))
                                ->whereNull('deleted_at');
                        },
                        ignoreRecord: true,
                    )
                    ->validationMessages([
                        'unique' => 'Pengguna ini sudah memiliki role tersebut di sekolah ini.',
                    ]),
                    
                Forms\Components\Select::make('role_type')
                    ->label('Role SIAKAD')
                    ->options(RoleType::class)
                    ->required()
                    ->native(false),
                    
                Forms\Components\Toggle::make('is_active')
                    ->label('Status Aktif')
                    ->default(true),
                    
                Forms\Components\DateTimePicker::make('joined_at')
                    ->label('Tanggal Bergabung')
                    ->default(now())
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                Tables\Columns\TextColumn::make('school.name')
                    ->label('Sekolah')
                    ->searchable()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('role_type')
                    ->label('Role')
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state->label()),
                    
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),
                    
                Tables\Columns\TextColumn::make('joined_at')
                    ->label('Bergabung Pada')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make()
                    ->using(function (array $data, string $model, RelationManager $livewire) {
                        $record = $model::withTrashed()
                            ->where('school_id', $data['school_id'])
                            ->where('user_id', $livewire->getOwnerRecord()->id)
                            ->where('role_type', $data['role_type'])
                            ->first();

                        if ($record) {
                            if ($record->trashed()) {
                                $record->restore();
                            }
                            $record->update($data);
                            return $record;
                        }

                        $data['user_id'] = $livewire->getOwnerRecord()->id;
                        return $model::create($data);
                    })
                    ->mutateFormDataUsing(function (array $data): array {
                        return $data;
                    })
                    ->after(function (Model $record) {
                        // Assign Spatie Role automatically when creating membership
                        $user = $record->user;
                        if ($user) {
                            $user->assignRole($record->role_type->value);
                        }
                    })
                    ->before(function (CreateAction $action, array $data) {
                        // Validasi Headmaster: Jangan izinkan > 1 headmaster aktif
                        if ($data['role_type'] === RoleType::HEADMASTER->value && $data['is_active']) {
                            $existingHeadmaster = \App\Models\SchoolMember::where('school_id', $data['school_id'])
                                ->where('role_type', RoleType::HEADMASTER)
                                ->where('is_active', true)
                                ->exists();
                            
                            if ($existingHeadmaster) {
                                Notification::make()
                                    ->danger()
                                    ->title('Aksi Ditolak')
                                    ->body('Sekolah ini sudah memiliki Kepala Sekolah yang aktif.')
                                    ->send();
                                
                                $action->halt();
                            }
                        }
                    }),
            ])
            ->actions([
                EditAction::make()
                    ->before(function (Model $record, array $data, EditAction $action) {
                         // Validasi jika mengubah menjadi Headmaster
                         if ($data['role_type'] === RoleType::HEADMASTER->value && $data['is_active']) {
                            $existingHeadmaster = \App\Models\SchoolMember::where('school_id', $data['school_id'])
                                ->where('role_type', RoleType::HEADMASTER)
                                ->where('is_active', true)
                                ->where('id', '!=', $record->id)
                                ->exists();
                            
                            if ($existingHeadmaster) {
                                Notification::make()
                                    ->danger()
                                    ->title('Aksi Ditolak')
                                    ->body('Sekolah ini sudah memiliki Kepala Sekolah yang aktif.')
                                    ->send();
                                
                                $action->halt();
                            }
                        }
                    })
                    ->after(function (Model $record, array $data) {
                        // Refresh spatie roles
                        $user = $record->user;
                        if ($user) {
                            // Collect all active SIAKAD roles the user has across all schools
                            $activeRoles = $user->schoolMemberships()
                                ->where('is_active', true)
                                ->pluck('role_type')
                                ->map(fn($role) => $role->value)
                                ->unique()
                                ->toArray();
                            
                            // Re-sync SIAKAD roles while keeping System roles
                            $systemRoles = ['admin', 'moderator', 'user'];
                            $currentSystemRoles = $user->roles->pluck('name')->intersect($systemRoles)->toArray();
                            
                            $rolesToSync = array_merge($currentSystemRoles, $activeRoles);
                            $user->syncRoles($rolesToSync);
                        }
                    }),
                DeleteAction::make()
                    ->after(function (Model $record) {
                        $user = $record->user;
                        if ($user) {
                            // Recalculate remaining active roles
                            $activeRoles = $user->schoolMemberships()
                                ->where('id', '!=', $record->id)
                                ->where('is_active', true)
                                ->pluck('role_type')
                                ->map(fn($role) => $role->value)
                                ->unique()
                                ->toArray();
                            
                            $systemRoles = ['admin', 'moderator', 'user'];
                            $currentSystemRoles = $user->roles->pluck('name')->intersect($systemRoles)->toArray();
                            
                            $rolesToSync = array_merge($currentSystemRoles, $activeRoles);
                            $user->syncRoles($rolesToSync);
                        }
                    }),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
