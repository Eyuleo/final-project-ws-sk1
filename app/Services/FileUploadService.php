<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

/**
 * FileUploadService handles all file upload operations including:
 * - Profile pictures with image optimization
 * - Portfolio files
 * - Order deliverables
 * - Message attachments
 */
class FileUploadService
{
    /**
     * Allowed MIME types for different file categories
     */
    protected const IMAGE_MIMES = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    protected const DOCUMENT_MIMES = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
    protected const ARCHIVE_MIMES = ['application/zip', 'application/x-rar-compressed'];
    protected const VIDEO_MIMES = ['video/mp4', 'video/quicktime', 'video/x-msvideo'];

    /**
     * Maximum file sizes in bytes
     */
    protected const MAX_IMAGE_SIZE = 5 * 1024 * 1024; // 5MB
    protected const MAX_DOCUMENT_SIZE = 10 * 1024 * 1024; // 10MB
    protected const MAX_VIDEO_SIZE = 50 * 1024 * 1024; // 50MB
    protected const MAX_ARCHIVE_SIZE = 20 * 1024 * 1024; // 20MB

    /**
     * Upload and optimize a profile picture
     *
     * @param UploadedFile $file The uploaded file
     * @param int $userId The user ID
     * @return string The stored file path
     */
    public function uploadProfilePicture(UploadedFile $file, int $userId): string
    {
        $this->validateImage($file);

        // Generate unique filename
        $filename = $this->generateFilename($file, 'profile');

        // Optimize and resize image
        $image = Image::make($file);
        
        // Resize to max 800x800 while maintaining aspect ratio
        $image->resize(800, 800, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });

        // Optimize quality
        $image->encode('jpg', 85);

        // Store the optimized image
        $path = "profiles/{$userId}/{$filename}";
        Storage::disk('public')->put($path, (string) $image);

        // Create thumbnail (200x200)
        $thumbnail = Image::make($file);
        $thumbnail->fit(200, 200);
        $thumbnail->encode('jpg', 80);
        $thumbnailPath = "profiles/{$userId}/thumb_{$filename}";
        Storage::disk('public')->put($thumbnailPath, (string) $thumbnail);

