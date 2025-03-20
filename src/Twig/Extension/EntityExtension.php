<?php 
namespace OSW3\Manager\Twig\Extension;

use Twig\TwigFunction;
use Twig\Extension\AbstractExtension;
use OSW3\Manager\DependencyInjection\Configuration;
use OSW3\Manager\Twig\Runtime\EntityExtensionRuntime;

class EntityExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [

            // manager_entities_nav_elements
            new TwigFunction(Configuration::NAME."_entities_nav_elements", [EntityExtensionRuntime::class, 'getNavElements']),

            // manager_entities_index_url
            new TwigFunction(Configuration::NAME."_entities_index_url", [EntityExtensionRuntime::class, 'getIndexUrl']),
            
            // manager_entity_read_url
            new TwigFunction(Configuration::NAME."_entity_read_url", [EntityExtensionRuntime::class, 'getReadUrl']),



            // REPOSITORY
            // --

            // manager_entity_id
            new TwigFunction(Configuration::NAME."_entity_id", [EntityExtensionRuntime::class, 'getId']),

            // manager_entity_title
            // new TwigFunction(Configuration::NAME."_entity_title", [EntityExtensionRuntime::class, 'getTitle']),

            // manager_entity_columns
            new TwigFunction(Configuration::NAME."_entity_columns", [EntityExtensionRuntime::class, 'getColumns']),

            // manager_entity_attributes
            new TwigFunction(Configuration::NAME."_entity_attributes", [EntityExtensionRuntime::class, 'getAttributes']),



            // LABELS
            // --

            // manager_entity_name
            new TwigFunction(Configuration::NAME."_entity_name", [EntityExtensionRuntime::class, 'getName']),

            // manager_entity_singular_name
            new TwigFunction(Configuration::NAME."_entity_singular_name", [EntityExtensionRuntime::class, 'getSingularName']),

            // manager_entity_menu_label
            new TwigFunction(Configuration::NAME."_entity_menu_label", [EntityExtensionRuntime::class, 'getMenuLabel']),

            // manager_entity_not_found
            new TwigFunction(Configuration::NAME."_entity_not_found", [EntityExtensionRuntime::class, 'getNotFound']),

        ];
    }
}