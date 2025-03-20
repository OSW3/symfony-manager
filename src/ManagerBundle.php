<?php 
namespace OSW3\Manager;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use OSW3\Manager\DependencyInjection\Configuration;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class ManagerBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        $projectDir = $container->getParameter('kernel.project_dir');


        // Generate the YAML bundle configuration file in the project
        // --
        
        (new Configuration)->generateProjectConfig($projectDir);
    }
}