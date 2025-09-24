<?php

namespace App\Filament\Resources;

use App\Filament\Resources\QuestionResource\Pages;
use App\Filament\Resources\QuestionResource\RelationManagers;
use App\Models\Question;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class QuestionResource extends Resource
{
    protected static ?string $model = Question::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    protected static ?string $modelLabel = '문제';

    protected static ?string $pluralModelLabel = '문제 관리';

    protected static ?string $navigationLabel = '문제 관리';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('no')
                    ->label('문제 번호')
                    ->required()
                    ->numeric()
                    ->unique(ignoreRecord: true),

                Forms\Components\RichEditor::make('question')
                    ->label('문제')
                    ->required()
                    ->columnSpanFull()
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

                Forms\Components\Section::make('보기')
                    ->schema([
                        Forms\Components\RichEditor::make('option_1')
                            ->label('보기 1')
                            ->required()
                            ->toolbarButtons(['bold', 'italic', 'strike', 'link']),

                        Forms\Components\RichEditor::make('option_2')
                            ->label('보기 2')
                            ->required()
                            ->toolbarButtons(['bold', 'italic', 'strike', 'link']),

                        Forms\Components\RichEditor::make('option_3')
                            ->label('보기 3')
                            ->required()
                            ->toolbarButtons(['bold', 'italic', 'strike', 'link']),

                        Forms\Components\RichEditor::make('option_4')
                            ->label('보기 4')
                            ->required()
                            ->toolbarButtons(['bold', 'italic', 'strike', 'link']),

                        Forms\Components\RichEditor::make('option_5')
                            ->label('보기 5')
                            ->required()
                            ->toolbarButtons(['bold', 'italic', 'strike', 'link']),
                    ])
                    ->columns(1),

                Forms\Components\Select::make('answer')
                    ->label('정답 번호')
                    ->options([
                        1 => '1번',
                        2 => '2번',
                        3 => '3번',
                        4 => '4번',
                        5 => '5번',
                    ])
                    ->nullable()
                    ->placeholder('선택하세요'),

                Forms\Components\RichEditor::make('explanation')
                    ->label('해설')
                    ->columnSpanFull()
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
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('no')
                    ->label('번호')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('question')
                    ->label('문제')
                    ->html()
                    ->limit(50)
                    ->searchable(),

                Tables\Columns\TextColumn::make('answer')
                    ->label('정답')
                    ->badge()
                    ->color(fn ($state) => $state === null ? 'danger' : 'success')
                    ->formatStateUsing(fn ($state) => $state === null ? '미입력' : $state . '번'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('생성일')
                    ->dateTime('Y-m-d H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('answer_status')
                    ->label('정답 상태')
                    ->options([
                        'with_answer' => '정답 있음',
                        'without_answer' => '정답 없음',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return match($data['value']) {
                            'with_answer' => $query->whereNotNull('answer'),
                            'without_answer' => $query->whereNull('answer'),
                            default => $query
                        };
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make()
                    ->label('수정'),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('no', 'asc');
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
            'index' => Pages\ListQuestions::route('/'),
            'create' => Pages\CreateQuestion::route('/create'),
            'edit' => Pages\EditQuestion::route('/{record}/edit'),
        ];
    }
}
