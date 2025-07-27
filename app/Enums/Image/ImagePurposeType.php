<?php

namespace App\Enums\Image; // Adjust namespace if your enums are in a different folder (e.g., App\Enums\Media)

use App\Traits\BasicEnumTrait; // Assuming this trait exists

/**
 * Enum ImagePurposeType
 *
 * Defines the various purposes an image can serve within the application.
 *
 * @package App\Enums
 */
enum ImagePurposeType: string
{
    use BasicEnumTrait; // Provides helper methods like ::values(), ::keys(), etc.

    case DEFAULT = 'default';
    case PRODUCT_THUMBNAIL = 'product_thumbnail';
    case PRODUCT_PREVIEW = 'product_preview'; // For videos, 360 views, etc.
    case PRODUCT_GALLERY = 'product_gallery'; // For additional images in a product gallery
    case USER_AVATAR = 'user_avatar'; // Alternative to profile_picture if distinct
    case POST_COVER = 'post_cover';
    case CATEGORY_ICON = 'category_icon';
    case BANNER = 'banner'; // For marketing banners or hero images
    case PAYMENT_PROOF = 'payment_proof'; // For marketing banners or hero images
}