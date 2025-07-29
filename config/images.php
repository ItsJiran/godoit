<?php

use App\Enums\Image\ImagePurposeType;

return [

    /*
    |--------------------------------------------------------------------------
    | Image Conversion Presets
    |--------------------------------------------------------------------------
    |
    | Define different sets of image conversions based on their purpose.
    | Each key represents a preset name (e.g., 'profile_picture', 'product_gallery').
    | Each preset contains an array of named conversions (e.g., 'thumbnail', 'medium').
    |
    | Supported 'method' values for conversions:
    | - 'fit': Resize and crop the image to the exact dimensions.
    | - 'resize': Resize the image to fit within the given dimensions, maintaining aspect ratio.
    |
    */

    'conversions' => [

        ImagePurposeType::DEFAULT->value => [ // A fallback or general purpose conversion set
            'thumbnail' => ['width' => 150, 'height' => 150, 'method' => 'fit'],
            'medium' => ['width' => 800, 'height' => 600, 'method' => 'resize'],
        ],

        ImagePurposeType::USER_AVATAR->value => [
            'small' => ['width' => 80, 'height' => 80, 'method' => 'fit'],
            'medium' => ['width' => 200, 'height' => 200, 'method' => 'fit'],
            'large' => ['width' => 400, 'height' => 400, 'method' => 'resize'], // Larger size for profile view
        ],


        ImagePurposeType::PRODUCT_THUMBNAIL->value => [
            'thumbnail' => ['width' => 200, 'height' => 200, 'method' => 'fit'],
            'compact' => ['width' => 600, 'height' => 400, 'method' => 'resize'],
            'display' => ['width' => 1200, 'height' => 800, 'method' => 'resize'],
            // Add a watermark conversion if needed, requires Intervention Image Watermark plugin or custom logic
            // 'watermarked' => ['width' => 1200, 'height' => 800, 'method' => 'resize', 'watermark' => true],
        ],

        ImagePurposeType::PRODUCT_GALLERY->value => [
            'thumbnail' => ['width' => 200, 'height' => 200, 'method' => 'fit'],
            'compact' => ['width' => 600, 'height' => 400, 'method' => 'resize'],
            'display' => ['width' => 1200, 'height' => 800, 'method' => 'resize'],
            // Add a watermark conversion if needed, requires Intervention Image Watermark plugin or custom logic
            // 'watermarked' => ['width' => 1200, 'height' => 800, 'method' => 'resize', 'watermark' => true],
        ],

        ImagePurposeType::PAYMENT_PROOF->value => [
            'preview' => ['width' => 400, 'height' => 400, 'method' => 'resize'], // Just a small preview
        ],

        // Add more presets as needed
    ],
];