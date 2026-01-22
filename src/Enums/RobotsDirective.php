<?php

namespace BlackpigCreatif\Sceau\Enums;

use Filament\Support\Contracts\HasLabel;

enum RobotsDirective: string implements HasLabel
{
    case IndexFollow = 'index,follow';
    case IndexNoFollow = 'index,nofollow';
    case NoIndexFollow = 'noindex,follow';
    case NoIndexNoFollow = 'noindex,nofollow';

    public function getLabel(): string
    {
        return match ($this) {
            self::IndexFollow => 'Index, Follow (default)',
            self::IndexNoFollow => 'Index, No Follow',
            self::NoIndexFollow => 'No Index, Follow',
            self::NoIndexNoFollow => 'No Index, No Follow',
        };
    }
}
