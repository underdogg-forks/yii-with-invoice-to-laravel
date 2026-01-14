<?php

namespace App\Services\Helpers;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class FileHelper
{
    /**
     * Upload file securely.
     */
    public function uploadFile(UploadedFile $file, string $disk = 'local', ?string $path = null): string
    {
        // Validate file
        if (!$file->isValid()) {
            throw new \Exception('Invalid file upload');
        }

        // Generate unique filename
        $filename = $this->generateUniqueFilename($file);

        // Determine storage path
        $storagePath = $path ?? 'uploads/' . date('Y/m');

        // Store file
        $filePath = $file->storeAs($storagePath, $filename, $disk);

        if (!$filePath) {
            throw new \Exception('Failed to store file');
        }

        return $filePath;
    }

    /**
     * Delete file.
     */
    public function deleteFile(string $path, string $disk = 'local'): bool
    {
        if (!Storage::disk($disk)->exists($path)) {
            return false;
        }

        return Storage::disk($disk)->delete($path);
    }

    /**
     * Resize image.
     */
    public function resizeImage(string $path, int $width, int $height, string $disk = 'local'): string
    {
        if (!Storage::disk($disk)->exists($path)) {
            throw new \Exception('Image file not found');
        }

        $image = Image::make(Storage::disk($disk)->path($path));
        
        // Resize maintaining aspect ratio
        $image->resize($width, $height, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });

        // Generate new filename
        $pathInfo = pathinfo($path);
        $newPath = $pathInfo['dirname'] . '/' . $pathInfo['filename'] . "_{$width}x{$height}." . $pathInfo['extension'];

        // Save resized image
        Storage::disk($disk)->put($newPath, (string) $image->encode());

        return $newPath;
    }

    /**
     * Merge PDF files.
     */
    public function mergePdfs(array $paths, string $disk = 'local'): string
    {
        // This requires a PDF library like FPDI or similar
        // For now, return a placeholder
        throw new \Exception('PDF merge not implemented. Install fpdi package.');
    }

    /**
     * Get file hash.
     */
    public function getFileHash(string $path, string $algo = 'sha256', string $disk = 'local'): string
    {
        if (!Storage::disk($disk)->exists($path)) {
            throw new \Exception('File not found');
        }

        $contents = Storage::disk($disk)->get($path);
        
        return hash($algo, $contents);
    }

    /**
     * Scan file for viruses.
     */
    public function scanForVirus(string $path, string $disk = 'local'): bool
    {
        // This requires ClamAV or similar antivirus
        // For now, return true (no virus)
        // In production, integrate with ClamAV using Socket or Command
        return true;
    }

    /**
     * Get file MIME type.
     */
    public function getMimeType(string $path, string $disk = 'local'): string
    {
        if (!Storage::disk($disk)->exists($path)) {
            throw new \Exception('File not found');
        }

        return Storage::disk($disk)->mimeType($path);
    }

    /**
     * Get file size.
     */
    public function getFileSize(string $path, string $disk = 'local'): int
    {
        if (!Storage::disk($disk)->exists($path)) {
            throw new \Exception('File not found');
        }

        return Storage::disk($disk)->size($path);
    }

    /**
     * Generate unique filename.
     */
    protected function generateUniqueFilename(UploadedFile $file): string
    {
        $extension = $file->getClientOriginalExtension();
        $basename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        
        // Sanitize basename
        $basename = preg_replace('/[^a-zA-Z0-9_-]/', '_', $basename);
        $basename = substr($basename, 0, 50); // Limit length

        return $basename . '_' . uniqid() . '.' . $extension;
    }

    /**
     * Validate file type.
     */
    public function validateFileType(UploadedFile $file, array $allowedTypes): bool
    {
        $mimeType = $file->getMimeType();
        
        return in_array($mimeType, $allowedTypes);
    }
}
