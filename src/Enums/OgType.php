<?php

namespace BlackpigCreatif\Sceau\Enums;

use Filament\Support\Contracts\HasLabel;

enum OgType: string implements HasLabel
{
    case Website = 'website';
    case Article = 'article';
    case Product = 'product';
    case Profile = 'profile';
    case Book = 'book';
    case Video = 'video.other';

    public function getLabel(): string
    {
        return match ($this) {
            self::Website => 'Website',
            self::Article => 'Article',
            self::Product => 'Product',
            self::Profile => 'Profile',
            self::Book => 'Book',
            self::Video => 'Video',
        };
    }
}
