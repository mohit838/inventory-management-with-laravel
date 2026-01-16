<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver; // Using GD by default
use Exception;

class MinioService
{
    protected string $disk = 'minio';

    public function uploadImage(UploadedFile $file, string $path = 'products', int $quality = 75, int $maxRetry = 3): ?string
    {
        $attempt = 0;
        $manager = new ImageManager(new Driver());

        do {
            $attempt++;
            try {
                // 1. Compress Image
                // We'll convert to WebP/JPG and resize if too big (optional, but good for SaaS)
                // For now, simple encoding/compression.
                $image = $manager->read($file->getRealPath());
                $image->scale(width: 1920); // Max width 1920
                $encoded = $image->toWebp(quality: $quality);

                // 2. Generate Filename
                $filename = $path . '/' . uniqid() . '.webp';

                // 3. Upload to MinIO
                Storage::disk($this->disk)->put($filename, (string)$encoded);

                // 4. Return URL
                return Storage::disk($this->disk)->url($filename);

            } catch (Exception $e) {
                Log::error("MinIO Upload Failed (Attempt $attempt): " . $e->getMessage());
                
                if ($attempt >= $maxRetry) {
                     // In a real generic class, we might throw or return null. 
                     // User said: "show message upload failed, try again or later".
                     // We can throw generic exception catchable by Controller.
                     throw new Exception("Upload failed after $maxRetry attempts. Please try again later. Error: " . $e->getMessage());
                }
                
                sleep(1); // Backoff slightly
            }
        } while ($attempt < $maxRetry);

        return null; // Should be unreachable due to throw above
    }
}
