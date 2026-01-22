<?php

namespace BlackpigCreatif\Sceau\Enums;

use Filament\Support\Contracts\HasLabel;

enum SchemaType: string implements HasLabel
{
    case Article = 'Article';
    case BlogPosting = 'BlogPosting';
    case NewsArticle = 'NewsArticle';
    case Product = 'Product';
    case LocalBusiness = 'LocalBusiness';
    case Organization = 'Organization';
    case Person = 'Person';
    case Event = 'Event';
    case FAQPage = 'FAQPage';
    case HowTo = 'HowTo';
    case Recipe = 'Recipe';
    case VideoObject = 'VideoObject';

    public function getLabel(): string
    {
        return match ($this) {
            self::Article => 'Article',
            self::BlogPosting => 'Blog Post',
            self::NewsArticle => 'News Article',
            self::Product => 'Product',
            self::LocalBusiness => 'Local Business',
            self::Organization => 'Organization',
            self::Person => 'Person',
            self::Event => 'Event',
            self::FAQPage => 'FAQ Page',
            self::HowTo => 'How-To Guide',
            self::Recipe => 'Recipe',
            self::VideoObject => 'Video',
        };
    }
}
