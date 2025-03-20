<?php 
namespace OSW3\Manager\Service;

use Doctrine\ORM\EntityManagerInterface;
use OSW3\Manager\DependencyInjection\Configuration;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
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

    private function isMenuActive(string $classname): bool {
        $options      = $this->params[$classname];
        $request      = $this->requestStack->getCurrentRequest();

        $currentRoute = $request->attributes->get('_route');
        $currentPath  = $request->attributes->get('path');

        $routeMatch   = in_array($currentRoute, ['manager:entity:index', 'manager:entity:read']);
        $pathMatch    = in_array($currentPath, [$options['index']['path'], $options['read']['path']]);

        return $routeMatch && $pathMatch;
    }

    public function getIndexUrl(string $classname): string {
        $options = $this->params[$classname];

        return $this->urlGenerator->generate('manager:entity:index', [
            'path' => $options['index']['path']
        ], false);
    }

    public function getReadUrl(string $classname, int|string $id): string {
        $options = $this->params[$classname];

        return $this->urlGenerator->generate('manager:entity:read', [
            'path' => $options['read']['path'],
            'id' => $id,
        ], false);
    }



    // SINGLE ENTITIES
    // --

    public function getEntityClassnameByPath(string $path): ?string {
        foreach ($this->params as $entity => $options) {
            if ($options['index']['path'] === $path) {
                return $entity;
            }
            else if ($options['read']['path'] === $path) {
                return $entity;
            }
        }

        return null;
    }

    /**
     * Get entity options
     * 
     * @return array
     */
    private function getEntityOptions(string $classname): array {
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

    public function getColumns(object $entity): array {
        $options = $this->params[$entity::class];
        $columns = $options['index']['columns'];

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

        $reflection = new \ReflectionClass($entity);
        $attributes = [];

        foreach ($reflection->getProperties() as $property) {
            $propertyName = $property->getName();
            $getter = 'get' . ucfirst($propertyName);

            if (method_exists($entity, $getter)) {
                $attributes[$propertyName] = $entity->$getter();
            }
        }

        return $attributes;
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

    public function getNotFound(string $classname): string {
        $options = $this->getEntityOptions($classname);
        return $options['labels']['not_found'];
    }
}