<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $modelLabel = '사용자';

    protected static ?string $pluralModelLabel = '사용자';

    protected static ?string $navigationGroup = '사용자 관리';

    protected static ?string $navigationLabel = '사용자';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('이름')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('email')
                    ->label('이메일')
                    ->email()
                    ->required()
                    ->maxLength(255),
                Forms\Components\DateTimePicker::make('email_verified_at')
                    ->label('이메일 인증일시'),
                Forms\Components\TextInput::make('password')
                    ->label('비밀번호')
                    ->password()
                    ->maxLength(255)
                    ->dehydrateStateUsing(fn ($state) => $state ? bcrypt($state) : null)
                    ->dehydrated(fn ($state) => filled($state))
                    ->required(fn (string $context): bool => $context === 'create'),
                Forms\Components\Select::make('provider')
                    ->label('가입 경로')
                    ->options([
                        'email' => '이메일',
                        'naver' => '네이버',
                        'kakao' => '카카오',
                    ])
                    ->default('email'),
                Forms\Components\TextInput::make('provider_id')
                    ->label('소셜 계정 ID')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\TextInput::make('avatar')
                    ->label('프로필 이미지')
                    ->maxLength(255)
                    ->default(null),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('이름')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('이메일')
                    ->searchable(),
                Tables\Columns\TextColumn::make('provider')
                    ->label('가입 경로')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => match($state) {
                        'naver' => '네이버',
                        'kakao' => '카카오',
                        null => '이메일',
                        default => '이메일',
                    })
                    ->color(fn (?string $state): string => match($state) {
                        'naver' => 'success',
                        'kakao' => 'warning',
                        null => 'primary',
                        default => 'primary',
                    }),
                Tables\Columns\TextColumn::make('email_verified_at')
                    ->label('이메일 인증')
                    ->dateTime('Y-m-d H:i')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('가입일')
                    ->dateTime('Y-m-d H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('수정일')
                    ->dateTime('Y-m-d H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('provider')
                    ->label('가입 경로')
                    ->options([
                        'email' => '이메일',
                        'naver' => '네이버',
                        'kakao' => '카카오',
                    ]),
                Tables\Filters\Filter::make('verified')
                    ->label('이메일 인증')
                    ->query(fn (Builder $query): Builder => $query->whereNotNull('email_verified_at')),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
