<?php


namespace Progrupa\FacebookAudienceBundle\DependencyInjection;


use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $treeBuilder->root('progrupa_facebook_audience')
            ->children()
                ->scalarNode('client_id')->isRequired(true)->end()
                ->scalarNode('client_secret')->isRequired(true)->end()
                ->scalarNode('marketing_token')->isRequired(true)->end()
                ->scalarNode('business_id')->isRequired(true)->end()
            ->end();

        return $treeBuilder;
    }

}