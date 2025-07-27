<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

use App\Enums\Image\ImagePurposeType;
use App\Http\Services\Media\ImageUploadService;

class Image extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'images';

    protected $fillable = [
        'path',
        'disk',
        'filename',
        'mime_type',
        'size',
        'alt_text',
        'purpose',
        'imageable_id',
        'imageable_type',
        'conversions',
    ];

    protected $casts = [
        'size' => 'integer',
        'conversions' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
        'purpose' => ImagePurposeType::class,
    ];

    /**
     * Get the parent imageable model (e.g., User, Product) that this image belongs to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function imageable(): \Illuminate\Database\Eloquent\Relations\MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the permanent URL for the original image file.
     * This is an accessor that automatically retrieves the URL when you access `$image->url`.
     *
     * @return string The public URL of the original image.
     */
    public function getUrlAttribute(): string
    {
        return Storage::disk($this->disk)->url($this->path);
    }

    /**
     * Get the permanent URL for a specific named conversion of the image.
     *
     * @param string $conversionName The name of the conversion (e.g., 'thumbnail', 'medium').
     * @return string|null The URL of the specified conversion, or null if the conversion does not exist.
     */
    public function getConversionUrl(string $conversionName): ?string
    {
        if (isset($this->conversions[$conversionName]['path'])) {
            return Storage::disk($this->disk)->url($this->conversions[$conversionName]['path']);
        }
        return null;
    }

    /**
     * Get a temporary URL for the original image or a specific named conversion.
     * This is useful for disks (like S3) that support signed URLs for private files.
     *
     * @param \DateTimeInterface|\DateInterval|int $expiration The expiration time for the URL.
     * Can be an integer (minutes), DateInterval object, or DateTimeInterface object.
     * @param string|null $conversionName The name of the conversion to get a temporary URL for.
     * If null, the temporary URL for the original image will be returned.
     * @return string The temporary URL, or the permanent URL if temporary URL generation fails (e.g., for local disk that doesn't support it).
     */
    public function getTemporaryUrl(\DateTimeInterface|\DateInterval|int $expiration = 5, ?string $path = null): string
    {
        try {
            if (is_int($expiration)) {
                $expiration = now()->addMinutes($expiration);
            }

            return Storage::disk($this->disk)->temporaryUrl($path, $expiration);
        } catch (\Exception $e) {
            // Fallback to permanent URL if temporary URL generation fails (e.g., for 'local' disk not supporting it)
            return $this->url; // Returns the permanent URL for the original image
        }
    }

    /**
     * Get the permanent URL of the conversion with the smallest area (width * height).
     * Falls back to the original image URL if no conversions are available.
     *
     * @return string The URL of the smallest conversion or the original image.
     */
    public function getSmallestConversionUrl(): string
    {
        if (empty($this->conversions)) {
            return $this->getUrlAttribute(); // Fallback to original if no conversions
        }

        $smallestArea = PHP_INT_MAX;
        $smallestConversionPath = null;

        foreach ($this->conversions as $conversion) {
            if (isset($conversion['width']) && isset($conversion['height']) && isset($conversion['path'])) {
                $area = $conversion['width'] * $conversion['height'];
                if ($area < $smallestArea) {
                    $smallestArea = $area;
                    $smallestConversionPath = $conversion['path'];
                }
            }
        }

        // Return URL if smallest conversion found, else fallback to original URL
        return $smallestConversionPath ? Storage::disk($this->disk)->url($smallestConversionPath) : $this->getUrlAttribute();
    }

    /**
     * Get the permanent URL of the conversion that best fits target width and height.
     *
     * This method tries to find:
     * 1. The largest conversion whose dimensions are less than or equal to the target dimensions (both width and height).
     * 2. If $allowUpscale is true and no such smaller image is found, it returns the smallest conversion that is larger than
     * the target dimensions in at least one direction (to ensure it "covers" the target).
     * 3. Falls back to the original image URL if no suitable conversion is found.
     *
     * @param int $targetWidth The desired target width.
     * @param int $targetHeight The desired target height.
     * @param bool $allowUpscale If true, will consider images larger than target dimensions if no suitable smaller one exists.
     * If false, will only consider images up to targetWidth and targetHeight.
     * @return string The URL of the best matching conversion or the original image.
     */
    public function getConversionByTargetDimensions(int $targetWidth, int $targetHeight, bool $allowUpscale = true) : Object | null
    {
        if (empty($this->conversions)) {
            return null; // Fallback to original if no conversions
        }

        $bestFitConversion = null;
        $bestFitPath = null;
        $bestFitArea = 0; // To find the largest image that fits within target dimensions

        $smallestConversion = null;
        $smallestLargerPath = null;
        $smallestLargerArea = PHP_INT_MAX; // To find the smallest image that is larger than target dimensions

        foreach ($this->conversions as $conversion) {
            if (isset($conversion['width']) && isset($conversion['height']) && isset($conversion['path'])) {
                $cWidth = $conversion['width'];
                $cHeight = $conversion['height'];
                $cPath = $conversion['path'];
                $cArea = $cWidth * $cHeight;

                if ($cWidth <= $targetWidth && $cHeight <= $targetHeight) {
                    // This conversion fits entirely within or matches target dimensions
                    // We want the largest one that fits
                    if ($cArea > $bestFitArea) {
                        $bestFitConversion = $conversion;
                        $bestFitArea = $cArea;
                        $bestFitPath = $cPath;
                    }
                } else {
                    // This conversion is larger than target dimensions in at least one direction
                    // We want the smallest one among these larger images
                    if ($cArea < $smallestLargerArea) {
                        $smallestConversion = $conversion;
                        $smallestLargerArea = $cArea;
                        $smallestLargerPath = $cPath;
                    }
                }
            }
        }

        // If a conversion that fits within/equal to target dimensions was found, use it
        if ($bestFitPath) {
            return (object) $bestFitConversion;
        }

        // If no suitable smaller image was found, and upscaling is allowed, use the smallest larger image
        if ($allowUpscale && $smallestLargerPath) {
            return (object) $smallestConversion;
        }

        // Fallback to original if no suitable conversion is found based on criteria
        return null;
    }

    /**
     * Scope a query to only include images with a specific purpose.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string|ImagePurposeType $purpose The purpose to filter by (e.g., 'user_avatar', 'product_thumbnail').
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWherePurpose(\Illuminate\Database\Eloquent\Builder $query, string|ImagePurposeType $purpose): \Illuminate\Database\Eloquent\Builder
    {
        $purposeValue = $purpose instanceof ImagePurposeType ? $purpose->value : $purpose;
        return $query->where('purpose', $purposeValue);
    }

    /**
     * Create a new Image record, handling file upload and conversions via ImageUploadService.
     * This static method centralizes the logic for uploading, processing, and associating images.
     *
     * @param UploadedFile $file The uploaded file instance.
     * @param \Illuminate\Database\Eloquent\Model $imageable The Eloquent model to attach the image to (e.g., User, Product).
     * @param ImagePurposeType|string|null $purpose The purpose of the image (enum case or string value, e.g., 'profile_picture').
     * @param string|null $altText The alt text for the image, used for accessibility and SEO.
     * @param string $diskName The name of the storage disk to use (e.g., 'public', 'private_proofs', 's3').
     * @param string|null $basePath Optional: The base path within the disk where the image should be stored. If null, a default is generated.
     * @param string $conversionPresetKey The key from `config('images.conversions')` to use for this image's conversions. Defaults to 'default'.
     * @return static The newly created Image model instance.
     * @throws \Exception If image processing or storage fails.
     */
    public static function createImageRecord(
        UploadedFile $file,
        $imageable,
        ImagePurposeType|string|null $purpose = null,
        ?string $altText = null,
        string $diskName = 'public',
        ?string $basePath = null,
        string $conversionPresetKey = ImagePurposeType::DEFAULT->value
    ): static {
        $purposeValue = $purpose instanceof ImagePurposeType ? $purpose->value : $purpose;

        return ImageUploadService::uploadAndAttach(
            $file,
            $imageable,
            $purposeValue,
            $altText,
            $diskName,
            $basePath,
            $conversionPresetKey // Pass the new parameter
        );
    }
}