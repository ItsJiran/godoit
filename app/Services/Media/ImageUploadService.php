<?php

namespace App\Services\Media;

use App\Models\Image;
use Illuminate\Http\UploadedFile;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager; // This is the v3 class
use Intervention\Image\Drivers\Gd\Driver as GdDriver; // <-- IMPORT THE GD DRIVER
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Config;

class ImageUploadService
{
    /**
     * Define default image conversion sizes.
     * @var array
     */
    protected static array $imageConversions = [
        // This static property is actually no longer needed if we fetch from config
        // but kept for reference if you want to keep a hardcoded default.
        // It's better now to rely purely on the config.
    ];

    /**
     * Uploads an image file, generates its conversions, and attaches it to an Eloquent model.
     *
     * @param UploadedFile $file The uploaded file instance.
     * @param Model $imageable The Eloquent model to attach the image to (e.g., Product, User).
     * @param string|null $purpose The purpose of the image (e.g., 'profile_picture', 'payment_proof').
     * @param string|null $altText The alt text for the image.
     * @param string $diskName The name of the storage disk to use (e.g., 'public', 'private_proofs', 's3').
     * @param string|null $basePath Optional: The base path within the disk. If null, a default is generated.
     * @param string $conversionPresetKey The key from config('images.conversions') to use for this image.
     * @return Image The created Image model instance.
     * @throws \Exception If image processing fails.
     */
    public static function uploadAndAttach(
        UploadedFile $file,
        Model $imageable,
        ?string $purpose = null,
        ?string $altText = null,
        string $diskName = 'public',
        ?string $basePath = null,
        string $conversionPresetKey = 'default'
    ): Image {
        // Determine the base storage path if not provided
        if (empty($basePath)) {
            $basePath = 'images/' . Str::plural(Str::snake(class_basename($imageable))) . '/' . $imageable->id;
        }

        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $extension = $file->getClientOriginalExtension();
        $uniqueFilename = Str::slug($originalFilename) . '-' . Str::random(8) . '.' . $extension;
        $fullPath = $basePath . '/' . $uniqueFilename;

        // 1. Store the original image file on the specified disk
        Storage::disk($diskName)->putFileAs($basePath, $file, $uniqueFilename);

        // 2. Generate and store conversions (thumbnails, medium, etc.)
        //    Pass the uploaded file, disk, and preset key.
        $conversionsData = self::generateAndStoreConversions($file, $basePath, $uniqueFilename, $diskName, $conversionPresetKey);

        // 3. Create the Image model record
        $image = new Image([
            'path' => $fullPath, // Path to the original file
            'disk' => $diskName, // Store the disk name
            'filename' => $file->getClientOriginalName(),
            'mime_type' => $file->getClientMimeType(),
            'size' => $file->getSize(),
            'alt_text' => $altText ?? $originalFilename,
            'purpose' => $purpose,
            'conversions' => $conversionsData,
        ]);

        $image->imageable()->associate($imageable);
        $image->save();

        return $image;
    }

    /**
     * Generates and stores various conversions (resized versions) of an image based on a preset key.
     *
     * @param UploadedFile $originalFile The original uploaded file.
     * @param string $basePath The base directory where files are stored.
     * @param string $originalFilename The filename of the original image (with extension).
     * @param string $diskName The name of the storage disk to use.
     * @param string $conversionPresetKey The key from config('images.conversions') to use.
     * @return array An associative array of conversion data.
     */
    protected static function generateAndStoreConversions(UploadedFile $originalFile, string $basePath, string $originalFilename, string $diskName, string $conversionPresetKey): array
    {
        // Get the specific conversion definitions from the config
        $conversionDefinitions = Config::get('images.conversions.' . $conversionPresetKey, Config::get('images.conversions.default'));

        if (empty($conversionDefinitions) || !is_array($conversionDefinitions)) {
            // No conversions defined for this preset, or invalid configuration
            return [];
        }

        $conversions = [];
        $extension = pathinfo($originalFilename, PATHINFO_EXTENSION);
        $filenameWithoutExt = pathinfo($originalFilename, PATHINFO_FILENAME);

        // Instantiate the ImageManager for v3
        // If you've configured a default driver in config/image.php, you can omit the driver class.
        // Otherwise, explicitly provide one like GdDriver::class or ImagickDriver::class if installed.
        // E.g., $manager = new ImageManager(new Intervention\Image\Drivers\Gd\Driver());
        $manager = new ImageManager(new GdDriver()); // <-- CORRECTED LINE

        // Read the original image from its real path
        $originalImage = $manager->read($originalFile->getRealPath());

        foreach ($conversionDefinitions as $name => $dims) {
            // Ensure required dimensions and method are present
            if (!isset($dims['width']) || !isset($dims['height']) || !isset($dims['method'])) {
                continue; // Skip invalid conversion definitions
            }

            $conversionFilename = $filenameWithoutExt . '-' . $name . '.' . $extension;
            $conversionFullPath = $basePath . '/' . $conversionFilename;

            // Clone the original image for each manipulation
            $manipulatedImage = clone $originalImage;

            if ($dims['method'] === 'fit') {
                $manipulatedImage->cover($dims['width'], $dims['height']); // v3 method for 'fit'
            } elseif ($dims['method'] === 'resize') {
                $manipulatedImage->resize($dims['width'], $dims['height']); // v3 method for 'resize'
            } else {
                // Handle unsupported methods or just skip
                continue;
            }

             // --- CORRECTED LINE FOR ENCODING AND STORING ---
            // Convert the manipulated image to JPEG and then get its binary buffer
            // You can change to toPng(), toWebp(), etc., if needed, or remove it to keep original format.
            Storage::disk($diskName)->put($conversionFullPath, (string) $manipulatedImage->toJpeg());
            // -------------------------------------------------

            $conversions[$name] = [
                'path' => $conversionFullPath,
                'url' => Storage::disk($diskName)->url($conversionFullPath),
                'width' => $manipulatedImage->width(),
                'height' => $manipulatedImage->height(),
                'size' => Storage::disk($diskName)->size($conversionFullPath),
            ];
        }

        return $conversions;
    }

    /**
     * Deletes an image record and its associated files (original and conversions).
     *
     * @param Image $image The Image model instance to delete.
     * @return bool
     */
    public static function deleteImage(Image $image): bool
    {
        // Use the disk stored in the image record for deletion
        if (Storage::disk($image->disk)->exists($image->path)) {
            Storage::disk($image->disk)->delete($image->path);
        }

        if (is_array($image->conversions)) {
            foreach ($image->conversions as $conversion) {
                if (isset($conversion['path']) && Storage::disk($image->disk)->exists($conversion['path'])) {
                    Storage::disk($image->disk)->delete($conversion['path']);
                }
            }
        }

        return $image->delete();
    }
}