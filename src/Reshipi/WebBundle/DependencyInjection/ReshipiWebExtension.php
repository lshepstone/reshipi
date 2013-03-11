<?php

namespace Reshipi\WebBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class ReshipiWebExtension extends Extension
{
//    /**
//     * {@inheritDoc}
//     */
//    public function load(array $configs, ContainerBuilder $container)
//    {
//        $configuration = new Configuration();
//        $config = $this->processConfiguration($configuration, $configs);
//
//        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
//        $loader->load('services.yml');
//    }

    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        foreach ($configs as $config) {
            $this->registerParameters($config, $container, 'reshipi_web');
        }

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
    }

    /**
     * Registers all config key-value pairs as parameters.
     *
     * @param array $config Array of config key-value pairs
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     * @param $namespace
     */
    private function registerParameters(array $config, ContainerBuilder $container, $namespace)
    {
        foreach ($config as $key => $value) {
            if (is_array($value)) {
                $this->registerParameters($value, $container, "$namespace.$key");
            }

            $container->setParameter("$namespace.$key", $value);
        }
    }
}
