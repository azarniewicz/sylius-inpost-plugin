<?php

declare(strict_types=1);

namespace Azarniewicz\SyliusInPostPlugin\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('azarniewicz_sylius_inpost');

        $treeBuilder->getRootNode()
            ->children()
                ->scalarNode('shipping_method_code')
                    ->cannotBeEmpty()
                    ->defaultValue('inpost_point')
                ->end()
                ->scalarNode('api_base_url')
                    ->cannotBeEmpty()
                    ->defaultValue('https://api-pl-points.easypack24.net/v1/points')
                ->end()
                ->scalarNode('geowidget_token')
                    ->defaultValue('')
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
