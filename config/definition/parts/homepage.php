<?php 

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;

return function (): ArrayNodeDefinition {

$builder = new TreeBuilder('homepage');
$node = $builder->getRootNode();

$node
    ->info("Configuration for the homepage settings.")
    ->addDefaultsIfNotSet()->children()

        ->scalarNode('route')
            ->info("Defines the route name for the homepage.")
            ->defaultValue('app_homepage')
        ->end()

        ->scalarNode('label')
            ->info("Text label used for the homepage link.")
            ->defaultValue('Back to homepage')
        ->end()

    ->end();

    return $node;
};