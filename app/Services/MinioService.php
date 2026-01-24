<?php

namespace App\Services;

use Exception;
use Illuminate\Contracts\Filesystem\Cloud;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver; // Using GD by default
use Intervention\Image\ImageManager;

class MinioService
{
    /**
     * Upload an image to private storage with compression.
     */
    public function uploadImage(UploadedFile $file, ?int $userId = null, string $path = 'products', int $quality = 75): ?string
    {
        $directory = $userId ? "tenants/{$userId}/{$path}" : $path;
        return $this->processAndUpload($file, 'minio_private', $directory, $quality);
    }

    /**
     * Upload an image to public storage and return the URL.
     */
    public function uploadPublic(UploadedFile $file, string $path = 'public', int $quality = 75): ?string
    {
        return $this->processAndUpload($file, 'minio', $path, $quality);
    }

    /**
     * Internal helper to handle image processing and storage interactions.
     */
    protected function processAndUpload(UploadedFile $file, string $disk, string $directory, int $quality): ?string
    {
        return retry(3, function () use ($file, $disk, $directory, $quality) {
            try {
                // 1. Process Image using Intervention v3
                $manager = new ImageManager(new Driver());
                $image = $manager->read($file->getRealPath());
                
                // Professional scaling and compression
                $image->scale(width: 1920);
                $encoded = $image->toWebp(quality: $quality);

                // 2. Generate path
                $filename = trim($directory, '/') . '/' . uniqid() . '.webp';

                // 3. Storage interaction
                /** @var Cloud $storage */
                $storage = Storage::disk($disk);
                
                if (! $storage->put($filename, (string) $encoded)) {
                    throw new Exception("Storage put operation failed for path: {$filename}");
                }

                // Temporary URL check
                if ($disk === 'minio') {
                    $url = $storage->url($filename);
                } else {
                    $url = $filename; // Store relative path for private
                }
                
                Log::info("Image uploaded successfully: {$filename}");
                return $url;

            } catch (Exception $e) {
                Log::error("MinIO Upload Error: " . $e->getMessage());
                throw $e; // Rethrow for retry() to catch
            }
        }, 100);
    }

    /**
     * Generate a temporary URL for a private file
     */
    public function getTemporaryUrl(string $path, int $minutes = 60): string
    {
        $disk = Storage::disk('minio_private');
        
        if (method_exists($disk, 'temporaryUrl')) {
            try {
                return $disk->temporaryUrl($path, now()->addMinutes($minutes));
            } catch (Exception $e) {
                Log::warning("Could not generate temporary URL: " . $e->getMessage());
            }
        }

        return $disk->url($path);
    }
}
