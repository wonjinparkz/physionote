<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ContentResource\Pages;
use App\Filament\Resources\ContentResource\RelationManagers;
use App\Models\Content;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ContentResource extends Resource
{
    protected static ?string $model = Content::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $modelLabel = '카드뉴스';

    protected static ?string $pluralModelLabel = '카드뉴스 관리';

    protected static ?string $navigationLabel = '카드뉴스';

    protected static ?string $navigationGroup = '콘텐츠 관리';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('기본 정보')
                    ->schema([
                        Forms\Components\Select::make('sub_category_id')
                            ->label('서브 카테고리')
                            ->relationship('subCategory', 'name')
                            ->searchable()
                            ->preload()
                            ->createOptionForm([
                                Forms\Components\TextInput::make('name')
                                    ->label('서브 카테고리명')
                                    ->required(),
                                Forms\Components\Hidden::make('category_id')
                                    ->default(1), // Default category ID
                            ]),

                        Forms\Components\TextInput::make('title')
                            ->label('제목')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\Hidden::make('category')
                            ->default('card_news'),

                        Forms\Components\RichEditor::make('body')
                            ->label('본문 콘텐츠')
                            ->required()
                            ->columnSpanFull()
                            ->fileAttachmentsDisk('public')
                            ->fileAttachmentsDirectory('content-attachments')
                            ->toolbarButtons([
                                'attachFiles',
                                'blockquote',
                                'bold',
                                'bulletList',
                                'codeBlock',
                                'h2',
                                'h3',
                                'italic',
                                'link',
                                'orderedList',
                                'redo',
                                'strike',
                                'table',
                                'undo',
                            ]),
                    ]),

                Forms\Components\Section::make('미디어 및 표시 설정')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\FileUpload::make('thumbnail')
                                    ->label('썸네일 이미지')
                                    ->image()
                                    ->maxSize(2048)
                                    ->directory('thumbnails')
                                    ->disk('public')
                                    ->imageResizeMode('cover')
                                    ->imageCropAspectRatio('16:9')
                                    ->imageResizeTargetWidth('1920')
                                    ->imageResizeTargetHeight('1080'),

                                Forms\Components\Select::make('badge')
                                    ->label('뱃지')
                                    ->options([
                                        'popular' => '인기',
                                        'essential' => '필수',
                                        'basic' => '기본',
                                        'new' => '신규',
                                        'updated' => '업데이트',
                                        'premium' => '프리미엄',
                                    ])
                                    ->placeholder('선택하세요'),
                            ]),

                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('sort_order')
                                    ->label('순서')
                                    ->numeric()
                                    ->default(0)
                                    ->helperText('숫자가 작을수록 먼저 표시됩니다'),

                                Forms\Components\Toggle::make('is_published')
                                    ->label('발행 여부')
                                    ->helperText('체크하면 사용자에게 표시됩니다')
                                    ->default(false),
                            ]),
                    ]),

                Forms\Components\Section::make('추가 데이터')
                    ->schema([
                        Forms\Components\KeyValue::make('custom_data')
                            ->label('커스텀 데이터')
                            ->addActionLabel('항목 추가')
                            ->keyLabel('키')
                            ->valueLabel('값')
                            ->reorderable()
                            ->helperText('유연하게 사용할 수 있는 추가 데이터를 JSON 형태로 저장합니다'),
                    ])
                    ->collapsed(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('thumbnail')
                    ->label('썸네일')
                    ->square()
                    ->size(60),

                Tables\Columns\TextColumn::make('subCategory.name')
                    ->label('서브 카테고리')
                    ->badge()
                    ->color('primary')
                    ->searchable(),

                Tables\Columns\TextColumn::make('title')
                    ->label('제목')
                    ->searchable()
                    ->limit(50),

                Tables\Columns\TextColumn::make('badge')
                    ->label('뱃지')
                    ->badge()
                    ->formatStateUsing(function ($state) {
                        $badges = [
                            'popular' => '인기',
                            'essential' => '필수',
                            'basic' => '기본',
                            'new' => '신규',
                            'updated' => '업데이트',
                            'premium' => '프리미엄',
                        ];
                        return $badges[$state] ?? '';
                    })
                    ->color(function ($state) {
                        return match ($state) {
                            'popular' => 'danger',
                            'essential' => 'warning',
                            'new' => 'success',
                            'premium' => 'primary',
                            default => 'gray',
                        };
                    }),

                Tables\Columns\TextColumn::make('sort_order')
                    ->label('순서')
                    ->sortable()
                    ->alignCenter(),

                Tables\Columns\ToggleColumn::make('is_published')
                    ->label('발행')
                    ->onColor('success')
                    ->offColor('gray')
                    ->onIcon('heroicon-m-check')
                    ->offIcon('heroicon-m-x-mark'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('생성일')
                    ->dateTime('Y-m-d H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('수정일')
                    ->dateTime('Y-m-d H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('badge')
                    ->label('뱃지')
                    ->options([
                        'popular' => '인기',
                        'essential' => '필수',
                        'basic' => '기본',
                        'new' => '신규',
                        'updated' => '업데이트',
                        'premium' => '프리미엄',
                    ]),

                Tables\Filters\TernaryFilter::make('is_published')
                    ->label('발행 상태')
                    ->placeholder('전체')
                    ->trueLabel('발행됨')
                    ->falseLabel('미발행'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\Action::make('duplicate')
                    ->label('복제')
                    ->icon('heroicon-o-document-duplicate')
                    ->color('gray')
                    ->action(function (Content $record) {
                        $newContent = $record->replicate();
                        $newContent->title = $record->title . ' (복사본)';
                        $newContent->is_published = false;
                        $newContent->save();
                    })
                    ->requiresConfirmation(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('publish')
                        ->label('선택 항목 발행')
                        ->icon('heroicon-o-eye')
                        ->color('success')
                        ->action(function ($records) {
                            $records->each->update(['is_published' => true]);
                        })
                        ->requiresConfirmation(),
                    Tables\Actions\BulkAction::make('unpublish')
                        ->label('선택 항목 미발행')
                        ->icon('heroicon-o-eye-slash')
                        ->color('gray')
                        ->action(function ($records) {
                            $records->each->update(['is_published' => false]);
                        })
                        ->requiresConfirmation(),
                ]),
            ])
            ->defaultSort('sort_order', 'asc')
            ->reorderable('sort_order');
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
            'index' => Pages\ListContents::route('/'),
            'create' => Pages\CreateContent::route('/create'),
            'edit' => Pages\EditContent::route('/{record}/edit'),
        ];
    }
}