<?php

namespace BlackpigCreatif\Sceau\Schemas\Runtime;

class BreadcrumbListSchema
{
    /**
     * Generate a BreadcrumbList schema.
     *
     * @param  array  $breadcrumbs  Array of breadcrumbs with 'name' and 'url' keys
     */
    public static function make(array $breadcrumbs): array
    {
        $items = collect($breadcrumbs)
            ->map(fn ($crumb, $index) => [
                '@type' => 'ListItem',
                'position' => $index + 1,
                'name' => $crumb['name'],
                'item' => $crumb['url'],
            ])
            ->values()
            ->toArray();

        return [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => $items,
        ];
    }
}
