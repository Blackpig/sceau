<?php

namespace BlackpigCreatif\Sceau\Enums;

use Filament\Support\Contracts\HasLabel;

enum TwitterCardType: string implements HasLabel
{
    case Summary = 'summary';
    case SummaryLargeImage = 'summary_large_image';

    public function getLabel(): string
    {
        return match ($this) {
            self::Summary => 'Summary',
            self::SummaryLargeImage => 'Summary with Large Image',
        };
    }
}
