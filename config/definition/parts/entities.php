<?php 

use ICanBoogie\Inflector;
use OSW3\Manager\Utils\StringUtil;
use Symfony\Component\Config\Definition\Builder\NodeBuilder;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\ScalarNodeDefinition;

if (!function_exists('path')) {
function path(): ScalarNodeDefinition {
    return (new ScalarNodeDefinition('path'))
        ->info('xxx.')
        ->defaultNull();
}}



return function (): ArrayNodeDefinition {

$builder = new TreeBuilder('entities');
$node = $builder->getRootNode();

$node
    ->info("xxx.")
    ->arrayPrototype()
    ->info('Specifies the namespace of the entity to be included in the search query (App\Entity\Pizza).')
    ->children()

        ->arrayNode('labels')
            ->info('xxx.')
            ->addDefaultsIfNotSet()->children()

            ->scalarNode('name')->defaultNull()->end()
            ->scalarNode('singular_name')->defaultNull()->end()
            ->scalarNode('menu_label')->defaultNull()->end()
            ->scalarNode('add_new')->defaultValue('entity.add_new')->end()
            // 'add_new_item'             => 'Ajouter un nouvel article',
            ->scalarNode('edit_item')->defaultValue('entity.edit_item')->end()
            // 'new_item'                 => 'Nouvel article',
            ->scalarNode('view_item')->defaultValue('entity.view_item')->end()
            ->scalarNode('view_items')->defaultValue('entity.view_items')->end()
            // 'search_items'             => 'Rechercher un article',
            ->scalarNode('not_found')->defaultValue('entity.not_found')->end()
            // 'not_found_in_trash'       => 'Aucun article trouvé dans la corbeille',
            // 'parent_item_colon'        => 'Article parent:',
            ->scalarNode('all_items')->defaultValue('entity.all_items')->end()
            // 'archives'                 => 'Archives des articles',
            // 'attributes'               => 'Attributs de l’article',
            // 'insert_into_item'         => 'Insérer dans l’article',
            // 'uploaded_to_this_item'    => 'Téléversé dans cet article',
            // 'featured_image'           => 'Image mise en avant',
            // 'set_featured_image'       => 'Définir l’image mise en avant',
            // 'remove_featured_image'    => 'Supprimer l’image mise en avant',
            // 'use_featured_image'       => 'Utiliser comme image mise en avant',
            // 'filter_items_list'        => 'Filtrer la liste des articles',
            // 'items_list_navigation'    => 'Navigation dans la liste des articles',
            // 'items_list'               => 'Liste des articles',
            // 'item_published'           => 'Article publié.',
            // 'item_published_privately' => 'Article publié en privé.',
            // 'item_reverted_to_draft'   => 'Article remis en brouillon.',
            // 'item_scheduled'           => 'Article planifié.',
            // 'item_updated'             => 'Article mis à jour.',

        ->end()->end()


        ->arrayNode('index')
            ->info('xxx.')
            ->addDefaultsIfNotSet()->children()

            ->scalarNode('path')
                ->info('xxx.')
                ->defaultNull()
            ->end()

            ->arrayNode('columns')
                ->info('xxx.')
                ->ignoreExtraKeys(false)
                ->variablePrototype()->end()
            ->end()

        ->end()->end()


        ->arrayNode('create')
            ->info('xxx.')
            ->addDefaultsIfNotSet()->children()

            ->scalarNode('path')
                ->info('xxx.')
                ->defaultNull()
            ->end()

            ->scalarNode('formtype')
                ->info('xxx.')
                ->defaultNull()
            ->end()

            ->enumNode('redirect')
                ->info('xxx.')
                ->values(['read', 'index', 'edit'])
                ->defaultValue('read')
            ->end()
            
        ->end()->end()


        ->arrayNode('read')
            ->info('xxx.')
            ->addDefaultsIfNotSet()->children()

            ->scalarNode('path')
                ->info('xxx.')
                ->defaultNull()
            ->end()
            
        ->end()->end()


        ->arrayNode('update')
            ->info('xxx.')
            ->addDefaultsIfNotSet()->children()

            ->scalarNode('path')
                ->info('xxx.')
                ->defaultNull()
            ->end()

            ->scalarNode('formtype')
                ->info('xxx.')
                ->defaultNull()
            ->end()

            ->enumNode('redirect')
                ->info('xxx.')
                ->values(['read', 'index', 'edit'])
                ->defaultValue('read')
            ->end()
            
        ->end()->end()


        ->arrayNode('delete')
            ->info('xxx.')
            ->addDefaultsIfNotSet()->children()

            ->scalarNode('path')
                ->info('xxx.')
                ->defaultNull()
            ->end()
            
        ->end()->end()


    ->end()->end();


    $node->validate()->always(function ($entities) {

        $pattern = '/([^\\\:]+)$/';
        $inflector = Inflector::get('en');

        foreach ($entities as $entity => $options)
        {
            preg_match($pattern, $entity, $matches);
            $entityName     = $matches[1];
            $entityPlural   = $inflector->pluralize($entityName);
            $entitySingular = $inflector->singularize($entityName);
            
            // Paths

            if ($entities[$entity]['index']['path'] === null) {
                $entities[$entity]['index']['path']  = StringUtil::camelToSlug(strtolower($entityPlural));
            }
            if ($entities[$entity]['create']['path'] === null) {
                $entities[$entity]['create']['path'] = StringUtil::camelToSlug(strtolower($entitySingular));
            }
            if ($entities[$entity]['read']['path'] === null) {
                $entities[$entity]['read']['path']   = StringUtil::camelToSlug(strtolower($entitySingular));
            }
            if ($entities[$entity]['update']['path'] === null) {
                $entities[$entity]['update']['path'] = StringUtil::camelToSlug(strtolower($entitySingular));
            }
            if ($entities[$entity]['delete']['path'] === null) {
                $entities[$entity]['delete']['path'] = StringUtil::camelToSlug(strtolower($entitySingular));
            }

            // Labels

            if ($entities[$entity]['labels']['name'] === null) {
                $entities[$entity]['labels']['name'] = $entityPlural;
            }
            if ($entities[$entity]['labels']['singular_name'] === null) {
                $entities[$entity]['labels']['singular_name'] = $entitySingular;
            }
            if ($entities[$entity]['labels']['menu_label'] === null) {
                $entities[$entity]['labels']['menu_label'] = $entityPlural;
            }

        }

        return $entities;

    })->end();

    return $node;
};

