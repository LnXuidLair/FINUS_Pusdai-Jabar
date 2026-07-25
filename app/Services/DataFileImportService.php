<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use InvalidArgumentException;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DataFileImportService
{
    public function readRows(UploadedFile $file): array
    {
        $extension = strtolower($file->getClientOriginalExtension());

        return match ($extension) {
            'csv', 'txt' => $this->readCsvRows($file->getRealPath()),
            'xlsx', 'xls' => $this->readSpreadsheetRows($file->getRealPath()),
            default => throw new InvalidArgumentException('Format file tidak didukung. Gunakan CSV, TXT, XLSX, atau XLS.'),
        };
    }

    public function normalizeHeader(array $header): array
    {
        return array_map(function ($value): string {
            $value = preg_replace('/^\xEF\xBB\xBF/', '', (string) $value);

            return Str::of($value)
                ->ascii()
                ->lower()
                ->trim()
                ->replaceMatches('/[\s\-]+/', '_')
                ->replaceMatches('/[^a-z0-9_]/', '')
                ->toString();
        }, $header);
    }

    public function streamCsvTemplate(string $filename, array $rows): StreamedResponse
    {
        return response()->streamDownload(function () use ($rows): void {
            $handle = fopen('php://output', 'wb');

            if ($handle === false) {
                return;
            }

            fwrite($handle, "\xEF\xBB\xBF");

            foreach ($rows as $row) {
                fputcsv($handle, $row);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    private function readCsvRows(string $path): array
    {
        $delimiter = $this->detectCsvDelimiter($path);
        $handle = fopen($path, 'r');

        if ($handle === false) {
            throw new InvalidArgumentException('File CSV tidak dapat dibaca.');
        }

        $rows = [];

        while (($row = fgetcsv($handle, 0, $delimiter)) !== false) {
            $row = $this->normalizeRow($row);

            if (! $this->isEmptyRow($row)) {
                $rows[] = $row;
            }
        }

        fclose($handle);

        return $rows;
    }

    private function readSpreadsheetRows(string $path): array
    {
        $spreadsheet = IOFactory::load($path);
        $sheet = $spreadsheet->getActiveSheet();

        $rawRows = $sheet->toArray(null, true, true, false);

        $rows = [];

        foreach ($rawRows as $row) {
            $row = $this->normalizeRow($row);

            if (! $this->isEmptyRow($row)) {
                $rows[] = $row;
            }
        }

        $spreadsheet->disconnectWorksheets();
        unset($spreadsheet);

        return $rows;
    }

    private function normalizeRow(array $row): array
    {
        return array_map(function ($value): string {
            return trim((string) $value);
        }, $row);
    }

    private function isEmptyRow(array $row): bool
    {
        return count(array_filter($row, fn ($value) => trim((string) $value) !== '')) === 0;
    }

    private function detectCsvDelimiter(string $path): string
    {
        $firstLine = '';
        $handle = fopen($path, 'r');

        if ($handle !== false) {
            $firstLine = fgets($handle) ?: '';
            fclose($handle);
        }

        $commaCount = substr_count($firstLine, ',');
        $semicolonCount = substr_count($firstLine, ';');

        return $semicolonCount > $commaCount ? ';' : ',';
    }
}