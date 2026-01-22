<?php

namespace BlackpigCreatif\Sceau\SchemaGenerators;

use BlackpigCreatif\Sceau\Models\SeoData;

class LocalBusinessSchema extends BaseSchema
{
    public function getType(): string
    {
        return 'LocalBusiness';
    }

    public function generate(SeoData $seoData): array
    {
        $schema = $this->baseSchema();

        // Name
        $schema['name'] = $this->getName($seoData);

        // URL
        $schema['url'] = $this->getUrl($seoData);

        // Description
        if ($description = $this->getDescription($seoData)) {
            $schema['description'] = $description;
        }

        // Image
        if ($image = $this->getImage($seoData)) {
            $schema['image'] = $image;
        }

        // Get settings for organization data
        $settings = $this->getSettings();

        if ($settings) {
            // Contact info
            if ($settings->telephone) {
                $schema['telephone'] = $settings->telephone;
            }

            if ($settings->email) {
                $schema['email'] = $settings->email;
            }

            // Address
            if ($this->hasAddress($settings)) {
                $schema['address'] = [
                    '@type' => 'PostalAddress',
                    'streetAddress' => $settings->address_street,
                    'addressLocality' => $settings->address_city,
                    'addressRegion' => $settings->address_region,
                    'postalCode' => $settings->address_postal_code,
                    'addressCountry' => $settings->address_country,
                ];

                $schema['address'] = $this->removeNullValues($schema['address']);

                // If only @type remains, remove the address
                if (count($schema['address']) === 1) {
                    unset($schema['address']);
                }
            }

            // LocalBusiness-specific fields
            if ($settings->price_range) {
                $schema['priceRange'] = $settings->price_range;
            }

            if ($settings->opening_hours) {
                $schema['openingHoursSpecification'] = array_map(function ($hours) {
                    return array_merge(['@type' => 'OpeningHoursSpecification'], $hours);
                }, $settings->opening_hours);
            }
        }

        // Remove null values
        return $this->removeNullValues($schema);
    }

    protected function hasAddress($settings): bool
    {
        return $settings->address_street
            || $settings->address_city
            || $settings->address_region
            || $settings->address_postal_code
            || $settings->address_country;
    }

    public function getSkeleton(): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'LocalBusiness',
            'name' => '',
            'url' => '',
            'image' => '',
            'telephone' => '',
            'email' => '',
            'priceRange' => '',
            'address' => [
                '@type' => 'PostalAddress',
                'streetAddress' => '',
                'addressLocality' => '',
                'addressRegion' => '',
                'postalCode' => '',
                'addressCountry' => '',
            ],
            'openingHoursSpecification' => [
                [
                    '@type' => 'OpeningHoursSpecification',
                    'dayOfWeek' => '',
                    'opens' => '',
                    'closes' => '',
                ],
            ],
        ];
    }
}
