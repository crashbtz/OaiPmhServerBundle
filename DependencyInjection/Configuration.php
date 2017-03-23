<?php

namespace Ftven\Bundle\OaiPmhServerBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('ftven_oai_pmh_server');

        $rootNode
            ->children()
                ->scalarNode('data_provider_service_name')
                    ->defaultValue('ftven.oaipmh.data_provider')
                ->end()
                ->scalarNode('count_per_load')
                    ->defaultValue(50)
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
