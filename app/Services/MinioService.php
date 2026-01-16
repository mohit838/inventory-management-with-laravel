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
    protected string $disk = 'minio_private';

    public function uploadImage(UploadedFile $file, int $userId, string $path = 'products', int $quality = 75, int $maxRetry = 3): ?string
    {
        $attempt = 0;
        $manager = new ImageManager(new Driver());

        do {
            $attempt++;
            try {
                // 1. Compress Image
                $image = $manager->read($file->getRealPath());
                $image->scale(width: 1920); // Max width 1920
                $encoded = $image->toWebp(quality: $quality);

                // 2. Generate Filename (Relative Path)
                // format: tenants/{userId}/{path}/{uuid}.webp
                $filename = "tenants/{$userId}/{$path}/" . uniqid() . '.webp';

                // 3. Upload to MinIO Private Disk
                Storage::disk($this->disk)->put($filename, (string)$encoded);

                // 4. Return Relative Path (Not URL)
                return $filename;

            } catch (Exception $e) {
                Log::error("MinIO Upload Failed (Attempt $attempt): " . $e->getMessage());
                
                if ($attempt >= $maxRetry) {
                     throw new Exception("Upload failed after $maxRetry attempts. Please try again later.");
                }
                
                sleep(1); 
            }
        } while ($attempt < $maxRetry);

        return null; 
    }
}
