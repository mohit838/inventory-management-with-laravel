<?php

namespace App\Services;

use Illuminate\Support\Facades\File;

class LogService
{
    protected string $filePath;

    public function __construct()
    {
        $this->filePath = storage_path('logs/laravel.log');
    }

    /**
     * Get the last lines from the log file.
     *
     * @param int $lines
     * @return array
     */
    public function getLogs(int $lines = 100): array
    {
        if (!File::exists($this->filePath)) {
            return [];
        }

        $fileContent = File::get($this->filePath);
        $linesArray = explode("\n", trim($fileContent));

        // Return the last N lines
        return array_slice($linesArray, -$lines);
    }

    /**
     * Clear the log file content.
     *
     * @return bool
     */
    public function clearLogs(): bool
    {
        if (File::exists($this->filePath)) {
            File::put($this->filePath, '');
            return true;
        }

        return false;
    }
}
