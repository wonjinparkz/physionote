<?php

namespace App\Filament\Resources\CategoryResource\Pages;

use App\Filament\Resources\CategoryResource;
use App\Models\Category;
use App\Models\SubCategory;
use Filament\Resources\Pages\Page;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Actions;
use Filament\Notifications\Notification;
use Illuminate\Support\Str;
use Illuminate\Contracts\Support\Htmlable;
use Filament\Forms\Get;
use Filament\Forms\Set;

class ManageSubCategories extends Page implements HasForms, HasTable
{
    use InteractsWithTable;
    use InteractsWithForms;

    protected static string $resource = CategoryResource::class;

    protected static string $view = 'filament.resources.category-resource.pages.manage-sub-categories';

    public ?Category $record = null;

    public function mount(int | string $record): void
    {
        $this->record = Category::findOrFail($record);
    }

    public function getTitle(): string | Htmlable
    {
        return $this->record->name . ' - 서브 카테고리 관리';
    }

    protected function updateQuestionSlug(Get $get, Set $set): void
    {
        $year = $get('year');
        $classTime = $get('class_time');
        $orderNo = $get('order_no');

        if ($year && $classTime && $orderNo) {
            $slug = $year . '-' . $classTime . '-' . $orderNo;
            $set('slug', $slug);
            $set('name', $classTime . '교시');
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make('create')
                ->label('서브 카테고리 추가')
                ->modalHeading(fn () => $this->record ? $this->record->name . ' 서브 카테고리 생성' : '서브 카테고리 생성')
                ->modalSubheading(fn () => $this->record && $this->record->name === '문제' ? '년도, 교시, 순서를 선택하여 생성' : null)
                ->createAnother(false)
                ->model(SubCategory::class)
                ->form(function () {
                    // 문제 카테고리인지 확인
                    $isQuestionCategory = $this->record->name === '문제';

                    if ($isQuestionCategory) {
                        return [
                            Forms\Components\Section::make('카테고리 정보')
                                ->description('년도, 교시, 순서를 선택하면 슬러그가 자동으로 생성됩니다.')
                                ->schema([
                                    Forms\Components\Grid::make(3)
                                        ->schema([
                                            Forms\Components\Select::make('year')
                                                ->label('년도')
                                                ->options(function () {
                                                    $years = [];
                                                    $currentYear = date('Y');
                                                    for ($i = $currentYear + 1; $i >= 2020; $i--) {
                                                        $years[$i] = $i . '년';
                                                    }
                                                    return $years;
                                                })
                                                ->required()
                                                ->reactive()
                                                ->afterStateUpdated(fn (Get $get, Set $set) =>
                                                    $this->updateQuestionSlug($get, $set)),

                                            Forms\Components\Select::make('class_time')
                                                ->label('교시')
                                                ->options([
                                                    '1' => '1교시',
                                                    '2' => '2교시',
                                                    '3' => '3교시',
                                                    '4' => '4교시',
                                                ])
                                                ->required()
                                                ->reactive()
                                                ->afterStateUpdated(fn (Get $get, Set $set) =>
                                                    $this->updateQuestionSlug($get, $set)),

                                            Forms\Components\TextInput::make('order_no')
                                                ->label('순서')
                                                ->numeric()
                                                ->default(1)
                                                ->required()
                                                ->reactive()
                                                ->afterStateUpdated(fn (Get $get, Set $set) =>
                                                    $this->updateQuestionSlug($get, $set)),
                                        ]),

                                    Forms\Components\Placeholder::make('generated_slug')
                                        ->label('생성될 슬러그')
                                        ->content(fn (Get $get) =>
                                            ($get('year') && $get('class_time') && $get('order_no'))
                                                ? $get('year') . '-' . $get('class_time') . '-' . $get('order_no')
                                                : '년도, 교시, 순서를 모두 선택해주세요'
                                        ),
                                ]),

                            Forms\Components\Textarea::make('description')
                                ->label('설명')
                                ->rows(3)
                                ->placeholder('예: 2025/1/1-물리치료 기초'),

                            Forms\Components\Grid::make(2)
                                ->schema([
                                    Forms\Components\TextInput::make('sort_order')
                                        ->label('정렬 순서')
                                        ->numeric()
                                        ->default(0),
                                    Forms\Components\Toggle::make('is_active')
                                        ->label('활성화')
                                        ->default(true),
                                ]),
                        ];
                    }

                    // 일반 카테고리용 폼
                    return [
                        Forms\Components\TextInput::make('name')
                            ->label('서브 카테고리명')
                            ->required()
                            ->maxLength(255)
                            ->reactive()
                            ->afterStateUpdated(fn ($state, Set $set) =>
                                $set('slug', Str::slug($state))),
                        Forms\Components\TextInput::make('slug')
                            ->label('슬러그')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                        Forms\Components\Textarea::make('description')
                            ->label('설명')
                            ->rows(3),
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('sort_order')
                                    ->label('정렬 순서')
                                    ->numeric()
                                    ->default(0),
                                Forms\Components\Toggle::make('is_active')
                                    ->label('활성화')
                                    ->default(true),
                            ]),
                    ];
                })
                ->mutateFormDataUsing(function (array $data): array {
                    $data['category_id'] = $this->record->id;

                    // 문제 카테고리인 경우 slug와 name 생성
                    if ($this->record->name === '문제') {
                        if (isset($data['year']) && isset($data['class_time']) && isset($data['order_no'])) {
                            $data['slug'] = $data['year'] . '-' . $data['class_time'] . '-' . $data['order_no'];
                            $data['name'] = $data['class_time'] . '교시';

                            // 임시 필드 제거
                            unset($data['year']);
                            unset($data['class_time']);
                            unset($data['order_no']);
                        }
                    }

                    return $data;
                })
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title('서브 카테고리가 생성되었습니다.')
                ),
            Actions\Action::make('back')
                ->label('뒤로가기')
                ->url(CategoryResource::getUrl('index'))
                ->color('gray'),
        ];
    }

    protected function getFormSchema($record = null): array
    {
        // 문제 카테고리인지 확인
        $isQuestionCategory = $this->record->name === '문제';

        if ($isQuestionCategory) {
            $year = null;
            $classTime = null;
            $orderNo = null;

            // 편집 모드인 경우 기존 값 파싱
            if ($record && $record->slug) {
                $parts = explode('-', $record->slug);
                $year = $parts[0] ?? null;
                $classTime = $parts[1] ?? null;
                $orderNo = $parts[2] ?? null;
            }

            return [
                Forms\Components\Section::make('카테고리 정보')
                    ->description('년도, 교시, 순서를 선택하면 슬러그가 자동으로 생성됩니다.')
                    ->schema([
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\Select::make('year')
                                    ->label('년도')
                                    ->options(function () {
                                        $years = [];
                                        $currentYear = date('Y');
                                        for ($i = $currentYear + 1; $i >= 2020; $i--) {
                                            $years[$i] = $i . '년';
                                        }
                                        return $years;
                                    })
                                    ->default($year)
                                    ->required()
                                    ->reactive()
                                    ->afterStateUpdated(fn (Get $get, Set $set) =>
                                        $this->updateQuestionSlug($get, $set)),

                                Forms\Components\Select::make('class_time')
                                    ->label('교시')
                                    ->options([
                                        '1' => '1교시',
                                        '2' => '2교시',
                                        '3' => '3교시',
                                        '4' => '4교시',
                                    ])
                                    ->default($classTime)
                                    ->required()
                                    ->reactive()
                                    ->afterStateUpdated(fn (Get $get, Set $set) =>
                                        $this->updateQuestionSlug($get, $set)),

                                Forms\Components\TextInput::make('order_no')
                                    ->label('순서')
                                    ->numeric()
                                    ->default($orderNo ?? 1)
                                    ->required()
                                    ->reactive()
                                    ->afterStateUpdated(fn (Get $get, Set $set) =>
                                        $this->updateQuestionSlug($get, $set)),
                            ]),

                        Forms\Components\Placeholder::make('generated_slug')
                            ->label('생성될 슬러그')
                            ->content(fn (Get $get) =>
                                ($get('year') && $get('class_time') && $get('order_no'))
                                    ? $get('year') . '-' . $get('class_time') . '-' . $get('order_no')
                                    : '년도, 교시, 순서를 모두 선택해주세요'
                            ),
                    ]),

                Forms\Components\Textarea::make('description')
                    ->label('설명')
                    ->rows(3)
                    ->placeholder('예: 2025/1/1-물리치료 기초'),

                Forms\Components\Grid::make(2)
                    ->schema([
                        Forms\Components\TextInput::make('sort_order')
                            ->label('정렬 순서')
                            ->numeric()
                            ->default(0),
                        Forms\Components\Toggle::make('is_active')
                            ->label('활성화')
                            ->default(true),
                    ]),
            ];
        }

        // 일반 카테고리용 기존 폼
        return [
            Forms\Components\Hidden::make('category_id')
                ->default($this->record->id),
            Forms\Components\TextInput::make('name')
                ->label('서브 카테고리명')
                ->required()
                ->maxLength(255)
                ->reactive()
                ->afterStateUpdated(fn ($state, Set $set) =>
                    $set('slug', Str::slug($state))),
            Forms\Components\TextInput::make('slug')
                ->label('슬러그')
                ->required()
                ->maxLength(255)
                ->unique(),
            Forms\Components\Textarea::make('description')
                ->label('설명')
                ->rows(3),
            Forms\Components\Grid::make(2)
                ->schema([
                    Forms\Components\TextInput::make('sort_order')
                        ->label('정렬 순서')
                        ->numeric()
                        ->default(0),
                    Forms\Components\Toggle::make('is_active')
                        ->label('활성화')
                        ->default(true),
                ]),
        ];
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(SubCategory::query()->where('category_id', $this->record->id))
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('서브 카테고리명')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('slug')
                    ->label('슬러그')
                    ->searchable(),
                Tables\Columns\TextColumn::make('contents_count')
                    ->label('콘텐츠 수')
                    ->counts('contents')
                    ->badge()
                    ->color('success'),
                Tables\Columns\TextColumn::make('sort_order')
                    ->label('순서')
                    ->sortable()
                    ->alignCenter(),
                Tables\Columns\ToggleColumn::make('is_active')
                    ->label('활성화'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('생성일')
                    ->dateTime('Y-m-d')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('활성화 상태'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->modalHeading(fn () => $this->record ? $this->record->name . ' 서브 카테고리 수정' : '서브 카테고리 수정')
                    ->modalSubheading(fn () => $this->record && $this->record->name === '문제' ? '년도, 교시, 순서를 수정' : null)
                    ->form(fn ($record) => $this->getFormSchema($record))
                    ->mutateRecordDataUsing(function (array $data, $record): array {
                        // 문제 카테고리인 경우 기존 값 파싱
                        if ($this->record->name === '문제' && $record->slug) {
                            $parts = explode('-', $record->slug);
                            $data['year'] = $parts[0] ?? null;
                            $data['class_time'] = $parts[1] ?? null;
                            $data['order_no'] = $parts[2] ?? null;
                        }
                        return $data;
                    })
                    ->mutateFormDataUsing(function (array $data) {
                        // 문제 카테고리인 경우 slug와 name 생성
                        if ($this->record->name === '문제') {
                            if (isset($data['year']) && isset($data['class_time']) && isset($data['order_no'])) {
                                $data['slug'] = $data['year'] . '-' . $data['class_time'] . '-' . $data['order_no'];
                                $data['name'] = $data['class_time'] . '교시';
                                $data['category_id'] = $this->record->id;

                                // 임시 필드 제거
                                unset($data['year']);
                                unset($data['class_time']);
                                unset($data['order_no']);
                            }
                        }
                        return $data;
                    }),
                Tables\Actions\DeleteAction::make()
                    ->before(function (SubCategory $record) {
                        if ($record->contents()->exists()) {
                            Notification::make()
                                ->title('삭제 실패')
                                ->body('콘텐츠가 있는 서브 카테고리는 삭제할 수 없습니다.')
                                ->danger()
                                ->send();
                            return false;
                        }
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('sort_order', 'asc')
            ->reorderable('sort_order');
    }
}