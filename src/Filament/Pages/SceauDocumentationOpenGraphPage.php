<?php

declare(strict_types=1);

namespace BlackpigCreatif\Sceau\Filament\Pages;

use BlackpigCreatif\Grimoire\Filament\Pages\GrimoireChapterPage;
use BlackpigCreatif\Sceau\Filament\Clusters\SceauDocumentationCluster;

/**
 * Built-in Chapter Page: Sceau user docs — Open Graph.
 */
final class SceauDocumentationOpenGraphPage extends GrimoireChapterPage
{
    public static string $tomeId = 'sceau';

    public static string $chapterSlug = 'open-graph';

    protected static ?string $cluster = SceauDocumentationCluster::class;
}
