<?php

namespace App\Services;

use App\Models\Question;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\Csv;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class QuestionImportService
{
    public function importFromFile($filePath)
    {
        $extension = pathinfo($filePath, PATHINFO_EXTENSION);
        $data = [];
        $importResults = [
            'success' => 0,
            'failed' => 0,
            'errors' => []
        ];

        try {
            if ($extension === 'csv') {
                $reader = new Csv();
                $reader->setInputEncoding('UTF-8');
                $spreadsheet = $reader->load($filePath);
            } else {
                $spreadsheet = IOFactory::load($filePath);
            }

            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray(null, true, true, true);

            // Skip header row if exists
            $firstRow = reset($rows);
            $hasHeader = false;

            // Check if first row contains headers
            if (is_string($firstRow['A']) && (
                Str::contains(strtolower($firstRow['A']), ['번호', 'no', 'number']) ||
                Str::contains(strtolower($firstRow['B']), ['문제', 'question'])
            )) {
                $hasHeader = true;
                array_shift($rows);
            }

            DB::beginTransaction();

            foreach ($rows as $rowNum => $row) {
                try {
                    // Skip empty rows
                    if (empty($row['A']) && empty($row['B'])) {
                        continue;
                    }

                    // Parse row data
                    $questionData = [
                        'no' => $this->parseNumber($row['A']),
                        'question' => $this->cleanHtml($row['B'] ?? ''),
                        'option_1' => $this->formatOption($row['C'] ?? '', 1),
                        'option_2' => $this->formatOption($row['D'] ?? '', 2),
                        'option_3' => $this->formatOption($row['E'] ?? '', 3),
                        'option_4' => $this->formatOption($row['F'] ?? '', 4),
                        'option_5' => $this->formatOption($row['G'] ?? '', 5),
                        'answer' => $this->parseAnswer($row['H'] ?? null),
                        'explanation' => $this->cleanHtml($row['I'] ?? null),
                    ];

                    // Validate required fields
                    if (empty($questionData['no'])) {
                        throw new \Exception("문제 번호가 없습니다.");
                    }
                    if (empty($questionData['question'])) {
                        throw new \Exception("문제 내용이 없습니다.");
                    }

                    // Check if question number already exists
                    $existingQuestion = Question::where('no', $questionData['no'])->first();

                    if ($existingQuestion) {
                        // Update existing question
                        $existingQuestion->update($questionData);
                    } else {
                        // Create new question
                        Question::create($questionData);
                    }

                    $importResults['success']++;
                } catch (\Exception $e) {
                    $importResults['failed']++;
                    $importResults['errors'][] = "행 {$rowNum}: " . $e->getMessage();
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception("파일 읽기 실패: " . $e->getMessage());
        }

        return $importResults;
    }

    private function parseNumber($value)
    {
        if (is_numeric($value)) {
            return (int) $value;
        }

        // Extract number from string (e.g., "1번" -> 1)
        preg_match('/\d+/', $value, $matches);
        return isset($matches[0]) ? (int) $matches[0] : null;
    }

    private function parseAnswer($value)
    {
        if (empty($value)) {
            return null;
        }

        if (is_numeric($value) && $value >= 1 && $value <= 5) {
            return (int) $value;
        }

        // Try to extract number from string
        preg_match('/\d/', $value, $matches);
        if (isset($matches[0]) && $matches[0] >= 1 && $matches[0] <= 5) {
            return (int) $matches[0];
        }

        return null;
    }

    private function cleanHtml($text)
    {
        if (empty($text)) {
            return '';
        }

        // Convert to string if not already
        $text = (string) $text;

        // Remove "문제:" prefix if exists
        $text = preg_replace('/^문제\s*:\s*/u', '', $text);

        // Basic HTML conversion for common formatting
        $text = nl2br($text);

        // Preserve line breaks
        $text = str_replace(["\r\n", "\r", "\n"], '<br>', $text);

        return trim($text);
    }

    private function formatOption($text, $number)
    {
        if (empty($text)) {
            return '';
        }

        // Convert to string if not already
        $text = (string) $text;

        // Check if option already has circled numbering (①, ②, ③, ④, ⑤)
        $hasCircledNumber = preg_match('/^[①②③④⑤⑥⑦⑧⑨⑩]/u', $text);

        // Check if option has other numbering formats (1., 1), etc.)
        $hasOtherNumber = preg_match('/^\d+[\.\)]\s*/u', $text);

        if (!$hasCircledNumber && !$hasOtherNumber) {
            // Add circled number based on option number only if no numbering exists
            $circledNumbers = ['①', '②', '③', '④', '⑤'];
            $text = $circledNumbers[$number - 1] . ' ' . $text;
        }

        // Basic HTML conversion for common formatting
        $text = nl2br($text);

        // Preserve line breaks
        $text = str_replace(["\r\n", "\r", "\n"], '<br>', $text);

        return trim($text);
    }
}