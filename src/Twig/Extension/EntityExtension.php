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

            // manager_entities_nav_create_elements
            new TwigFunction(Configuration::NAME."_entities_nav_create_elements", [EntityExtensionRuntime::class, 'getNavCreateElements']),

            // manager_entities_index_url
            new TwigFunction(Configuration::NAME."_entities_index_url", [EntityExtensionRuntime::class, 'getIndexUrl']),

            // manager_entities_create_url
            new TwigFunction(Configuration::NAME."_entities_create_url", [EntityExtensionRuntime::class, 'getCreateUrl']),
            
            // manager_entity_read_url
            new TwigFunction(Configuration::NAME."_entity_read_url", [EntityExtensionRuntime::class, 'getReadUrl']),
            
            // manager_entity_edit_url
            new TwigFunction(Configuration::NAME."_entity_edit_url", [EntityExtensionRuntime::class, 'getUpdateUrl']),
            
            // manager_entity_delete_url
            new TwigFunction(Configuration::NAME."_entity_delete_url", [EntityExtensionRuntime::class, 'getDeleteUrl']),



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

            // manager_entity_label_add_new
            new TwigFunction(Configuration::NAME."_entity_label_add_new", [EntityExtensionRuntime::class, 'label_AddNew']),

            // manager_entity_label_edit_item
            new TwigFunction(Configuration::NAME."_entity_label_edit_item", [EntityExtensionRuntime::class, 'label_EditItem']),

            // manager_entity_label_view_item
            new TwigFunction(Configuration::NAME."_entity_label_view_item", [EntityExtensionRuntime::class, 'label_ViewItem']),

            // manager_entity_label_view_items
            new TwigFunction(Configuration::NAME."_entity_label_view_items", [EntityExtensionRuntime::class, 'label_ViewItems']),

            // manager_entity_label_not_found
            new TwigFunction(Configuration::NAME."_entity_label_not_found", [EntityExtensionRuntime::class, 'label_NotFound']),

            // manager_entity_label_all_items
            new TwigFunction(Configuration::NAME."_entity_label_all_items", [EntityExtensionRuntime::class, 'label_AllItems']),

        ];
    }
}