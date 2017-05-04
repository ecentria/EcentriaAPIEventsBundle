<?php
/*
 * This file is part of the ecentria group, inc. software.
 *
 * (c) 2015, ecentria group, inc.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ecentria\Libraries\EcentriaAPIEventsBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * @author Justin Shanks <justin.shanks@opticsplanet.com>
 * 
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class EcentriaAPIEventsExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');

        $configuration = $this->getConfiguration($configs, $container);
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter($this->getAlias() . '.domain_message_prefix', $config['domain_message_prefix']);

        $container->getDefinition('ecentria.api.domain_message.manager')
            ->addMethodCall('setEventPrefix', [$config['domain_message_prefix']]);

        $container->getDefinition('ecentria.api.domain_message_consumer.service')
            ->addArgument(new Reference($config['domain_message_serializer']))
            ->addArgument(new Reference($config['requeue_as_new_producer']))
            ->addMethodCall('setMessageClassName', [$config['domain_message_class_name']]);
    }

    /**
     * {@inheritDoc}
     */
    public function getAlias()
    {
        return 'ecentria_api_events';
    }
}
