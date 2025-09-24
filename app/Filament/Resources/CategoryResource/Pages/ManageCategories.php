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
use Illuminate\Database\Eloquent\Model;
use Filament\Support\Enums\MaxWidth;

class ManageCategories extends Page implements HasForms, HasTable
{
    use InteractsWithTable;
    use InteractsWithForms;

    protected static string $resource = CategoryResource::class;

    protected static string $view = 'filament.resources.category-resource.pages.manage-categories';

    public $selectedCategoryId;

    public function mount(): void
    {
        $firstCategory = Category::orderBy('sort_order')->first();
        $this->selectedCategoryId = request()->get('category', $firstCategory?->id);
    }

    public function getTitle(): string | Htmlable
    {
        return '카테고리 관리';
    }

    public function getCategories()
    {
        return Category::orderBy('sort_order')->get();
    }

    public function selectCategory($categoryId)
    {
        $this->selectedCategoryId = $categoryId;
        $this->resetTable();
    }

    public function getSelectedCategory()
    {
        return Category::find($this->selectedCategoryId);
    }

    protected function getHeaderActions(): array
    {
        $category = $this->getSelectedCategory();

        if (!$category) {
            return [
                Actions\Action::make('create_category')
                    ->label('카테고리 생성')
                    ->model(Category::class)
                    ->form([
                        Forms\Components\TextInput::make('name')
                            ->label('카테고리명')
                            ->required()
                            ->maxLength(255)
                            ->reactive()
                            ->afterStateUpdated(fn ($state, Forms\Set $set) =>
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
                    ])
                    ->action(function (array $data) {
                        $category = Category::create($data);
                        $this->selectedCategoryId = $category->id;

                        Notification::make()
                            ->success()
                            ->title('카테고리가 생성되었습니다.')
                            ->send();
                    }),
            ];
        }

        return [
            Actions\CreateAction::make('create')
                ->label('서브 카테고리 추가')
                ->model(SubCategory::class)
                ->form([
                    Forms\Components\Hidden::make('category_id')
                        ->default($category->id),
                    Forms\Components\TextInput::make('name')
                        ->label('서브 카테고리명')
                        ->required()
                        ->maxLength(255)
                        ->reactive()
                        ->afterStateUpdated(fn ($state, Forms\Set $set) =>
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
                ])
                ->mutateFormDataUsing(function (array $data) use ($category): array {
                    $data['category_id'] = $category->id;
                    return $data;
                })
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title('서브 카테고리가 생성되었습니다.')
                ),
            Actions\Action::make('manage_category')
                ->label('카테고리 편집')
                ->icon('heroicon-o-cog')
                ->color('gray')
                ->form([
                    Forms\Components\TextInput::make('name')
                        ->label('카테고리명')
                        ->required()
                        ->maxLength(255)
                        ->default($category->name),
                    Forms\Components\TextInput::make('slug')
                        ->label('슬러그')
                        ->required()
                        ->maxLength(255)
                        ->default($category->slug),
                    Forms\Components\Textarea::make('description')
                        ->label('설명')
                        ->rows(3)
                        ->default($category->description),
                    Forms\Components\Grid::make(2)
                        ->schema([
                            Forms\Components\TextInput::make('sort_order')
                                ->label('정렬 순서')
                                ->numeric()
                                ->default($category->sort_order),
                            Forms\Components\Toggle::make('is_active')
                                ->label('활성화')
                                ->default($category->is_active),
                        ]),
                ])
                ->action(function (array $data) use ($category) {
                    $category->update($data);

                    Notification::make()
                        ->success()
                        ->title('카테고리가 수정되었습니다.')
                        ->send();
                }),
            Actions\Action::make('create_category')
                ->label('카테고리 생성')
                ->icon('heroicon-o-plus')
                ->color('success')
                ->form([
                    Forms\Components\TextInput::make('name')
                        ->label('카테고리명')
                        ->required()
                        ->maxLength(255)
                        ->reactive()
                        ->afterStateUpdated(fn ($state, Forms\Set $set) =>
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
                ])
                ->action(function (array $data) {
                    $category = Category::create($data);
                    $this->selectedCategoryId = $category->id;

                    Notification::make()
                        ->success()
                        ->title('카테고리가 생성되었습니다.')
                        ->send();
                }),
        ];
    }

    public function table(Table $table): Table
    {
        $category = $this->getSelectedCategory();

        if (!$category) {
            return $table
                ->query(SubCategory::query()->whereNull('id'))
                ->columns([])
                ->emptyStateHeading('카테고리를 먼저 생성해주세요')
                ->emptyStateIcon('heroicon-o-folder');
        }

        return $table
            ->query(SubCategory::query()->where('category_id', $this->selectedCategoryId))
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
                    ->form([
                        Forms\Components\TextInput::make('name')
                            ->label('서브 카테고리명')
                            ->required()
                            ->maxLength(255),
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
                    ]),
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
            ->reorderable('sort_order')
            ->emptyStateHeading('서브 카테고리가 없습니다')
            ->emptyStateDescription('상단의 "서브 카테고리 추가" 버튼을 클릭하여 추가하세요')
            ->emptyStateIcon('heroicon-o-folder-open');
    }
}