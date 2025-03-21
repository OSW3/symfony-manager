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

    public function getNavCreateElements(): array {
        return $this->entityService->getNavCreateElements();
    }

    public function getIndexUrl(string $classname): string {
        return $this->entityService->getIndexUrl($classname);
    }

    public function getCreateUrl(string $classname): string {
        return $this->entityService->getCreateUrl($classname);
    }

    public function getReadUrl(string $classname, int|string $id): string {
        return $this->entityService->getReadUrl($classname, $id);
    }

    public function getUpdateUrl(string $classname, int|string $id): string {
        return $this->entityService->getUpdateUrl($classname, $id);
    }

    public function getDeleteUrl(string $classname, int|string $id): string {
        return $this->entityService->getDeleteUrl($classname, $id);
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

    public function label_AddNew(string $classname): string {
        return $this->entityService->label_AddNew($classname);
    }

    public function label_EditItem(string $classname): string {
        return $this->entityService->label_EditItem($classname);
    }

    public function label_ViewItems(string $classname): string {
        return $this->entityService->label_ViewItems($classname);
    }

    public function label_ViewItem(string $classname): string {
        return $this->entityService->label_ViewItem($classname);
    }

    public function label_NotFound(string $classname): string {
        return $this->entityService->label_NotFound($classname);
    }

    public function label_AllItems(string $classname): string {
        return $this->entityService->label_AllItems($classname);
    }
}