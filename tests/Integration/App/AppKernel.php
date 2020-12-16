<?php
declare(strict_types=1);
/*
 * This file is part of the eCORE CART software.
 *
 * (c) 2020 ecentria, Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ecentria\Libraries\EcentriaAPIEventsBundle\Tests\Integration\App;

use Ecentria\Libraries\EcentriaAPIEventsBundle\EcentriaAPIEventsBundle;
use JMS\SerializerBundle\JMSSerializerBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Routing\RouteCollectionBuilder;

/**
 * Class AppKernel
 *
 * @author Maxim Starovoitov <maxim.starovoitov@ecentria.com>
 */
class AppKernel extends Kernel
{
    use MicroKernelTrait;

    /**
     * {@inheritDoc}
     */
    public function registerBundles()
    {
        return [
            new FrameworkBundle(),
            new JMSSerializerBundle(),
            new EcentriaAPIEventsBundle(),
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function getProjectDir()
    {
        return __DIR__;
    }

    /**
     * {@inheritDoc}
     */
    public function getCacheDir()
    {
        return sprintf('%s/var/cache', $this->getProjectDir());
    }

    /**
     * {@inheritDoc}
     */
    public function getLogDir()
    {
        return sprintf('%s/var/log', $this->getProjectDir());
    }

    /**
     * {@inheritDoc}
     */
    protected function configureRoutes(RouteCollectionBuilder $routes)
    {
    }

    /**
     * {@inheritDoc}
     */
    protected function configureContainer(ContainerBuilder $builder, LoaderInterface $loader)
    {
        // SF Framework Stuff
        $builder->prependExtensionConfig(
            'framework',
            [
                'secret' => 'secret',
                'test' => true,
                'translator' => false
            ]
        );

        $builder->prependExtensionConfig(
            'jms_serializer',
            [
                'metadata' => [
                    'cache' => false
                ],
                'property_naming' => [
                    'separator' => '_'
                ]
            ]
        );

        $builder->prependExtensionConfig(
            'ecentria_api_events',
            [
                'domain_message_serializer' => 'jms_serializer.serializer'
            ]
        );

        $builder->setAlias('serializer', 'jms_serializer.serializer');
    }
}
