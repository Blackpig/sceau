<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Upload Configuration
    |--------------------------------------------------------------------------
    |
    | Configure where SEO-related uploads (like OG images) should be stored.
    |
    */

    'uploads' => [
        'disk' => env('SCEAU_UPLOAD_DISK', 'public'),
        'directory' => env('SCEAU_UPLOAD_DIRECTORY', 'seo-images'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Character Limits
    |--------------------------------------------------------------------------
    |
    | Configure the optimal and maximum character limits for SEO fields.
    |
    */

    'limits' => [
        'title' => [
            'optimal_min' => 50,
            'optimal_max' => 65,
            'max' => 70,
        ],
        'description' => [
            'optimal_min' => 150,
            'optimal_max' => 160,
            'max' => 160,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Values
    |--------------------------------------------------------------------------
    |
    | Configure default values for various SEO fields.
    |
    */

    'defaults' => [
        'robots_directive' => 'index,follow',
        'og_type' => 'website',
        'twitter_card_type' => 'summary_large_image',
    ],

    /*
    |--------------------------------------------------------------------------
    | Schema Types
    |--------------------------------------------------------------------------
    |
    | Optionally limit the available schema types. Leave null to allow all.
    | Example: ['Article', 'BlogPosting', 'Product']
    |
    */

    'schema_types' => null,

    /*
    |--------------------------------------------------------------------------
    | Organization Data
    |--------------------------------------------------------------------------
    |
    | Configure organization/business data for structured data (JSON-LD).
    | This data is used when auto-generating Organization or LocalBusiness schemas.
    |
    */

    'organization' => [
        // Contact information
        'telephone' => env('SCEAU_ORG_TELEPHONE'),
        'email' => env('SCEAU_ORG_EMAIL'),

        // Physical address (for LocalBusiness schema)
        'address' => [
            'street' => env('SCEAU_ORG_ADDRESS_STREET'),
            'city' => env('SCEAU_ORG_ADDRESS_CITY'),
            'region' => env('SCEAU_ORG_ADDRESS_REGION'),
            'postal_code' => env('SCEAU_ORG_ADDRESS_POSTAL_CODE'),
            'country' => env('SCEAU_ORG_ADDRESS_COUNTRY'),
        ],

        // For LocalBusiness schema
        'price_range' => env('SCEAU_ORG_PRICE_RANGE'), // e.g., "$$", "$$$"

        // Opening hours (array of opening hours specifications)
        // Example:
        // 'opening_hours' => [
        //     [
        //         '@type' => 'OpeningHoursSpecification',
        //         'dayOfWeek' => ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'],
        //         'opens' => '09:00',
        //         'closes' => '17:00',
        //     ],
        // ],
        'opening_hours' => null,
    ],

];
