<?php

namespace BlackpigCreatif\Sceau\Models;

use Illuminate\Database\Eloquent\Model;

class SeoSettings extends Model
{
    protected $table = 'sceau_seo_settings';

    protected $fillable = [
        'site_name',
        'site_url',
        'telephone',
        'email',
        'address_street',
        'address_city',
        'address_region',
        'address_postal_code',
        'address_country',
        'price_range',
        'opening_hours',
    ];

    protected function casts(): array
    {
        return [
            'opening_hours' => 'array',
        ];
    }

    /**
     * Get the singleton instance.
     * Creates one if it doesn't exist.
     */
    public static function getInstance(): self
    {
        return static::firstOrCreate([]);
    }
}
