<?php 
namespace OSW3\Manager\Service;

use OSW3\Manager\DependencyInjection\Configuration;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\DependencyInjection\ContainerInterface;

class HomepageService
{
    private array $params;

    public function __construct(
        #[Autowire(service: 'service_container')] 
        private ContainerInterface $container,
        private TranslatorInterface $translation
    ){
        $this->params = $container->getParameter(Configuration::NAME)['homepage'];
    }

    public function getRoute(): string {
        return $this->params['route'];
    }

    public function getLabel(): string {
        return $this->translation->trans( $this->params['label'], [], Configuration::DOMAIN);
    }
}