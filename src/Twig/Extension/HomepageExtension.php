<?php 
namespace OSW3\Manager\Twig\Extension;

use Twig\TwigFunction;
use Twig\Extension\AbstractExtension;
use OSW3\Manager\DependencyInjection\Configuration;
use OSW3\Manager\Twig\Runtime\HomepageExtensionRuntime;

class HomepageExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            // manager_homepage_route
            new TwigFunction(Configuration::NAME."_homepage_route", [HomepageExtensionRuntime::class, 'getRoute']),

            // manager_homepage_label
            new TwigFunction(Configuration::NAME."_homepage_label", [HomepageExtensionRuntime::class, 'getLabel']),
        ];
    }
}