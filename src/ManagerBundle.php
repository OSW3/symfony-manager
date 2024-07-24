<?php 
namespace OSW3\Manager;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use OSW3\Manager\DependencyInjection\Configuration;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class ManagerBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        (new Configuration)->generateProjectConfig($container->getParameter('kernel.project_dir'));
    }
}