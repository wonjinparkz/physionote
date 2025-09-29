<?php

namespace App\Filament\Resources\QuestionResource\Pages;

use App\Filament\Resources\QuestionResource;
use App\Services\QuestionImportService;
use App\Models\Category;
use App\Models\SubCategory;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Filament\Forms;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Builder;

class ListQuestions extends ListRecords
{
    protected static string $resource = QuestionResource::class;

    public function getTabs(): array
    {
        $tabs = [
            'all' => Tab::make('전체')
                ->badge($this->getModel()::count()),
        ];

        // 문제 카테고리의 하위 카테고리들을 탭으로 추가
        $questionCategory = Category::where('name', '문제')
            ->with('subCategories')
            ->first();

        if ($questionCategory) {
            foreach ($questionCategory->subCategories as $subCategory) {
                // 해당 서브카테고리에 문제가 있는지 확인
                $questionCount = $this->getModel()::where('sub_category_id', $subCategory->id)->count();

                // 문제가 있는 경우에만 탭 추가
                if ($questionCount > 0) {
                    $slug = $subCategory->slug ?: \Illuminate\Support\Str::slug($subCategory->name);
                    // 설명이 있으면 설명을 표시, 없으면 이름을 표시
                    $tabLabel = $subCategory->description ?: $subCategory->name;

                    $tabs[$slug] = Tab::make($tabLabel)
                        ->modifyQueryUsing(fn (Builder $query) => $query->where('sub_category_id', $subCategory->id))
                        ->badge($questionCount);
                }

                // 하위의 하위 카테고리가 있으면 추가
                $subSubCategories = SubCategory::where('category_id', $subCategory->id)->get();
                foreach ($subSubCategories as $subSub) {
                    // 하위 서브카테고리에 문제가 있는지 확인
                    $subQuestionCount = $this->getModel()::where('sub_category_id', $subSub->id)->count();

                    // 문제가 있는 경우에만 탭 추가
                    if ($subQuestionCount > 0) {
                        $subSlug = $subSub->slug ?: \Illuminate\Support\Str::slug($subSub->name);
                        // 하위 카테고리도 설명이 있으면 설명 표시
                        $subTabLabel = $subSub->description ?: ($subCategory->name . ' > ' . $subSub->name);

                        $tabs[$subSlug] = Tab::make($subTabLabel)
                            ->modifyQueryUsing(fn (Builder $query) => $query->where('sub_category_id', $subSub->id))
                            ->badge($subQuestionCount);
                    }
                }
            }
        }

        return $tabs;
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('import')
                ->label('파일 업로드')
                ->icon('heroicon-o-arrow-up-tray')
                ->color('info')
                ->form([
                    Forms\Components\FileUpload::make('file')
                        ->label('엑셀/CSV 파일')
                        ->acceptedFileTypes([
                            'application/vnd.ms-excel',
                            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                            'text/csv',
                            '.xlsx',
                            '.xls',
                            '.csv'
                        ])
                        ->required()
                        ->helperText('엑셀 또는 CSV 파일을 업로드하세요. (열 순서: 번호, 문제, 보기1~5, 정답, 해설)'),

                    Forms\Components\Section::make('카테고리 일괄 적용')
                        ->description('업로드된 모든 문제에 적용할 카테고리를 선택하세요.')
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
                                            $label = $subCategory->name;
                                            if ($subCategory->slug) {
                                                $label .= ' | ' . $subCategory->slug;
                                            }
                                            if ($subCategory->description) {
                                                $label .= ' | ' . $subCategory->description;
                                            }
                                            $options[$subCategory->id] = $label;
                                        } else {
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
                        ->columns(1)
                ])
                ->action(function (array $data) {
                    try {
                        $filePath = storage_path('app/public/' . $data['file']);

                        $importService = new QuestionImportService();

                        // sub_category_id만 설정 (category_id는 자동으로 결정됨)
                        $importService->setCategoryDefaults(
                            null,  // category_id는 자동 설정
                            $data['sub_category_id'] ?? null
                        );
                        $results = $importService->importFromFile($filePath);

                        // Delete temporary file
                        Storage::disk('public')->delete($data['file']);

                        if ($results['success'] > 0) {
                            Notification::make()
                                ->title('가져오기 완료')
                                ->body("성공: {$results['success']}개, 실패: {$results['failed']}개")
                                ->success()
                                ->send();
                        }

                        if ($results['failed'] > 0 && !empty($results['errors'])) {
                            foreach ($results['errors'] as $error) {
                                Notification::make()
                                    ->title('오류')
                                    ->body($error)
                                    ->danger()
                                    ->send();
                            }
                        }
                    } catch (\Exception $e) {
                        Notification::make()
                            ->title('가져오기 실패')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),
            Actions\CreateAction::make(),
        ];
    }
}
