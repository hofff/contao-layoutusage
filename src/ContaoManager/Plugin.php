<?php

declare(strict_types=1);

namespace Hofff\Contao\LayoutUsage\ContaoManager;

use Contao\CoreBundle\ContaoCoreBundle;
use Contao\ManagerPlugin\Bundle\BundlePluginInterface;
use Contao\ManagerPlugin\Bundle\Config\BundleConfig;
use Contao\ManagerPlugin\Bundle\Parser\ParserInterface;
use Contao\ManagerPlugin\Routing\RoutingPluginInterface;
use Hofff\Contao\LayoutUsage\HofffContaoLayoutUsageBundle;
use Symfony\Component\Config\Loader\LoaderResolverInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\RouteCollection;

final class Plugin implements BundlePluginInterface, RoutingPluginInterface
{
    /** {@inheritDoc} */
    public function getBundles(ParserInterface $parser): array
    {
        return [
            BundleConfig::create(HofffContaoLayoutUsageBundle::class)
                ->setLoadAfter([ContaoCoreBundle::class])
                ->setReplace(['hofff_layoutusage']),
        ];
    }

    /** {@inheritDoc} */
    public function getRouteCollection(LoaderResolverInterface $resolver, KernelInterface $kernel): RouteCollection|null
    {
        $file   = '@HofffContaoLayoutUsageBundle/Resources/config/routes.xml';
        $loader = $resolver->resolve($file);

        if ($loader) {
            return $loader->load($file);
        }

        return null;
    }
}
