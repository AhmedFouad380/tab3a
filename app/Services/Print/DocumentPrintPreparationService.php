<?php

namespace App\Services\Print;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class DocumentPrintPreparationService
{
    /**
     * Prepare a print-ready file for the kiosk, enforcing grayscale conversion when requested.
     */
    public function preparePrintFile(string $relativeFilePath, string $colorMode = 'black_and_white', ?string $pageRange = 'all'): string
    {
        $disk = Storage::disk('public');
        $fullPath = null;

        if ($disk->exists($relativeFilePath)) {
            $fullPath = $disk->path($relativeFilePath);
        } else {
            $directPath = storage_path('app/public/'.$relativeFilePath);
            if (file_exists($directPath)) {
                $fullPath = $directPath;
            }
        }

        if (! $fullPath || ! file_exists($fullPath)) {
            return $relativeFilePath;
        }

        // If color mode is color and no specific transformation is needed, return original
        if ($colorMode === 'color') {
            return $relativeFilePath;
        }

        $extension = strtolower(pathinfo($fullPath, PATHINFO_EXTENSION));
        $filename = pathinfo($fullPath, PATHINFO_FILENAME);

        $disk->makeDirectory('print_ready');

        $outputFileName = $filename.'_bw_'.$colorMode.'.'.$extension;
        $outputRelPath = 'print_ready/'.$outputFileName;
        $outputFullPath = $disk->path($outputRelPath);

        // 1. Process Images (JPG, JPEG, PNG, WEBP)
        if (in_array($extension, ['jpg', 'jpeg', 'png', 'webp'])) {
            $converted = $this->convertImageToGrayscale($fullPath, $outputFullPath, $extension);
            if ($converted) {
                return $outputRelPath;
            }
        }

        // 2. Process PDFs
        if ($extension === 'pdf') {
            $converted = $this->convertPdfToGrayscaleWithGhostscript($fullPath, $outputFullPath);
            if ($converted) {
                return $outputRelPath;
            }
        }

        return $relativeFilePath;
    }

    /**
     * Convert raster image to pure grayscale using GD or Imagick.
     */
    protected function convertImageToGrayscale(string $inputPath, string $outputPath, string $extension): bool
    {
        try {
            if (extension_loaded('gd')) {
                $image = match ($extension) {
                    'png' => @imagecreatefrompng($inputPath),
                    'webp' => @imagecreatefromwebp($inputPath),
                    default => @imagecreatefromjpeg($inputPath),
                };

                if ($image !== false) {
                    imagefilter($image, IMG_FILTER_GRAYSCALE);
                    imagefilter($image, IMG_FILTER_CONTRAST, -5); // subtle contrast enhancement for crisp text

                    match ($extension) {
                        'png' => imagepng($image, $outputPath, 8),
                        'webp' => imagewebp($image, $outputPath, 90),
                        default => imagejpeg($image, $outputPath, 95),
                    };

                    imagedestroy($image);

                    return file_exists($outputPath) && filesize($outputPath) > 0;
                }
            }

            if (extension_loaded('imagick')) {
                $im = new \Imagick($inputPath);
                $im->transformImageColorspace(\Imagick::COLORSPACE_GRAY);
                $im->writeImage($outputPath);
                $im->clear();
                $im->destroy();

                return file_exists($outputPath) && filesize($outputPath) > 0;
            }
        } catch (\Throwable $e) {
            Log::warning('Image grayscale conversion error: '.$e->getMessage());
        }

        return false;
    }

    /**
     * Convert PDF to pure DeviceGray using Ghostscript if installed on server.
     */
    protected function convertPdfToGrayscaleWithGhostscript(string $inputPath, string $outputPath): bool
    {
        if (! $this->isExecAvailable()) {
            return false;
        }

        $gsBinary = $this->findGhostscriptBinary();
        if (! $gsBinary) {
            return false;
        }

        try {
            $cmd = sprintf(
                '%s -sDEVICE=pdfwrite -dColorConversionStrategy=Gray -dProcessColorModel=/DeviceGray -dCompatibilityLevel=1.4 -dNOPAUSE -dBATCH -sOutputFile=%s %s 2>&1',
                escapeshellcmd($gsBinary),
                escapeshellarg($outputPath),
                escapeshellarg($inputPath)
            );

            $output = [];
            $returnCode = 1;
            exec($cmd, $output, $returnCode);

            return $returnCode === 0 && file_exists($outputPath) && filesize($outputPath) > 0;
        } catch (\Throwable $e) {
            Log::warning('Ghostscript PDF conversion failed: '.$e->getMessage());
        }

        return false;
    }

    /**
     * Locate Ghostscript binary if available on system.
     */
    protected function findGhostscriptBinary(): ?string
    {
        $directPaths = ['/usr/bin/gs', '/usr/local/bin/gs', '/opt/homebrew/bin/gs'];
        foreach ($directPaths as $path) {
            if (@file_exists($path) && @is_executable($path)) {
                return $path;
            }
        }

        if (! $this->isExecAvailable()) {
            return null;
        }

        $possiblePaths = ['gs', '/usr/bin/gs', '/usr/local/bin/gs', '/opt/homebrew/bin/gs'];

        foreach ($possiblePaths as $path) {
            $output = [];
            $returnCode = 1;
            @exec("which $path 2>/dev/null", $output, $returnCode);
            if ($returnCode === 0 && ! empty($output[0]) && @is_executable($output[0])) {
                return $output[0];
            }
        }

        return null;
    }

    /**
     * Safely determine if the shell exec() function is enabled and usable on this PHP environment.
     */
    protected function isExecAvailable(): bool
    {
        if (! function_exists('exec')) {
            return false;
        }

        $disabled = (string) ini_get('disable_functions');
        if (! empty($disabled)) {
            $disabledList = array_map('trim', explode(',', strtolower($disabled)));
            if (in_array('exec', $disabledList, true)) {
                return false;
            }
        }

        return true;
    }
}
