<?php 
namespace OSW3\Manager\Twig\Runtime;

use OSW3\Manager\Service\EntityService;
use Twig\Extension\RuntimeExtensionInterface;

class EntityExtensionRuntime implements RuntimeExtensionInterface
{
    public function __construct(
        private EntityService $entityService
    ){}

    public function getNavElements(): array {
        return $this->entityService->getNavElements();
    }

    public function getIndexUrl(string $classname): string {
        return $this->entityService->getIndexUrl($classname);
    }

    public function getReadUrl(string $classname, int|string $id): string {
        return $this->entityService->getReadUrl($classname, $id);
    }


    // REPOSITORY
    // --

    public function getId(object $entity) {
        return $entity->getId();
    }

    public function getColumns(object $entity): array {
        return $this->entityService->getColumns($entity);
    }

    public function getAttributes(object $entity): array {
        return $this->entityService->getAttributes($entity);
    }


    // LABELS
    // --
    
    public function getName(string $classname): string {
        return $this->entityService->getName($classname);
    }
    
    public function getSingularName(string $classname): string {
        return $this->entityService->getSingularName($classname);
    }

    public function getMenuLabel(string $classname): string {
        return $this->entityService->getMenuLabel($classname);
    }

    public function getNotFound(string $classname): string {
        return $this->entityService->getNotFound($classname);
    }
}