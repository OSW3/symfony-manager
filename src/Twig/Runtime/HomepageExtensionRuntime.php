<?php 
namespace OSW3\Manager\Twig\Runtime;

use OSW3\Manager\Service\HomepageService;
use Twig\Extension\RuntimeExtensionInterface;

class HomepageExtensionRuntime implements RuntimeExtensionInterface
{
    public function __construct(
        private HomepageService $homepageService
    ){}

    public function getRoute(): string {
        return $this->homepageService->getRoute();
    }

    public function getLabel(): string {
        return $this->homepageService->getLabel();
    }
}