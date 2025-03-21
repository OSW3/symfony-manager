<?php 
namespace OSW3\Manager\DependencyInjection;

use Symfony\Component\Filesystem\Path;
use Symfony\Component\Config\FileLocator;
use OSW3\Manager\DependencyInjection\Configuration;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;

class ManagerExtension extends Extension implements PrependExtensionInterface 
{
	/**
	 * Bundle configuration Injection
	 *
	 * @param array $configs
	 * @param ContainerBuilder $container
	 *
	 * @return void
	 */
	public function load(array $configs, ContainerBuilder $container)
    {
		// Default Config
		// --
		
		$config = $this->processConfiguration(new Configuration(), $configs);
		$container->setParameter($this->getAlias(), $config);		
        

		// Bundle config location
		// --
		
		$locator = new FileLocator(Path::join(__DIR__, "/../../", "config"));
		$loader = new YamlFileLoader($container, $locator);
		

		// Services Injection
		// --
		
		$loader->load('services.yaml');
    }

	/**
	 * Prepend some data to the global app configuration
	 *
	 * @param ContainerBuilder $container
	 *
	 * @return void
	 */
	public function prepend(ContainerBuilder $container)
    {
        // Extend Twig configuration
        // --

        $twigConfig = [];
		$this->extendsTwigConfig($twigConfig, Path::join(__DIR__, "/../../", "templates"), Configuration::NAME);

        $container->prependExtensionConfig('twig', $twigConfig);
    }

	/**
	 * Add a path to extends twig sources
	 *
	 * @param array $twigConfig
	 * @param string $directory
	 * @param string $alias
	 * @return void
	 */
	private function extendsTwigConfig(array &$twigConfig, string $directory, string $alias) 
	{
		if (is_dir($directory))
		{
			$twigConfig['paths'][$directory] = $alias;
		}
	}
}