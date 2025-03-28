<?php 
namespace OSW3\Manager\Service;

use OSW3\Manager\Utils\StringUtil;
use OSW3\Manager\Enum\RequestOperators;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use OSW3\Manager\DependencyInjection\Configuration;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class EntityService
{
    private array $params;

    public function __construct(
        #[Autowire(service: 'service_container')] 
        private ContainerInterface $container,
        private EntityManagerInterface $entityManager,
        private RequestStack $requestStack,
        private TranslatorInterface $translation,
        private UrlGeneratorInterface $urlGenerator,
    ){
        $this->params = $container->getParameter(Configuration::NAME)['entities'];

        // Check entities exists
        foreach ($this->getAll() as $c) if (!class_exists($c)) {
            throw new \Exception(sprintf("The entity class %s is not found by the Manager", $c));
        }
    }




    // ENTITIES LIST
    // --

    /**
     * Get the list of entities
     * 
     * @return array
     */
    public function getAll(): array {
        return array_keys($this->params);
    }

    /**
     * List of entities for nav elements
     * 
     * @return array
     */
    public function getNavElements(): array {
        $nav = [];

        foreach($this->params as $entity => $options) {
            $route  = 'manager:entity:index';
            $path   = $options['index']['path'];
            $url    = $this->urlGenerator->generate($route, ['path' => $path]);
            $active = $this->isMenuActive($entity);

            $nav[] = [
                'label'  => $this->getMenuLabel($entity),
                'url'    => $url,
                'active' => $active,
            ];
        }

        return $nav;
    }
    
    public function getNavCreateElements(): array {
        $nav = [];

        foreach($this->params as $classname => $options) {

            $route    = 'manager:entity:create';
            $path     = $options['create']['path'];
            $url      = $this->urlGenerator->generate($route, ['path' => $path]);
            $formtype = $this->getFormType($classname, false);
            // $active = $this->isMenuActive($entity);

            if (class_exists($formtype)) {
                $nav[] = [
                    'label'  => $this->label_AddNew($classname),
                    'url'    => $url,
                    // 'active' => $active,
                ];
            }

        }

        return $nav;
    }

    private function isMenuActive(string $classname): bool {
        $options      = $this->params[$classname];
        $request      = $this->requestStack->getCurrentRequest();

        $currentRoute = $request->attributes->get('_route');
        $currentPath  = $request->attributes->get('path');

        $routeMatch   = in_array($currentRoute, [
                            'manager:entity:index', 
                            'manager:entity:create', 
                            'manager:entity:read',
                            'manager:entity:update',
                            'manager:entity:delete'
                        ]);
        $pathMatch    = in_array($currentPath, [
                            $options['index']['path'], 
                            $options['create']['path'],
                            $options['read']['path'],
                            $options['update']['path'],
                            $options['delete']['path']
                        ]);

        return $routeMatch && $pathMatch;
    }


    // urls

    public function getIndexUrl(string $classname): string {
        $options = $this->params[$classname];

        return $this->urlGenerator->generate('manager:entity:index', [
            'path' => $options['index']['path']
        ], false);
    }

    public function getCreateUrl(string $classname): string {
        $options = $this->params[$classname];

        return $this->urlGenerator->generate('manager:entity:create', [
            'path' => $options['create']['path']
        ], false);
    }

    public function getReadUrl(string $classname, int|string $id): string {
        $options = $this->params[$classname];

        return $this->urlGenerator->generate('manager:entity:read', [
            'path' => $options['read']['path'],
            'id' => $id,
        ], false);
    }

    public function getUpdateUrl(string $classname, int|string $id): string {
        $options = $this->params[$classname];

        return $this->urlGenerator->generate('manager:entity:update', [
            'path' => $options['update']['path'],
            'id' => $id,
        ], false);
    }

    public function getDeleteUrl(string $classname, int|string $id): string {
        $options = $this->params[$classname];

        return $this->urlGenerator->generate('manager:entity:delete', [
            'path' => $options['delete']['path'],
            'id' => $id,
        ], false);
    }



    // SINGLE ENTITIES
    // --

    public function getEntityClassnameByPath(string $path): ?string {

        foreach ($this->params as $entity => $options) {
                 if ($options['index']['path'] === $path) return $entity;
            else if ($options['create']['path'] === $path) return $entity;
            else if ($options['read']['path'] === $path) return $entity;
            else if ($options['update']['path'] === $path) return $entity;
            else if ($options['delete']['path'] === $path) return $entity;
        }

        return null;
    }

    /**
     * Get entity options
     * 
     * @return array
     */
    public function getEntityOptions(string $classname): array {
        return $this->params[$classname];
    }




    // REPOSITORY
    // --

    /**
     * Get the entity repository
     */
    public function getRepository(string $classname) {
        return $this->entityManager->getRepository($classname);
    }


    public function count($repository, string $classname): int {
        $options  = $this->params[$classname];
        $criteria = $options['index']['criteria'];
        $entities = $this->findBy($repository, $classname, $criteria);
        return count($entities);
    }

    public function getPaged($repository, string $classname): array {
        $options  = $this->params[$classname];
        $request  = $this->requestStack->getCurrentRequest();
        $criteria = $options['index']['criteria'];
        $orderBy  = $options['index']['orderBy'];
        $page     = $request->query->get('page') ?? 1;
        $limit    = $options['index']['per_page'];
        $offset   = ($limit * $page) - $limit;

        $entities = $this->findBy(
            $repository,
            $classname,
            $criteria, 
            $orderBy, 
            $limit, 
            $offset
        );

        return $entities;
    }

    /**
     * Total pages for pagination
     * 
     * @return int
     */
    public function getPages($repository, string $classname): int {
        $options  = $this->params[$classname];
        $criteria = $options['index']['criteria'];
        $limit    = $options['index']['per_page'];
        $entities = $this->findBy($repository, $classname, $criteria);
        $total    = count($entities);
        return (int) ceil($total/$limit);
    }

    public function getColumns(object $entity): array {
        $options = $this->params[$entity::class];
        $columns = $options['index']['columns'];
        $columns = array_merge([$options['id'] => null], $columns);

        array_walk($columns, function(&$column, $property) use ($entity) {
            $label  = $column ?? ucfirst($property);
            $getter = "get".ucfirst($property);
            $value  = $entity->$getter();

            $column = [
                'property' => $property,
                'label'    => $label,
                'value'    => $value,
            ];
        });

        return $columns;
    }

    public function getAttributes(object $entity): array {

        $classname         = $entity::class;

        // Note: :-s
        if (str_starts_with($classname, "Proxies\\__CG__\\")) {
            $classname = str_replace("Proxies\\__CG__\\", "", $classname);
        }

        $options           = $this->getEntityOptions($classname);
        $reflection        = new \ReflectionClass($entity);
        $allowedAttributes = $options['read']['attributes'];
        $attributes        = [];

        foreach ($reflection->getProperties() as $property) {
            $name   = $property->getName();

            // TODO: Exit control from "read" page
            // if (!empty($allowedAttributes) && !array_key_exists($name, $allowedAttributes)) {
            //     continue;
            // }

            if (str_starts_with($name, 'is') || str_starts_with($name, 'has')) {
                $getter = ucfirst($name);
            } else {
                $getter = 'get' . ucfirst($name);
            }

            $label      = $allowedAttributes[$name]['label'] ?? $name;
            $format     = $allowedAttributes[$name]['format'] ?? null;
            $attr = $allowedAttributes[$name]['attributes'] ?? [];
            $type       = $property->getType()->getName();

            $value  = $this->normalizeValue(
                value : $entity->$getter(),
                type  : $type,
                format: $format,
                attributes: $attr,
            );

            if (method_exists($entity, $getter)) {
                $attributes[$name] = [
                    'name'  => $name,
                    'label' => $label,
                    'value' => $value,
                    'type'  => $type,
                ];
            }
        }

        return $attributes;
    }

    private function normalizeValue(mixed $value, string $type, ?string $format = null, array $attributes=[]): ?string {

        if ($value === null) {
            return null;
        }

        if ($type === null || $type === 'string') {
            return $value;
        }

        switch ($type) {
            case 'bool': 
                return (bool) $value;

            case 'int' : 
                return (int) $value;

            case 'DateTimeInterface' : 
            case 'DateTimeImmutable' : 
                return $format 
                    ? $value?->format($format) 
                    : $value?->format('Y-m-d H:i:s')
                ;
        }

        if (class_exists($type)) {

            // TODO: Format ty data / get entity value
            // $repository = $this->getRepository($type);

            // dump($format);
            // dd($value->getId());


            // Vérifie si l'objet a une méthode __toString()
            // if (method_exists($value, '__toString')) {
            //     $value = (string) $value;
            // dump($value);

            // }

            dump($value);
            dump($attributes);
            // dump($this->getAttributes($value));
            // dump($value->getContent());


            return "Entity data";
        }

        return $value;
    }

    public function getIdAttribute(string $classname): string {
        $options  = $this->params[$classname];
        return $options['id'];
    }

    public function getFormType(string $classname, bool $withException=true) {
        $options  = $this->params[$classname];
        $formType = $options['create']['formtype'] ?? str_replace('Entity', 'Form', $classname) . 'Type';

        if ($withException && !class_exists($formType)) {
            throw new \Exception(sprintf("The FormType %s is not found for the entity %s", $formType, $classname));
        }

        return $formType;
    }



    // LABELS
    // --

    /**
     * Get the entity page title
     * 
     * @return string
     */
    public function getName(string $classname): string {
        $options = $this->getEntityOptions($classname);
        return $options['labels']['name'];
    }

    /**
     * Get the entity page title
     * 
     * @return string
     */
    public function getSingularName(string $classname): string {
        $options = $this->getEntityOptions($classname);
        return $options['labels']['singular_name'];
    }

    public function getMenuLabel(string $classname): string {
        $options = $this->getEntityOptions($classname);
        return $options['labels']['menu_label'];
    }

    public function label_AddNew(string $classname): string {
        $options = $this->getEntityOptions($classname);
        return     $this->translation->trans($options['labels']['add_new'], [
                        '%entityName%' => strtolower($this->getSingularName($classname))
                    ], Configuration::DOMAIN);
    }

    public function label_EditItem(string $classname): string {
        $options = $this->getEntityOptions($classname);
        $word    = strtolower($this->getSingularName($classname));
        return   $this->translation->trans($options['labels']['edit_item'], [
                    '%entityName%' => $word
                ], Configuration::DOMAIN);
    }

    public function label_ViewItems(string $classname): string {
        $options = $this->getEntityOptions($classname);
        return     $this->translation->trans($options['labels']['view_items'], [
                        '%entityName%' => strtolower($this->getName($classname)),
                    ], Configuration::DOMAIN);
    }

    public function label_ViewItem(string $classname): string {
        $options = $this->getEntityOptions($classname);
        return     $this->translation->trans($options['labels']['view_item'], [
                        '%entityName%' => strtolower($this->getSingularName($classname)),
                    ], Configuration::DOMAIN);
    }

    public function label_DeleteItem(string $classname): string {
        $options = $this->getEntityOptions($classname);
        return     $this->translation->trans($options['labels']['delete_item'], [
                        '%entityName%' => strtolower($this->getSingularName($classname)),
                    ], Configuration::DOMAIN);
    }

    public function label_NotFound(string $classname): string {
        $options = $this->getEntityOptions($classname);
        return     $this->translation->trans($options['labels']['not_found'], [
                        '%entityName%' => strtolower($this->getSingularName($classname)),
                    ], Configuration::DOMAIN);
    }

    public function label_AllItems(string $classname): string {
        $options = $this->getEntityOptions($classname);
        $word    = strtolower($this->getName($classname));
        return   $this->translation->trans($options['labels']['all_items'], [
                    '%items%' => $word
                ], Configuration::DOMAIN);
    }


    // DQL
    // --

    private function parseDateExpression($value) {
        $pattern = '/^(NOW|\d{2}-\d{2}-\d{4}(?: \d{2}:\d{2}:\d{2})?)\s*([\+\-])\s*(\d+)\s*(days|weeks|months|years|hours|minutes|seconds)$/i';
        
        if (preg_match($pattern, $value, $matches)) {
            $baseDate = strtoupper($matches[1]) === 'NOW' ? date('Y-m-d H:i:s') : date('Y-m-d H:i:s', strtotime($matches[1]));
            $modifier = "{$matches[2]}{$matches[3]} {$matches[4]}";
    
            return date('Y-m-d H:i:s', strtotime("$modifier", strtotime($baseDate)));
        }
        
        return $value;
    }
    private function findBy($repository, string $classname, array $criteria, array|null $orderBy = null, int|null $limit = null, int|null $offset = null): array
    {
        $dql = "SELECT e FROM $classname e WHERE 1=1";

        foreach ($criteria as $property => $options) {
            $operator = $options['operator'];
            $value    = $options['value'];
            $value    = $this->parseDateExpression($value);

            $operator = match($options['operator']) {
                RequestOperators::LIKE->value           => "LIKE '%{$value}%'",
                RequestOperators::LEFT_LIKE->value      => "LIKE '%{$value}'",
                RequestOperators::RIGHT_LIKE->value     => "LIKE '{$value}%'",
                RequestOperators::NOT_LIKE->value       => "NOT LIKE '%{$value}%'",
                RequestOperators::NOT_LEFT_LIKE->value  => "NOT LIKE '%{$value}'",
                RequestOperators::NOT_RIGHT_LIKE->value => "NOT LIKE '{$value}%'",
                RequestOperators::IS_NOT->value         => "!= '{$value}'",
                RequestOperators::EQUAL->value          => "= '{$value}'",
                RequestOperators::GREATER->value        => "> '{$value}'",
                RequestOperators::LESS->value           => "< '{$value}'",
                RequestOperators::GREATER_EQUAL->value  => ">= '{$value}'",
                RequestOperators::LESS_EQUAL->value     => "<= '{$value}'",
                RequestOperators::IN->value             => "IN ('{$value}')",
                RequestOperators::BETWEEN->value        => "BETWEEN '{$value[0]}' AND '{$value[1]}'",
                RequestOperators::IS_NULL->value        => "IS NULL",
                RequestOperators::IS_NOT_NULL->value    => "IS NOT NULL",
                default                                 => "= '{$value}'",
            };

            $dql.= " AND e.$property $operator ";
        }



        // Ajouter le tri
        if (!empty($orderBy)) {
            $orderParts = [];
            foreach ($orderBy as $property => $direction) {
                $orderParts[] = "e.$property $direction";
            }
            $dql .= " ORDER BY " . implode(', ', $orderParts);
        }



        $query = $this->entityManager->createQuery($dql);
        // foreach ($parameters as $key => $value) {
        //     $query->setParameter($key, $value);
        // }

        if ($limit !== null) {
            $query->setMaxResults($limit);
        }
        if ($offset !== null) {
            $query->setFirstResult($offset);
        }

        return $query->getResult();
    }
}