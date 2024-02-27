<?php

declare(strict_types=1);

namespace Hofff\Contao\LayoutUsage\ContaoManager;

use Contao\CoreBundle\ContaoCoreBundle;
use Contao\ManagerPlugin\Bundle\BundlePluginInterface;
use Contao\ManagerPlugin\Bundle\Config\BundleConfig;
use Contao\ManagerPlugin\Bundle\Parser\ParserInterface;
use Hofff\Contao\LayoutUsage\HofffContaoLayoutUsageBundle;
use Hofff\Contao\RootRelations\HofffContaoRootRelationsBundle;

final class Plugin implements BundlePluginInterface
{
    /** {@inheritDoc} */
    public function getBundles(ParserInterface $parser): array
    {
        return [
            BundleConfig::create(HofffContaoLayoutUsageBundle::class)
                ->setLoadAfter([ContaoCoreBundle::class])
                ->setRepladce(['hofff_layoutusage']),
        ];
    }
}
