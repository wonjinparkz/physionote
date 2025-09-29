<?php

namespace App\Filament\Resources;

use App\Filament\Resources\QuestionResource\Pages;
use App\Filament\Resources\QuestionResource\RelationManagers;
use App\Models\Question;
use App\Models\Category;
use App\Models\SubCategory;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Get;

class QuestionResource extends Resource
{
    protected static ?string $model = Question::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    protected static ?string $modelLabel = '기출문제';

    protected static ?string $pluralModelLabel = '기출문제 관리';

    protected static ?string $navigationLabel = '기출문제 관리';

    protected static ?string $navigationGroup = '문제 관리';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('no')
                    ->label('문제 번호')
                    ->required()
                    ->numeric(),

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

                Forms\Components\Section::make('카테고리')
                    ->schema([
                        Forms\Components\Select::make('sub_category_id')
                            ->label('카테고리')
                            ->options(function () {
                                $questionCategory = Category::where('name', '문제')
                                    ->with('subCategories')
                                    ->first();

                                if (!$questionCategory) {
                                    return [];
                                }

                                $options = [];
                                foreach ($questionCategory->subCategories as $subCategory) {
                                    $subSubCategories = SubCategory::where('category_id', $subCategory->id)->get();

                                    if ($subSubCategories->isEmpty()) {
                                        // 하위 카테고리가 없으면 직접 추가
                                        $label = $subCategory->name;
                                        if ($subCategory->slug) {
                                            $label .= ' | ' . $subCategory->slug;
                                        }
                                        if ($subCategory->description) {
                                            $label .= ' | ' . $subCategory->description;
                                        }
                                        $options[$subCategory->id] = $label;
                                    } else {
                                        // 하위 카테고리가 있으면 그룹으로 추가
                                        $group = [];
                                        foreach ($subSubCategories as $subSub) {
                                            $label = $subSub->name;
                                            if ($subSub->slug) {
                                                $label .= ' | ' . $subSub->slug;
                                            }
                                            if ($subSub->description) {
                                                $label .= ' | ' . $subSub->description;
                                            }
                                            $group[$subSub->id] = $label;
                                        }
                                        $options[$subCategory->name] = $group;
                                    }
                                }

                                return $options;
                            })
                            ->searchable()
                            ->nullable()
                            ->helperText('문제 카테고리 아래의 카테고리를 선택하세요'),
                    ])
                    ->columns(1),

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
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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

                Tables\Filters\SelectFilter::make('sub_category_id')
                    ->label('카테고리')
                    ->options(function () {
                        $questionCategory = Category::where('name', '문제')
                            ->with('subCategories')
                            ->first();

                        if (!$questionCategory) {
                            return [];
                        }

                        $options = [];
                        foreach ($questionCategory->subCategories as $subCategory) {
                            $subSubCategories = SubCategory::where('category_id', $subCategory->id)->get();

                            if ($subSubCategories->isEmpty()) {
                                $label = $subCategory->name;
                                if ($subCategory->slug) {
                                    $label .= ' | ' . $subCategory->slug;
                                }
                                if ($subCategory->description) {
                                    $label .= ' | ' . $subCategory->description;
                                }
                                $options[$subCategory->id] = $label;
                            } else {
                                foreach ($subSubCategories as $subSub) {
                                    $parentLabel = $subCategory->name;
                                    $label = $subSub->name;
                                    if ($subSub->slug) {
                                        $label .= ' | ' . $subSub->slug;
                                    }
                                    if ($subSub->description) {
                                        $label .= ' | ' . $subSub->description;
                                    }
                                    $options[$subSub->id] = $parentLabel . ' > ' . $label;
                                }
                            }
                        }

                        return $options;
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