        return $path;
    }

    /**
     * Upload a portfolio file
     *
     * @param UploadedFile $file The uploaded file
     * @param int $userId The user ID
     * @return array File information [path, type, size, original_name]
     */
    public function uploadPortfolioFile(UploadedFile $file, int $userId): array
    {
        $this->validatePortfolioFile($file);

        $filename = $this->generateFilename($file, 'portfolio');
        $path = "portfolios/{$userId}/{$filename}";

        // Store the file
        Storage::disk('public')->putFileAs(
            "portfolios/{$userId}",
            $file,
            $filename
        );

        // If it's an image, create a thumbnail
        if ($this->isImage($file)) {
            $this->createThumbnail($file, "portfolios/{$userId}", $filename);
        }

        return [
            'path' => $path,
            'type' => $file->getMimeType(),
            'size' => $file->getSize(),
            'original_name' => $file->getClientOriginalName(),
        ];
    }

    /**
     * Upload order deliverable files
     *
     * @param UploadedFile $file The uploaded file
     * @param int $orderId The order ID
     * @return array File information [path, type, size, original_name]
     */
    public function uploadDeliverable(UploadedFile $file, int $orderId): array
    {
        $this->validateDeliverableFile($file);

        $filename = $this->generateFilename($file, 'deliverable');
        $path = "deliverables/{$orderId}/{$filename}";

        // Store the file
        Storage::disk('public')->putFileAs(
            "deliverables/{$orderId}",
            $file,
            $filename
        );

        return [
            'path' => $path,
            'type' => $file->getMimeType(),
            'size' => $file->getSize(),
            'original_name' => $file->getClientOriginalName(),
        ];
    }

    /**
     * Upload message attachment
     *
     * @param UploadedFile $file The uploaded file
     * @param int $orderId The order ID
     * @return array File information [path, type, size, original_name]
     */
    public function uploadMessageAttachment(UploadedFile $file, int $orderId): array
    {
        $this->validateMessageAttachment($file);

        $filename = $this->generateFilename($file, 'message');
        $path = "messages/{$orderId}/{$filename}";

        // Store the file
        Storage::disk('public')->putFileAs(
            "messages/{$orderId}",
            $file,
            $filename
        );

        // If it's an image, create a thumbnail
        if ($this->isImage($file)) {
            $this->createThumbnail($file, "messages/{$orderId}", $filename);
        }

        return [
            'path' => $path,
            'type' => $file->getMimeType(),
            'size' => $file->getSize(),
            'original_name' => $file->getClientOriginalName(),
        ];
    }

    /**
     * Delete a file
     *
     * @param string $path The file path
     * @return bool Success status
     */
    public function deleteFile(string $path): bool
    {
        if (Storage::disk('public')->exists($path)) {
            // Also delete thumbnail if it exists
            $directory = dirname($path);
            $filename = basename($path);
            $thumbnailPath = "{$directory}/thumb_{$filename}";
            
            if (Storage::disk('public')->exists($thumbnailPath)) {
                Storage::disk('public')->delete($thumbnailPath);
            }

            return Storage::disk('public')->delete($path);
        }

        return false;
    }

    /**
     * Get file URL
     *
     * @param string $path The file path
     * @return string The public URL
     */
    public function getFileUrl(string $path): string
    {
        return Storage::disk('public')->url($path);
    }

    /**
     * Generate a unique filename
     *
     * @param UploadedFile $file The uploaded file
     * @param string $prefix Filename prefix
     * @return string The generated filename
     */
    protected function generateFilename(UploadedFile $file, string $prefix): string
    {
        $extension = $file->getClientOriginalExtension();
        $timestamp = now()->timestamp;
        $random = Str::random(8);
        
        return "{$prefix}_{$timestamp}_{$random}.{$extension}";
    }

    /**
     * Create a thumbnail for an image
     *
     * @param UploadedFile $file The original file
     * @param string $directory The storage directory
     * @param string $filename The filename
     * @return void
     */
    protected function createThumbnail(UploadedFile $file, string $directory, string $filename): void
    {
        $thumbnail = Image::make($file);
        $thumbnail->fit(300, 300);
        $thumbnail->encode('jpg', 80);
        
        $thumbnailPath = "{$directory}/thumb_{$filename}";
        Storage::disk('public')->put($thumbnailPath, (string) $thumbnail);
    }

    /**
     * Validate image file
     *
     * @param UploadedFile $file The file to validate
     * @throws \InvalidArgumentException
     */
    protected function validateImage(UploadedFile $file): void
    {
        if (!in_array($file->getMimeType(), self::IMAGE_MIMES)) {
            throw new \InvalidArgumentException('Invalid image file type. Allowed types: JPEG, PNG, GIF, WebP');
        }

        if ($file->getSize() > self::MAX_IMAGE_SIZE) {
            throw new \InvalidArgumentException('Image file size exceeds maximum allowed size of 5MB');
        }
    }

    /**
     * Validate portfolio file
     *
     * @param UploadedFile $file The file to validate
     * @throws \InvalidArgumentException
     */
    protected function validatePortfolioFile(UploadedFile $file): void
    {
        $allowedMimes = array_merge(
            self::IMAGE_MIMES,
            self::DOCUMENT_MIMES,
            self::VIDEO_MIMES
        );

        if (!in_array($file->getMimeType(), $allowedMimes)) {
            throw new \InvalidArgumentException('Invalid portfolio file type');
        }

        $maxSize = $this->isVideo($file) ? self::MAX_VIDEO_SIZE : self::MAX_DOCUMENT_SIZE;
        if ($file->getSize() > $maxSize) {
            throw new \InvalidArgumentException('Portfolio file size exceeds maximum allowed size');
        }
    }

    /**
     * Validate deliverable file
     *
     * @param UploadedFile $file The file to validate
     * @throws \InvalidArgumentException
     */
    protected function validateDeliverableFile(UploadedFile $file): void
    {
        $allowedMimes = array_merge(
            self::IMAGE_MIMES,
            self::DOCUMENT_MIMES,
            self::ARCHIVE_MIMES,
            self::VIDEO_MIMES
        );

        if (!in_array($file->getMimeType(), $allowedMimes)) {
            throw new \InvalidArgumentException('Invalid deliverable file type');
        }

        $maxSize = match (true) {
            $this->isVideo($file) => self::MAX_VIDEO_SIZE,
            $this->isArchive($file) => self::MAX_ARCHIVE_SIZE,
            default => self::MAX_DOCUMENT_SIZE,
        };

        if ($file->getSize() > $maxSize) {
            throw new \InvalidArgumentException('Deliverable file size exceeds maximum allowed size');
        }
    }

    /**
     * Validate message attachment
     *
     * @param UploadedFile $file The file to validate
     * @throws \InvalidArgumentException
     */
    protected function validateMessageAttachment(UploadedFile $file): void
    {
        $allowedMimes = array_merge(
            self::IMAGE_MIMES,
            self::DOCUMENT_MIMES
        );

        if (!in_array($file->getMimeType(), $allowedMimes)) {
            throw new \InvalidArgumentException('Invalid message attachment type');
        }

        if ($file->getSize() > self::MAX_DOCUMENT_SIZE) {
            throw new \InvalidArgumentException('Message attachment size exceeds maximum allowed size of 10MB');
        }
    }

    /**
     * Check if file is an image
     *
     * @param UploadedFile $file The file to check
     * @return bool
     */
    protected function isImage(UploadedFile $file): bool
    {
        return in_array($file->getMimeType(), self::IMAGE_MIMES);
    }

    /**
     * Check if file is a video
     *
     * @param UploadedFile $file The file to check
     * @return bool
     */
    protected function isVideo(UploadedFile $file): bool
    {
        return in_array($file->getMimeType(), self::VIDEO_MIMES);
    }

    /**
     * Check if file is an archive
     *
     * @param UploadedFile $file The file to check
     * @return bool
     */
    protected function isArchive(UploadedFile $file): bool
    {
        return in_array($file->getMimeType(), self::ARCHIVE_MIMES);
    }
}
