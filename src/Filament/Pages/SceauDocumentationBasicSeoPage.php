<?php

declare(strict_types=1);

namespace BlackpigCreatif\Sceau\Filament\Pages;

use BlackpigCreatif\Grimoire\Filament\Pages\GrimoireChapterPage;
use BlackpigCreatif\Sceau\Filament\Clusters\SceauDocumentationCluster;

/**
 * Built-in Chapter Page: Sceau user docs — basic SEO.
 */
final class SceauDocumentationBasicSeoPage extends GrimoireChapterPage
{
    public static string $tomeId = 'sceau';

    public static string $chapterSlug = 'basic-seo';

    protected static ?string $cluster = SceauDocumentationCluster::class;
}
