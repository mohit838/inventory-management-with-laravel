<?php

namespace App\Services;

use Exception;
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
                /** @var \Illuminate\Contracts\Filesystem\Cloud $storage */
                $storage = Storage::disk($disk);
                $storage->put($filename, (string) $encoded);

                // 4. Return appropriate path or URL
                return $disk === 'minio' ? $storage->url($filename) : $filename;

            } catch (Exception $e) {
                Log::error("MinIO Upload Failed on disk [{$disk}]: " . $e->getMessage());
                throw $e; // Rethrow for retry() to catch
            }
        }, 100);
    }
}
