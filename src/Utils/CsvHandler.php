<?php

namespace App\Utils;

class CsvHandler
{
    public function read(string $filePath, string $delimiter = ','): array
    {
        $rows = [];
        if (!file_exists($filePath) || !is_readable($filePath)) {
            return $rows;
        }

        if (($handle = fopen($filePath, 'r')) !== false) {
            $headers = fgetcsv($handle, 1000, $delimiter);
            if (!$headers) {
                return $rows;
            }

            while (($data = fgetcsv($handle, 1000, $delimiter)) !== false) {
                $rows[] = array_combine($headers, $data);
            }
            fclose($handle);
        }

        return $rows;
    }

    public function outputToBrowser(string $filename, array $data, string $delimiter = ','): void
    {
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $output = fopen('php://output', 'w');

        if (!empty($data)) {
            fputcsv($output, array_keys($data[0]), $delimiter);

            foreach ($data as $row) {
                fputcsv($output, $row, $delimiter);
            }
        }

        fclose($output);
        exit;
    }
}
