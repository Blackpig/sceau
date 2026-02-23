<?php

declare(strict_types=1);

namespace BlackpigCreatif\Sceau\Filament\Clusters;

use BlackpigCreatif\Grimoire\Filament\Clusters\GrimoireTomeCluster;

/**
 * Built-in Cluster stub for Sceau's user documentation Tome.
 *
 * This Cluster lives inside the Sceau package — no host app stub generation
 * is required. SceauPlugin registers it directly with the panel.
 */
final class SceauDocumentationCluster extends GrimoireTomeCluster
{
    public static string $tomeId = 'sceau';
}
