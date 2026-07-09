<?php
declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| File Information
|--------------------------------------------------------------------------
| Written By: Jules Warner
| Company: WILabs
| Product: WI Ecosystem
| Project: WICMS / WICOS / WIKitchenCompli
| File: WIImportExportService.php
| Location: /root/WIAdmin/WICore/WIClass/WIImportExportService.php
| Type: PHP Shared Service
| Layer: Shared Backend Utility
| Purpose Area: Import / Export / CSV Foundation
| Version: 1.0.0
| Created: 2026-06-06
| Last Updated: 2026-06-06
| Status: Production Ready - Import Export Foundation
|--------------------------------------------------------------------------
| Summary:
| Shared import/export helper for CSV template generation, CSV parsing,
| CSV export rendering and import/export job logging across WI modules.
| Upload storage remains owned by WIMedia; this class processes already
| stored/linked media files and generated export rows only.
|--------------------------------------------------------------------------
*/

if (!class_exists('WIImportExportService')) {
    final class WIImportExportService
    {
        private WIdb $WIdb;

        public function __construct(?WIdb $WIdb = null)
        {
            $this->WIdb = $WIdb ?? WIdb::getInstance();
        }

        /**
         * @param array<int, string> $headers
         * @param array<int, array<string, mixed>> $rows
         */
        public function toCsv(array $headers, array $rows): string
        {
            $stream = fopen('php://temp', 'r+');

            if (!is_resource($stream)) {
                return '';
            }

            fputcsv($stream, $headers);

            foreach ($rows as $row) {
                $line = [];
                foreach ($headers as $header) {
                    $line[] = $row[$header] ?? '';
                }
                fputcsv($stream, $line);
            }

            rewind($stream);
            $csv = stream_get_contents($stream);
            fclose($stream);

            return is_string($csv) ? $csv : '';
        }

        /**
         * @return array<int, array<string, string>>
         */
        public function parseCsvFile(string $path, int $maxRows = 1000): array
        {
            if ($path === '' || !is_file($path) || !is_readable($path)) {
                return [];
            }

            $handle = fopen($path, 'r');

            if (!is_resource($handle)) {
                return [];
            }

            $headers = [];
            $rows = [];
            $rowIndex = 0;

            while (($data = fgetcsv($handle)) !== false) {
                $data = array_map(static function (mixed $value): string {
                    $value = (string) $value;
                    $value = preg_replace('/^\xEF\xBB\xBF/', '', $value) ?? $value;
                    return trim($value);
                }, $data);

                if ($rowIndex === 0) {
                    $headers = array_map([$this, 'normaliseHeader'], $data);
                    $rowIndex++;
                    continue;
                }

                if ($rowIndex > $maxRows) {
                    break;
                }

                $row = [];
                foreach ($headers as $index => $header) {
                    if ($header === '') {
                        continue;
                    }
                    $row[$header] = $data[$index] ?? '';
                }

                if (implode('', $row) !== '') {
                    $rows[] = $row;
                }

                $rowIndex++;
            }

            fclose($handle);

            return $rows;
        }

        public function normaliseHeader(mixed $value): string
        {
            $header = strtolower(trim((string) $value));
            $header = preg_replace('/[^a-z0-9]+/', '_', $header) ?? $header;
            return trim($header, '_');
        }

        public function resolveMediaFilePath(int $mediaId, string $projectRoot): string
        {
            if ($mediaId <= 0 || !$this->WIdb->tableExists('wi_media')) {
                return '';
            }

            $rows = $this->WIdb->select(
                'SELECT `id`, `file_path`, `stored_name`, `original_name`, `extension`, `mime_type`, `media_type`
                 FROM `wi_media`
                 WHERE `id` = :media_id
                   AND (`deleted_at` IS NULL OR `deleted_at` = "0000-00-00 00:00:00")
                 LIMIT 1',
                ['media_id' => $mediaId]
            );

            $row = is_array($rows[0] ?? null) ? $rows[0] : [];
            $filePath = (string) ($row['file_path'] ?? '');

            if ($filePath === '') {
                return '';
            }

            if (str_starts_with($filePath, '/') && is_file($filePath)) {
                return $filePath;
            }

            $normalised = ltrim(str_replace('\\', '/', $filePath), '/');
            $candidates = [
                rtrim($projectRoot, '/') . '/' . $normalised,
                rtrim($projectRoot, '/') . '/WIAdmin/' . $normalised,
                rtrim($projectRoot, '/') . '/WIAdmin/WIMedia/' . basename($normalised),
            ];

            foreach ($candidates as $candidate) {
                if (is_file($candidate) && is_readable($candidate)) {
                    return $candidate;
                }
            }

            return '';
        }

        /**
         * @param array<string, mixed> $data
         */
        public function logJob(array $data): void
        {
            if (!$this->WIdb->tableExists('wi_import_export_jobs')) {
                return;
            }

            $allowed = [];
            foreach ($data as $column => $value) {
                if ($this->WIdb->columnExists('wi_import_export_jobs', (string) $column)) {
                    $allowed[$column] = $value;
                }
            }

            if ($allowed === []) {
                return;
            }

            $this->WIdb->insert('wi_import_export_jobs', $allowed);
        }
    }
}
