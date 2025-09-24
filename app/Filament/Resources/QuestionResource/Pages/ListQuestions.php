<?php

namespace App\Filament\Resources\QuestionResource\Pages;

use App\Filament\Resources\QuestionResource;
use App\Services\QuestionImportService;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Forms;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Storage;

class ListQuestions extends ListRecords
{
    protected static string $resource = QuestionResource::class;

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
                        ->helperText('엑셀 또는 CSV 파일을 업로드하세요. (열 순서: 번호, 문제, 보기1~5, 정답, 해설)')
                ])
                ->action(function (array $data) {
                    try {
                        $filePath = storage_path('app/public/' . $data['file']);

                        $importService = new QuestionImportService();
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
